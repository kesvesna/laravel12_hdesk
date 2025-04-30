<?php

namespace App\Http\Controllers\Backend\Counters;

use App\Exports\CounterCounts\CounterCountExport;
use App\Exports\CounterCounts\CounterCountExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\Counters\CounterCountFilter;
use App\Http\Requests\Counters\CounterCountFilterRequest;
use App\Http\Requests\Counters\StoreCounterCountFormRequest;
use App\Http\Requests\Counters\StoreCounterCountFromTrkRoomCounterFormRequest;
use App\Http\Requests\Counters\UpdateCounterCountFormRequest;
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
use App\Models\Trks\Trk;
use App\Providers\CounterCounts\CounterCountProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CounterCountController extends Controller
{
    public function __construct(CounterCountProvider $counterCountProvider)
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(CounterCount::class, 'counter_count');
        $this->counterCountProvider = $counterCountProvider;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CounterCountFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(CounterCountFilter::class, ['queryParams' => array_filter($data)]);

        $counter_counts = CounterCount::filter($filter)
            ->with([])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.counter_counts.pagination'));

        return \response()->view('backend.counter_counts.index', [
            'counter_counts' => $counter_counts,
            'trks' => Trk::orderBy('sort_order')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'counter_types' => CounterType::orderBy('name', 'desc')->get(),
            'counters' => Counter::orderBy('number')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        return \response()->view('backend.counter_counts.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
            'counter_types' => CounterType::orderBy('name', 'desc')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'counters' => Counter::orderBy('number')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCounterCountFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store counter count',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $trk = Trk::find($data['trk_id']);
                $floor = Floor::find($data['floor_id']);

                $trk_room_counter = TrkRoomCounter::where('trk_id', $data['trk_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('number', $data['number'])
                    ->first();

                if (empty($trk_room_counter->id)) {
                    return redirect()->back()->withInput()->with('error', 'Счетчика с номером ' . $data['number'] . ' на ' . $trk->name . ', ' . $floor->name . ' не существует');
                }

                $last_count_day = $this->counterCountProvider->getLastCountDay($trk_room_counter->id);

//                if (Str::length($last_count_day) != Str::length($data['current_count_day'])) {
//                    return redirect()->back()->withInput()->with('error', 'Разрядность данных текущих = ' . $data['current_count_day'] . ' и предыдущих = ' . $last_count_day . ' не совпадает');
//                }

                if ($last_count_day > $data['current_count_day']) {
                    return redirect()->back()->withInput()->with('error', 'Текущие данные = ' . $data['current_count_day'] . ' не могут быть меньше предыдущих = ' . $last_count_day);
                }

                CounterCount::create([
                    'trk_room_counter_id' => $trk_room_counter->id,
                    'tariff' => Tariff::DAY,
                    'date' => date('Y-m-d'),
                    'count' => $data['current_count_day'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                if (isset($data['current_count_night'])) {

                    $last_count_night = $this->counterCountProvider->getLastCountNight($trk_room_counter->id);

//                    if (Str::length($last_count_night) != Str::length($data['current_count_night'])) {
//                        return redirect()->back()->withInput()->with('error', 'Разрядность данных текущих = ' . $data['current_count_night'] . ' и предыдущих = ' . $last_count_night . ' не совпадает');
//                    }

                    if ($last_count_night > $data['current_count_night']) {
                        return redirect()->back()->withInput()->with('error', 'Текущие данные = ' . $data['current_count_night'] . ' не могут быть меньше предыдущих = ' . $last_count_night);
                    }

                    CounterCount::create([
                        'trk_room_counter_id' => $trk_room_counter->id,
                        'tariff' => Tariff::NIGHT,
                        'date' => date('Y-m-d'),
                        'count' => $data['current_count_night'],
                        'comment' => $data['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                return redirect()->route('counter_counts.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);

            }
        }
        return redirect()->back()->withInput()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_trk_room_counter(TrkRoomCounter $trk_room_counter): Response
    {
        return \response()->view('backend.counter_counts.create_from_trk_room_counter', [
            'trk_room_counter' => $trk_room_counter,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room_counter(StoreCounterCountFromTrkRoomCounterFormRequest $request, TrkRoomCounter $trk_room_counter): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store counter count from trk_room_counter',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $last_count_day = $this->counterCountProvider->getLastCountDay($trk_room_counter->id);

//                if (Str::length($last_count_day) != Str::length($data['current_count_day'])) {
//                    return redirect()->back()->withInput()->with('error', 'Разрядность данных текущих = ' . $data['current_count_day'] . ' и предыдущих = ' . $last_count_day . ' не совпадает');
//                }

                if ($last_count_day > $data['current_count_day']) {
                    return redirect()->back()->withInput()->with('error', 'Текущие данные = ' . $data['current_count_day'] . ' не могут быть меньше предыдущих = ' . $last_count_day);
                }

                CounterCount::create([
                    'trk_room_counter_id' => $trk_room_counter->id,
                    'tariff' => Tariff::DAY,
                    'date' => date('Y-m-d'),
                    'count' => $data['current_count_day'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                if (isset($data['current_count_night'])) {

                    $last_count_night = $this->counterCountProvider->getLastCountNight($trk_room_counter->id);

//                    if (Str::length($last_count_night) != Str::length($data['current_count_night'])) {
//                        return redirect()->back()->withInput()->with('error', 'Разрядность данных текущих = ' . $data['current_count_night'] . ' и предыдущих = ' . $last_count_night . ' не совпадает');
//                    }

                    if ($last_count_night > $data['current_count_night']) {
                        return redirect()->back()->withInput()->with('error', 'Текущие данные = ' . $data['current_count_night'] . ' не могут быть меньше предыдущих = ' . $last_count_night);
                    }

                    CounterCount::create([
                        'trk_room_counter_id' => $trk_room_counter->id,
                        'tariff' => Tariff::NIGHT,
                        'date' => date('Y-m-d'),
                        'count' => $data['current_count_night'],
                        'comment' => $data['comment'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                //return redirect()->route('counter_counts.index')->with('success', 'Данные сохранены');

                return redirect()->back()->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CounterCount $counter_count): Response
    {
        return \response()->view('backend.counter_counts.show', [
            'counter_count' => $counter_count,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CounterCount $counter_count): Response
    {
        return \response()->view('backend.counter_counts.edit', [
            'counter_count' => $counter_count,
            'trks' => Trk::orderBy('sort_order')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
            'counter_types' => CounterType::orderBy('name', 'desc')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'counters' => Counter::orderBy('number')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCounterCountFormRequest $request, CounterCount $counter_count): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update counter count',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();



                $counter_count->update([
                    'date' => $data['date'],
                    'count' => $data['count'],
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);


                return redirect()->back()->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CounterCount $counter_count): RedirectResponse
    {
        Log::info('User try to delete counter',
            [
                'user' => Auth::user()->name,
                'counter_count' => $counter_count,
            ]);

        try {

            $trk_room_counter = TrkRoomCounter::find($counter_count->trk_room_counter_id);

            if(count($trk_room_counter->counts) == 1)
            {
                return redirect()->back()->with('error', 'Единственные показания этого счетчика, их нельзя удалять');
            }


            $counter_count->update([
                'destroyer_id' => Auth::id(),
            ]);

            $counter_count->delete();

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('counter_counts.index')->with('success', 'Данные удалены');

    }

    public function export(ExportCounterCountFormRequest $request)
    {
        $data = $request->validated();

        switch ($data['file_type']) {
            case '.pdf':

                return (new CounterCountExportPdf(
                    $data['trk_id'],
                    $data['start_date'],
                    $data['finish_date'],
                    $data['file_type'],
                    $data['floor_id'] ?? null,
                    $data['brand_id'] ?? null,
                    $data['counter_type_id'] ?? null,
                    $data['tariff'] ?? null,
                    $data['counter_number'] ?? null
                ))->export_pdf();

            case '.html':
                return Excel::download(new CounterCountExport(
                    $data['trk_id'],
                    $data['start_date'],
                    $data['finish_date'],
                    $data['file_type'],
                    $data['floor_id'] ?? null,
                    $data['brand_id'] ?? null,
                    $data['counter_type_id'] ?? null,
                    $data['tariff'] ?? null,
                    $data['counter_number'] ?? null
                ), 'Показания__Счетчиков__' . $data['start_date'] . '__' . $data['finish_date'] . $data['file_type']);

            default:
                return Excel::download(new CounterCountExport(
                    $data['trk_id'],
                    $data['start_date'],
                    $data['finish_date'],
                    $data['floor_id'] ?? null,
                    $data['brand_id'] ?? null,
                    $data['counter_type_id'] ?? null,
                    $data['tariff'] ?? null,
                    $data['counter_number'] ?? null
                ), 'Показания__Счетчиков__' . $data['start_date'] . '__' . $data['finish_date'] . '.xlsx');

        }

    }
}
