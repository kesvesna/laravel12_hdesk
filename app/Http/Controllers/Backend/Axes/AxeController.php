<?php

namespace App\Http\Controllers\Backend\Axes;

use App\Http\Controllers\Controller;
use App\Http\Filters\Axes\AxeFilter;
use App\Http\Requests\Axes\AxeFilterRequest;
use App\Http\Requests\Axes\StoreAxeFormRequest;
use App\Http\Requests\Axes\UpdateAxeFormRequest;
use App\Models\Axes\Axe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AxeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Axe::class, 'axe');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AxeFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(AxeFilter::class, ['queryParams' => array_filter($data)]);

        $axes = Axe::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.axe.pagination'));

        return \response()->view('backend.axe.index', [
            'axes' => $axes,
            'all_axes' => Axe::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');

        return \response()->view('backend.axe.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAxeFormRequest $request): RedirectResponse
    {
        Log::info('user try to store new axe', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
        ]);

        $this->authorize('store');

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                Axe::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('axe.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Axe $axe): Response
    {
        $this->authorize('view');

        return \response()->view('backend.axe.show', [
            'axe' => $axe,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Axe $axe): Response
    {

        $this->authorize('edit');

        return \response()->view('backend.axe.edit', [
            'axe' => $axe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAxeFormRequest $request, Axe $axe): RedirectResponse
    {
        Log::info('user try to update axe', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
            'axe' => $axe
        ]);

        $this->authorize('update');

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $axe->update([
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
    public function destroy(Axe $axe): RedirectResponse
    {
        Log::info('user try to delete axe', [
            'user' => Auth::user()->name,
            'axe' => $axe,
        ]);

        $this->authorize('delete');

        try {
            $axe->update([
                'destroyer_id' => Auth::id(),
            ]);
            $axe->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('axe.index')->with('success', 'Данные удалены');

    }
}
