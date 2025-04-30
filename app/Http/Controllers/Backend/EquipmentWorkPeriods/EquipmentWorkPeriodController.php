<?php

namespace App\Http\Controllers\Backend\EquipmentWorkPeriods;

use App\Exports\EquipmentWorkPeriods\EquipmentWorkPeriodExport;
use App\Exports\EquipmentWorkPeriods\EquipmentWorkPeriodExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentWorkPeriods\EquipmentWorkPeriodFilter;
use App\Http\Filters\TrkEquipments\TrkEquipmentFilter;
use App\Http\Requests\EquipmentWorkPeriods\EquipmentWorkPeriodFilterRequest;
use App\Http\Requests\EquipmentWorkPeriods\StoreEquipmentWorkPeriodFormRequest;
use App\Http\Requests\EquipmentWorkPeriods\StoreEquipmentWorkPeriodFromEquipmentFormRequest;
use App\Http\Requests\EquipmentWorkPeriods\UpdateEquipmentWorkPeriodFormRequest;
use App\Http\Requests\EquipmentWorkPeriods\UpdateWorkInEquipmentWorkPeriodFromWorkNameFormRequest;
use App\Http\Requests\Exports\ExportEquipmentWorkPeriodFormRequest;
use App\Models\Avrs\Avr;
use App\Models\Avrs\AvrWork;
use App\Models\Buildings\Building;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentWorkPeriods\EquipmentWorkPeriod;
use App\Models\Floors\Floor;
use App\Models\Rooms\Room;
use App\Models\Systems\System;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use App\Models\WorkNames\WorkName;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentWorkPeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EquipmentWorkPeriodFilterRequest $request): Response
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

        $filter = app()->make(EquipmentWorkPeriodFilter::class, ['queryParams' => array_filter($data)]);

        $equipment_work_periods = EquipmentWorkPeriod::filter($filter)
            ->select(['equipment_work_periods.*', 'trks.sort_order', 'equipment_names.name'])
            ->join('trk_equipments', 'trk_equipments.id', '=', 'equipment_work_periods.equipment_id')
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_equipments.trk_room_id')
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->orderBy('trks.sort_order')
            ->orderBy('equipment_names.name')
            ->orderBy('next_to_be_at', 'asc')
            ->paginate(config('backend.equipment_work_periods.pagination'));

        return \response()->view('backend.equipment_work_periods.index', [
            'equipment_work_periods' => $equipment_work_periods,
            'old_filters' => $data,
            'trks' => $all_trks,
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'works' => WorkName::orderBy('name')->get(),
            'systems' => $all_systems,
            'room_names' => Room::orderBy('name')->get(),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.equipment_work_periods.create', [
            'work_names' => WorkName::orderBy('name')->get(),
            'trks' => Trk::orderBy('sort_order')->get(),
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'systems' => System::orderBy('name')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipmentWorkPeriodFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store new work periods',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if (empty($trk_room)) {

                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Отсутствует помещение: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . '. Посмотрите в ТРК/Помещение')->withInput();
                }


                $equipment_name_id = EquipmentName::where('name', $data['equipment_name'])->pluck('id')->first();
                $system = System::where('id', $data['system_id'])->first();

                $equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('equipment_name_id', $equipment_name_id)
                    ->where('system_id', $system->id)
                    ->first();

                if (empty($equipment->id)) {

                    return redirect()->back()->with('error', 'Отсутствует оборудование: ' . $system->name . ', ' . $data['equipment_name'] . ' на ' . $trk_room->trk->name . ' в ' . $trk_room->room->name . ' Посмотрите в ТРК/Оборудование')->withInput();
                }

                foreach ($data['works'] as $work) {

                    $work_name = WorkName::where('name', $work['work_name'])->first();

                    if (empty($work_name->id)) {

                        return redirect()->back()->with('error', 'Отсутствует тех. мероприятие: ' . $work['work_name'])->withInput();
                    }


                    if(
                        !EquipmentWorkPeriod::where('equipment_id', $equipment->id)
                            ->where('work_name_id', $work_name->id)
                            ->exists()
                    )
                    {
                        $avr_work_ids = AvrWork::where('trk_equipment_id', $equipment->id)
                            ->where('work_name_id', $work_name->id)
                            ->pluck('avr_id')
                            ->toArray();

                        $last_work = Avr::whereIn('id', $avr_work_ids)
                            ->orderBy('date', 'desc')
                            ->first();

                        if(!empty($last_work))
                        {

                            $next_to_be_at = Carbon::createFromFormat('Y-m-d', $last_work->date);
                            $next_to_be_at = $next_to_be_at->addDays($work['value']);

                            EquipmentWorkPeriod::create([
                                'equipment_id' => $equipment->id,
                                'work_name_id' => $work_name->id,
                                'repeat_days' => $work['value'],
                                'last_was_at' => $last_work->date,
                                'next_to_be_at' => $next_to_be_at,
                                'comment' => $work['comment'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        } else {

                            EquipmentWorkPeriod::create([
                                'equipment_id' => $equipment->id,
                                'work_name_id' => $work_name->id,
                                'repeat_days' => $work['value'],
                                'last_was_at' => $last_work->date ?? null,
                                'next_to_be_at' => $next_to_be_at ?? null,
                                'comment' => $work['comment'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                    }

                }

                return redirect()->route('trk_equipments.show', $equipment->id)->with('success', 'Данные сохранены');

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

        return \response()->view('backend.equipment_work_periods.create_from_equipment', [
            'trk_equipment' => $trk_equipment,
            'work_names' => WorkName::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_equipment(StoreEquipmentWorkPeriodFromEquipmentFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store work period from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                EquipmentWorkPeriod::where('equipment_id', $data['equipment_id'])->forceDelete();

                foreach ($data['works'] as $work) {
                    $work_name = WorkName::where('name', $work['work_name'])->first();

                    $avr_work_ids = AvrWork::where('trk_equipment_id', $data['equipment_id'])
                        ->where('work_name_id', $work_name->id)
                        ->pluck('avr_id')
                        ->toArray();

                    $last_work = Avr::whereIn('id', $avr_work_ids)
                        ->orderBy('date', 'desc')
                        ->first();

                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $last_work->date);
                    $next_to_be_at = $next_to_be_at->addDays($work['value']);

                    EquipmentWorkPeriod::create([
                        'equipment_id' => $data['equipment_id'],
                        'work_name_id' => $work_name->id,
                        'repeat_days' => $work['value'],
                        'last_was_at' => $last_work->date,
                        'next_to_be_at' => $next_to_be_at,
                        'comment' => $work['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                return redirect()->route('trk_equipments.show', $data['equipment_id'])->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipmentWorkPeriod $equipment_work_period): Response
    {
        $avr_works = AvrWork::where('trk_equipment_id', $equipment_work_period->equipment_id)
            ->where('work_name_id', $equipment_work_period->work_name_id)
            ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
            ->orderBy('avrs.date')
            ->select('avr_works.*', 'avrs.date as avr_date')
            ->get();
        //dd(count($avr_works));
        $period_days = null;

        if (!empty($avr_works->first()->avr_date)) {

            $start_date = new DateTime($avr_works->first()->avr_date);
            $finish_date = new DateTime($avr_works->last()->avr_date);

            $interval = $start_date->diff($finish_date);
            $days = $interval->format('%a');
            $period_days = (int)round($days / count($avr_works));
        }

        return \response()->view('backend.equipment_work_periods.show', [
            'equipment_work_period' => $equipment_work_period,
            'avr_works' => $avr_works ?? null,
            'period_days' => $period_days ?? null,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentWorkPeriod $equipment_work_period): Response
    {
        $last_avr = AvrWork::where('trk_equipment_id', $equipment_work_period->equipment_id)
            ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
            ->where('work_name_id', $equipment_work_period->work_name_id)
            ->orderBy('avrs.date','DESC')
            ->select('avrs.date as last_avr_date', 'avr_works.*')
            ->first();

        return \response()->view('backend.equipment_work_periods.edit', [
            'equipment_work_period' => $equipment_work_period,
            'work_names' => WorkName::orderBy('name')->get(),
            'last_avr' => $last_avr,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentWorkPeriodFormRequest $request, EquipmentWorkPeriod $equipment_work_period): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update equipment_work_period from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'equipment_work_period' => $equipment_work_period,
                ]);

            try {

                $data = $request->validated();

                $work_name = WorkName::where('name', $data['work_name'])->first();

                if (empty($work_name->id)) {
                    return redirect()->back()->with('error', 'Нет такого названия работы, создайте его.');
                }

                $next_to_be_at = Carbon::createFromFormat('Y-m-d', $data['last_was_at']);
                $next_to_be_at = $next_to_be_at->addDays($data['value']);

                $equipment_work_period->update([
                    'equipment_id' => $data['equipment_id'],
                    'work_name_id' => $work_name->id,
                    'repeat_days' => $data['value'],
                    'last_was_at' => $data['last_was_at'],
                    'next_to_be_at' => $next_to_be_at,
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);


                return redirect()->route('trk_equipments.show', $data['equipment_id'])->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipmentWorkPeriod $equipment_work_period): RedirectResponse
    {
        Log::info('User try to delete equipment_work_period',
            [
                'user' => Auth::user()->name,
                'equipment_work_period' => $equipment_work_period,
            ]);

        try {

            $trk_equipment = TrkEquipment::find($equipment_work_period->equipment_id);

            $equipment_work_period->update([
                'destroyer_id' => Auth::id(),
            ]);

            $equipment_work_period->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }

        return redirect()->route('trk_equipments.show', $trk_equipment)->with('success', 'Данные удалены');

    }

    public function change_work_name_in_equipment_work_periods(UpdateWorkInEquipmentWorkPeriodFromWorkNameFormRequest $request, WorkName $work_name): RedirectResponse
    {
        //$this->authorize('change_work_name_in_avrs');

        Log::info('user try to change work name in equipment work periods from work name show page', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'work_name' => $work_name,
        ]);

        if ($request->isMethod('patch')) {

            try {

                $data = $request->validated();

                DB::beginTransaction();

                $equipment_period_works = EquipmentWorkPeriod::where('work_name_id', $work_name->id)->get();

                foreach ($equipment_period_works as $equipment_period_work) {

                    foreach ($data['works'] as $key => $value) {

                        $last_avr_work = AvrWork::where('trk_equipment_id', $equipment_period_work->equipment_id)
                            ->where('work_name_id', $key)
                            ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
                            ->select('avrs.date', 'avr_works.*')
                            ->orderBy('avrs.date', 'desc')
                            ->first();

                        if (!empty($last_avr_work->id)) {

                            $next_to_be_at = Carbon::createFromFormat('Y-m-d', $last_avr_work->date);
                            $next_to_be_at = $next_to_be_at->addDays($value['period_days']);

                            $new_period_work = EquipmentWorkPeriod::where('work_name_id', $key)
                                ->where('equipment_id', $equipment_period_work->equipment_id)
                                ->first();

                            if (empty($new_period_work->id)) {

                                $new_period_work = EquipmentWorkPeriod::create([
                                    'work_name_id' => $key,
                                    'equipment_id' => $equipment_period_work->equipment_id,
                                    'repeat_days' => $value['period_days'],
                                    'last_was_at' => $last_avr_work->date,
                                    'next_to_be_at' => $next_to_be_at,
                                    'last_editor_id' => Auth::id(),
                                    'author_id' => Auth::id(),
                                ]);

                            } else {

                                $new_period_work->update([
                                    'last_was_at' => $last_avr_work->date,
                                    'next_to_be_at' => $next_to_be_at,
                                    'last_editor_id' => Auth::id(),
                                ]);
                            }
                        }
                    }
                    $equipment_period_work->delete();
                }

                DB::commit();

                return redirect()->route('work_names.show', $work_name->id)->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }
        }

        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    public function export(ExportEquipmentWorkPeriodFormRequest $request)
    {
        $data = $request->validated();

        $trk = Trk::find($data['trk_id']);

        $filter = app()->make(TrkEquipmentFilter::class, ['queryParams' => array_filter($data)]);

        $trk_equipment_ids = TrkEquipment::filter($filter)
            ->pluck('id')
            ->toArray();

        $trk_equipment_ids = implode("', '", $trk_equipment_ids);

        $equipment_work_periods = EquipmentWorkPeriod::whereRaw("equipment_id in ('$trk_equipment_ids')")
            ->whereIn('work_name_id', $data['work_name_ids'])
            ->whereBetween($data['work_type'], [$data['start_date'], $data['finish_date']])
            //->orderBy('last_was_at')
            ->get();

        if (count($equipment_work_periods) == 0) {
            return redirect()->back()->with('error', 'Нет таких тех.мероприятий в базе. Создайте их сначала.');
        }

        switch ($data['file_type']) {
            case '.pdf':

                return (new EquipmentWorkPeriodExportPdf(
                    $data['trk_id'],
                    $data['system_id'],
                    $data['start_date'],
                    $data['finish_date'],
                    $equipment_work_periods
                ))->export_pdf();

            case '.html':
                return Excel::download(new EquipmentWorkPeriodExport(
                    $data['trk_id'],
                    $data['system_id'],
                    $data['start_date'],
                    $data['finish_date'],
                    $equipment_work_periods,
                ), $trk->name . ', ' . 'тех. мероприятия__' . $data['start_date'] . '__' . $data['finish_date'] . '.html');

            default:
                return Excel::download(new EquipmentWorkPeriodExport(
                    $data['trk_id'],
                    $data['system_id'],
                    $data['start_date'],
                    $data['finish_date'],
                    $equipment_work_periods,
                ), $trk->name . ', ' . 'тех. мероприятия__' . $data['start_date'] . '__' . $data['finish_date'] . '.xlsx');

        }

    }
}
