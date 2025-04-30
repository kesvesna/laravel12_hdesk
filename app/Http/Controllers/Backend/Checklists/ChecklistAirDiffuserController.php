<?php

namespace App\Http\Controllers\Backend\Checklists;

use App\Http\Controllers\Controller;
use App\Http\Filters\Checklists\AirDiffuser\ChecklistAirDiffuserFilter;
use App\Http\Requests\Checklists\AirDiffuser\ChecklistAirDiffuserFilterRequest;
use App\Http\Requests\Checklists\AirDiffuser\StoreChecklistAirDiffuserFormRequest;
use App\Http\Requests\Checklists\AirDiffuser\StoreChecklistAirDiffuserFromTrkEquipmentFormRequest;
use App\Http\Requests\Checklists\AirDiffuser\StoreChecklistAirDiffuserFromTrkRoomFormRequest;
use App\Http\Requests\Checklists\AirDiffuser\UpdateChecklistAirDiffuserFormRequest;
use App\Models\Avrs\Avr;
use App\Models\Avrs\AvrEquipment;
use App\Models\Avrs\AvrExecutor;
use App\Models\Avrs\AvrWork;
use App\Models\Buildings\Building;
use App\Models\Checklists\ChecklistAirDiffuser;
use App\Models\DocCommunications\DocCommunication;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentUsers\EquipmentUser;
use App\Models\EquipmentWorkPeriods\EquipmentWorkPeriod;
use App\Models\Floors\Floor;
use App\Models\Rooms\Room;
use App\Models\SpareParts\SparePartName;
use App\Models\Systems\System;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\WorkNames\WorkName;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color\BuiltIn;

class ChecklistAirDiffuserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(ChecklistAirDiffuser::class, 'checklists_air_diffuser');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ChecklistAirDiffuserFilterRequest $request): Response
    {

        $data = $request->validated();

        $filter = app()->make(ChecklistAirDiffuserFilter::class, ['queryParams' => array_filter($data)]);

        $checklists = ChecklistAirDiffuser::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.checklists_air_diffuser.pagination'));

        return \response()->view('backend.checklists.air_diffuser.index', [
            'checklists' => $checklists,
            'old_filters' => $data,
            'all_trks' => Trk::orderBy('sort_order')->get(),
            'all_systems' => System::orderBy('sort_order')->get(),
            'all_equipment_names' => EquipmentName::orderBy('name')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $first_trk = Trk::orderBy('sort_order')->pluck('id')->first();

        $building_ids = TrkRoom::where('trk_id', $first_trk)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $buildings = Building::whereIn('id', $building_ids)->orderBy('name')->get();
        $first_building = $buildings->first();

        $floor_ids = TrkRoom::where('trk_id', $first_trk)
            ->where('building_id', $first_building->id)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $floors = Floor::whereIn('id', $floor_ids)->orderBy('name')->get();
        $first_floor = $floors->first();

        $room_ids = TrkRoom::where('trk_id', $first_trk)
            ->where('building_id', $first_building->id)
            ->where('floor_id', $first_floor->id)
            ->pluck('room_id')
            ->toArray();

        $rooms = Room::whereIn('id', $room_ids)->orderBy('name')->get();

        $system = System::where('name', System::AIR_RECYCLE)->first();

        $trk_equipment_name_ids = TrkEquipment::where('system_id', $system->id)->pluck('equipment_name_id')->toArray();
        $trk_equipment_name_ids = implode("', '", $trk_equipment_name_ids);

        $equipment_names = EquipmentName::whereRaw("id in ('$trk_equipment_name_ids')")->orderBy('name')->get();

        return \response()->view('backend.checklists.air_diffuser.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::where('name', System::AIR_RECYCLE)->get(),
            'rooms' => $rooms,
            'buildings' => $buildings,
            'floors' => $floors,
            'executors' => User::orderBy('name')->get(),
            'work_types' => WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get(),
            'equipment_names' => $equipment_names,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChecklistAirDiffuserFormRequest $request): RedirectResponse
    {
        Log::info('user try to store new checklist air diffuser', [
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

                if (empty($trk_room->id)) {

                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $system = System::where('name', System::AIR_RECYCLE)->first();

                $equipment_name = EquipmentName::where('name', array_key_first($data['equipments']))->first();

                if (empty($equipment_name->id)) {

                    return redirect()->back()->with('error', 'Нет оборудования с таким названием: ' . array_key_first($data['equipments']))->withInput();
                }

                $equipment_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id_2'])
                    ->where('floor_id', $data['floor_id_2'])
                    ->where('room_id', $data['room_id_2'])
                    ->first();

                $trk_equipment = TrkEquipment::where('trk_room_id', $equipment_room->id)
                    ->where('system_id', $system->id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {

                    return redirect()->back()->with('error', 'Нет такой установки: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ' - ' . $equipment_name->name)->withInput();
                }

                DB::beginTransaction();

                $avr = Avr::create([
                    'trk_room_id' => $trk_room->id,
                    'system_id' => $system->id,
                    'date' => now(),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                array_shift($data['equipments']);

                foreach ($data['equipments'] as $equipment) {

                    $checklist = ChecklistAirDiffuser::create([
                        'trk_room_id' => $trk_room->id,
                        'trk_equipment_id' => $trk_equipment->id,
                        'air_direction_type' => $equipment['air_direction_type'],
                        'measuring_point_number' => $equipment['measuring_point_number'],
                        'length_or_diameter' => $equipment['length_or_diameter'],
                        'width' => $equipment['width'],
                        'air_speed' => $equipment['air_speed'],
                        'estimated_coefficient' => $equipment['estimated_coefficient'],
                        'diffuser_cross_sectional_area' => $equipment['diffuser_cross_sectional_area'],
                        'air_flow_rate' => $equipment['air_flow_rate'],
                        'air_pressure' => $equipment['air_pressure'],
                        'air_temperature' => $equipment['air_temperature'],
                        'air_throttling_valve' => $equipment['air_throttling_valve'],
                        'comment' => $equipment['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    DocCommunication::create([
                        'from_id' => $checklist->id,
                        'from_type' => get_class($checklist),
                        'to_id' => $avr->id,
                        'to_type' => get_class($avr),
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                AvrEquipment::create([
                    'trk_equipment_id' => $trk_equipment->id,
                    'avr_id' => $avr->id,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                foreach($data['work_types'] as $work)
                {
                    AvrWork::create([
                        'avr_id' => $avr->id,
                        'work_name_id' => $work,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                        'trk_equipment_id' => $trk_equipment->id,
                    ]);

                    //=================================================================================
                    $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $trk_equipment->id)
                        ->where('work_name_id', $work)
                        ->first();

                    if(!empty($equipment_period_work->id))
                    {

                        $next_to_be_at = Carbon::createFromFormat('Y-m-d H:i:s', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                        ]);
                    }
                    //=================================================================================
                }

                $work_type = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

                AvrWork::create([
                    'avr_id' => $avr->id,
                    'work_name_id' => $work_type->id,
                    'description' => $data['comment'] ?? null,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'trk_equipment_id' => $trk_equipment->id,
                ]);

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $equipment_user = EquipmentUser::where('trk_room_id', $trk_room->id)
                    ->where('equipment_id', $trk_equipment->id)
                    ->first();

                if(empty($equipment_user->id))
                {
                    EquipmentUser::create([
                        'trk_room_id' => $trk_room->id,
                        'equipment_id' => $trk_equipment->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('checklists_air_diffuser.index')->with('success', 'Данные сохранены.');

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
        $this->authorize('create', ChecklistAirDiffuser::class);

        $work_types = WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get();

        $trk_room_ids = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)->pluck('room_id')->toArray();

        $rooms = Room::whereIn('id', $trk_room_ids)->orderBy('name')->get();

        $trk_building_ids = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)->pluck('building_id')->toArray();

        $buildings = Building::whereIn('id', $trk_building_ids)->orderBy('name')->get();

        $trk_floor_ids = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)->pluck('floor_id')->toArray();

        $floors = Floor::whereIn('id', $trk_floor_ids)->orderBy('name')->get();

        return \response()->view('backend.checklists.air_diffuser.create_from_trk_equipment', [
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
            'trk_equipment' => $trk_equipment,
            'buildings' => $buildings,
            'floors' => $floors,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_equipment(StoreChecklistAirDiffuserFromTrkEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        $this->authorize('create', ChecklistAirDiffuser::class);

        Log::info('user try to store new checklist air diffuser from trk equipment', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_equipment' => $trk_equipment,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)
                    ->where('building_id', $data['building_id_2'])
                    ->where('floor_id', $data['floor_id_2'])
                    ->where('room_id', $data['room_id_2'])
                    ->first();

                if (empty($trk_room->id)) {

                    $trk = Trk::find($trk_equipment->trk_room->trk->id);
                    $building = Building::find($data['building_id_2']);
                    $floor = Floor::find($data['floor_id_2']);
                    $room = Room::find($data['room_id_2']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                DB::beginTransaction();

                $avr = Avr::create([
                    'trk_room_id' => $trk_room->id,
                    'system_id' => $trk_equipment->system->id,
                    'date' => now(),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                foreach ($data['equipments'] as $equipment) {

                    $checklist = ChecklistAirDiffuser::create([
                        'trk_room_id' => $trk_room->id,
                        'trk_equipment_id' => $trk_equipment->id,
                        'air_direction_type' => $equipment['air_direction_type'],
                        'measuring_point_number' => $equipment['measuring_point_number'],
                        'length_or_diameter' => $equipment['length_or_diameter'],
                        'width' => $equipment['width'],
                        'air_speed' => $equipment['air_speed'],
                        'estimated_coefficient' => $equipment['estimated_coefficient'],
                        'diffuser_cross_sectional_area' => $equipment['diffuser_cross_sectional_area'],
                        'air_flow_rate' => $equipment['air_flow_rate'],
                        'air_pressure' => $equipment['air_pressure'],
                        'air_temperature' => $equipment['air_temperature'],
                        'air_throttling_valve' => $equipment['air_throttling_valve'],
                        'comment' => $equipment['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    DocCommunication::create([
                        'from_id' => $checklist->id,
                        'from_type' => get_class($checklist),
                        'to_id' => $avr->id,
                        'to_type' => get_class($avr),
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }


                AvrEquipment::create([
                    'trk_equipment_id' => $trk_equipment->id,
                    'avr_id' => $avr->id,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);


                foreach($data['work_types'] as $work)
                {
                    AvrWork::create([
                        'avr_id' => $avr->id,
                        'work_name_id' => $work,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                        'trk_equipment_id' => $trk_equipment->id,
                    ]);

                    //=================================================================================
                    $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $trk_equipment->id)
                        ->where('work_name_id', $work)
                        ->first();

                    if(!empty($equipment_period_work->id))
                    {

                        $next_to_be_at = Carbon::createFromFormat('Y-m-d H:i:s', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                        ]);
                    }
                    //=================================================================================
                }

                $work_type = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

                AvrWork::create([
                    'avr_id' => $avr->id,
                    'work_name_id' => $work_type->id,
                    'description' => $data['comment'] ?? null,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'trk_equipment_id' => $trk_equipment->id,
                ]);

                $executors = User::whereIn('name', $data['executors'])->get();

                foreach ($executors as $executor) {
                    AvrExecutor::create([
                        'avr_id' => $avr->id,
                        'user_id' => $executor->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $equipment_user = EquipmentUser::where('trk_room_id', $trk_room->id)
                    ->where('equipment_id', $trk_equipment->id)
                    ->first();

                if(empty($equipment_user->id))
                {
                    EquipmentUser::create([
                        'trk_room_id' => $trk_room->id,
                        'equipment_id' => $trk_equipment->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('checklists_air_diffuser.index')->with('success', 'Данные сохранены.');

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
        $this->authorize('create', ChecklistAirDiffuser::class);

        $work_types = WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get();

        $system_condition = System::where('name', System::AIR_RECYCLE)->first();

        $equipment_user_ids = EquipmentUser::where('trk_room_id', $trk_room->id)
            ->pluck('equipment_id')
            ->toArray();

        $trk_room_equipments = TrkEquipment::whereIn('id', $equipment_user_ids)
            ->where('system_id', $system_condition->id)
            ->get();

        $trk_room_ids = TrkRoom::where('trk_id', $trk_room->trk->id)->pluck('room_id')->toArray();

        $rooms = Room::whereIn('id', $trk_room_ids)->orderBy('name')->get();

        $trk_building_ids = TrkRoom::where('trk_id', $trk_room->trk->id)->pluck('building_id')->toArray();

        $buildings = Building::whereIn('id', $trk_building_ids)->orderBy('name')->get();

        $trk_floor_ids = TrkRoom::where('trk_id', $trk_room->trk->id)->pluck('floor_id')->toArray();

        $floors = Floor::whereIn('id', $trk_floor_ids)->orderBy('name')->get();

        $trk_room_ids = TrkRoom::where('trk_id', $trk_room->trk->id)->pluck('id')->toArray();

        $trk_room_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
            ->where('system_id', $system_condition->id)
            ->pluck('equipment_name_id')
            ->toArray();

        $equipment_names = EquipmentName::whereIn('id', $trk_room_equipment_ids)->get();
        return \response()->view('backend.checklists.air_diffuser.create_from_trk_room', [
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
            'trk_room_equipments' => $trk_room_equipments,
            'trk_room' => $trk_room,
            'buildings' => $buildings,
            'floors' => $floors,
            'equipment_names' => $equipment_names,
            'rooms' => $rooms,
            'system' => $system_condition,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreChecklistAirDiffuserFromTrkRoomFormRequest $request, TrkRoom $trk_room): RedirectResponse
    {
        $this->authorize('create', ChecklistAirDiffuser::class);

        Log::info('user try to store new checklist air diffuser from trk room', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_room' => $trk_room,
        ]);

        if ($request->isMethod('post')) {

            try {

                $data = $request->validated();

                $system_balk = System::where('name', System::AIR_RECYCLE)->first();

                $equipment_name = EquipmentName::where('name', $data['equipment_id'])->first();

                if (empty($equipment_name->id)) {

                    return redirect()->back()->with('error', 'Нет оборудования с таким названием: ' . $data['equipment_id'])->withInput();
                }

                $equipment_room = TrkRoom::where('trk_id', $trk_room->trk->id)
                    ->where('building_id', $data['building_id_2'])
                    ->where('floor_id', $data['floor_id_2'])
                    ->where('room_id', $data['room_id_2'])
                    ->first();

                $trk_equipment = TrkEquipment::where('trk_room_id', $equipment_room->id)
                    ->where('system_id', $system_balk->id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {

                    return redirect()->back()->with('error', 'Нет такой установки: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ' - ' . $equipment_name->name)->withInput();
                }

                DB::beginTransaction();

                foreach ($data['equipments'] as $equipment) {

                    $checklist = ChecklistAirDiffuser::create([

                        'trk_room_id' => $trk_room->id,
                        'trk_equipment_id' => $trk_equipment->id,
                        'air_direction_type' => $equipment['air_direction_type'],
                        'measuring_point_number' => $equipment['measuring_point_number'],
                        'length_or_diameter' => $equipment['length_or_diameter'],
                        'width' => $equipment['width'],
                        'air_speed' => $equipment['air_speed'],
                        'estimated_coefficient' => $equipment['estimated_coefficient'],
                        'diffuser_cross_sectional_area' => $equipment['diffuser_cross_sectional_area'],
                        'air_flow_rate' => $equipment['air_flow_rate'],
                        'air_pressure' => $equipment['air_pressure'],
                        'air_temperature' => $equipment['air_temperature'],
                        'air_throttling_valve' => $equipment['air_throttling_valve'],
                        'comment' => $equipment['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    $avr = Avr::create([
                        'trk_room_id' => $trk_room->id,
                        'system_id' => $system_balk->id,
                        'date' => now(),
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    AvrEquipment::create([
                        'trk_equipment_id' => $trk_equipment->id,
                        'avr_id' => $avr->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    foreach($data['work_types'] as $work)
                    {
                        AvrWork::create([
                            'avr_id' => $avr->id,
                            'work_name_id' => $work,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                            'trk_equipment_id' => $trk_equipment->id,
                        ]);

                        //=================================================================================
                        $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $trk_equipment->id)
                            ->where('work_name_id', $work)
                            ->first();

                        if(!empty($equipment_period_work->id))
                        {

                            $next_to_be_at = Carbon::createFromFormat('Y-m-d H:i:s', $avr->date);
                            $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                            $equipment_period_work->update([
                                'last_was_at' => $avr->date,
                                'next_to_be_at' => $next_to_be_at,
                            ]);
                        }
                        //=================================================================================
                    }

                    $work_type = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

                    AvrWork::create([
                        'avr_id' => $avr->id,
                        'work_name_id' => $work_type->id,
                        'description' => $data['comment'] ?? null,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                        'trk_equipment_id' => $trk_equipment->id,
                    ]);

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
                        'from_id' => $checklist->id,
                        'from_type' => get_class($checklist),
                        'to_id' => $avr->id,
                        'to_type' => get_class($avr),
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $equipment_user = EquipmentUser::where('trk_room_id', $trk_room->id)
                    ->where('equipment_id', $trk_equipment->id)
                    ->first();

                if(empty($equipment_user->id))
                {
                    EquipmentUser::create([
                        'trk_room_id' => $trk_room->id,
                        'equipment_id' => $trk_equipment->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('checklists_air_diffuser.index')->with('success', 'Данные сохранены.');

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
    public function show(ChecklistAirDiffuser $checklists_air_diffuser): Response
    {
        return \response()->view('backend.checklists.air_diffuser.show', [
            'checklist' => $checklists_air_diffuser,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChecklistAirDiffuser $checklists_air_diffuser): Response
    {
        $work_types = WorkName::whereIn('name', ['ТО 4', 'ТО 5', 'ТО 6'])->get();
        $system = System::where('name', System::AIR_RECYCLE)->first();

        $equipment_names_ids = TrkEquipment::where('id', $checklists_air_diffuser->trk_equipment_id)
            ->where('system_id', $system->id)
            ->pluck('equipment_name_id')
            ->toArray();

        $equipment_names = EquipmentName::whereIn('id', $equipment_names_ids)->get();

        return \response()->view('backend.checklists.air_diffuser.edit', [
            'checklist' => $checklists_air_diffuser,
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'equipment_names' => $equipment_names,
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChecklistAirDiffuserFormRequest $request, ChecklistAirDiffuser $checklists_air_diffuser): RedirectResponse
    {

        Log::info('user try to update checklist air diffuser', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'checklist air diffuser' => $checklists_air_diffuser
        ]);

        if ($request->isMethod('patch')) {

            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if (empty($trk_room->id)) {

                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $system = System::where('name', System::AIR_RECYCLE)->first();

                $equipment_name = EquipmentName::where('name', $data['equipment_id'])->first();

                if (empty($equipment_name->id)) {

                    return redirect()->back()->with('error', 'Нет оборудования с таким названием: ' . $data['equipment_id'])->withInput();
                }

                $equipment_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id_2'])
                    ->where('floor_id', $data['floor_id_2'])
                    ->where('room_id', $data['room_id_2'])
                    ->first();

                $trk_equipment = TrkEquipment::where('trk_room_id', $equipment_room->id)
                    ->where('system_id', $system->id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {

                    return redirect()->back()->with('error', 'Нет такой установки: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ' - ' . $equipment_name->name)->withInput();
                }

                DB::beginTransaction();

                $avr = $checklists_air_diffuser->avr->first();

                foreach($avr->avr_equipments as $avr_equipment)
                {
                    if(
                            $avr_equipment->trk_equipment_id == $checklists_air_diffuser->trk_equipment_id
                        && $avr_equipment->trk_equipment_id != $trk_equipment->id
                    )
                    {
                        $avr_equipment->update([
                            'trk_equipment_id' => $trk_equipment->id,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                AvrWork::where('avr_id', $avr->id)
                    ->where('trk_equipment_id', $checklists_air_diffuser->trk_equipment_id)
                    ->forceDelete();

                foreach($data['work_types'] as $work)
                {
                    AvrWork::create([
                        'avr_id' => $avr->id,
                        'work_name_id' => $work,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                        'trk_equipment_id' => $trk_equipment->id,
                    ]);

                    //=================================================================================
                    $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $trk_equipment->id)
                        ->where('work_name_id', $work)
                        ->first();

                    if(!empty($equipment_period_work->id))
                    {

                        $next_to_be_at = Carbon::createFromFormat('Y-m-d H:i:s', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays($equipment_period_work->repeat_days);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                        ]);
                    }
                    //=================================================================================
                }

                $work_type = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

                AvrWork::create([
                    'avr_id' => $avr->id,
                    'work_name_id' => $work_type->id,
                    'description' => $data['comment'] ?? null,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'trk_equipment_id' => $trk_equipment->id,
                ]);

                $equipment_user = EquipmentUser::where('trk_room_id', $trk_room->id)
                    ->where('equipment_id', $trk_equipment->id)
                    ->first();

                if(empty($equipment_user->id))
                {
                    EquipmentUser::create([
                        'trk_room_id' => $trk_room->id,
                        'equipment_id' => $trk_equipment->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $checklists_air_diffuser->update([
                    'trk_room_id' => $trk_room->id,
                    'trk_equipment_id' => $trk_equipment->id,
                    'air_direction_type' => $data['air_direction_type'],
                    'measuring_point_number' => $data['measuring_point_number'],
                    'length_or_diameter' => $data['length_or_diameter'],
                    'width' => $data['width'],
                    'air_speed' => $data['air_speed'],
                    'estimated_coefficient' => $data['estimated_coefficient'],
                    'diffuser_cross_sectional_area' => $data['diffuser_cross_sectional_area'],
                    'air_flow_rate' => $data['air_flow_rate'],
                    'air_pressure' => $data['air_pressure'],
                    'air_temperature' => $data['air_temperature'],
                    'air_throttling_valve' => $data['air_throttling_valve'],
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('checklists_air_diffuser.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChecklistAirDiffuser $checklists_air_diffuser): RedirectResponse
    {
        Log::info('user try to delete ChecklistAirDiffuser', [
            'user' => Auth::user()->name,
            'checklists_air_diffuser' => $checklists_air_diffuser
        ]);

        try {

            DB::beginTransaction();

                $avr_id = $checklists_air_diffuser->avr->first()->id;

                DocCommunication::where('from_id', $checklists_air_diffuser->id)
                    ->where('from_type', ChecklistAirDiffuser::class)
                    ->where('to_id', $avr_id)
                    ->where('to_type', Avr::class)
                    ->delete();

            $checklists_air_diffuser->update([
                'destroyer_id' => Auth::id(),
            ]);

            $checklists_air_diffuser->delete();

            DB::commit();

            return redirect()->route('checklists_air_diffuser.index')->with('success', 'Данные удалены.');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
        }

        return redirect()->back()->with('error', 'Ошибка удаления данных, смотрите логи.');

    }
}
