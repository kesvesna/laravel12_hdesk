<?php

namespace App\Http\Controllers\Backend\Counters;

use App\Exports\CounterCounts\CounterCountExport;
use App\Exports\CounterCounts\CounterCountExportPdf;
use App\Exports\CounterCounts\OneCounterCountExport;
use App\Exports\CounterCounts\OneCounterCountExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\Counters\TrkRoomCounterFilter;
use App\Http\Requests\Counters\StoreTrkRoomCounterFormRequest;
use App\Http\Requests\Counters\StoreTrkRoomCounterFromTrkRoomFormRequest;
use App\Http\Requests\Counters\TrkRoomCounterFilterRequest;
use App\Http\Requests\Counters\UpdateTrkRoomCounterFormRequest;
use App\Http\Requests\Exports\ExportCounterCountFormRequest;
use App\Models\Brands\Brand;
use App\Models\Counters\Counter;
use App\Models\Counters\CounterCount;
use App\Models\Counters\CounterType;
use App\Models\Counters\Tariff;
use App\Models\Counters\TrkRoomCounter;
use App\Models\Floors\Floor;
use App\Models\Organizations\Organization;
use App\Models\Rooms\Room;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TrkRoomCounterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TrkRoomCounter::class, 'trk_room_counter');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TrkRoomCounterFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(TrkRoomCounterFilter::class, ['queryParams' => array_filter($data)]);

        $trk_ids = UserResponsibilityTrkSystem::where('user_id', Auth::id())
            ->groupBy('trk_id')
            ->pluck('trk_id')
            ->toArray();

        $all_trks = null;
        $trk_room_counters = null;

        if(count($trk_ids) == 0)
        {

            $trk_room_counters = TrkRoomCounter::filter($filter)
                ->select(['trk_room_counters.*', 'floors.name', 'trks.sort_order', 'brands.name'])
                ->join('floors', 'floors.id', '=', 'trk_room_counters.floor_id')
                ->join('trks', 'trks.id', '=', 'trk_room_counters.trk_id')
                ->join('brands', 'brands.id', '=', 'trk_room_counters.brand_id')
                ->orderBy('trks.sort_order', 'asc')
                ->orderBy('floors.name', 'asc')
                ->orderBy('brands.name', 'asc')
                ->paginate(config('backend.trk_room_counters.pagination'));

            $all_trks = Trk::orderBy('sort_order')->get();

        } else {

            $trk_room_counters = TrkRoomCounter::filter($filter)
                ->select(['trk_room_counters.*', 'floors.name', 'trks.sort_order', 'brands.name'])
                ->whereIn('trk_id', $trk_ids)
                ->join('floors', 'floors.id', '=', 'trk_room_counters.floor_id')
                ->join('trks', 'trks.id', '=', 'trk_room_counters.trk_id')
                ->join('brands', 'brands.id', '=', 'trk_room_counters.brand_id')
                ->orderBy('trks.sort_order', 'asc')
                ->orderBy('floors.name', 'asc')
                ->orderBy('brands.name', 'asc')
                ->paginate(config('backend.trk_room_counters.pagination'));

            $all_trks = Trk::whereIn('id', $trk_ids)->orderBy('sort_order')->get();
        }

        return \response()->view('backend.trk_room_counters.index', [
            'trk_room_counters' => $trk_room_counters,
            'all_trks' => $all_trks,
            'all_floors' => Floor::orderBy('name')->get(),
            'all_brands' => Brand::orderBy('name')->get(),
            'all_numbers' => $trk_room_counters,
            'all_counter_types' => CounterType::all(),
            'old_filters' => $data,
            'user_trk_ids' => $trk_ids,
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

        $user_trks = UserResponsibilityTrkSystem::where('user_id', Auth::id())->groupBy('trk_id')->pluck('trk_id')->toArray();

        $show_user_trk = false;
        $user_trk = null;

        if(count($user_trks) ==1)
        {
            $show_user_trk = true;
            $user_trk = $user_trks[0];
        }

        return \response()->view('backend.trk_room_counters.create', [
            'all_trks' => Trk::orderBy('sort_order')->get(),
            'all_rooms' => $first_trk_rooms,
            'all_floors' => Floor::orderBy('name')->get(),
            'all_brands' => Brand::orderBy('name')->get(),
            'all_organizations' => Organization::orderBy('name')->get(),
            'all_counters' => Counter::orderBy('number')->get(),
            'all_counter_types' => CounterType::all(),
            'all_counter_tariffs' => Tariff::orderBy('name')->get(),
            'show_user_trk' => $show_user_trk,
            'user_trk' => $user_trk,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkRoomCounterFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {

            Log::info('User try to store trk room counter',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);
            //tODO проверку что такой этаж есть на этом ТРК
            try {

                $data = $request->validated();

                DB::beginTransaction();

                $trk_room_counter = TrkRoomCounter::onlyTrashed()
                    ->where('trk_id', $data['trk_id'])
                    ->where('brand_id', $data['brand_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('organization_id', $data['organization_id'])
                    ->where('counter_type_id', $data['counter_type_id'])
                    ->where('tariff_name_id', $data['tariff_name_id'])
                    ->where('number', $data['number'])
                    ->first();

                if(!empty($trk_room_counter->id))
                {
                    return redirect()->back()->with('error', 'Такой счетчик уже есть в удаленных, попросите админа его восстановить')->withInput();
                }

                $trk_room_counter = TrkRoomCounter::create([
                    'trk_id' => $data['trk_id'],
                    'brand_id' => $data['brand_id'],
                    'floor_id' => $data['floor_id'],
                    'organization_id' => $data['organization_id'],
                    'counter_type_id' => $data['counter_type_id'],
                    'tariff_name_id' => $data['tariff_name_id'],
                    'number' => $data['number'],
                    'using_purpose' => $data['using_purpose'],
                    'mounted_at' => $data['mounted_at'],
                    'coefficient' => $data['coefficient'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                CounterCount::create([
                    'trk_room_counter_id' => $trk_room_counter->id,
                    'tariff' => Tariff::DAY,
                    'date' => date('Y-m-d'),
                    'count' => $data['last_count_day'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                if(
                    $trk_room_counter->counter_tariff->name == TrkRoomCounter::TARIFF_DAY_NIGHT
                    && isset($data['last_count_night'])
                    && !empty($data['last_count_night'])
                )
                {
                    CounterCount::create([
                        'trk_room_counter_id' => $trk_room_counter->id,
                        'tariff' => Tariff::NIGHT,
                        'date' => date('Y-m-d'),
                        'count' => $data['last_count_night'],
                        'comment' => $data['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('trk_room_counters.index')->with('success', 'Данные сохранены');

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
        $this->authorize('create');

        return \response()->view('backend.trk_room_counters.create_from_trk_room', [
            'trk_room' => $trk_room,
            'all_floors' => Floor::orderBy('name')->get(),
            'all_counter_types' => CounterType::all(),
            'all_counter_tariffs' => Tariff::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreTrkRoomCounterFromTrkRoomFormRequest $request): RedirectResponse
    {
        $this->authorize('create');

        if ($request->isMethod('post')) {
            Log::info('User try to store trk room counter from trk room',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $trk_room = TrkRoom::find($data['trk_room_id']);

                $trk_room_counter = TrkRoomCounter::onlyTrashed()
                    ->where('trk_id', $trk_room->trk_id)
                    ->where('brand_id', $trk_room->renter->brand->id)
                    ->where('floor_id', $trk_room->floor_id)
                    ->where('organization_id', $trk_room->renter->organization->id)
                    ->where('counter_type_id', $data['counter_type_id'])
                    ->where('tariff_name_id', $data['tariff_name_id'])
                    ->where('number', $data['number'])
                    ->first();

                if(!empty($trk_room_counter->id))
                {
                    return redirect()->back()->with('error', 'Такой счетчик уже есть в удаленных, попросите админа его восстановить')->withInput();
                }

                DB::beginTransaction();

                $trk_room_counter = TrkRoomCounter::create([
                    'trk_id' => $trk_room->trk_id,
                    'floor_id' => $trk_room->floor_id,
                    'brand_id' => $trk_room->renter->brand->id,
                    'organization_id' => $trk_room->renter->organization->id,
                    'counter_type_id' => $data['counter_type_id'],
                    'tariff_name_id' => $data['tariff_name_id'],
                    'number' => $data['number'],
                    'using_purpose' => $data['using_purpose'],
                    'comment' => $data['comment'],
                    'mounted_at' => $data['mounted_at'],
                    'coefficient' => $data['coefficient'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                CounterCount::create([
                    'trk_room_counter_id' => $trk_room_counter->id,
                    'tariff' => Tariff::DAY,
                    'date' => date('Y-m-d'),
                    'count' => $data['last_count_day'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                if(
                    $trk_room_counter->counter_tariff->name == TrkRoomCounter::TARIFF_DAY_NIGHT
                    && isset($data['last_count_night'])
                    && !empty($data['last_count_night'])
                )
                {
                    CounterCount::create([
                        'trk_room_counter_id' => $trk_room_counter->id,
                        'tariff' => Tariff::NIGHT,
                        'date' => date('Y-m-d'),
                        'count' => $data['last_count_night'],
                        'comment' => $data['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                return redirect()->route('trk_room.show', $data['trk_room_id'])->with('success', 'Данные сохранены');

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
    public function show(TrkRoomCounter $trk_room_counter): Response
    {

        $average_day_consumption = null;
        $average_night_consumption = null;

        if(count($trk_room_counter->day_counts) > 2)
        {
            $average_day_consumption = $trk_room_counter->coefficient * round( ($trk_room_counter->day_counts->last()->count - $trk_room_counter->day_counts->first()->count) / (count($trk_room_counter->day_counts) - 1), 0);

        } else if (count($trk_room_counter->day_counts) == 2)
        {
            $average_day_consumption = $trk_room_counter->coefficient * round( ($trk_room_counter->day_counts->last()->count - $trk_room_counter->day_counts->first()->count), 0);
        } else {
            $average_day_consumption = 0;
        }

        if(count($trk_room_counter->night_counts) > 2) {

            $average_night_consumption = $trk_room_counter->coefficient * round(($trk_room_counter->night_counts->last()->count - $trk_room_counter->night_counts->first()->count) / (count($trk_room_counter->night_counts) - 1), 0);

        }else if (count($trk_room_counter->night_counts) == 2)
        {
            $average_night_consumption = $trk_room_counter->coefficient * round( ($trk_room_counter->night_counts->last()->count - $trk_room_counter->night_counts->first()->count), 0);

        }

        return \response()->view('backend.trk_room_counters.show', [
            'trk_room_counter' => $trk_room_counter,
            'average_day_consumption' => $average_day_consumption,
            'average_night_consumption' => $average_night_consumption,
            'prev_day_count' => $trk_room_counter->day_counts->first()->count,
            'prev_night_count' => $trk_room_counter->night_counts->first()->count ?? null,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrkRoomCounter $trk_room_counter): Response
    {

        return \response()->view('backend.trk_room_counters.edit', [
            'trk_room_counter' => $trk_room_counter,
            'all_trks' => Trk::orderBy('sort_order')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
            'all_brands' => Brand::orderBy('name')->get(),
            'all_organizations' => Organization::orderBy('name')->get(),
            'all_counters' => Counter::orderBy('number')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkRoomCounterFormRequest $request, TrkRoomCounter $trk_room_counter): RedirectResponse
    {
        Log::info('User try to update trk room counter',
            [
                'user' => Auth::user()->name,
                'request' => $request,
                'trk_room_counter' => $trk_room_counter
            ]);

        if ($request->isMethod('patch')) {

            $data = $request->validated();

            $trk_room_counter->update([
                'trk_id' => $data['trk_id'],
                'floor_id' => $data['floor_id'],
                'brand_id' => $data['brand_id'],
                'organization_id' => $data['organization_id'],
                'number' => $data['number'],
                'mounted_at' => $data['mounted_at'],
                'using_purpose' => $data['using_purpose'],
                'comment' => $data['comment'],
                'last_editor_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrkRoomCounter $trk_room_counter): RedirectResponse
    {
        Log::info('user try to delete trk counter', [
            'user' => Auth::user()->name,
            'trk_room_counter' => $trk_room_counter
        ]);

        try {

            DB::beginTransaction();

            CounterCount::where('trk_room_counter_id', $trk_room_counter->id)
                ->delete();

            $trk_room_counter->update([
                'destroyer_id' => Auth::id()
            ]);

            $trk_room_counter->delete();

            DB::commit();

            return redirect()->route('trk_room_counters.index')->with('success', 'Данные удалены.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

        return redirect()->back()->with('error', 'Ошибка удаления данных, смотрите логи.');
    }

    public function export(ExportCounterCountFormRequest $request)
    {
        $data = $request->validated();

        switch ($data['file_type']) {
            case '.pdf':

                return (new CounterCountExportPdf(
                    $data,
                ))->export_pdf();

            case '.html':
                return Excel::download(new CounterCountExport(
                    $data,
                ), 'Показания__Счетчиков__' . $data['start_date'] . '__' . $data['finish_date'] . $data['file_type']);

            default:
                return Excel::download(new CounterCountExport(
                    $data,
                ), 'Показания__Счетчиков__' . $data['start_date'] . '__' . $data['finish_date'] . '.xlsx');
        }
    }

    public function export_one_counter(TrkRoomCounter $trk_room_counter, string $type)
    {
        $data['file_type'] = $type;

        switch ($data['file_type']) {
            case 'not excel':

                return (new OneCounterCountExportPdf(
                    $trk_room_counter,
                ))->export_pdf();

            case '.html':
                return Excel::download(new OneCounterCountExport(
                    $trk_room_counter,
                ), 'Показания__Счетчика.pdf');

            default:
                return Excel::download(new OneCounterCountExport(
                    $trk_room_counter,
                ), 'Показания__Счетчика.xlsx');
        }
    }
}
