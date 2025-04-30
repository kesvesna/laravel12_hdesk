<?php

namespace App\Http\Controllers\Backend\TrkRoomRepairs;

use App\Http\Controllers\Controller;
use App\Http\Filters\TrkRoomRepairs\TrkRoomRepairFilter;
use App\Http\Requests\TrkRoomRepairs\SetRepairsToAnotherEquipmentFormRequest;
use App\Http\Requests\TrkRoomRepairs\StoreTrkRoomRepairFormRequest;
use App\Http\Requests\TrkRoomRepairs\StoreTrkRoomRepairFromOperationApplicationFormRequest;
use App\Http\Requests\TrkRoomRepairs\StoreTrkRoomRepairFromTechActFormRequest;
use App\Http\Requests\TrkRoomRepairs\StoreTrkRoomRepairFromTrkEquipmentFormRequest;
use App\Http\Requests\TrkRoomRepairs\TrkRoomRepairFilterRequest;
use App\Http\Requests\TrkRoomRepairs\UpdateTrkRoomRepairDoneProgressFormRequest;
use App\Http\Requests\TrkRoomRepairs\UpdateTrkRoomRepairFormRequest;
use App\Models\Buildings\Building;
use App\Models\DocCommunications\DocCommunication;
use App\Models\Equipments\EquipmentName;
use App\Models\Floors\Floor;
use App\Models\OperationApplications\OperationApplication;
use App\Models\Rooms\Room;
use App\Models\Systems\System;
use App\Models\TechActs\TechAct;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRoomRepairs\TrkRoomRepair;
use App\Models\TrkRoomRepairs\TrkRoomRepairExecutor;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrkRoomRepairController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TrkRoomRepair::class, 'trk_repair');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TrkRoomRepairFilterRequest $request): Response
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

        $filter = app()->make(TrkRoomRepairFilter::class, ['queryParams' => array_filter($data)]);

        $repairs = TrkRoomRepair::filter($filter)
            ->orderBy('deadline_at', 'desc')
            ->paginate(config('backend.trk_repairs.pagination'));

        return \response()->view('backend.trk_repairs.index', [
            'trk_repairs' => $repairs,
            'all_trks' => $all_trks,
            'all_user_divisions' => UserDivision::orderBy('name')->where('visibility', 1)->get(),
            'all_equipment_names' => EquipmentName::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $trks = Trk::orderBy('sort_order')->get();
        $first_trk = $trks->first();

        $trk_building_ids = TrkRoom::where('trk_id', $first_trk->id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $buildings = Building::whereIn('id', $trk_building_ids)
            ->orderBy('name')
            ->get();
        $first_building = $buildings->first();

        $trk_floor_ids = TrkRoom::where('trk_id', $first_trk->id)
            ->where('building_id', $first_building->id)
            ->pluck('floor_id')
            ->toArray();

        $floors = Floor::whereIn('id', $trk_floor_ids)
            ->orderBy('name')
            ->get();
        $first_floor = $floors->first();

        $trk_room_ids = TrkRoom::where('trk_id', $first_trk->id)
            ->where('building_id', $first_building->id)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $rooms = Room::whereIn('id', $trk_room_ids)
            ->orderBy('name')
            ->get();
        $first_room = $rooms->first();

        $trk_rooms = TrkRoom::where('trk_id', $first_trk->id)
            ->where('building_id', $first_building->id)
            ->where('floor_id', $first_floor->id)
            ->pluck('id')
            ->toArray();

        $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_rooms)
            ->pluck('equipment_name_id')
            ->toArray();

        $equipment_system_ids = TrkEquipment::whereIn('trk_room_id', $trk_rooms)
            ->groupBy('system_id')
            ->pluck('system_id')
            ->toArray();

        $equipment_names = EquipmentName::whereIn('id', $trk_equipment_ids)->get();

        $systems = System::whereIn('id', $equipment_system_ids)
            ->orderBy('name')
            ->get();

        return \response()->view('backend.trk_repairs.create', [
            'trks' => $trks,
            'systems' => $systems,
            'equipment_names' => $equipment_names,
            'rooms' => $rooms,
            'buildings' => $buildings,
            'floors' => $floors,
            'executors' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkRoomRepairFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store trk room repair',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            $user = User::find(Auth::id());

            if (!(
                $user->town_id &&
                $user->organization_id &&
                $user->user_function_id &&
                $user->user_division_id
            )
            ) {
                return redirect()->route('profile.role_setting');
            }


            try {
                $data = $request->validated();

                $trk_room_id = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->pluck('id')->first();

                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room_id)
                    ->where('system_id', $data['system_id'])
                    ->where('equipment_name_id', $data['equipment_id'])
                    ->first();

                if (empty($trk_equipment->id)) {
                    $equipment = EquipmentName::where('id', $data['equipment_id'])->first();
                    $room = Room::where('id', $data['room_id'])->first();
                    $trk = Trk::where('id', $data['trk_id'])->first();
                    return redirect()->back()->with('error', 'Нет такого оборудования ' . $equipment->name . ' в ' . $room->name . ' на ' . $trk->name);
                }

                TrkRoomRepair::create([
                    'trk_room_id' => $trk_room_id,
                    'equipment_id' => $trk_equipment->id,
                    'deadline_at' => $data['deadline_at'],
                    'description' => $data['description'],
                    'user_division_id' => $user->user_division_id,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('trk_repairs.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_equipment(TrkEquipment $trk_equipment): Response
    {
        return \response()->view('backend.trk_repairs.create_from_equipment', [
            'trk_equipment' => $trk_equipment,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_equipment(StoreTrkRoomRepairFromTrkEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store trk room repair from trk equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'trk_equipment' => $trk_equipment,
                ]);

            try {
                $data = $request->validated();

                TrkRoomRepair::create([
                    'trk_room_id' => $trk_equipment->trk_room->id,
                    'equipment_id' => $trk_equipment->id,
                    'deadline_at' => $data['deadline_at'],
                    'description' => $data['description'],
                    'user_division_id' => Auth::user()->user_division_id,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('trk_repairs.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
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
        $building_ids = TrkRoom::where('trk_id', $tech_act->trk_id)->groupBy('building_id')->pluck('building_id')->toArray();
        $floor_ids = TrkRoom::where('trk_id', $tech_act->trk_id)->groupBy('floor_id')->pluck('floor_id')->toArray();
        $room_ids = TrkRoom::where('trk_id', $tech_act->trk_id)->groupBy('room_id')->pluck('room_id')->toArray();
        $trk_room_ids = TrkRoom::where('trk_id', $tech_act->trk_id)->pluck('id')->toArray();

        $buildings = Building::whereIn('id', $building_ids)->orderBy('name')->get();
        $floors = Floor::whereIn('id', $floor_ids)->orderBy('name')->get();
        $rooms = Room::whereIn('id', $room_ids)->orderBy('name')->get();

        $equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)->pluck('equipment_name_id')->toArray();
        $equipment_names = EquipmentName::whereIn('id', $equipment_ids)->orderBy('name')->get();

        return \response()->view('backend.trk_repairs.create_from_tech_act', [
            'tech_act' => $tech_act,
            'systems' => System::orderBy('name')->get(),
            'buildings' => $buildings,
            'floors' => $floors,
            'rooms' => $rooms,
            'equipment_names' => $equipment_names,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_tech_act(StoreTrkRoomRepairFromTechActFormRequest $request, TechAct $tech_act): RedirectResponse
    {
        if ($request->isMethod('post')) {

            Log::info('User try to store trk room repair from tech act',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'tech_act' => $tech_act,
                ]);

            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $tech_act->trk_id)
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if(empty($trk_room->id))
                {
                    $trk = Trk::where('id', $tech_act->trk_id)->first();
                    $building = Building::where('id', $data['building_id'])->first();
                    $floor = Floor::where('id', $data['floor_id'])->first();
                    $room = Room::where('id', $data['room_id'])->first();

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('system_id', $data['system_id'])
                    ->where('equipment_name_id', $data['equipment_id'])
                    ->first();

                if(empty($trk_equipment->id))
                {
                    $trk = Trk::where('id', $tech_act->trk_id)->first();
                    $building = Building::where('id', $data['building_id'])->first();
                    $floor = Floor::where('id', $data['floor_id'])->first();
                    $room = Room::where('id', $data['room_id'])->first();
                    $system = System::where('id', $data['system_id'])->first();
                    $equipment_name = EquipmentName::where('id', $data['equipment_id'])->first();

                    return redirect()->back()->with('error', 'Нет такого оборудования: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . ' - ' . $system->name . ', ' . $equipment_name->name)->withInput();
                }

                $trk_repair = TrkRoomRepair::create([
                    'trk_room_id' => $trk_equipment->trk_room->id,
                    'equipment_id' => $trk_equipment->id,
                    'deadline_at' => $data['deadline_at'],
                    'description' => $data['description'],
                    'user_division_id' => Auth::user()->user_division_id,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DocCommunication::create([
                    'from_id' => $tech_act->id,
                    'from_type' => get_class($tech_act),
                    'to_id' => $trk_repair->id,
                    'to_type' => get_class($trk_repair),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('trk_repairs.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_operation_application(OperationApplication $operation_application): Response
    {

        $this->authorize('create', new TrkRoomRepair());

        $trk_rooms = TrkRoom::where('trk_id', $operation_application->trk->id)->pluck('id')->toArray();
        $trk_equipments = TrkEquipment::whereIn('trk_room_id', $trk_rooms)->pluck('equipment_name_id')->toArray();
        $equipments = EquipmentName::whereIn('id', $trk_equipments)->orderBy('name')->get();

        $trk_buildings = TrkRoom::where('trk_id', $operation_application->trk->id)->pluck('building_id')->toArray();
        $buildings = Building::whereIn('id', $trk_buildings)->orderBy('name')->get();

        $trk_floors = TrkRoom::where('trk_id', $operation_application->trk->id)->pluck('floor_id')->toArray();
        $floors = Floor::whereIn('id', $trk_floors)->orderBy('name')->get();

        $trk_rooms = TrkRoom::where('trk_id', $operation_application->trk->id)->pluck('room_id')->toArray();
        $rooms = Room::whereIn('id', $trk_rooms)->orderBy('name')->get();

        return \response()->view('backend.trk_repairs.create_from_operation_application',
            [
            'trk' => Trk::find($operation_application->trk->id),
            'all_buildings' => $buildings,
            'all_rooms' => $rooms,
            'all_floors' => $floors,
            'all_equipments' => $equipments,
            'operation_application' => $operation_application,
                'all_systems' => System::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_operation_application(StoreTrkRoomRepairFromOperationApplicationFormRequest $request, OperationApplication $operation_application): RedirectResponse
    {
        $this->authorize('create', new TrkRoomRepair());

        if ($request->isMethod('post')) {
            Log::info('User try to store trk room repair from operation application',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {
                $data = $request->validated();

                $trk_room_id = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->pluck('id')
                    ->first();

                if(empty($trk_room_id))
                {
                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $equipment_name = EquipmentName::where('name', $data['equipment_id'])->first();

                if(empty($equipment_name->id))
                {
                    return redirect()->back()->with('error', 'Нет такого названия для оборудования: ' . $equipment_name->name)->withInput();
                }

                $trk_equipment_id = TrkEquipment::where('trk_room_id', $trk_room_id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->where('system_id', $data['system_id'])
                    ->pluck('id')
                    ->first();

                if(empty($trk_equipment_id))
                {
                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);
                    $system = System::find($data['system_id']);

                    return redirect()->back()->with('error', 'Нет такого оборудования: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . ', ' .$system->name . ', ' . $equipment_name->name)->withInput();
                }

                DB::beginTransaction();

                $repair = TrkRoomRepair::create([
                    'trk_room_id' => $trk_room_id,
                    'equipment_id' => $trk_equipment_id,
                    'deadline_at' => $data['deadline_at'],
                    'description' => $data['description'],
                    'operation_app_id' => $operation_application->id,
                    'user_division_id' => auth()->user()->user_division_id,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                switch ($data['operation_type']) {

                    case "save_and_create_spare_part_order":
                        return redirect()->route('orders.create_from_repair', $repair)->with('success', 'Данные сохранены.');

                    default:
                        return redirect()->route('trk_repairs.index')->with('success', 'Данные сохранены');
                }

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
    public function show(TrkRoomRepair $trk_repair): Response
    {
        return \response()->view('backend.trk_repairs.show', [
            'trk_repair' => $trk_repair,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrkRoomRepair $trk_repair): Response
    {
        $trk_room_ids = TrkRoom::where('trk_id', $trk_repair->trk_room->trk->id)->pluck('room_id')->toArray();
        $rooms = Room::whereIn('id', $trk_room_ids)->orderBy('name')->get();

        $trk_rooms = TrkRoom::where('trk_id', $trk_repair->trk_room->trk->id)->pluck('id')->toArray();
        $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_rooms)->pluck('equipment_name_id')->toArray();
        $equipments = EquipmentName::whereIn('id', $trk_equipment_ids)->orderBy('name')->get();

        $trk_building_ids = TrkRoom::where('trk_id', $trk_repair->trk_room->trk->id)->pluck('building_id')->toArray();
        $buildings = Building::whereIn('id', $trk_building_ids)->orderBy('name')->get();

        $trk_floor_ids = TrkRoom::where('trk_id', $trk_repair->trk_room->trk->id)->pluck('floor_id')->toArray();
        $floors = Floor::whereIn('id', $trk_floor_ids)->orderBy('name')->get();

        return \response()->view('backend.trk_repairs.edit', [
            'trk_repair' => $trk_repair,
            'trks' => Trk::orderBy('sort_order')->get(),
            'buildings' => $buildings,
            'floors' => $floors,
            'rooms' => $rooms,
            'equipments' => $equipments,
            'divisions' => UserDivision::all(),
            'executor_names' => User::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function done_progress(TrkRoomRepair $trk_repair): Response
    {
        $this->authorize('done_progress', $trk_repair);

        $first_trk_room_ids = TrkRoom::where('id', $trk_repair->trk_room_id)->pluck('room_id')->toArray();
        $first_trk_rooms = Room::whereIn('id', $first_trk_room_ids)->orderBy('name')->get();

        return \response()->view('backend.trk_repairs.done_progress', [
            'trk_repair' => $trk_repair,
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => $first_trk_rooms,
            'equipments' => TrkEquipment::where('id', $trk_repair->trk_equipment->id)->get(),
            'divisions' => UserDivision::all(),
            'executor_names' => User::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function done_progress_update(UpdateTrkRoomRepairDoneProgressFormRequest $request, TrkRoomRepair $trk_repair): RedirectResponse
    {
        $this->authorize('done_progress_update', $trk_repair);

        Log::info('User try to update repair done progress',
            [
                'user' => Auth::user()->name,
                'request' => $request,
                'trk_repair' => $trk_repair,
            ]);

        if ($request->isMethod('patch')) {
            try {
                $data = $request->validated();

                $executors = User::whereIn('name', $data['executor_names'])->pluck('id')->toArray();

                if (empty($executors)) {
                    return redirect()->back()->with('error', 'Нет такого сотрудник(а/ов).');
                }

                DB::beginTransaction();

                $trk_repair->update([
                    'executed_result' => $data['executed_result'],
                    'executed_at' => $data['executed_at'],
                    'done_progress' => $data['done_progress'],
                    'last_editor_id' => Auth::id(),
                ]);

                TrkRoomRepairExecutor::where('trk_room_repair_id', $trk_repair->id)->forceDelete();

                foreach ($executors as $executor) {

                    TrkRoomRepairExecutor::create([
                        'trk_room_repair_id' => $trk_repair->id,
                        'executor_id' => $executor,
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                switch ($data['operation_type']) {
                    case "save_and_create_spare_part_order":
                        return redirect()->route('orders.create_from_repair', $trk_repair)->with('success', 'Данные сохранены.');

                    case "save_and_create_avr":
                        return redirect()->route('avrs.create_from_repair', $trk_repair)->with('success', 'Данные сохранены.');

                    default:
                        return redirect()->route('trk_repairs.show', $trk_repair)->with('success', 'Изменения сохранены');
                }


            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

            }

        }
        return redirect()->back()->with('error', 'Изменения не сохранены')->withInput();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkRoomRepairFormRequest $request, TrkRoomRepair $trk_repair): RedirectResponse
    {
        Log::info('User try to update repair',
            [
                'user' => Auth::user()->name,
                'request' => $request,
            ]);

        if ($request->isMethod('patch')) {

            try {

                $data = $request->validated();


                $executors = User::whereIn('name', $data['executor_names'])->pluck('id')->toArray();

                if (empty($executors)) {
                    return redirect()->back()->with('error', 'Нет такого сотрудник(а/ов).')->withInput();
                }

                $trk_room = TrkRoom::where('trk_id', $trk_repair->trk_room->trk->id)
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if (empty($trk_room->id)) {
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);
                    return redirect()->back()->with('error', 'Не существует такого помещения: ' . $trk_repair->trk_room->trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('equipment_name_id', $data['equipment_id'])
                    ->first();

                if (empty($trk_equipment->id)) {
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);
                    $equipment = EquipmentName::find($data['equipment_id']);
                    return redirect()->back()->with('error', 'Не существует такого оборудования: ' . $trk_repair->trk_room->trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . ', ' . $equipment->name)->withInput();
                }

                DB::beginTransaction();

                $trk_repair->update([
                    'trk_room_id' => $trk_room->id,
                    'equipment_id' => $trk_equipment->id,
                    'executed_result' => $data['executed_result'],
                    'executed_at' => $data['executed_at'],
                    'done_progress' => $data['done_progress'],
                    'last_editor_id' => Auth::id(),
                ]);

                TrkRoomRepairExecutor::where('trk_room_repair_id', $trk_repair->id)->forceDelete();

                foreach ($executors as $executor) {
                    TrkRoomRepairExecutor::create([
                        'trk_room_repair_id' => $trk_repair->id,
                        'executor_id' => $executor,
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('trk_repairs.show', $trk_repair)->with('success', 'Изменения сохранены');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

            }

        }
        return redirect()->back()->with('error', 'Изменения не сохранены')->withInput();
    }

    /**
     * Update the specified resource in storage.
     */
    public function set_repairs_from_this_to_another_equipment(SetRepairsToAnotherEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
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

                foreach ($trk_equipment->repairs as $repair) {
                    $repair->update([
                        'equipment_id' => $new_equipment->id,
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
    public function destroy(TrkRoomRepair $trk_repair): RedirectResponse
    {
        Log::info('user try to delete trk repair', [
            'user' => Auth::user()->name,
            'repair' => $trk_repair
        ]);

        try {

            DB::beginTransaction();

            DocCommunication::where('to_type', TechAct::class)
                ->where('to_id', $trk_repair->id)
                ->delete();

            $trk_repair->update([
                'destroyer_id' => Auth::id()
            ]);

            $trk_repair->delete();

            DB::commit();

            return redirect()->route('trk_repairs.index')->with('success', 'Данные удалены.');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
        }

        return redirect()->back()->with('error', 'Ошибка удаления данных, смотрите логи.');
    }
}
