<?php

namespace App\Http\Controllers\Backend\Counters;

use App\Http\Controllers\Controller;
use App\Http\Requests\Counters\StoreTariffNameFormRequest;
use App\Http\Requests\Counters\UpdateTariffNameFormRequest;
use App\Models\Counters\Tariff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TariffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Tariff::class, 'tariff');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $tariffs = Tariff::orderBy('sort_order')
            ->paginate(config('backend.tariff.pagination'));

        return \response()->view('backend.tariff_names.index', [
            'tariff_names' => $tariffs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.tariff_names.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTariffNameFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store tariff name',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {
                $data = $request->validated();
                Tariff::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('tariff_names.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Tariff $tariff_name): Response
    {
        return \response()->view('backend.tariff_names.show', [
            'tariff_name' => $tariff_name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tariff $tariff_name): Response
    {
        return \response()->view('backend.tariff_names.edit', [
            'tariff_name' => $tariff_name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTariffNameFormRequest $request, Tariff $tariff_name): RedirectResponse
    {
        Log::info('User try to update tariff name',
            [
                'user' => Auth::user()->name,
                'request' => $request,
                'tariff_name' => $tariff_name
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $tariff_name->update([
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
    public function destroy(Tariff $tariff_name): RedirectResponse
    {
        Log::info('User try to delete tariff name',
            [
                'user' => Auth::user()->name,
                'tariff_name' => $tariff_name,
            ]);

        try {
            $tariff_name->update([
                'destroyer_id' => Auth::id(),
            ]);
            $tariff_name->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('tariff_names.index')->with('success', 'Данные удалены');

    }
}
