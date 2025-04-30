<?php

namespace App\Http\Controllers\Backend\Checklists;

use App\Http\Controllers\Controller;
use App\Http\Filters\Checklists\Conditioner\ChecklistConditionerFilter;
use App\Http\Requests\Checklists\Conditioner\ChecklistConditionerFilterRequest;
use App\Http\Requests\Checklists\Conditioner\StoreChecklistConditionerFormRequest;
use App\Http\Requests\Checklists\Conditioner\StoreChecklistConditionerFromTrkEquipmentFormRequest;
use App\Http\Requests\Checklists\Conditioner\StoreChecklistConditionerFromTrkRoomFormRequest;
use App\Http\Requests\Checklists\Conditioner\UpdateChecklistConditionerFormRequest;
use App\Models\Avrs\Avr;
use App\Models\Avrs\AvrEquipment;
use App\Models\Avrs\AvrExecutor;
use App\Models\Avrs\AvrWork;
use App\Models\Buildings\Building;
use App\Models\Checklists\ChecklistConditioner;
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

class ChecklistConditionerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(ChecklistConditioner::class, 'checklists_conditioner');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ChecklistConditionerFilterRequest $request): Response
    {

        $data = $request->validated();

        $filter = app()->make(ChecklistConditionerFilter::class, ['queryParams' => array_filter($data)]);

        $checklists = ChecklistConditioner::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.checklists_conditioner.pagination'));

        return \response()->view('backend.checklists.conditioner.index', [
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
        $first_trk_room_ids = TrkRoom::where('trk_id', $first_trk)->pluck('room_id')->toArray();
        $first_trk_rooms = Room::whereIn('id', $first_trk_room_ids)->orderBy('name')->get();

        $condition_system = System::where('name', System::AIR_CONDITION)->first();

        $trk_room_ids = TrkRoom::where('trk_id', $first_trk)->pluck('id')->toArray();

        $equipment_name_ids = TrkEquipment::where('system_id', $condition_system->id)
            ->whereIn('trk_room_id', $trk_room_ids)
            ->pluck('equipment_name_id')
            ->toArray();

        $equipment_names = EquipmentName::whereIn('id', $equipment_name_ids)->get();

        $work_types = WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get();

        return \response()->view('backend.checklists.conditioner.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::where('name', System::AIR_CONDITION)->get(),
            'equipment_names' => $equipment_names,
            'rooms' => $first_trk_rooms,
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'work_types' => $work_types,
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChecklistConditionerFormRequest $request): RedirectResponse
    {
        Log::info('user try to store new checklist conditioner', [
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
                    return redirect()->back()->with('error', 'Нет такого помещения на выбранном ТРК')->withInput();
                }

                $condition_system = System::where('name', System::AIR_CONDITION)->first();

                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('id', $data['equipment_id'])
                    ->where('system_id', $condition_system->id)
                    ->first();

                if (empty($trk_equipment)) {
                    return redirect()->back()->with('error', 'Нет такого кондиционера: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ', ' . $trk_room->room->name)->withInput();
                }

                DB::beginTransaction();

                $checklist = ChecklistConditioner::create([
                    'trk_room_id' => $trk_room->id,
                    'trk_equipment_id' => $trk_equipment->id,
                    'conditioner_number' => 1,
                    'air_inlet_temperature' => $data['air_inlet_temperature'],
                    'air_outlet_temperature' => $data['air_outlet_temperature'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                $avr = Avr::create([
                    'trk_room_id' => $trk_room->id,
                    'system_id' => $data['system_id'],
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

                return redirect()->route('checklists_conditioner.index')->with('success', 'Данные сохранены.');

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
        $this->authorize('create', ChecklistConditioner::class);

        $work_types = WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get();

        return \response()->view('backend.checklists.conditioner.create_from_trk_equipment', [
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
            'trk_equipment' => $trk_equipment,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_equipment(StoreChecklistConditionerFromTrkEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        $this->authorize('create', ChecklistConditioner::class);

        Log::info('user try to store new checklist conditioner from trk equipment', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_equipment' => $trk_equipment,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                DB::beginTransaction();

                $checklist = ChecklistConditioner::create([
                    'trk_room_id' => $trk_equipment->trk_room_id,
                    'trk_equipment_id' => $trk_equipment->id,
                    'conditioner_number' => 1,
                    'air_inlet_temperature' => $data['air_inlet_temperature'],
                    'air_outlet_temperature' => $data['air_outlet_temperature'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                $avr = Avr::create([
                    'trk_room_id' => $trk_equipment->trk_room_id,
                    'system_id' => $trk_equipment->system->id,
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

                $equipment_user = EquipmentUser::where('trk_room_id', $trk_equipment->trk_room_id)
                    ->where('equipment_id', $trk_equipment->id)
                    ->first();

                if(empty($equipment_user->id))
                {
                    EquipmentUser::create([
                        'trk_room_id' => $trk_equipment->trk_room_id,
                        'equipment_id' => $trk_equipment->id,
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
     * Show the form for creating a new resource.
     */
    public function create_from_trk_room(TrkRoom $trk_room): Response
    {
        $this->authorize('create', ChecklistConditioner::class);

        $work_types = WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get();

        $system_condition = System::where('name', System::AIR_CONDITION)->first();

        $trk_room_equipments = TrkEquipment::where('trk_room_id', $trk_room->id)
            ->where('system_id', $system_condition->id)
            ->get();

        return \response()->view('backend.checklists.conditioner.create_from_trk_room', [
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
            'trk_room_equipments' => $trk_room_equipments,
            'trk_room' => $trk_room,
            'system' => $system_condition,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreChecklistConditionerFromTrkRoomFormRequest $request, TrkRoom $trk_room): RedirectResponse
    {
        $this->authorize('create', ChecklistConditioner::class);

        Log::info('user try to store new checklist conditioner from trk room', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_room' => $trk_room,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $system_conditioner = System::where('name', System::AIR_CONDITION)->first();

                DB::beginTransaction();

                foreach ($data['equipments'] as $equipment) {

                    $trk_equipment = TrkEquipment::find($equipment['id']);

                    $checklist = ChecklistConditioner::create([
                        'trk_room_id' => $trk_room->id,
                        'trk_equipment_id' => $trk_equipment->id,
                        'conditioner_number' => 1,
                        'air_inlet_temperature' => $equipment['air_inlet_temperature'],
                        'air_outlet_temperature' => $equipment['air_outlet_temperature'],
                        'comment' => $equipment['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    $avr = Avr::create([
                        'trk_room_id' => $trk_equipment->trk_room_id,
                        'system_id' => $system_conditioner->id,
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

                return redirect()->route('checklists_conditioner.index')->with('success', 'Данные сохранены.');

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
    public function show(ChecklistConditioner $checklists_conditioner): Response
    {
        return \response()->view('backend.checklists.conditioner.show', [
            'checklist' => $checklists_conditioner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChecklistConditioner $checklists_conditioner): Response
    {
        $work_types = WorkName::whereIn('name', ['ТО 4', 'ТО 5', 'ТО 6'])->get();
        $system_conditioner = System::where('name', System::AIR_CONDITION)->first();

        $trk_building_ids = TrkRoom::where('trk_id', $checklists_conditioner->trk_room->trk->id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $buildings = Building::whereIn('id', $trk_building_ids)->orderBy('name')->get();

        $trk_room_ids = TrkRoom::where('trk_id', $checklists_conditioner->trk_room->trk->id)
            ->pluck('room_id')
            ->toArray();

        $rooms = Room::whereIn('id', $trk_room_ids)->orderBy('name')->get();

        $trk_floor_ids = TrkRoom::where('trk_id', $checklists_conditioner->trk_room->trk->id)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $floors = Floor::whereIn('id', $trk_floor_ids)->orderBy('name')->get();

        $trk_room_ids = TrkRoom::where('trk_id', $checklists_conditioner->trk_room->trk->id)
            ->pluck('id')
            ->toArray();

        $equipment_names_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
            ->where('system_id', $system_conditioner->id)
            ->pluck('equipment_name_id')
            ->toArray();

        $equipment_names = EquipmentName::whereIn('id', $equipment_names_ids)->orderBy('name')->get();

        return \response()->view('backend.checklists.conditioner.edit', [
            'checklist' => $checklists_conditioner,
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('name')->get(),
            'rooms' => $rooms,
            'buildings' => $buildings,
            'floors' => $floors,
            'equipment_names' => $equipment_names,
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChecklistConditionerFormRequest $request, ChecklistConditioner $checklists_conditioner): RedirectResponse
    {

        Log::info('user try to update checklist conditioner', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'checklist conditioner' => $checklists_conditioner
        ]);

        if ($request->isMethod('patch')) {
            try {

                $data = $request->validated();

                $system = System::where('name', System::AIR_CONDITION)->first();

                $trk_room = TrkRoom::where('trk_id', $checklists_conditioner->trk_room->trk->id)
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if (empty($trk_room->id)) {

                    $trk = Trk::find($checklists_conditioner->trk_room->trk->id);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $equipment_name = EquipmentName::where('name', $data['equipment_id'])->first();

                if (empty($equipment_name->id)) {

                    return redirect()->back()->with('error', 'Нет кондиционера с таким названием: ' . $data['equipment_id'])->withInput();
                }

                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('system_id', $system->id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {

                    return redirect()->back()->with('error', 'Нет такого кондиционера: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ' - ' . $equipment_name->name)->withInput();
                }

                DB::beginTransaction();

                $avr = $checklists_conditioner->avr->first();

                foreach($avr->avr_equipments as $avr_equipment)
                {
                    if(
                        $avr_equipment->trk_equipment_id == $checklists_conditioner->trk_equipment_id
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
                    ->where('trk_equipment_id', $checklists_conditioner->trk_equipment_id)
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

                $checklists_conditioner->update([
                    'trk_equipment_id' => $trk_equipment->id,
                    'trk_room_id' => $trk_room->id,
                    'air_inlet_temperature' => $data['air_inlet_temperature'],
                    'air_outlet_temperature' => $data['air_outlet_temperature'],
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('checklists_conditioner.index')->with('success', 'Данные сохранены.');

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
    public function destroy(ChecklistConditioner $checklists_conditioner): RedirectResponse
    {
        Log::info('user try to delete ChecklistConditioner', [
            'user' => Auth::user()->name,
            'checklists_conditioner' => $checklists_conditioner
        ]);

        try {

            DB::beginTransaction();

            $avr_id = $checklists_conditioner->avr->first()->id;

            DocCommunication::where('from_id', $checklists_conditioner->id)
                ->where('from_type', ChecklistConditioner::class)
                ->where('to_id', $avr_id)
                ->where('to_type', Avr::class)
                ->delete();

            $checklists_conditioner->update([
                'destroyer_id' => Auth::id(),
            ]);

            $checklists_conditioner->delete();

            DB::commit();

            return redirect()->route('checklists_conditioner.index')->with('success', 'Данные удалены.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

        return redirect()->back()->with('error', 'Ошибка удаления данных, смотрите логи.');

    }
}
