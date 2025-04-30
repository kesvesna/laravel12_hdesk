<?php

namespace App\Http\Controllers\Backend\Counters;

use App\Http\Controllers\Controller;
use App\Http\Requests\Counters\StoreCounterTypeFormRequest;
use App\Http\Requests\Counters\UpdateCounterTypeFormRequest;
use App\Models\Counters\CounterType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CounterTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(CounterType::class, 'counter_type');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $counter_types = CounterType::orderBy('sort_order')
            ->paginate(config('backend.counter_types.pagination'));

        return \response()->view('backend.counter_types.index', [
            'counter_types' => $counter_types,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.counter_types.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCounterTypeFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store counter type',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {
                $data = $request->validated();
                CounterType::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('counter_types.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(CounterType $counter_type): Response
    {
        return \response()->view('backend.counter_types.show', [
            'counter_type' => $counter_type,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CounterType $counter_type): Response
    {
        return \response()->view('backend.counter_types.edit', [
            'counter_type' => $counter_type,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCounterTypeFormRequest $request, CounterType $counter_type): RedirectResponse
    {
        Log::info('User try to update counter type',
            [
                'user' => Auth::user()->name,
                'request' => $request,
                'counter_type' => $counter_type
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $counter_type->update([
                'name' => $data['name'],
                'last_editor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CounterType $counter_type): RedirectResponse
    {
        Log::info('User try to delete tariff name',
            [
                'user' => Auth::user()->name,
                'counter_type' => $counter_type,
            ]);

        try {
            $counter_type->update([
                'destroyer_id' => Auth::id(),
            ]);
            $counter_type->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('counter_types.index')->with('success', 'Данные удалены');

    }
}
