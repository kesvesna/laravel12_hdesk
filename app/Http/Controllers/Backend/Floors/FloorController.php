<?php

namespace App\Http\Controllers\Backend\Floors;

use App\Http\Controllers\Controller;
use App\Http\Requests\Floors\StoreFloorFormRequest;
use App\Http\Requests\Floors\UpdateFloorFormRequest;
use App\Models\Floors\Floor;
use App\Models\TrkRooms\TrkRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FloorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Floor::class, 'floor');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $floors = Floor::orderBy('sort_order')
            ->paginate(config('backend.floor.pagination'));
        return \response()->view('backend.floor.index', [
            'floors' => $floors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.floor.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFloorFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                Floor::create([
                    'name' => $data['name'],
                    'alias' => Str::slug($data['name']),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('floor.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Floor $floor): Response
    {
        $trk_rooms = TrkRoom::where('floor_id', $floor->id)->get();

        return \response()->view('backend.floor.show', [
            'floor' => $floor,
            'trk_rooms' => $trk_rooms,
            'floors' => Floor::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Floor $floor): Response
    {
        return \response()->view('backend.floor.edit', [
            'floor' => $floor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFloorFormRequest $request, Floor $floor): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $floor->update([
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
    public function destroy(Floor $floor): RedirectResponse
    {
        try {

            $trk_rooms = TrkRoom::where('floor_id', $floor->id)->get();

            if (count($trk_rooms) > 0) {
                return redirect()->back()->with('error', 'Невозможно удалить этот этаж, он используется для помещений')->withInput();
            }

            $floor->update([
                'destroyer_id' => Auth::id(),
            ]);

            $floor->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('floor.index')->with('success', 'Данные удалены');

    }
}
