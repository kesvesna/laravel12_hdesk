<?php

namespace App\Http\Controllers\Backend\Buildings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buildings\StoreBuildingFormRequest;
use App\Http\Requests\Buildings\UpdateBuildingFormRequest;
use App\Models\Buildings\Building;
use App\Models\TrkRooms\TrkRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BuildingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Building::class, 'building');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $buildings = Building::orderBy('sort_order')
            ->paginate(config('backend.building.pagination'));
        return \response()->view('backend.building.index', [
            'buildings' => $buildings,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.building.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuildingFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                Building::create([
                    'name' => $data['name'],
                    'alias' => Str::slug($data['name']),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('building.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building): Response
    {

        $trk_rooms = TrkRoom::where('building_id', $building->id)->get();

        return \response()->view('backend.building.show', [
            'building' => $building,
            'trk_rooms' => $trk_rooms,
            'buildings' => Building::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building): Response
    {
        return \response()->view('backend.building.edit', [
            'building' => $building,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuildingFormRequest $request, Building $building): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $building->update([
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
    public function destroy(Building $building): RedirectResponse
    {
        try {

            $trk_rooms = TrkRoom::where('building_id', $building->id)->get();

            if (count($trk_rooms) > 0) {
                return redirect()->back()->with('error', 'Невозможно удалить этот блок, он используется для помещений')->withInput();
            }

            $building->update([
                'destroyer_id' => Auth::id(),
            ]);

            $building->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('building.index')->with('success', 'Данные удалены');

    }
}
