<?php

namespace App\Http\Controllers\Backend\Trks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trks\StoreTrkFormRequest;
use App\Http\Requests\Trks\UpdateTrkFormRequest;
use App\Models\Towns\Town;
use App\Models\Trks\Trk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TrkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Trk::class, 'trk');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $trks = Trk::orderBy('created_at', 'desc')
            ->paginate(config('backend.trk.pagination'));
        return \response()->view('backend.trk.index', [
            'trks' => $trks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.trk.create', [
            'towns' => Town::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                Trk::create([
                    'name' => $data['name'],
                    'town_id' => $data['town_id'],
                    'alias' => Str::slug($data['name']),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('trk.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trk $trk): Response
    {
        return \response()->view('backend.trk.show', [
            'trk' => $trk,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trk $trk): Response
    {
        return \response()->view('backend.trk.edit', [
            'trk' => $trk,
            'towns' => Town::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkFormRequest $request, Trk $trk): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $trk->update([
                'name' => $data['name'],
                'town_id' => $data['town_id'],
                'last_editor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trk $trk): RedirectResponse
    {
        try {
            $trk->update([
                'destroyer_id' => Auth::id(),
            ]);
            $trk->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('trk.index')->with('success', 'Данные удалены');
    }
}
