<?php

namespace App\Http\Controllers\Backend\TareTypes;

use App\Http\Controllers\Controller;
use App\Http\Filters\TareTypes\TareTypeFilter;
use App\Http\Requests\TareTypes\StoreTareTypeFormRequest;
use App\Http\Requests\TareTypes\TareTypeFilterRequest;
use App\Http\Requests\TareTypes\UpdateTareTypeFormRequest;
use App\Models\TareTypes\TareType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TareTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TareType::class, 'tare_type');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TareTypeFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(TareTypeFilter::class, ['queryParams' => array_filter($data)]);

        $tare_types = TareType::filter($filter)
            ->orderBy('name')
            ->paginate(config('backend.tare_types.pagination'));


        return \response()->view('backend.tare_types.index', [
            'types' => $tare_types,
            'old_filters' => $data,
            'all_types' => TareType::orderBy('created_at')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');

        return \response()->view('backend.tare_types.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTareTypeFormRequest $request): RedirectResponse
    {
        $this->authorize('store');

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                TareType::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('tare_types.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(TareType $tare_type): Response
    {
        $this->authorize('view', $tare_type);

        return \response()->view('backend.tare_types.show', [
            'tare_type' => $tare_type,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TareType $tare_type): Response
    {
        $this->authorize('edit', $tare_type);

        return \response()->view('backend.tare_types.edit', [
            'tare_type' => $tare_type,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTareTypeFormRequest $request, TareType $tare_type): RedirectResponse
    {
        $this->authorize('update', $tare_type);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $tare_type->update([
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
    public function destroy(TareType $tare_type): RedirectResponse
    {
        $this->authorize('delete', $tare_type);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'tare_type' => $tare_type,

            ]);

        try {

            $tare_type->update([
                'destroyer_id' => Auth::id(),
            ]);
            $tare_type->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('tare_types.index')->with('success', 'Данные удалены');

    }
}
