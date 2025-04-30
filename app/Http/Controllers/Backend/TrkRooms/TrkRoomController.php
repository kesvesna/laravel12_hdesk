<?php

namespace App\Http\Controllers\Backend\TrkRooms;

use App\Http\Controllers\Controller;
use App\Http\Filters\TrkRooms\TrkRoomFilter;
use App\Http\Requests\Buildings\UpdateBuildingInRoomsFormRequest;
use App\Http\Requests\Floors\UpdateFloorInRoomsFormRequest;
use App\Http\Requests\TrkRooms\StoreTrkRoomFormRequest;
use App\Http\Requests\TrkRooms\TrkRoomFilterRequest;
use App\Http\Requests\TrkRooms\UpdateRoomInTrkRoomsFromRoomNameFormRequest;
use App\Http\Requests\TrkRooms\UpdateTrkRoomFormRequest;
use App\Models\Avrs\Avr;
use App\Models\Axes\Axe;
use App\Models\Buildings\Building;
use App\Models\Checklists\ChecklistAirDiffuser;
use App\Models\Checklists\ChecklistAirDuct;
use App\Models\Checklists\ChecklistAirExtract;
use App\Models\Checklists\ChecklistAirSupply;
use App\Models\Checklists\ChecklistBalk;
use App\Models\Checklists\ChecklistConditioner;
use App\Models\Checklists\ChecklistFancoil;
use App\Models\Counters\TrkRoomCounter;
use App\Models\EquipmentUsers\EquipmentUser;
use App\Models\Floors\Floor;
use App\Models\Renters\Renter;
use App\Models\RenterTrkRoomBrands\RenterTrkRoomBrand;
use App\Models\RoomPurposes\RoomPurpose;
use App\Models\Rooms\Room;
use App\Models\Systems\System;
use App\Models\Towns\Town;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRoomClimates\TrkRoomClimate;
use App\Models\TrkRoomRepairs\TrkRoomRepair;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use App\Models\WorkNames\WorkName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrkRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TrkRoom::class, 'trk_room');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TrkRoomFilterRequest $request): Response
    {

        $data = $request->validated();

        $filter = app()->make(TrkRoomFilter::class, ['queryParams' => array_filter($data)]);

        $trk_rooms = TrkRoom::filter($filter)
            ->with(['trk', 'building', 'floor', 'room'])
            ->select(['trk_rooms.*', 'trks.sort_order', 'buildings.name', 'floors.name', 'rooms.name'])
            ->whereIn('trk_id', Auth::user()->responsibility_trk_ids())
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->join('buildings', 'buildings.id', '=', 'trk_rooms.building_id')
            ->join('floors', 'floors.id', '=', 'trk_rooms.floor_id')
            ->join('rooms', 'rooms.id', '=', 'trk_rooms.room_id')
            ->orderBy('trks.sort_order', 'asc')
            ->orderBy('buildings.name', 'asc')
            ->orderBy('floors.name', 'asc')
            ->orderBy('rooms.name', 'asc')
            ->paginate(config('backend.trk_room.pagination'));

        return \response()->view('backend.trk_room.index', [
            'trk_rooms' => $trk_rooms,
            'old_filters' => $data,
            'all_trks' => Trk::orderBy('sort_order')->whereIn('id', Auth::user()->responsibility_trk_ids())->get(),
            'all_buildings' => Building::orderBy('sort_order')->get(),
            'all_floors' => Floor::orderBy('sort_order')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_room_purposes' => RoomPurpose::orderby('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.trk_room.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'buildings' => Building::orderBy('sort_order')->get(),
            'floors' => Floor::orderBy('sort_order')->get(),
            'rooms' => Room::orderBy('name')->orderBy('created_at', 'desc')->get(),
            'room_purposes' => RoomPurpose::orderby('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkRoomFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $not_exists_room = '';

                foreach ($data['room_names'] as $room) {

                    $room_id = Room::where('name', $room)->pluck('id')->first();

                        if (!empty($room_id)) {

                            $deleted_trk_room = TrkRoom::onlyTrashed()
                                ->where('trk_id', $data['trk_id'])
                                ->where('building_id', $data['building_id'])
                                ->where('floor_id', $data['floor_id'])
                                ->where('room_id', $room_id)
                                ->first();

                            if(empty($deleted_trk_room->id))
                            {
                                if (
                                    !TrkRoom::where('trk_id', $data['trk_id'])
                                        ->where('building_id', $data['building_id'])
                                        ->where('floor_id', $data['floor_id'])
                                        ->where('room_id', $room_id)
                                        ->exists()
                                ) {
                                    TrkRoom::withTrashed()->updateOrCreate([
                                        'trk_id' => $data['trk_id'],
                                        'building_id' => $data['building_id'],
                                        'floor_id' => $data['floor_id'],
                                        'room_id' => $room_id,
                                        'room_purpose_id' => $data['room_purpose_id'],
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                        'need_daily_checking' => $data['need_daily_checking'] ?? 0,
                                    ])->restore();
                                }
                            } else {
                                $deleted_trk_room->update([
                                    'trk_id' => $data['trk_id'],
                                    'building_id' => $data['building_id'],
                                    'floor_id' => $data['floor_id'],
                                    'room_id' => $room_id,
                                    'room_purpose_id' => $data['room_purpose_id'],
                                    'last_editor_id' => Auth::id(),
                                    'need_daily_checking' => $data['need_daily_checking'] ?? 0,
                                ]);
                                $deleted_trk_room->restore();
                            }

                        } else {

                            $not_exists_room .= $room . ', ';

                        }

                }

                if (strlen($not_exists_room) > 0) {

                    return redirect()->route('trk_room.index')->with('alert', 'Сохранена часть. Помещения с именами: ' . $not_exists_room . ' отсутствуют. Создайте их в разделе Архитектура/Помещения');

                } else {

                    return redirect()->route('trk_room.index')->with('success', 'Все данные сохранены');

                }

            } catch (\Exception $e) {

                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(TrkRoom $trk_room): Response
    {

        $checklists_air_supply = null;
        $checklists_air_extract = null;

        $system_air_recycle = System::where('name', System::AIR_RECYCLE)->first();

        $trk_equipment_ids = TrkEquipment::where('trk_room_id', $trk_room->id)
            ->where('system_id', $system_air_recycle->id)
            ->pluck('id')
            ->toArray();

        if (!empty($trk_equipment_ids)) {
            $checklists_air_supply = ChecklistAirSupply::whereIn('trk_equipment_id', $trk_equipment_ids)->get();
            $checklists_air_extract = ChecklistAirExtract::whereIn('trk_equipment_id', $trk_equipment_ids)->get();
        }

        $checklists_air_duct = ChecklistAirDuct::where('trk_room_id', $trk_room->id)->get();
        $checklists_air_diffuser = ChecklistAirDiffuser::where('trk_room_id', $trk_room->id)->get();

        return \response()->view('backend.trk_room.show', [
            'trk_room' => $trk_room,
            'checklists_air_supply' => $checklists_air_supply,
            'checklists_air_extract' => $checklists_air_extract,
            'checklists_air_duct' => $checklists_air_duct,
            'checklists_air_diffuser' => $checklists_air_diffuser,
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
//            'all_work_names' => WorkName::orderBy('name')->get(),
//            'all_executors' => User::orderBy('name')->get(),
//            'all_cities' => Town::orderBy('name')->get(),
//            'all_divisions' => UserDivision::whereNot('name', UserDivision::RENTER)
//                ->whereNot('name', UserDivision::SECURITY)
//                ->whereNot('name', UserDivision::DETK)
//                ->whereNot('name', UserDivision::CONTRACTOR)
//                ->orderBy('name')->get(),
//            'all_trks' => Trk::orderBy('sort_order')->get(),
//            'all_systems' => UserResponsibilityTrkSystem::where('user_id', Auth::id())
//                ->pluck('system_id')
//                ->toArray(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrkRoom $trk_room): Response
    {

        return \response()->view('backend.trk_room.edit', [
            'trk_room' => $trk_room,
            'trks' => Trk::orderBy('sort_order')->get(),
            'buildings' => Building::orderBy('sort_order')->get(),
            'floors' => Floor::orderBy('sort_order')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'axes' => Axe::orderBy('name')->get(),
            'room_purposes' => RoomPurpose::orderby('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkRoomFormRequest $request, TrkRoom $trk_room): RedirectResponse
    {
        if ($request->isMethod('patch')) {

            $data = $request->validated();

            $deleted_trk_room = TrkRoom::onlyTrashed()
                ->where('trk_id', $trk_room->trk_id)
                ->where('building_id', $data['building_id'])
                ->where('floor_id', $data['floor_id'])
                ->where('room_id', $data['room_id'])
                ->first();

            if(empty($deleted_trk_room->id))
            {

                    $trk_room->update([
                        //'trk_id' => $data['trk_id'],
                        'building_id' => $data['building_id'],
                        'floor_id' => $data['floor_id'],
                        'room_id' => $data['room_id'],
                        'comment' => $data['comment'],
                        'axe_id' => $data['axe_id'],
                        'room_purpose_id' => $data['room_purpose_id'],
                        'last_editor_id' => Auth::id(),
                        'need_daily_checking' => $data['need_daily_checking'] ?? 0,
                    ]);

            } else {

                $deleted_trk_room->update([
                    //'trk_id' => $data['trk_id'],
                    'building_id' => $data['building_id'],
                    'floor_id' => $data['floor_id'],
                    'room_id' => $data['room_id'],
                    'comment' => $data['comment'],
                    'axe_id' => $data['axe_id'],
                    'room_purpose_id' => $data['room_purpose_id'],
                    'last_editor_id' => Auth::id(),
                    'need_daily_checking' => $data['need_daily_checking'] ?? 0,
                ]);

                $deleted_trk_room->restore();
            }

            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    public function change_room_name_in_trk_rooms(UpdateRoomInTrkRoomsFromRoomNameFormRequest $request, TrkRoom $trk_room): RedirectResponse
    {
        //$this->authorize('change_work_name_in_avrs');

        Log::info('user try to change room name in trk roms from room name show page', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'room' => $trk_room->room->name,
        ]);

        if ($request->isMethod('patch')) {

            try {

                $data = $request->validated();

                $old_trk_rooms = TrkRoom::where('room_id', $trk_room->room->id)->get();
                $new_room_name = Room::find($data['room_name_id']);

                DB::beginTransaction();

                foreach ($old_trk_rooms as $old_trk_room) {

                    if(
                        !TrkRoom::where('trk_id', $trk_room->trk->id)
                            ->where('building_id', $trk_room->building->id)
                            ->where('floor_id', $trk_room->floor->id)
                            ->where('room_id', $data['room_name_id'])
                            ->exists()
                    )
                    {
                        $old_trk_room->update([
                            'room_id' => $data['room_name_id'],
                            'last_editor_id' => Auth::id(),
                        ]);
                    } else {

                        return redirect()->back()->with('error', 'Помещение уже существует: '
                            . $trk_room->trk->name . ', '
                            . $trk_room->building->name . ', '
                            . $trk_room->floor->name . ', '
                            . $new_room_name->name)
                            ->withInput();
                    }
                }

                DB::commit();

                return redirect()->route('room.show', $trk_room->room->id)->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }

            return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
        }
    }

    public function change_building_in_rooms(UpdateBuildingInRoomsFormRequest $request, Building $building)
    {
        if ($request->isMethod('patch')) {

            try {
                $data = $request->validated();

                $trk_rooms = TrkRoom::where('building_id', $building->id)->get();

                DB::beginTransaction();

                foreach ($trk_rooms as $trk_room) {
                    $old_trk_room = TrkRoom::where('trk_id', $trk_room->trk_id)
                        ->where('building_id', $data['building_id'])
                        ->where('floor_id', $trk_room->floor_id)
                        ->where('room_id', $trk_room->room_id)
                        ->first();

                    if (empty($old_trk_room->id)) {
                        $trk_room->update([
                            'building_id' => $data['building_id'],
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()->back()->with('success', 'Изменения сохранены');

            } catch (\Exception $e) {

                DB::rollback();
                Log::error($e);
                return redirect()->back()->with('error', $e);

            }

        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    public function change_floor_in_rooms(UpdateFloorInRoomsFormRequest $request, Floor $floor)
    {
        if ($request->isMethod('patch')) {

            try {
                $data = $request->validated();

                $trk_rooms = TrkRoom::where('floor_id', $floor->id)->get();

                DB::beginTransaction();

                foreach ($trk_rooms as $trk_room) {
                    $old_trk_room = TrkRoom::where('trk_id', $trk_room->trk_id)
                        ->where('floor_id', $data['floor_id'])
                        ->where('building_id', $trk_room->building_id)
                        ->where('room_id', $trk_room->room_id)
                        ->first();

                    if (empty($old_trk_room->id)) {
                        $trk_room->update([
                            'floor_id' => $data['floor_id'],
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()->back()->with('success', 'Изменения сохранены');

            } catch (\Exception $e) {

                DB::rollback();
                Log::error($e);
                return redirect()->back()->with('error', $e);
            }
        }

        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrkRoom $trk_room): RedirectResponse
    {
        try {

            if (EquipmentUser::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'У помещения есть источники ресурсов. Удалите сначала информацию о них из этого помещения.');
            }

            if (TrkEquipment::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'В помещении находится оборудование, удалите или привяжите оборудование к другому помещению.');
            }

            if (RenterTrkRoomBrand::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'В помещении находится арендатор, удалите или привяжите арендатора к другому помещению.');
            }

            if (Avr::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'Для помещения есть АВР, удалите или привяжите акты к другому помещению.');
            }

            if (TrkRoomClimate::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'Для помещения есть чеклисты, удалите или привяжите чеклисты климата к другому помещению.');
            }

            if (ChecklistConditioner::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'Для помещения есть чеклисты кондиционеров, удалите или привяжите чеклисты кондиционеров к другому помещению.');
            }

            if (ChecklistFancoil::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'Для помещения есть чеклисты фанкойлов, удалите или привяжите чеклисты фанкойлов к другому помещению.');
            }

            if (ChecklistBalk::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'Для помещения есть чеклисты балок, удалите или привяжите чеклисты балок к другому помещению.');
            }

            if (ChecklistAirDuct::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'Для помещения есть чеклисты воздуховодов, удалите или привяжите чеклисты воздуховодов к другому помещению.');
            }

            if (ChecklistAirDiffuser::where('trk_room_id', $trk_room->id)->exists()) {
                return redirect()->back()->with('error', 'Для помещения есть чеклисты диффузоров, удалите или привяжите чеклисты диффузоров к другому помещению.');
            }

            if (TrkRoomRepair::where('trk_room_id', $trk_room->id)->exists())
            {
                $trk_repair = TrkRoomRepair::where('trk_room_id', $trk_room->id)->first();
                return redirect()->back()->with('error', 'Нельзя удалить помещение, к нему привязан ремонт: ' . $trk_repair->trk_equipment->trk_room->trk->name . ', ' .
                    $trk_repair->trk_equipment->trk_room->building->name . ', ' .
                    $trk_repair->trk_equipment->trk_room->floor->name . ', ' .
                    $trk_repair->trk_equipment->trk_room->room->name . ', ' .
                    $trk_repair->trk_equipment->equipment_name->name);
            }

            $trk_room->update([
                'destroyer_id' => Auth::id(),
            ]);

            $trk_room->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }

        return redirect()->route('trk_room.index')->with('success', 'Данные удалены');

    }
}
