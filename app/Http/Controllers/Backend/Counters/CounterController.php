<?php

namespace App\Http\Controllers\Backend\Counters;

use App\Http\Controllers\Controller;
use App\Http\Filters\Counters\CounterFilter;
use App\Http\Requests\Counters\CounterFilterRequest;
use App\Http\Requests\Counters\StoreCounterFormRequest;
use App\Http\Requests\Counters\UpdateCounterFormRequest;
use App\Models\Counters\Counter;
use App\Models\Counters\CounterType;
use App\Models\Counters\Tariff;
use App\Models\Trks\Trk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CounterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Floor::class, 'floor');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CounterFilterRequest $request): Response
    {

        $data = $request->validated();

        $filter = app()->make(CounterFilter::class, ['queryParams' => array_filter($data)]);

        $counters = Counter::filter($filter)
            ->with(['type'])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.counters.pagination'));

        return \response()->view('backend.counters.index', [
            'counters' => $counters,
            'all_counters' => Counter::all(),
            'all_counter_types' => CounterType::all(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.counters.create', [
            'all_counter_types' => CounterType::orderBy('name', 'desc')->get(),
            'all_counter_tariffs' => Tariff::all(),
            'all_trks' => Trk::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCounterFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store counter',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {
                $data = $request->validated();
                Counter::create([
                    'trk_id' => $data['trk_id'],
                    'number' => $data['number'],
                    'counter_type_id' => $data['counter_type_id'],
                    'tariff_name_id' => $data['tariff_name_id'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('counters.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Counter $counter): Response
    {
        return \response()->view('backend.counters.show', [
            'counter' => $counter,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Counter $counter): Response
    {
        return \response()->view('backend.counters.edit', [
            'counter' => $counter,
            'counter_types' => CounterType::all(),
            'counter_tariffs' => Tariff::all(),
            'trks' => Trk::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCounterFormRequest $request, Counter $counter): RedirectResponse
    {
        Log::info('User try to update counter',
            [
                'user' => Auth::user()->name,
                'request' => $request,
                'counter' => $counter
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $counter->update([
                'number' => $data['number'],
                'trk_id' => $data['trk_id'],
                'counter_type_id' => $data['counter_type_id'],
                'tariff_name_id' => $data['tariff_name_id'],
                'last_editor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counter $counter): RedirectResponse
    {
        Log::info('User try to delete counter',
            [
                'user' => Auth::user()->name,
                'counter' => $counter,
            ]);

        try {
            $counter->update([
                'destroyer_id' => Auth::id(),
            ]);
            $counter->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('counters.index')->with('success', 'Данные удалены');

    }
}
