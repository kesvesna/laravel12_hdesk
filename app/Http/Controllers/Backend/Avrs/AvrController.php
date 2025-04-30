<?php

namespace App\Http\Controllers\Backend\Avrs;

use App\Exports\Avrs\AvrExportPdf;
use App\Exports\Avrs\AvrsExport;
use App\Exports\Avrs\AvrsExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\Avrs\AvrFilter;
use App\Http\Requests\Avrs\AvrFilterRequest;
use App\Http\Requests\Avrs\SetAvrsToAnotherEquipmentFormRequest;
use App\Http\Requests\Avrs\SetAvrsToAnotherRoomFormRequest;
use App\Http\Requests\Avrs\StoreAvrFormRequest;
use App\Http\Requests\Avrs\StoreAvrFromRepairFormRequest;
use App\Http\Requests\Avrs\StoreAvrFromTechActFormRequest;
use App\Http\Requests\Avrs\StoreAvrFromTrkEquipmentFormRequest;
use App\Http\Requests\Avrs\StoreAvrFromTrkRoomFormRequest;
use App\Http\Requests\Avrs\UpdateAvrFormRequest;
use App\Jobs\Avrs\NewAvrEmailJob;
use App\Models\Avrs\Avr;
use App\Models\Avrs\AvrEquipment;
use App\Models\Avrs\AvrExecutor;
use App\Models\Avrs\AvrSparePart;
use App\Models\Avrs\AvrWork;
use App\Models\Buildings\Building;
use App\Models\DocCommunications\DocCommunication;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentWorkPeriods\EquipmentWorkPeriod;
use App\Models\Floors\Floor;
use App\Models\OperationApplications\OperationApplication;
use App\Models\Rooms\Room;
use App\Models\SpareParts\SparePartName;
use App\Models\Systems\System;
use App\Models\TechActs\TechAct;
use App\Models\Towns\Town;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRoomRepairs\TrkRoomRepair;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use App\Models\WorkNames\WorkName;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AvrController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Avr::class, 'avr');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AvrFilterRequest $request): Response
    {

        $data = $request->validated();

        $user_trks = UserResponsibilityTrkSystem::where('user_id', Auth::id())
            ->pluck('trk_id')
            ->toArray();

        $all_trks = Trk::orderBy('sort_order')->get();

        if (count($user_trks) > 0 && !auth()->user()->hasRole('sadmin')) {
            $data['trk_ids'] = $user_trks;
            $all_trks = Trk::whereIn('id', $user_trks)->get();
        }

        $user_systems = UserResponsibilityTrkSystem::where('user_id', Auth::id())
            ->pluck('system_id')
            ->toArray();

        $all_systems = System::orderBy('name')->get();

        if (count($user_systems) > 0 && !auth()->user()->hasRole('sadmin')) {
            $data['system_ids'] = $user_systems;
            $all_systems = System::whereIn('id', $user_systems)->get();
        }

        $filter = app()->make(AvrFilter::class, ['queryParams' => array_filter($data)]);

        $avrs = Avr::filter($filter)
            ->with([
                'trk_room',
                'system',
                'avr_equipments',
                'avr_works',
                'avr_spare_parts',
                'avr_executors'
            ])
            ->orderBy('date', 'desc')
            ->paginate(config('backend.avrs.pagination'));

        return \response()->view('backend.avrs.index', [
            'avrs' => $avrs,
            'old_filters' => $data,
            'all_trks' => $all_trks,
            'all_systems' => $all_systems,
            'all_equipment_names' => EquipmentName::orderBy('name')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_work_names' => WorkName::orderBy('name')->get(),
            'all_executors' => User::orderBy('name')->get(),
            'all_buildings' => Building::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
            'all_cities' => Town::orderBy('name')->get(),
            'all_divisions' => UserDivision::whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::SECURITY)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::CONTRACTOR)
                ->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $trks = Trk::orderBy('sort_order')->get();

        $buildings = Building::orderBy('name')->get();

        $floors = Floor::where('visibility', 1)->orderBy('name')->get();

        $rooms = Room::where('visibility', 1)->orderBy('name')->get();

        $equipment_names = EquipmentName::where('visibility', 1)->orderBy('name')->get();

        $systems = System::where('visibility', 1)->orderBy('name')->get();

        return \response()->view('backend.avrs.create', [
            'trks' => $trks,
            'systems' => $systems,
            'equipment_names' => $equipment_names,
            'rooms' => $rooms,
            'buildings' => $buildings,
            'floors' => $floors,
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::where('visibility', 1)->orderBy('name')->get(),
            'models' => AvrSparePart::orderBy('spare_part_model')->pluck('spare_part_model'),
            'spare_parts' => SparePartName::where('visibility', 1)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAvrFormRequest $request): RedirectResponse
    {
        Log::info('user try to store new AVR', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                $system = System::where('id', $data['system_id'])->first();

                if (empty($trk_room)) {

                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Отсутствует помещение: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . '. Посмотрите в ТРК/Помещение')->withInput();
                }

                DB::beginTransaction();

                $avr = Avr::where('trk_room_id', $trk_room->id)
                    ->where('system_id', $system->id)
                    ->whereDate('date', $data['date'])
                    ->where('author_id', Auth::id())
                    ->first();

                if(empty($avr->id))
                {
                    $avr = Avr::create([
                        'trk_room_id' => $trk_room->id,
                        'system_id' => $system->id,
                        'date' => $data['date'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                foreach ($data['equipment'] as $equipment_name => $value) {

                    $equipment_name_id = EquipmentName::where('name', $equipment_name)->pluck('id')->first();

                    $equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                        ->where('equipment_name_id', $equipment_name_id)
                        ->where('system_id', $system->id)
                        ->first();

                    if (empty($equipment->id)) {

                        return redirect()->back()->with('error', 'Отсутствует: система - ' . $system->name . ', оборудование - ' . $equipment_name . ' на ' . $trk_room->trk->name . ' в ' . $trk_room->room->name . ' Посмотрите в ТРК/Оборудование')->withInput();
                    }

                    $avr_equipment = AvrEquipment::where('trk_equipment_id', $equipment->id)
                        ->join('avrs', 'avrs.id', '=', 'avr_equipments.avr_id')
                        ->where('avrs.date', $data['date'])
                        ->first();

                    if(!empty($avr_equipment->id))
                    {
                        $avr = $avr_equipment->avr;
                    }

                    if(
                        !AvrEquipment::where('trk_equipment_id', $equipment->id)
                            ->where('avr_id', $avr->id)
                            ->where('author_id', Auth::id())
                            ->exists()
                    )
                    {

                        AvrEquipment::create([
                            'trk_equipment_id' => $equipment->id,
                            'avr_id' => $avr->id,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                    foreach ($value as $key => $item) {
                        if ($key == 'work') {
                            foreach ($item as $work) {

                                $work_name = WorkName::where('name', $work['type'])
                                    ->where('visibility', 1)
                                    ->first();

                                if (empty($work_name->id)) {

                                    return redirect()->back()->with('error', 'Нет такого типа работ: ' . $work['type'] . '')->withInput();
                                }

                             $avr_work = AvrWork::where('avr_id', $avr->id)
                                 ->where('work_name_id', $work_name->id)
                                 ->where('trk_equipment_id', $equipment->id)
                                 ->withTrashed()
                                 ->first();

                                if(empty($avr_work->id))
                                {
                                    AvrWork::create([
                                        'avr_id' => $avr->id,
                                        'work_name_id' => $work_name->id,
                                        'description' => $work['comment'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $equipment->id,
                                    ]);

                                } else {

                                    $avr_work->update([
                                        'avr_id' => $avr->id,
                                        'work_name_id' => $work_name->id,
                                        'description' => $work['comment'],
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $equipment->id,
                                        'deleted_at' => NULL,
                                    ]);
                                }

                                //=================================================================================
                                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $equipment->id)
                                    ->where('work_name_id', $work_name->id)
                                    ->first();

                                if(!empty($equipment_period_work->id))
                                {

                                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                    $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                    $equipment_period_work->update([
                                        'last_was_at' => $avr->date,
                                        'next_to_be_at' => $next_to_be_at,
                                    ]);
                                }
                                //=================================================================================

                            }
                        }

                        if ($key == 'spare_part') {

                            foreach ($item as $spare_part) {

                                $spare_part_name = SparePartName::where('name', $spare_part['name'])->first();

                                if (!empty($spare_part_name->id)) {

                                    if(!isset($spare_part['model'])){
                                        $spare_part['model'] = null;
                                    }

                                    if(!isset($spare_part['value'])){
                                        $spare_part['value'] = null;
                                    }

                                    AvrSparePart::create([
                                        'avr_id' => $avr->id,
                                        'spare_part_name_id' => $spare_part_name->id,
                                        'spare_part_model' => $spare_part['model'],
                                        'value' => $spare_part['value'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $equipment->id,
                                    ]);
                                }
                            }
                        }
                    }
                }

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {

                    $avr_executor = AvrExecutor::where('avr_id', $avr->id)
                        ->where('user_id', $executor->id)
                        ->first();

                    if(empty($avr_executor->id))
                    {
                        AvrExecutor::create([
                            'avr_id' => $avr->id,
                            'user_id' => $executor->id,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                }

                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('avrs.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_operation_application(OperationApplication $operation_application): Response
    {

        $this->authorize('create', Avr::class);

        $trk_rooms = TrkRoom::where('trk_id', $operation_application->trk->id)->pluck('room_id')->toArray();
        $rooms = Room::whereIn('id', $trk_rooms)->orderBy('name')->get();

        $trk_rooms = TrkRoom::where('trk_id', $operation_application->trk->id)->pluck('id')->toArray();
        $trk_equipments = TrkEquipment::whereIn('trk_room_id', $trk_rooms)->pluck('equipment_name_id')->toArray();
        $equipments = EquipmentName::whereIn('id', $trk_equipments)->orderBy('name')->get();

        $trk_buildings = TrkRoom::where('trk_id', $operation_application->trk_id)->pluck('building_id')->toArray();
        $buildings = Building::whereIn('id', $trk_buildings)->orderBy('name')->get();

        $trk_floors = TrkRoom::where('trk_id', $operation_application->trk_id)->pluck('floor_id')->toArray();
        $floors = Floor::whereIn('id', $trk_floors)->orderBy('name')->get();

        return \response()->view('backend.avrs.create_from_operation_application', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::where('visibility', 1)->orderBy('sort_order')->get(),
            'equipment_names' => $equipments,
            'rooms' => $rooms,
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::where('visibility', 1)->orderBy('name')->get(),
            'spare_parts' => SparePartName::where('visibility', 1)->orderBy('name')->get(),
            'operation_application' => $operation_application,
            'buildings' => $buildings,
            'floors' => $floors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_operation_application(StoreAvrFormRequest $request, OperationApplication $operation_application): RedirectResponse
    {
        $this->authorize('create', Avr::class);

        Log::info('user try to store new AVR from operation application', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'operation_application' => $operation_application,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('room_id', $data['room_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if (empty($trk_room)) {

                    $trk = Trk::where('id', $data['trk_id'])->first();
                    $building = Building::where('id', $data['building_id'])->first();
                    $floor = Floor::where('id', $data['floor_id'])->first();
                    $room = Room::where('id', $data['room_id'])->first();

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . ' Посмотрите в ТРК/Помещение')->withInput();
                }

                DB::beginTransaction();

                $avr = Avr::create([
                    'trk_room_id' => $trk_room->id,
                    'system_id' => $data['system_id'],
                    'date' => $data['date'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                foreach ($data['equipment'] as $equipment_name => $value) {

                    $equipment_name_id = EquipmentName::where('name', $equipment_name)->pluck('id')->first();

                    $equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                        ->where('equipment_name_id', $equipment_name_id)
                        ->where('system_id', $data['system_id'])
                        ->first();

                    if (empty($equipment->id)) {

                        return redirect()->back()->with('error', 'Нет такого оборудования: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ', ' . $trk_room->room->name . ' - ' . $equipment_name . ' Посмотрите в ТРК/Оборудование')->withInput();
                    }

                    AvrEquipment::create([
                        'trk_equipment_id' => $equipment->id,
                        'avr_id' => $avr->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);


                    foreach ($value as $key => $item) {

                        if ($key == 'work') {

                            foreach ($item as $work) {

                                $work_name = WorkName::where('name', $work['type'])
                                    ->where('visibility', 1)
                                    ->first();

                                if(empty($work_name->id))
                                {
                                    return redirect()->back()->with('error', 'Нет такого типа работ: ' . $work['type'])->withInput();
                                }

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_name->id,
                                    'description' => $work['comment'],
                                    'author_id' => Auth::id(),
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $equipment->id,
                                ]);


                                //=================================================================================
                                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $equipment->id)
                                    ->where('work_name_id', $work_name->id)
                                    ->first();

                                if(!empty($equipment_period_work->id))
                                {

                                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                    $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                    $equipment_period_work->update([
                                        'last_was_at' => $avr->date,
                                        'next_to_be_at' => $next_to_be_at,
                                    ]);
                                }
                                //=================================================================================
                            }
                        }

                        if ($key == 'spare_part') {
                            foreach ($item as $spare_part) {

                                $spare_part_name = SparePartName::where('name', $spare_part['name'])->first();

                                if (!empty($spare_part_name->id)) {
                                    AvrSparePart::create([
                                        'avr_id' => $avr->id,
                                        'spare_part_name_id' => $spare_part_name->id,
                                        'spare_part_model' => $spare_part['model'],
                                        'value' => $spare_part['value'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $equipment->id,
                                    ]);
                                }
                            }
                        }
                    }
                }

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DocCommunication::create([
                    'from_id' => $operation_application->id,
                    'from_type' => get_class($operation_application),
                    'to_id' => $avr->id,
                    'to_type' => get_class($avr),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);


                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('avrs.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_tech_act(TechAct $tech_act): Response
    {

        $this->authorize('create', Avr::class);

        $trk_rooms = TrkRoom::where('trk_id', $tech_act->trk_id)->pluck('room_id')->toArray();
        $rooms = Room::whereIn('id', $trk_rooms)->orderBy('name')->get();

        $trk_rooms = TrkRoom::where('trk_id', $tech_act->trk_id)->pluck('id')->toArray();
        $trk_rooms = implode("', '", $trk_rooms);
        $trk_equipments = TrkEquipment::whereRaw("trk_room_id in ('$trk_rooms')")->pluck('equipment_name_id')->toArray();
        $equipments = EquipmentName::whereIn('id', $trk_equipments)
            ->where('visibility', 1)
            ->orderBy('name')
            ->get();

        $trk_buildings = TrkRoom::where('trk_id', $tech_act->trk_id)->pluck('building_id')->toArray();
        $buildings = Building::whereIn('id', $trk_buildings)->orderBy('name')->get();

        $trk_floors = TrkRoom::where('trk_id', $tech_act->trk_id)->pluck('floor_id')->toArray();
        $floors = Floor::whereIn('id', $trk_floors)->orderBy('name')->get();

        return \response()->view('backend.avrs.create_from_tech_act', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::where('visibility', 1)->orderBy('sort_order')->get(),
            'equipment_names' => $equipments,
            'rooms' => $rooms,
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::where('visibility', 1)->orderBy('name')->get(),
            'spare_parts' => SparePartName::where('visibility', 1)->orderBy('name')->get(),
            'tech_act' => $tech_act,
            'buildings' => $buildings,
            'floors' => $floors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_tech_act(StoreAvrFromTechActFormRequest $request, TechAct $tech_act): RedirectResponse
    {
        $this->authorize('create', Avr::class);

        Log::info('user try to store new AVR from tech_act', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'tech_act' => $tech_act,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('room_id', $data['room_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('building_id', $data['building_id'])
                    ->first();

                if (empty($trk_room)) {

                    $trk = Trk::where('id', $data['trk_id'])->first();
                    $building = Building::where('id', $data['building_id'])->first();
                    $floor = Floor::where('id', $data['floor_id'])->first();
                    $room = Room::where('id', $data['room_id'])->first();

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . ' Посмотрите в ТРК/Помещение');
                }

                DB::beginTransaction();

                $avr = Avr::create([
                    'trk_room_id' => $trk_room->id,
                    'system_id' => $data['system_id'],
                    'date' => $data['date'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                foreach ($data['equipment'] as $equipment_name => $value) {

                    $equipment_name_id = EquipmentName::where('name', $equipment_name)->pluck('id')->first();

                    $equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                        ->where('equipment_name_id', $equipment_name_id)
                        ->where('system_id', $data['system_id'])
                        ->first();

                    if (empty($equipment->id)) {

                        return redirect()->back()->with('error', 'Нет такого оборудования ' . $equipment_name . ' на ' . $trk_room->trk->name . ' в ' . $trk_room->room->name . ' Посмотрите в ТРК/Оборудование');
                    }

                    AvrEquipment::create([
                        'trk_equipment_id' => $equipment->id,
                        'avr_id' => $avr->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);


                    foreach ($value as $key => $item) {
                        if ($key == 'work') {
                            foreach ($item as $work) {

                                $work_name = WorkName::where('name', $work['type'])
                                    ->where('visibility', 1)
                                    ->first();

                                if (empty($work_name->id)) {

                                    return redirect()->back()->with('error', 'Нет такого типа работ: ' . $work['type'] . '')->withInput();
                                }

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_name->id,
                                    'description' => $work['comment'],
                                    'author_id' => Auth::id(),
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $equipment->id,
                                ]);

                                //=================================================================================
                                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $equipment->id)
                                    ->where('work_name_id', $work_name->id)
                                    ->first();

                                if(!empty($equipment_period_work->id))
                                {

                                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                    $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                    $equipment_period_work->update([
                                        'last_was_at' => $avr->date,
                                        'next_to_be_at' => $next_to_be_at,
                                    ]);
                                }
                                //=================================================================================
                            }
                        }

                        if ($key == 'spare_part') {
                            foreach ($item as $spare_part) {

                                $spare_part_name = SparePartName::where('name', $spare_part['name'])->first();

                                if (!empty($spare_part_name->id)) {
                                    AvrSparePart::create([
                                        'avr_id' => $avr->id,
                                        'spare_part_name_id' => $spare_part_name->id,
                                        'spare_part_model' => $spare_part['model'],
                                        'value' => $spare_part['value'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $equipment->id,
                                    ]);
                                }
                            }
                        }
                    }
                }

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DocCommunication::create([
                    'from_id' => $tech_act->id,
                    'from_type' => get_class($tech_act),
                    'to_id' => $avr->id,
                    'to_type' => get_class($avr),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('avrs.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_repair(TrkRoomRepair $repair): Response
    {
        $this->authorize('create', Avr::class);

        $trk_rooms = TrkRoom::where('trk_id', $repair->trk_room->trk->id)->pluck('room_id')->toArray();
        $rooms = Room::whereIn('id', $trk_rooms)->orderBy('name')->get();

        $trk_rooms = TrkRoom::where('trk_id', $repair->trk_room->trk->id)->pluck('id')->toArray();
        $trk_rooms = implode("', '", $trk_rooms);
        $trk_equipments = TrkEquipment::whereRaw("trk_room_id in ('$trk_rooms')")->pluck('equipment_name_id')->toArray();
        $equipments = EquipmentName::whereIn('id', $trk_equipments)->where('visibility', 1)->orderBy('name')->get();

        $trk_buildings = TrkRoom::where('trk_id', $repair->trk_room->trk->id)->pluck('building_id')->toArray();
        $buildings = Building::whereIn('id', $trk_buildings)->orderBy('name')->get();

        $trk_floors = TrkRoom::where('trk_id', $repair->trk_room->trk->id)->pluck('floor_id')->toArray();
        $floors = Floor::whereIn('id', $trk_floors)->orderBy('name')->get();

        return \response()->view('backend.avrs.create_from_repair', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::where('visibility', 1)->orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::where('visibility', 1)->orderBy('name')->get(),
            'rooms' => $rooms,
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::where('visibility', 1)->orderBy('name')->get(),
            'spare_parts' => SparePartName::where('visibility', 1)->orderBy('name')->get(),
            'repair' => $repair,
            'buildings' => $buildings,
            'floors' => $floors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_repair(StoreAvrFromRepairFormRequest $request, TrkRoomRepair $repair): RedirectResponse
    {
        $this->authorize('create', Avr::class);

        Log::info('user try to store new AVR from repair', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'repair' => $repair,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                DB::beginTransaction();

                $avr = Avr::create([
                    'trk_room_id' => $repair->trk_room->id,
                    'system_id' => $repair->trk_equipment->system->id,
                    'date' => $data['date'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                foreach ($data['equipment'] as $equipment_name => $value) {

                    $equipment_name_id = EquipmentName::where('name', $data['equipment_name'])->pluck('id')->first();

                    $equipment = TrkEquipment::where('trk_room_id', $repair->trk_room->id)
                        ->where('equipment_name_id', $equipment_name_id)
                        ->where('system_id', $repair->trk_equipment->system->id)
                        ->first();

                    if (empty($equipment->id)) {

                        return redirect()->back()->with('error', 'Отсутствует: система - ' . $repair->trk_equipment->system->name . ', оборудование - ' . $equipment_name . ' на ' . $repair->trk_room->trk->name . ' в ' . $repair->trk_room->room->name . ' Посмотрите в ТРК/Оборудование')->withInput();
                    }

                    AvrEquipment::create([
                        'trk_equipment_id' => $equipment->id,
                        'avr_id' => $avr->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);


                    foreach ($value as $key => $item) {

                        if ($key == 'works') {

                            foreach ($item as $work) {

                                $work_name = WorkName::where('name', $work['name'])
                                    ->where('visibility', 1)
                                    ->first();

                                if (empty($work_name->id)) {

                                    return redirect()->back()->with('error', 'Нет такого типа работ: ' . $work['name'] . '')->withInput();
                                }

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_name->id,
                                    'description' => $work['comment'],
                                    'author_id' => Auth::id(),
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $repair->trk_equipment->id,
                                ]);

                                //=================================================================================
                                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $repair->trk_equipment->id)
                                    ->where('work_name_id', $work_name->id)
                                    ->first();

                                if(!empty($equipment_period_work->id))
                                {

                                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                    $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                    $equipment_period_work->update([
                                        'last_was_at' => $avr->date,
                                        'next_to_be_at' => $next_to_be_at,
                                    ]);
                                }
                                //=================================================================================
                            }
                        }

                        if ($key == 'spare_parts') {
                            foreach ($item as $spare_part) {

                                $spare_part_name = SparePartName::where('name', $spare_part['name'])->first();

                                if (!empty($spare_part_name->id)) {
                                    AvrSparePart::create([
                                        'avr_id' => $avr->id,
                                        'spare_part_name_id' => $spare_part_name->id,
                                        'spare_part_model' => $spare_part['model'],
                                        'value' => $spare_part['value'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $repair->trk_equipment->id,
                                    ]);
                                }
                            }
                        }
                    }
                }

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DocCommunication::create([
                    'from_id' => $repair->id,
                    'from_type' => get_class($repair),
                    'to_id' => $avr->id,
                    'to_type' => get_class($avr),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);


                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('avrs.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }
        }

        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_trk_equipment(TrkEquipment $trk_equipment): Response
    {
        $this->authorize('create', Avr::class);

        return \response()->view('backend.avrs.create_from_trk_equipment', [
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::where('visibility', 1)->orderBy('name')->get(),
            'spare_parts' => SparePartName::where('visibility', 1)->orderBy('name')->get(),
            'trk_equipment' => $trk_equipment,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_equipment(StoreAvrFromTrkEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        $this->authorize('create', Avr::class);

        Log::info('user try to store new AVR from trk equipment', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_equipment' => $trk_equipment,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                DB::beginTransaction();

                $avr = Avr::create([
                    'trk_room_id' => $trk_equipment->trk_room->id,
                    'system_id' => $trk_equipment->system->id,
                    'date' => $data['date'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                foreach ($data['equipment'] as $equipment_name => $value) {

                    AvrEquipment::create([
                        'trk_equipment_id' => $trk_equipment->id,
                        'avr_id' => $avr->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);


                    foreach ($value as $key => $item) {
                        if ($key == 'work') {
                            foreach ($item as $work) {

                                $work_name = WorkName::where('name', $work['type'])
                                    ->where('visibility', 1)
                                    ->first();

                                if(empty($work_name->id))
                                {
                                    return redirect()->back()->with('error', 'Не существует типа работ: ' . $work['type']);
                                }

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_name->id,
                                    'description' => $work['comment'],
                                    'author_id' => Auth::id(),
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $trk_equipment->id,
                                ]);

                                //=================================================================================
                                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $trk_equipment->id)
                                    ->where('work_name_id', $work_name->id)
                                    ->first();

                                if(!empty($equipment_period_work->id))
                                {

                                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                    $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                    $equipment_period_work->update([
                                        'last_was_at' => $avr->date,
                                        'next_to_be_at' => $next_to_be_at,
                                    ]);
                                }
                                //=================================================================================
                            }
                        }

                        if ($key == 'spare_part') {
                            foreach ($item as $spare_part) {

                                $spare_part_name = SparePartName::where('name', $spare_part['name'])->first();

                                if (!empty($spare_part_name->id)) {
                                    AvrSparePart::create([
                                        'avr_id' => $avr->id,
                                        'spare_part_name_id' => $spare_part_name->id,
                                        'spare_part_model' => $spare_part['model'],
                                        'value' => $spare_part['value'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $trk_equipment->id,
                                    ]);
                                }
                            }
                        }
                    }
                }

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('avrs.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                DB::rollBack();
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
        $this->authorize('create', Avr::class);

        $room_system_ids = TrkEquipment::where('trk_room_id', $trk_room->id)->pluck('system_id')->toArray();

        $systems = System::whereIn('id', $room_system_ids)->orderBy('name')->get();
        $system = $systems->first();

        $equiment_name_ids = TrkEquipment::where('trk_room_id', $trk_room->id)
            ->where('system_id', $system->id)
            ->pluck('equipment_name_id')
            ->toArray();

        $equiment_names = EquipmentName::whereIn('id', $equiment_name_ids)->where('visibility', 1)->get();

        return \response()->view('backend.avrs.create_from_trk_room', [
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::where('visibility', 1)->orderBy('name')->get(),
            'spare_parts' => SparePartName::where('visibility', 1)->orderBy('name')->get(),
            'trk_room' => $trk_room,
            'systems' => $systems,
            'equipment_names' => $equiment_names,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreAvrFromTrkRoomFormRequest $request, TrkRoom $trk_room): RedirectResponse
    {
        $this->authorize('create', Avr::class);

        Log::info('user try to store new AVR from trk room', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_room' => $trk_room,
        ]);

        if ($request->isMethod('post')) {

            try {

                $data = $request->validated();

                DB::beginTransaction();

                $avr = Avr::create([
                    'trk_room_id' => $trk_room->id,
                    'system_id' => $data['system_id'],
                    'date' => $data['date'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);


                if($data['avr_type'] == 'simple_avr'){

                    foreach ($data['equipment'] as $equipment_name => $value) {

                        if($equipment_name == 0)
                        {
                            return redirect()->back()->with('error', 'Необходимо заполнять все поля Оборудование, если акт не Групповой');
                        }

                        $equipment_name = EquipmentName::where('name', $equipment_name)->first();

                        $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                            ->where('equipment_name_id', $equipment_name->id)
                            ->where('system_id', $data['system_id'])
                            ->first();

                        if (empty($trk_equipment->id)) {
                            return redirect()->back()->with('error', 'Не найдено оборудование ' . $equipment_name);
                        }

                        AvrEquipment::create([
                            'trk_equipment_id' => $trk_equipment->id,
                            'avr_id' => $avr->id,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);


                        foreach ($value as $key => $item) {

                            if ($key == 'work') {

                                foreach ($item as $work) {

                                    $work_name = WorkName::where('name', $work['type'])
                                        ->where('visibility', 1)
                                        ->first();

                                    if($work['type'] == "")
                                    {
                                        return redirect()->back()->with('error', 'Необходимо заполнять все работы в тех. мероприятиях, если акт не Групповой');
                                    }

                                    if(empty($work_name->id))
                                    {
                                        return redirect()->back()->with('error', 'Не найден тип работ ' . $work['type']);
                                    }

                                    AvrWork::create([
                                        'avr_id' => $avr->id,
                                        'work_name_id' => $work_name->id,
                                        'description' => $work['comment'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $trk_equipment->id,
                                    ]);

                                    //=================================================================================
                                    $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $trk_equipment->id)
                                        ->where('work_name_id', $work_name->id)
                                        ->first();

                                    if(!empty($equipment_period_work->id))
                                    {

                                        $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                        $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                        $equipment_period_work->update([
                                            'last_was_at' => $avr->date,
                                            'next_to_be_at' => $next_to_be_at,
                                        ]);
                                    }
                                    //=================================================================================
                                }
                            }

                            if ($key == 'spare_part') {
                                foreach ($item as $spare_part) {

                                    $spare_part_name = SparePartName::where('name', $spare_part['name'])->first();

                                    if (!empty($spare_part_name->id)) {

                                        AvrSparePart::create([
                                            'avr_id' => $avr->id,
                                            'spare_part_name_id' => $spare_part_name->id,
                                            'spare_part_model' => $spare_part['model'],
                                            'value' => $spare_part['value'],
                                            'author_id' => Auth::id(),
                                            'last_editor_id' => Auth::id(),
                                            'trk_equipment_id' => $trk_equipment->id,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                if($data['avr_type'] == 'group_works_avr')
                {

                    $all_equipments_in_room = TrkEquipment::where('trk_room_id', $trk_room->id)
                        ->where('system_id', $data['system_id'])
                        ->get();

                    foreach($all_equipments_in_room as $one_equipment)
                    {
                        AvrEquipment::create([
                            'trk_equipment_id' => $one_equipment->id,
                            'avr_id' => $avr->id,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);

                        foreach($data['group_works'] as $key => $value)
                        {
                            if(!is_null($value['name']))
                            {

                                $work_name = WorkName::where('name', $value['name'])
                                    ->where('visibility', 1)
                                    ->first();

                                if(empty($work_name->id))
                                {
                                    return redirect()->back()->with('error', 'Не найден тип работ ' . $value['name']);
                                }

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_name->id,
                                    'description' => $value['comment'],
                                    'author_id' => Auth::id(),
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $one_equipment->id,
                                ]);

                                //=================================================================================
                                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $one_equipment->id)
                                    ->where('work_name_id', $work_name->id)
                                    ->first();

                                if(!empty($equipment_period_work->id))
                                {

                                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                    $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                    $equipment_period_work->update([
                                        'last_was_at' => $avr->date,
                                        'next_to_be_at' => $next_to_be_at,
                                    ]);
                                }
                                //=================================================================================

                            } else {
                                return redirect()->back()->with('error', 'Необходимо заполнить тип работ в Групповом акте');
                            }


                        }
                    }
                }

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('avrs.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Avr $avr): Response
    {
        return \response()->view('backend.avrs.show', [
            'avr' => $avr,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avr $avr): Response
    {
        return \response()->view('backend.avrs.edit', [
            'avr' => $avr,
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('name')->get(),
            'rooms' => Room::where('visibility', 1)->orderBy('name')->get(),
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::where('visibility', 1)->orderBy('name')->get(),
            'equipment_names' => EquipmentName::where('visibility', 1)->orderBy('name')->get(),
            'works' => WorkName::where('visibility', 1)->orderBy('name')->get(),
            'spare_parts' => SparePartName::where('visibility', 1)->orderBy('name')->get(),
            'executors' => User::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAvrFormRequest $request, Avr $avr): RedirectResponse
    {
        Log::info('user try to update AVR', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'avr' => $avr
        ]);

        if ($request->isMethod('patch')) {

            try {

                $checklist = DocCommunication::where('to_id', $avr->id)
                    ->where('to_type', 'App\\Models\\Avrs\\Avr')
                    ->where('from_type', 'App\\Models\\Checklists\\ChecklistConditioner')
                    ->orWhere('from_type', 'App\\Models\\Checklists\\ChecklistBalk')
                    ->orWhere('from_type', 'App\\Models\\Checklists\\ChecklistFancoil')
                    ->orWhere('from_type', 'App\\Models\\Checklists\\ChecklistAirDiffuser')
                    ->orWhere('from_type', 'App\\Models\\Checklists\\ChecklistAirAirDuct')
                    ->orWhere('from_type', 'App\\Models\\Checklists\\ChecklistAirExtract')
                    ->orWhere('from_type', 'App\\Models\\Checklists\\ChecklistAirAirSupply')
                    ->first();

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if (empty($trk_room)) {
                    return redirect()->back()->with('error', 'Нет такого помещения на выбранном ТРК');
                }

                DB::beginTransaction();

                $avr->update([
                    'trk_room_id' => $trk_room->id,
                    'system_id' => $data['system_id'],
                    'date' => $data['date'],
                    'last_editor_id' => Auth::id(),
                ]);

                AvrEquipment::where('avr_id', $avr->id)
                    ->forceDelete();

                AvrWork::where('avr_id', $avr->id)
                    ->forceDelete();

                AvrSparePart::where('avr_id', $avr->id)
                    ->forceDelete();

                foreach ($data['equipment'] as $equipment_name => $value) {

                    $equipment_name_id = EquipmentName::where('name', $equipment_name)->pluck('id')->first();

                    $equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                        ->where('equipment_name_id', $equipment_name_id)
                        ->where('system_id', $data['system_id'])
                        ->first();

                    if (empty($equipment->id)) {
                        return redirect()->back()->with('error', 'Нет такого оборудования: ' . $trk_room->trk->name . ' - ' . $trk_room->building->name . ' - ' . $trk_room->floor->name . ' - ' . $trk_room->room->name . ' - ' . $equipment_name . ' Создайте его сначала.');
                    }

                    AvrEquipment::create([
                        'trk_equipment_id' => $equipment->id,
                        'avr_id' => $avr->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                        'deleted_at' => null
                    ]);

                    foreach ($value as $key => $item) {

                        if ($key == 'work') {

                            foreach ($item as $work) {

                                $work_name = WorkName::where('name', $work['type'])
                                    ->where('visibility', 1)
                                    ->first();

                                if (empty($work_name)) {
                                    return redirect()->back()->with('error', 'Нет такого типа работ: ' . $work['type'])->withInput();
                                }

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_name->id,
                                    'description' => $work['comment'],
                                    'author_id' => Auth::id(),
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $equipment->id,
                                ]);

                                //=================================================================================
                                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $equipment->id)
                                    ->where('work_name_id', $work_name->id)
                                    ->first();

                                if(!empty($equipment_period_work->id))
                                {

                                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                                    $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                                    $equipment_period_work->update([
                                        'last_was_at' => $avr->date,
                                        'next_to_be_at' => $next_to_be_at,
                                    ]);
                                }
                                //=================================================================================
                            }
                        }

                        if ($key == 'spare_part') {

                            foreach ($item as $spare_part) {

                                $spare_part_name = SparePartName::where('name', $spare_part['name'])->first();

                                if (!empty($spare_part_name->id)) {

                                    AvrSparePart::create([
                                        'avr_id' => $avr->id,
                                        'spare_part_name_id' => $spare_part_name->id,
                                        'spare_part_model' => $spare_part['model'],
                                        'value' => $spare_part['value'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $equipment->id,
                                    ]);
                                }
                            }
                        }
                    }
                }

                $executors = User::whereIn('name', $data['executors'])->get();

                AvrExecutor::where('avr_id', $avr->id)->forceDelete();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('avrs.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function set_avrs_from_this_to_another_equipment(SetAvrsToAnotherEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        if ($request->isMethod('patch')) {

            $data = $request->validated();

            $trk_room = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)
                ->where('building_id', $data['building_id'])
                ->where('floor_id', $data['floor_id'])
                ->where('room_id', $data['room_id'])
                ->first();

            if (empty($trk_room->id)) {
                return redirect()->back()->with('error', 'Такого помещения не существует');
            }

            $new_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                ->where('system_id', $data['system_id'])
                ->where('equipment_name_id', $data['equipment_name_id'])
                ->first();

            if (empty($new_equipment->id)) {
                return redirect()->back()->with('error', 'Такого оборудования не существует');
            }

            try {

                DB::beginTransaction();

                foreach ($trk_equipment->avrs as $avr_equipment) {

                    $avr_equipment->update([
                        'trk_equipment_id' => $new_equipment->id,
                        'last_editor_id' => Auth::id(),
                    ]);

                    $avr_works = AvrWork::where('avr_id', $avr_equipment->avr_id)
                        ->where('trk_equipment_id', $trk_equipment->id)
                        ->get();

                    foreach ($avr_works as $avr_work) {
                        $avr_work->update([
                            'trk_equipment_id' => $new_equipment->id,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                    $avr = Avr::where('id', $avr_equipment->avr_id)->first();

                    $avr_another_equipments = AvrEquipment::where('avr_id', $avr->id)
                        ->whereNot('trk_equipment_id', $new_equipment->id)
                        ->get();

                    if (count($avr_another_equipments) > 0) // Если для этого акта есть еще другое оборудование
                    {

                        Avr::create([
                            'trk_room_id' => $trk_room->id,
                            'date' => $avr->date,
                            'system_id' => $avr->system_id,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);

                    } else { // Если в акте была одна единица оборудования

                        $avr->update([
                            'trk_room_id' => $trk_room->id,
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
     * Update the specified resource in storage.
     */
    public function set_avrs_from_this_to_another_room(SetAvrsToAnotherRoomFormRequest $request, TrkRoom $trk_room): RedirectResponse
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

                foreach ($trk_room->avrs as $avr_room) {

                    $avr_room->update([
                        'trk_room_id' => $new_trk_room->id,
                        'last_editor_id' => Auth::id(),
                    ]);
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
    public function destroy(Avr $avr): RedirectResponse
    {
        Log::info('user try to delete AVR', [
            'user' => Auth::user()->name,
            'avr' => $avr
        ]);

        try {

            DB::beginTransaction();

            AvrEquipment::where('avr_id', $avr->id)
                ->delete();

            AvrWork::where('avr_id', $avr->id)
                ->delete();

            AvrSparePart::where('avr_id', $avr->id)
                ->delete();

            AvrExecutor::where('avr_id', $avr->id)
                ->delete();

            if(count($avr->conditioner_checklists) > 0)
            {
                return redirect()->route('avrs.show', $avr)->with('error', 'Нельзя удалять акт. На нем есть чеклисты кондиционеров.');
            }

            if(count($avr->fancoil_checklists) > 0)
            {
                return redirect()->route('avrs.show', $avr)->with('error', 'Нельзя удалять акт. На нем есть чеклисты фанкойлов.');
            }

            if(count($avr->balk_checklists) > 0)
            {
                return redirect()->route('avrs.show', $avr)->with('error', 'Нельзя удалять акт. На нем есть чеклисты балок.');
            }

            if(count($avr->air_diffuser_checklists) > 0)
            {
                return redirect()->route('avrs.show', $avr)->with('error', 'Нельзя удалять акт. На нем есть чеклисты диффузоров.');
            }

            if(count($avr->air_duct_checklists) > 0)
            {
                return redirect()->route('avrs.show', $avr)->with('error', 'Нельзя удалять акт. На нем есть чеклисты воздуховодов.');
            }

            if(count($avr->air_supply_checklists) > 0)
            {
                return redirect()->route('avrs.show', $avr)->with('error', 'Нельзя удалять акт. На нем есть чеклисты приточек.');
            }

            if(count($avr->air_extract_checklists) > 0)
            {
                return redirect()->route('avrs.show', $avr)->with('error', 'Нельзя удалять акт. На нем есть чеклисты вытяжек.');
            }

            DocCommunication::where('to_id', $avr->id)->delete();

            $avr->update([
                'destroyer_id' => Auth::id()
            ]);

            $avr->delete();

            DB::commit();

            return redirect()->route('avrs.index')->with('success', 'Данные удалены.');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
        }

        return redirect()->back()->with('error', 'Ошибка удаления данных, смотрите логи.');

    }

    public function print_one(Avr $avr)
    {
        $this->authorize('read');

        return (new AvrExportPdf(
            $avr
        ))->export_pdf();
    }

    public function export(AvrFilterRequest $request)
    {
        $data = $request->validated();

        $filter = app()->make(AvrFilter::class, ['queryParams' => array_filter($data)]);

        $avrs = Avr::filter($filter)
            ->with([
                'trk_room',
                'system',
                //'avr_equipments',
                //'avr_works',
                //'avr_spare_parts',
                //'avr_executors'
            ])
            ->whereBetween('date', [$data['start_date'], $data['finish_date']])
            ->orderBy('date', 'desc')
            ->get();

        if (count($avrs) > 1600) {

            return redirect()->back()->with('error', 'Актов для выгрузки ' . count($avrs) . ', 1600 это максимум для комфортной работы приложения, попробуйте сузить фильтр для уменьшения количества.');
        }

        $avrs = $avrs->sortByDesc('date');

        if (count($avrs) == 0) {

            return redirect()->back()->with('error', 'Нет таких актов в базе. Создайте их сначала.');
        }

        switch ($data['file_type']) {
            case '.pdf':

                return (new AvrsExportPdf(
                    $avrs,
                    $data,
                ))->export_pdf();

            case '.html':
                return Excel::download(new AvrsExport(
                    $avrs,
                    $data,
                ), 'Акты_выполненных_работ__' . $data['start_date'] . '__' . $data['finish_date'] . '.html');

            default:
                return Excel::download(new AvrsExport(
                    $avrs,
                    $data,
                ), 'Акты_выполненных_работ__' . $data['start_date'] . '__' . $data['finish_date'] . '.xlsx');
        }
    }

    public function index_frame(AvrFilterRequest $request): Response
    {

        $data = $request->validated();

        $user_trks = UserResponsibilityTrkSystem::where('user_id', Auth::id())
            ->pluck('trk_id')
            ->toArray();

        $all_trks = Trk::orderBy('sort_order')->get();

        if (count($user_trks) > 0 && !auth()->user()->hasRole('sadmin')) {
            $data['trk_ids'] = $user_trks;
            $all_trks = Trk::whereIn('id', $user_trks)->get();
        }

        $user_systems = UserResponsibilityTrkSystem::where('user_id', Auth::id())
            ->pluck('system_id')
            ->toArray();

        $all_systems = System::orderBy('name')->get();

        if (count($user_systems) > 0 && !auth()->user()->hasRole('sadmin')) {
            $data['system_ids'] = $user_systems;
            $all_systems = System::whereIn('id', $user_systems)->get();
        }

        $filter = app()->make(AvrFilter::class, ['queryParams' => array_filter($data)]);

        $avrs = Avr::filter($filter)
            ->with([
                'trk_room',
                'system',
                'avr_equipments',
                'avr_works',
                'avr_spare_parts',
                'avr_executors'
            ])
            ->orderBy('date', 'desc')
            ->paginate(config('backend.avrs.pagination'));

        return \response()->view('backend.avrs.index_frame', [
            'avrs' => $avrs,
            'old_filters' => $data,
            'all_trks' => $all_trks,
            'all_systems' => $all_systems,
            'all_equipment_names' => EquipmentName::orderBy('name')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_work_names' => WorkName::orderBy('name')->get(),
            'all_executors' => User::orderBy('name')->get(),
            'all_buildings' => Building::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
            'all_cities' => Town::orderBy('name')->get(),
            'all_divisions' => UserDivision::whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::SECURITY)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::CONTRACTOR)
                ->orderBy('name')->get(),
        ]);
    }

}
