<?php

namespace App\Http\Controllers\Backend\TrkEquipments;

use App\Exports\Equipments\EquipmentsExport;
use App\Exports\Equipments\EquipmentsExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\TrkEquipments\TrkEquipmentFilter;
use App\Http\Requests\TrkEquipments\StoreTrkEquipmentFormRequest;
use App\Http\Requests\TrkEquipments\StoreTrkEquipmentFromTrkRoomFormRequest;
use App\Http\Requests\TrkEquipments\TrkEquipmentFilterRequest;
use App\Http\Requests\TrkEquipments\UpdateEquipmentInTrkEquipmentsFromEquipmentNameFormRequest;
use App\Http\Requests\TrkEquipments\UpdateTrkEquipmentFormRequest;
use App\Http\Requests\TrkRooms\SetEquipmentsToAnotherRoomFormRequest;
use App\Jobs\TrkEquipments\NewTrkEquipmentEmailJob;
use App\Models\Avrs\AvrEquipment;
use App\Models\Axes\Axe;
use App\Models\Buildings\Building;
use App\Models\Checklists\ChecklistAirDiffuser;
use App\Models\Checklists\ChecklistAirDuct;
use App\Models\Checklists\ChecklistAirExtract;
use App\Models\Checklists\ChecklistAirSupply;
use App\Models\Checklists\ChecklistBalk;
use App\Models\Checklists\ChecklistConditioner;
use App\Models\Checklists\ChecklistFancoil;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentStatuses\EquipmentStatus;
use App\Models\Floors\Floor;
use App\Models\Rooms\Room;
use App\Models\Systems\System;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class TrkEquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TrkEquipment::class, 'trk_equipment');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TrkEquipmentFilterRequest $request): Response
    {
        $data = $request->validated();

        $system_ids = System::all()->pluck('id')->toArray();

        $filter = app()->make(TrkEquipmentFilter::class, ['queryParams' => array_filter($data)]);

        $trk_equipments = TrkEquipment::filter($filter)
            ->with([
                'trk_room',
                'system',
                'equipment_name',
                'equipment_status',
                'spare_parts',
                'parameters',
                'work_periods',
                'users',
                'avrs',
                'repairs',
                'checklists_air_supply',
                'checklists_air_extract'
            ])
            ->select([
                'trk_equipments.*',
                'buildings.name',
                'floors.name',
                'rooms.name',
                'trks.sort_order',
                'equipment_names.name'
                ])
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_equipments.trk_room_id')
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->join('buildings', 'buildings.id', '=', 'trk_rooms.building_id')
            ->join('floors', 'floors.id', '=', 'trk_rooms.floor_id')
            ->join('rooms', 'rooms.id', '=', 'trk_rooms.room_id')
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->orderBy('trks.sort_order', 'asc')
            ->orderBy('buildings.name', 'asc')
            ->orderBy('floors.name', 'asc')
            ->orderBy('rooms.name', 'asc')
            ->orderBy('equipment_names.name', 'asc')
            ->paginate(config('backend.trk_equipments.pagination'));

        $all_trks = Trk::orderBy('sort_order')->whereIn('id', Auth::user()->responsibility_trk_ids())->get();

        if (Auth::user()->hasRole('sadmin')) {
            $all_trks = Trk::orderBy('sort_order')->get();
        }

        return \response()->view('backend.trk_equipments.index', [
            'trk_equipments' => $trk_equipments,
            'old_filters' => $data,
            'all_trks' => $all_trks,
            'all_systems' => System::orderBy('sort_order')->whereIn('id', $system_ids)->get(),
            'all_equipment_names' => EquipmentName::orderBy('name')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_buildings' => Building::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $first_trk = Trk::orderBy('sort_order')->pluck('id')->first();
        $first_trk_room_ids = TrkRoom::where('trk_id', $first_trk)->pluck('room_id')->toArray();
        $first_trk_rooms = Room::whereIn('id', $first_trk_room_ids)->orderBy('name')->get();

        return \response()->view('backend.trk_equipments.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => $first_trk_rooms,
            'equipment_statuses' => EquipmentStatus::orderBy('sort_order')->get(),
            'divisions' => UserDivision::orderBy('name')->where('visibility', 1)->get(),
            'buildings' => Building::orderBy('sort_order')->get(),
            'floors' => Floor::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkEquipmentFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $not_exists_room = '';
                $not_exists_equipment = '';

                foreach ($data['equipment_names'] as $equipment_name) {

                    $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                        ->where('room_id', $data['room_id'])
                        ->where('building_id', $data['building_id'])
                        ->where('floor_id', $data['floor_id'])
                        ->first();

                    if (empty($trk_room)) {

                        $room_name = Room::where('id', $data['room_id'])->pluck('name')->first();
                        $trk_name = Trk::where('id', $data['trk_id'])->pluck('name')->first();
                        $building_name = Building::where('id', $data['building_id'])->pluck('name')->first();
                        $floor_name = Floor::where('id', $data['floor_id'])->pluck('name')->first();
                        $not_exists_room .= '[ ' . $trk_name . ', ' . $building_name . ', ' . $floor_name . ', ' . $room_name . ']; ';
                    }

                    $equipment = EquipmentName::where('name', $equipment_name)->first();

                    if (empty($equipment)) {

                        $not_exists_equipment .= $equipment_name . ', ';
                    }

                    $trk_equipment = null;

                    if (!empty($trk_room) && !empty($equipment)) {

                        TrkEquipment::withTrashed()->updateOrCreate([
                            'trk_room_id' => $trk_room->id,
                            'system_id' => $data['system_id'],
                            'equipment_name_id' => $equipment->id,
                            'equipment_status_id' => $data['equipment_status_id'],
                            'responsible_division_id' => $data['responsible_division_id'],
                            'comment' => $data['comment'],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ])->restore();

                        $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                            ->where('system_id', $data['system_id'])
                            ->where('equipment_name_id', $equipment->id)
                            ->where('equipment_status_id', $data['equipment_status_id'])
                            ->where('responsible_division_id', $data['responsible_division_id'])
                            ->first();
                    }

                }

                $message = '';

                if (strlen($not_exists_room) > 0) {
                    $message .= 'Помещения: ' . $not_exists_room . ' отсутствуют. Создайте сначала их. ';
                }

                if (strlen($not_exists_equipment) > 0) {
                    $message .= 'Оборудование с таким названием не существует: ' . $not_exists_equipment . ' создайте его в разделе Архитектура/Оборудование.';
                }

                if (strlen($message) > 0) {
                    return redirect()->route('trk_equipments.index')->with('alert', 'Сохранена часть. ' . $message);
                }

                if(!empty($trk_equipment->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewTrkEquipmentEmailJob::dispatch($emails, $trk_equipment);
                }

                return redirect()->route('trk_equipments.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_trk_room(TrkRoom $trk_room): Response
    {
        $this->authorize('create');

        return \response()->view('backend.trk_equipments.create_from_trk_room', [
            'trk_room' => $trk_room,
            'systems' => System::orderBy('name')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'equipment_statuses' => EquipmentStatus::orderBy('sort_order')->get(),
            'divisions' => UserDivision::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreTrkEquipmentFromTrkRoomFormRequest $request): RedirectResponse
    {
        $this->authorize('create');

        if ($request->isMethod('post')) {
            Log::info('User try to store equipment from trk room',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $not_exists_room = '';
                $not_exists_equipment = '';
                $trk_equipment = null;

                foreach ($data['equipment_names'] as $equipment_name) {

                    $trk_room = TrkRoom::find($data['trk_room_id']);

                    if (empty($trk_room)) {

                        $room_name = Room::where('id', $data['room_id'])->pluck('name')->first();
                        $not_exists_room .= $room_name . ', ';
                    }

                    $equipment = EquipmentName::where('name', $equipment_name)->first();

                    if (empty($equipment)) {

                        $not_exists_equipment .= $equipment_name . ', ';
                    }

                    if (!empty($trk_room) && !empty($equipment)) {

                        TrkEquipment::withTrashed()->updateOrCreate([
                            'trk_room_id' => $trk_room->id,
                            'system_id' => $data['system_id'],
                            'equipment_name_id' => $equipment->id,
                            'equipment_status_id' => $data['equipment_status_id'],
                            'responsible_division_id' => $data['responsible_division_id'],
                            'comment' => $data['comment'],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ])->restore();

                        $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                            ->where('system_id', $data['system_id'])
                            ->where('equipment_name_id', $equipment->id)
                            ->where('equipment_status_id', $data['equipment_status_id'])
                            ->where('responsible_division_id', $data['responsible_division_id'])
                            ->first();
                    }

                }

                $message = '';

                if (strlen($not_exists_room) > 0) {
                    $message .= 'Помещения: ' . $not_exists_room . ' отсутствуют на этом ТРК. Создайте их в разделе ТРК/Помещения. ';
                }

                if (strlen($not_exists_equipment) > 0) {
                    $message .= 'Оборудование с таким названием не существует: ' . $not_exists_equipment . ' создайте его в разделе Архитектура/Оборудование.';
                }

                if (strlen($message) > 0) {
                    return redirect()->route('trk_room.show', $data['trk_room_id'])->with('alert', 'Сохранена часть. ' . $message);
                }

                if(!empty($trk_equipment->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewTrkEquipmentEmailJob::dispatch($emails, $trk_equipment);
                }


                return redirect()->route('trk_room.show', $data['trk_room_id'])->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrkEquipment $trk_equipment): Response
    {

        $checklists_conditioner = null;
        $checklists_fancoil = null;
        $checklists_balk = null;
        $checklists_air_supply = null;
        $checklists_air_extract = null;
        $checklists_air_duct = null;
        $checklists_air_diffuser = null;

        if ($trk_equipment->system->name == System::AIR_CONDITION) {
            $checklists_conditioner = ChecklistConditioner::where('trk_equipment_id', $trk_equipment->id)->get();
            $checklists_fancoil = ChecklistFancoil::where('trk_equipment_id', $trk_equipment->id)->get();
            $checklists_balk = ChecklistBalk::where('trk_equipment_id', $trk_equipment->id)->get();
        }

        if ($trk_equipment->system->name == System::AIR_RECYCLE) {
            $checklists_air_supply = ChecklistAirSupply::where('trk_equipment_id', $trk_equipment->id)->get();
            $checklists_air_extract = ChecklistAirExtract::where('trk_equipment_id', $trk_equipment->id)->get();
            $checklists_air_duct = ChecklistAirDuct::where('trk_equipment_id', $trk_equipment->id)->get();
            $checklists_air_diffuser = ChecklistAirDiffuser::where('trk_equipment_id', $trk_equipment->id)->get();
        }

        $avrs = AvrEquipment
            ::where('trk_equipment_id', $trk_equipment->id)
            ->join('avrs', 'avrs.id', '=', 'avr_equipments.avr_id')
            ->orderBy('avrs.date', 'desc')
            ->select('avr_equipments.*')
            ->get();

        return \response()->view('backend.trk_equipments.show', [
            'trk_equipment' => $trk_equipment,
            'checklists_conditioner' => $checklists_conditioner,
            'checklists_fancoil' => $checklists_fancoil,
            'checklists_balk' => $checklists_balk,
            'checklists_air_supply' => $checklists_air_supply,
            'checklists_air_extract' => $checklists_air_extract,
            'checklists_air_duct' => $checklists_air_duct,
            'checklists_air_diffuser' => $checklists_air_diffuser,
            'avrs' => $avrs,
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'equipments' => EquipmentName::orderBy('name')->get(),
            'systems' => System::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrkEquipment $trk_equipment): Response
    {
        $trk_room_ids = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)->pluck('room_id')->toArray();
        $rooms = Room::whereIn('id', $trk_room_ids)->orderBy('name')->get();

        $trk_building_ids = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)->pluck('building_id')->toArray();
        $buildings = Building::whereIn('id', $trk_building_ids)->orderBy('name')->get();

        $trk_floor_ids = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)->pluck('floor_id')->toArray();
        $floors = Floor::whereIn('id', $trk_floor_ids)->orderBy('name')->get();

        return \response()->view('backend.trk_equipments.edit', [
            'trk_equipment' => $trk_equipment,
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => $rooms,
            'buildings' => $buildings,
            'floors' => $floors,
            'equipment_statuses' => EquipmentStatus::orderBy('sort_order')->get(),
            'divisions' => UserDivision::orderBy('name')->get(),
            'axes' => Axe::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        if ($request->isMethod('patch')) {

            $data = $request->validated();

            $trk_room = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)
                ->where('building_id', $data['building_id'])
                ->where('floor_id', $data['floor_id'])
                ->where('room_id', $data['room_id'])
                ->first();

            if (empty($trk_room->id)) {

                $building = Building::find($data['building_id']);
                $floor = Floor::find($data['floor_id']);
                $room = Room::find($data['room_id']);
                return redirect()->back()->with('error', 'Не существует такого помещения: ' . $trk_equipment->trk_room->trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
            }

            //TODO сделать перенос технических мероприятий, чеклистов балок, фанкойлов, кондиционеров на другое оборудование


            TrkEquipment::where('trk_room_id', $trk_room->id)
                ->where('system_id', $data['system_id'])
                ->where('equipment_name_id', $data['equipment_name_id'])
                ->whereNotNull('deleted_at')
                ->forceDelete();

            $trk_equipment->update([
                'trk_room_id' => $trk_room->id,
                'system_id' => $data['system_id'],
                'equipment_name_id' => $data['equipment_name_id'],
                'equipment_status_id' => $data['equipment_status_id'],
                'responsible_division_id' => $data['responsible_division_id'],
                'comment' => $data['comment'],
                'axis_id' => $data['axis_id'],
                'last_editor_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    public function change_equipment_name_in_trk_equipments(UpdateEquipmentInTrkEquipmentsFromEquipmentNameFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        //$this->authorize('change_work_name_in_avrs');

        Log::info('user try to change equipment name in trk equipments from equipment name show page', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'equipment' => $trk_equipment->equipment_name->name,
        ]);

        if ($request->isMethod('patch')) {
            try {

                $data = $request->validated();

                $trk_equipments = TrkEquipment::where('equipment_name_id', $trk_equipment->equipment_name_id)->get();
                $old_equipment_name = EquipmentName::where('id', $trk_equipment->equipment_name_id)->first();

                DB::beginTransaction();

                foreach ($trk_equipments as $trk_equipment) {

                    $trk_equipment->update([
                        'equipment_name_id' => $data['equipment_name_id'],
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('equipment_names.show', $old_equipment_name->id)->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }

            return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function set_equipments_from_this_to_another_room(SetEquipmentsToAnotherRoomFormRequest $request, TrkRoom $trk_room): RedirectResponse
    {
        if ($request->isMethod('patch')) {

            $data = $request->validated();

            $new_trk_room = TrkRoom::where('trk_id', $trk_room->trk->id)
                ->where('building_id', $data['building_id'])
                ->where('floor_id', $data['floor_id'])
                ->where('room_id', $data['room_id'])
                ->first();

            if (empty($new_trk_room->id)) {
                return redirect()->back()->with('error', 'Такого помещения не существует');
            }

            try {

                DB::beginTransaction();

                foreach ($trk_room->equipments as $equipment) {

                    if(
                        !TrkEquipment::where('trk_room_id', $new_trk_room->id)
                            ->where('system_id', $equipment->system->id)
                            ->where('equipment_name_id', $equipment->equipment_name->id)
                            ->exists()
                    )
                    {
                        $equipment->update([
                            'trk_room_id' => $new_trk_room->id,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()->back()->with('success', 'Изменения сохранены');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }

        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrkEquipment $trk_equipment): RedirectResponse
    {
        try {

            if (count($trk_equipment->repairs) > 0) {
                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть ремонт, привяжите к другому оборудованию или удалите все');
            }

            if (count($trk_equipment->avrs) > 0) {
                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть акты, привяжите к другому оборудованию или удалите все');
            }

            if (count($trk_equipment->spare_parts) > 0) {
                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть запчасти, привяжите к другому оборудованию или удалите все');
            }

            if (count($trk_equipment->parameters) > 0) {
                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть параметры, привяжите к другому оборудованию или удалите все');
            }

            if (count($trk_equipment->checklists_air_supply) > 0) {
                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть чеклисты притока, привяжите к другому оборудованию или удалите все');
            }

            if (count($trk_equipment->checklists_air_extract) > 0) {
                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть чеклисты вытяжки, привяжите к другому оборудованию или удалите все');
            }

            if (count($trk_equipment->users) > 0) {
                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть потребители, привяжите к другому оборудованию или удалите все');
            }

//            if(count($trk_equipment->work_periods) > 0)
//            {
//                return redirect()->back()->with('error', 'Оборудование нельзя удалить, на нем есть запланированные тех. мероприятия, привяжите к другому оборудованию или удалите все');
//            }

            //TODO удаление актов, ремонта, запчастей, характеристик, технических мероприятий,
            // чек листов (приток, вытяжка, балка, фанкойл, кондиционер). Думаю диффузоры и возддуховоды
            // можно оставить, т.к. там есть привязка к помещению помимо оборудования
            // перед удалением акта необходимо проверить, что другого оборудования нет в этом акте

            $trk_equipment->update([
                'destroyer_id' => Auth::id(),
            ]);

            $trk_equipment->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('trk_equipments.index')->with('success', 'Данные удалены');

    }


    public function export(TrkEquipmentFilterRequest $request)
    {
        $data = $request->validated();

        $filter = app()->make(TrkEquipmentFilter::class, ['queryParams' => array_filter($data)]);

        $equipments = TrkEquipment::filter($filter)
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_equipments.trk_room_id')
            ->join('floors', 'floors.id', '=', 'trk_rooms.floor_id')
            ->join('buildings', 'buildings.id', '=', 'trk_rooms.building_id')
            ->join('rooms', 'rooms.id', '=', 'trk_rooms.room_id')
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->orderBy('buildings.name')
            ->orderBy('floors.sort_order')
            ->orderBy('rooms.name')
            ->orderByRaw('CONVERT(equipment_names.name, SIGNED) asc')
            ->get();

        if (count($equipments) > 1600) {

            return redirect()->back()->with('error', 'Оборудования для выгрузки ' . count($equipments) . ', 1600 это максимум для комфортной работы приложения, попробуйте сузить фильтр для уменьшения количества.');
        }

        if (count($equipments) == 0) {

            return redirect()->back()->with('error', 'Нет такого оборудования');
        }

        switch ($data['file_type']) {
            case '.pdf':

                return (new EquipmentsExportPdf(
                    $equipments,
                    $data,
                ))->export_pdf();

            case '.html':
                return Excel::download(new EquipmentsExport(
                    $equipments,
                    $data,
                ), 'Оборудование__' . '.html');

            default:
                return Excel::download(new EquipmentsExport(
                    $equipments,
                    $data,
                ), 'Оборудование__' . '.xlsx');
        }
    }

    public function index_frame(TrkEquipmentFilterRequest $request): Response
    {
        $data = $request->validated();

        $system_ids = System::all()->pluck('id')->toArray();

        $filter = app()->make(TrkEquipmentFilter::class, ['queryParams' => array_filter($data)]);

        $trk_equipments = TrkEquipment::filter($filter)
            ->with([
                'trk_room',
                'system',
                'equipment_name',
                'equipment_status',
                'spare_parts',
                'parameters',
                'work_periods',
                'users',
                'avrs',
                'repairs',
                'checklists_air_supply',
                'checklists_air_extract'
            ])
            ->select([
                'trk_equipments.*',
                'buildings.name',
                'floors.name',
                'rooms.name',
                'trks.sort_order',
                'equipment_names.name'
            ])
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_equipments.trk_room_id')
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->join('buildings', 'buildings.id', '=', 'trk_rooms.building_id')
            ->join('floors', 'floors.id', '=', 'trk_rooms.floor_id')
            ->join('rooms', 'rooms.id', '=', 'trk_rooms.room_id')
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->orderBy('trks.sort_order', 'asc')
            ->orderBy('buildings.name', 'asc')
            ->orderBy('floors.name', 'asc')
            ->orderBy('rooms.name', 'asc')
            ->orderBy('equipment_names.name', 'asc')
            ->paginate(config('backend.trk_equipments.pagination'));

        $all_trks = Trk::orderBy('sort_order')->whereIn('id', Auth::user()->responsibility_trk_ids())->get();

        if (Auth::user()->hasRole('sadmin')) {
            $all_trks = Trk::orderBy('sort_order')->get();
        }

        return \response()->view('backend.trk_equipments.index_frame', [
            'trk_equipments' => $trk_equipments,
            'old_filters' => $data,
            'all_trks' => $all_trks,
            'all_systems' => System::orderBy('sort_order')->whereIn('id', $system_ids)->get(),
            'all_equipment_names' => EquipmentName::orderBy('name')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_buildings' => Building::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
        ]);
    }
}
