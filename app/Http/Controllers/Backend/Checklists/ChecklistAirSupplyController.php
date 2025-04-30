<?php

namespace App\Http\Controllers\Backend\Checklists;

use App\Exports\Checklists\AirSupply\AirSupplyBlankExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\Checklists\AirSupply\ChecklistAirSupplyFilter;
use App\Http\Requests\Checklists\AirSupply\ChecklistAirSupplyFilterRequest;
use App\Http\Requests\Checklists\AirSupply\SetChecklistsAirSupplyToAnotherEquipmentFormRequest;
use App\Http\Requests\Checklists\AirSupply\StoreChecklistAirSupplyFormRequest;
use App\Http\Requests\Checklists\AirSupply\StoreChecklistAirSupplyFromTrkEquipmentFormRequest;
use App\Http\Requests\Checklists\AirSupply\StoreChecklistAirSupplyFromTrkRoomFormRequest;
use App\Http\Requests\Checklists\AirSupply\UpdateChecklistAirSupplyFormRequest;
use App\Jobs\Avrs\NewAvrEmailJob;
use App\Models\Avrs\Avr;
use App\Models\Avrs\AvrEquipment;
use App\Models\Avrs\AvrExecutor;
use App\Models\Avrs\AvrWork;
use App\Models\Buildings\Building;
use App\Models\Checklists\ChecklistAirSupply;
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
use Maatwebsite\Excel\Facades\Excel;

class ChecklistAirSupplyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(ChecklistAirSupply::class, 'checklists_air_supply');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ChecklistAirSupplyFilterRequest $request): Response
    {

        $data = $request->validated();

        $filter = app()->make(ChecklistAirSupplyFilter::class, ['queryParams' => array_filter($data)]);

        $checklists = ChecklistAirSupply::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.checklists_air_supply.pagination'));

        return \response()->view('backend.checklists.air_supply.index', [
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


        return \response()->view('backend.checklists.air_supply.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::where('name', System::AIR_RECYCLE)->get(),
            'equipment_names' => $equipment_names,
            'rooms' => $rooms,
            'buildings' => $buildings,
            'floors' => $floors,
            'executors' => User::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'work_types' => WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChecklistAirSupplyFormRequest $request): RedirectResponse
    {
        Log::info('user try to store new checklist air_supply', [
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

                $equipment_name = EquipmentName::where('name', $data['equipment_id'])->first();

                if (empty($equipment_name->id)) {

                    return redirect()->back()->with('error', 'Нет оборудования с таким названием: ' . $data['equipment_id'])->withInput();
                }


                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('system_id', $system->id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {

                    return redirect()->back()->with('error', 'Нет такой установки: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ' - ' . $equipment_name->name)->withInput();
                }

                DB::beginTransaction();

                $checklist = ChecklistAirSupply::create([
                    //'trk_room_id' => $trk_room->id,
                    'trk_equipment_id' => $trk_equipment->id,
                    'outside_air_t' => $data['outside_air_t'],
                    'setpoint_air_t' => $data['setpoint_air_t'],
                    'supply_air_t' => $data['supply_air_t'],
                    'supply_engine_t' => $data['supply_engine_t'],
                    'front_bearing_t' => $data['front_bearing_t'],
                    'supply_engine_terminal_contact_t' => $data['supply_engine_terminal_contact_t'],
                    'service_switch_contact_t' => $data['service_switch_contact_t'],
                    'supply_engine_actual_current' => $data['supply_engine_actual_current'],
                    'supply_engine_passport_current' => $data['supply_engine_passport_current'],
                    'supply_engine_actual_frequency' => $data['supply_engine_actual_frequency'],
                    'supply_engine_passport_frequency' => $data['supply_engine_passport_frequency'],
                    'supply_air_actual_rate' => $data['supply_air_actual_rate'],
                    'supply_air_passport_rate' => $data['supply_air_passport_rate'],
                    'hot_water_valve_open_percent' => $data['hot_water_valve_open_percent'],
                    'inlet_hot_water_t' => $data['inlet_hot_water_t'],
                    'outlet_hot_water_t' => $data['outlet_hot_water_t'],
                    'cold_water_valve_open_percent' => $data['cold_water_valve_open_percent'],
                    'inlet_cold_water_t' => $data['inlet_cold_water_t'],
                    'outlet_cold_water_t' => $data['outlet_cold_water_t'],
                    'supply_air_dumper_open_percent' => $data['supply_air_dumper_open_percent'],
                    'recycle_air_dumper_open_percent' => $data['recycle_air_dumper_open_percent'],
                    'recuperator_speed_rate_percent' => $data['recuperator_speed_rate_percent'],
                    'hot_water_pump_actual_current' => $data['hot_water_pump_actual_current'],
                    'hot_water_pump_passport_current' => $data['hot_water_pump_passport_current'],
                    'glycol_pump_actual_current' => $data['glycol_pump_actual_current'],
                    'glycol_pump_passport_current' => $data['glycol_pump_passport_current'],
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

                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('checklists_air_supply.index')->with('success', 'Данные сохранены.');

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
    public function create_from_trk_equipment(TrkEquipment $trk_equipment): Response
    {
        $this->authorize('create', ChecklistAirSupply::class);

        $work_types = WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get();

        return \response()->view('backend.checklists.air_supply.create_from_trk_equipment', [
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
            'trk_equipment' => $trk_equipment,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_equipment(StoreChecklistAirSupplyFromTrkEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        $this->authorize('create', ChecklistAirSupply::class);

        Log::info('user try to store new checklist air_supply from trk equipment', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_equipment' => $trk_equipment,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                DB::beginTransaction();

                $checklist = ChecklistAirSupply::create([
                    //'trk_room_id' => $trk_equipment->trk_room_id,
                    'trk_equipment_id' => $trk_equipment->id,
                    'outside_air_t' => $data['outside_air_t'],
                    'setpoint_air_t' => $data['setpoint_air_t'],
                    'supply_air_t' => $data['supply_air_t'],
                    'supply_engine_t' => $data['supply_engine_t'],
                    'front_bearing_t' => $data['front_bearing_t'],
                    'supply_engine_terminal_contact_t' => $data['supply_engine_terminal_contact_t'],
                    'service_switch_contact_t' => $data['service_switch_contact_t'],
                    'supply_engine_actual_current' => $data['supply_engine_actual_current'],
                    'supply_engine_passport_current' => $data['supply_engine_passport_current'],
                    'supply_engine_actual_frequency' => $data['supply_engine_actual_frequency'],
                    'supply_engine_passport_frequency' => $data['supply_engine_passport_frequency'],
                    'supply_air_actual_rate' => $data['supply_air_actual_rate'],
                    'supply_air_passport_rate' => $data['supply_air_passport_rate'],
                    'hot_water_valve_open_percent' => $data['hot_water_valve_open_percent'],
                    'inlet_hot_water_t' => $data['inlet_hot_water_t'],
                    'outlet_hot_water_t' => $data['outlet_hot_water_t'],
                    'cold_water_valve_open_percent' => $data['cold_water_valve_open_percent'],
                    'inlet_cold_water_t' => $data['inlet_cold_water_t'],
                    'outlet_cold_water_t' => $data['outlet_cold_water_t'],
                    'supply_air_dumper_open_percent' => $data['supply_air_dumper_open_percent'],
                    'recycle_air_dumper_open_percent' => $data['recycle_air_dumper_open_percent'],
                    'recuperator_speed_rate_percent' => $data['recuperator_speed_rate_percent'],
                    'hot_water_pump_actual_current' => $data['hot_water_pump_actual_current'],
                    'hot_water_pump_passport_current' => $data['hot_water_pump_passport_current'],
                    'glycol_pump_actual_current' => $data['glycol_pump_actual_current'],
                    'glycol_pump_passport_current' => $data['glycol_pump_passport_current'],
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

                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('checklists_air_supply.index')->with('success', 'Данные сохранены.');

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
        $this->authorize('create', ChecklistAirSupply::class);

        $work_types = WorkName::whereIn('name', WorkName::CHECKLIST_WORK_TYPES)->get();

        $system_condition = System::where('name', System::AIR_RECYCLE)->first();

        $trk_room_equipments = TrkEquipment::where('trk_room_id', $trk_room->id)
            ->where('system_id', $system_condition->id)
            ->get();

        return \response()->view('backend.checklists.air_supply.create_from_trk_room', [
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
    public function store_from_trk_room(StoreChecklistAirSupplyFromTrkRoomFormRequest $request, TrkRoom $trk_room): RedirectResponse
    {
        $this->authorize('create', ChecklistAirSupply::class);

        Log::info('user try to store new checklist air_supply from trk room', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'trk_room' => $trk_room,
        ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                $system_air_supply = System::where('name', System::AIR_RECYCLE)->first();

                DB::beginTransaction();

                foreach ($data['equipments'] as $equipment) {

                    $trk_equipment = TrkEquipment::find($equipment['id']);

                    $checklist = ChecklistAirSupply::create([
                        //'trk_room_id' => $trk_room->id,
                        'trk_equipment_id' => $trk_equipment->id,
                        'outside_air_t' => $equipment['outside_air_t'],
                        'setpoint_air_t' => $equipment['setpoint_air_t'],
                        'supply_air_t' => $equipment['supply_air_t'],
                        'supply_engine_t' => $equipment['supply_engine_t'],
                        'front_bearing_t' => $equipment['front_bearing_t'],
                        'supply_engine_terminal_contact_t' => $equipment['supply_engine_terminal_contact_t'],
                        'service_switch_contact_t' => $equipment['service_switch_contact_t'],
                        'supply_engine_actual_current' => $equipment['supply_engine_actual_current'],
                        'supply_engine_passport_current' => $equipment['supply_engine_passport_current'],
                        'supply_engine_actual_frequency' => $equipment['supply_engine_actual_frequency'],
                        'supply_engine_passport_frequency' => $equipment['supply_engine_passport_frequency'],
                        'supply_air_actual_rate' => $equipment['supply_air_actual_rate'],
                        'supply_air_passport_rate' => $equipment['supply_air_passport_rate'],
                        'hot_water_valve_open_percent' => $equipment['hot_water_valve_open_percent'],
                        'inlet_hot_water_t' => $equipment['inlet_hot_water_t'],
                        'outlet_hot_water_t' => $equipment['outlet_hot_water_t'],
                        'cold_water_valve_open_percent' => $equipment['cold_water_valve_open_percent'],
                        'inlet_cold_water_t' => $equipment['inlet_cold_water_t'],
                        'outlet_cold_water_t' => $equipment['outlet_cold_water_t'],
                        'supply_air_dumper_open_percent' => $equipment['supply_air_dumper_open_percent'],
                        'recycle_air_dumper_open_percent' => $equipment['recycle_air_dumper_open_percent'],
                        'recuperator_speed_rate_percent' => $equipment['recuperator_speed_rate_percent'],
                        'hot_water_pump_actual_current' => $equipment['hot_water_pump_actual_current'],
                        'hot_water_pump_passport_current' => $equipment['hot_water_pump_passport_current'],
                        'glycol_pump_actual_current' => $equipment['glycol_pump_actual_current'],
                        'glycol_pump_passport_current' => $equipment['glycol_pump_passport_current'],
                        'comment' => $equipment['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    $avr = Avr::create([
                        'trk_room_id' => $trk_equipment->trk_room_id,
                        'system_id' => $system_air_supply->id,
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


                DB::commit();

                if(!empty($avr->id))
                {
                    $emails = User::role('sadmin')->pluck('email')->toArray();

                    NewAvrEmailJob::dispatch($emails, $avr);
                }

                return redirect()->route('checklists_air_supply.index')->with('success', 'Данные сохранены.');

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
    public function show(ChecklistAirSupply $checklists_air_supply): Response
    {
        return \response()->view('backend.checklists.air_supply.show', [
            'checklist' => $checklists_air_supply,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChecklistAirSupply $checklists_air_supply): Response
    {
        $work_types = WorkName::whereIn('name', ['ТО 4', 'ТО 5', 'ТО 6'])->get();
        $system_air_supply = System::where('name', System::AIR_RECYCLE)->first();

        $trk_building_ids = TrkRoom::where('trk_id', $checklists_air_supply->trk_equipment->trk_room->trk->id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $buildings = Building::whereIn('id', $trk_building_ids)->orderBy('name')->get();

        $trk_room_ids = TrkRoom::where('trk_id', $checklists_air_supply->trk_equipment->trk_room->trk->id)
            ->pluck('room_id')
            ->toArray();

        $rooms = Room::whereIn('id', $trk_room_ids)->orderBy('name')->get();

        $trk_floor_ids = TrkRoom::where('trk_id', $checklists_air_supply->trk_equipment->trk_room->trk->id)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $floors = Floor::whereIn('id', $trk_floor_ids)->orderBy('name')->get();

        $trk_room_ids = TrkRoom::where('trk_id', $checklists_air_supply->trk_equipment->trk_room->trk->id)
            ->pluck('id')
            ->toArray();

        $equipment_names_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
            ->where('system_id', $system_air_supply->id)
            ->pluck('equipment_name_id')
            ->toArray();

        $equipment_names = EquipmentName::whereIn('id', $equipment_names_ids)->orderBy('name')->get();

        return \response()->view('backend.checklists.air_supply.edit', [
            'checklist' => $checklists_air_supply,
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::orderBy('name')->get(),
            'rooms' => $rooms,
            'buildings' => $buildings,
            'floors' => $floors,
            'equipment_names' => $equipment_names,
            'works' => WorkName::orderBy('name')->get(),
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'executors' => User::orderBy('name')->get(),
            'work_types' => $work_types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChecklistAirSupplyFormRequest $request, ChecklistAirSupply $checklists_air_supply): RedirectResponse
    {

        Log::info('user try to update checklist air_supply', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'checklist air_supply' => $checklists_air_supply
        ]);

        if ($request->isMethod('patch')) {
            try {

                $data = $request->validated();

                $system_air_supply = System::where('name', System::AIR_RECYCLE)->first();

                $trk_room = TrkRoom::where('trk_id', $checklists_air_supply->trk_equipment->trk_room->trk->id)
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if (empty($trk_room->id)) {

                    $trk = Trk::find($checklists_air_supply->trk_equipment->trk_room->trk->id);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $equipment_name = EquipmentName::where('name', $data['equipment_id'])->first();

                if (empty($equipment_name->id)) {

                    return redirect()->back()->with('error', 'Нет притока с таким названием: ' . $data['equipment_id'])->withInput();
                }

                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('system_id', $system_air_supply->id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {

                    return redirect()->back()->with('error', 'Нет такой приточной установки: ' . $trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->floor->name . ' - ' . $equipment_name->name)->withInput();
                }

                DB::beginTransaction();

                $avr = $checklists_air_supply->avr->first();

                foreach($avr->avr_equipments as $avr_equipment)
                {
                    if(
                        $avr_equipment->trk_equipment_id == $checklists_air_supply->trk_equipment_id
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
                    ->where('trk_equipment_id', $checklists_air_supply->trk_equipment_id)
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
                        $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
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

                $checklists_air_supply->update([
                    'trk_equipment_id' => $trk_equipment->id,
                    'outside_air_t' => $data['outside_air_t'],
                    'setpoint_air_t' => $data['setpoint_air_t'],
                    'supply_air_t' => $data['supply_air_t'],
                    'supply_engine_t' => $data['supply_engine_t'],
                    'front_bearing_t' => $data['front_bearing_t'],
                    'supply_engine_terminal_contact_t' => $data['supply_engine_terminal_contact_t'],
                    'service_switch_contact_t' => $data['service_switch_contact_t'],
                    'supply_engine_actual_current' => $data['supply_engine_actual_current'],
                    'supply_engine_passport_current' => $data['supply_engine_passport_current'],
                    'supply_engine_actual_frequency' => $data['supply_engine_actual_frequency'],
                    'supply_engine_passport_frequency' => $data['supply_engine_passport_frequency'],
                    'supply_air_actual_rate' => $data['supply_air_actual_rate'],
                    'supply_air_passport_rate' => $data['supply_air_passport_rate'],
                    'hot_water_valve_open_percent' => $data['hot_water_valve_open_percent'],
                    'inlet_hot_water_t' => $data['inlet_hot_water_t'],
                    'outlet_hot_water_t' => $data['outlet_hot_water_t'],
                    'cold_water_valve_open_percent' => $data['cold_water_valve_open_percent'],
                    'inlet_cold_water_t' => $data['inlet_cold_water_t'],
                    'outlet_cold_water_t' => $data['outlet_cold_water_t'],
                    'supply_air_dumper_open_percent' => $data['supply_air_dumper_open_percent'],
                    'recycle_air_dumper_open_percent' => $data['recycle_air_dumper_open_percent'],
                    'recuperator_speed_rate_percent' => $data['recuperator_speed_rate_percent'],
                    'hot_water_pump_actual_current' => $data['hot_water_pump_actual_current'],
                    'hot_water_pump_passport_current' => $data['hot_water_pump_passport_current'],
                    'glycol_pump_actual_current' => $data['glycol_pump_actual_current'],
                    'glycol_pump_passport_current' => $data['glycol_pump_passport_current'],
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('checklists_air_supply.index')->with('success', 'Данные сохранены.');

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
    public function set_checklists_air_supply_from_this_to_another_equipment(SetChecklistsAirSupplyToAnotherEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
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
                ->where('equipment_name_id', $data['equipment_name_id'])
                ->first();

            if (empty($new_equipment->id)) {
                return redirect()->back()->with('error', 'Такого оборудования не существует');
            }

            try {

                DB::beginTransaction();

                foreach ($trk_equipment->checklists_air_supply as $checklist) {
                    $checklist->update([
                        'trk_equipment_id' => $new_equipment->id,
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
    public function destroy(ChecklistAirSupply $checklists_air_supply): RedirectResponse
    {
        Log::info('user try to delete ChecklistAirSupply', [
            'user' => Auth::user()->name,
            'checklists_air_supply' => $checklists_air_supply
        ]);

        try {

            DB::beginTransaction();

            $avr_id = $checklists_air_supply->avr->first()->id;

            DocCommunication::where('from_id', $checklists_air_supply->id)
                ->where('from_type', ChecklistAirSupply::class)
                ->where('to_id', $avr_id)
                ->where('to_type', Avr::class)
                ->delete();

            $checklists_air_supply->update([
                'destroyer_id' => Auth::id(),
            ]);

            $checklists_air_supply->delete();

            DB::commit();

            return redirect()->route('checklists_air_supply.index')->with('success', 'Данные удалены.');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
        }

        return redirect()->back()->with('error', 'Ошибка удаления данных, смотрите логи.');

    }

    public function export_blank()
    {
        return (new AirSupplyBlankExportPdf())->export_pdf();
    }
}
