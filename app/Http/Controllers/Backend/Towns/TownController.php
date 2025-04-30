<?php

namespace App\Http\Controllers\Backend\Towns;

use App\Http\Controllers\Controller;
use App\Http\Requests\Towns\StoreTownFormRequest;
use App\Http\Requests\Towns\UpdateTownFormRequest;
use App\Models\Towns\Town;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TownController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Town::class, 'town');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $towns = Town::orderBy('created_at', 'desc')
            ->paginate(config('backend.town.pagination'));
        return \response()->view('backend.town.index', [
            'towns' => $towns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.town.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTownFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                Town::create([
                    'name' => $data['name'],
                    'alias' => Str::slug($data['name']),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('town.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Town $town): Response
    {
        return \response()->view('backend.town.show', [
            'town' => $town,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Town $town): Response
    {
        return \response()->view('backend.town.edit', [
            'town' => $town,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTownFormRequest $request, Town $town): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $town->update([
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
    public function destroy(Town $town): RedirectResponse
    {
        try {
            $town->update([
                'destroyer_id' => Auth::id(),
            ]);
            $town->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('town.index')->with('success', 'Данные удалены');
    }
}
