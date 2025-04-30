<?php

namespace App\Http\Controllers\Backend\TrkRoomClimates;

use App\Http\Controllers\Controller;
use App\Http\Filters\TrkRoomClimates\TrkRoomClimateFilter;
use App\Http\Requests\TrkRoomClimates\StoreTrkRoomClimateFormRequest;
use App\Http\Requests\TrkRoomClimates\StoreTrkRoomClimateFromTrkRoomFormRequest;
use App\Http\Requests\TrkRoomClimates\TrkRoomClimateFilterRequest;
use App\Http\Requests\TrkRoomClimates\UpdateTrkRoomClimateFormRequest;
use App\Models\Buildings\Building;
use App\Models\Floors\Floor;
use App\Models\Rooms\Room;
use App\Models\TrkRoomClimates\TrkRoomClimate;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrkRoomClimateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TrkRoomClimate::class, 'trk_room_climate');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TrkRoomClimateFilterRequest $request): Response
    {
        $data = $request->validated();

        $user_trks = UserResponsibilityTrkSystem::where('user_id', Auth::id())
            ->pluck('trk_id')
            ->toArray();

        $all_trks = Trk::orderBy('sort_order')->get();

        if (count($user_trks) > 0 && !auth()->user()->hasRole('sadmin')) {
            $data['trk_ids'] = $user_trks;
            $all_trks = Trk::whereIn('id', $user_trks)->get();
        }

        $filter = app()->make(TrkRoomClimateFilter::class, ['queryParams' => array_filter($data)]);

        $trk_room_climates = TrkRoomClimate::filter($filter)
            ->with([])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.trk_room_climates.pagination'));

        return \response()->view('backend.trk_room_climates.index', [
            'trk_room_climates' => $trk_room_climates,
            'all_trks' => $all_trks,
            'all_rooms' => Room::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        return \response()->view('backend.trk_room_climates.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkRoomClimateFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store trk room climate',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $trk_room_id = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('room_id', $data['room_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->pluck('id')->first();

                if (empty($trk_room_id)) {
                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name);
                }

                TrkRoomClimate::withTrashed()->updateOrCreate([
                    'trk_room_id' => $trk_room_id,
                    't_inside' => $data['t_inside'],
                    't_outside' => $data['t_outside'],
                    't_supply_air' => $data['t_supply_air'],
                    'h_inside' => $data['h_inside'],
                    't_extract_air' => $data['t_extract_air'],
                    'q_supply_air_total' => $data['q_supply_air_total'],
                    'q_extract_air_total' => $data['q_extract_air_total'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ])->restore();


                return redirect()->route('trk_room_climates.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_trk_room(TrkRoom $trk_room): Response
    {
        $this->authorize('create', TrkRoomClimate::class);

        return \response()->view('backend.trk_room_climates.create_from_trk_room', [
            'trk_room' => $trk_room,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreTrkRoomClimateFromTrkRoomFormRequest $request): RedirectResponse
    {
        $this->authorize('create', TrkRoomClimate::class);

        if ($request->isMethod('post')) {
            Log::info('User try to store trk room climate from trk room',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $not_exists_room = '';

                $trk_room = TrkRoom::find($data['trk_room_id']);

                if (empty($trk_room)) {
                    $room_name = Room::where('id', $data['room_id'])->pluck('name')->first();
                    $not_exists_room .= $room_name . ', ';
                }


                TrkRoomClimate::withTrashed()->updateOrCreate([
                    'trk_room_id' => $trk_room->id,
                    't_inside' => $data['t_inside'],
                    't_outside' => $data['t_outside'],
                    't_supply_air' => $data['t_supply_air'],
                    'h_inside' => $data['h_inside'],
                    't_extract_air' => $data['t_extract_air'],
                    'q_supply_air_total' => $data['q_supply_air_total'],
                    'q_extract_air_total' => $data['q_extract_air_total'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ])->restore();


                $message = '';

                if (strlen($not_exists_room) > 0) {
                    $message .= 'Помещения: ' . $not_exists_room . ' отсутствуют на этом ТРК. Создайте их в разделе ТРК/Помещения. ';
                }

                if (strlen($message) > 0) {
                    return redirect()->route('trk_room.show', $data['trk_room_id'])->with('alert', 'Сохранена часть. ' . $message);
                }


                return redirect()->route('trk_room.show', $data['trk_room_id'])->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrkRoomClimate $trk_room_climate): Response
    {
        return \response()->view('backend.trk_room_climates.show', [
            'trk_room_climate' => $trk_room_climate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrkRoomClimate $trk_room_climate): Response
    {
        return \response()->view('backend.trk_room_climates.edit', [
            'trk_room_climate' => $trk_room_climate,
            'all_trks' => Trk::orderBy('sort_order')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_buildings' => Building::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkRoomClimateFormRequest $request, TrkRoomClimate $trk_room_climate): RedirectResponse
    {
        Log::info('User try to update trk_room_climate',
            [
                'user' => Auth::user()->name,
                'request' => $request,
                'trk_room_climate' => $trk_room_climate
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $trk_room_id = TrkRoom::where('trk_id', $data['trk_id'])
                ->where('room_id', $data['room_id'])
                ->where('building_id', $data['building_id'])
                ->where('floor_id', $data['floor_id'])
                ->pluck('id')->first();

            if (empty($trk_room_id)) {
                $trk = Trk::find($data['trk_id']);
                $building = Building::find($data['building_id']);
                $floor = Floor::find($data['floor_id']);
                $room = Room::find($data['room_id']);

                return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name);
            }

            $trk_room_climate->update([
                'trk_room_id' => $trk_room_id,
                't_inside' => $data['t_inside'],
                't_outside' => $data['t_outside'],
                't_supply_air' => $data['t_supply_air'],
                'h_inside' => $data['h_inside'],
                't_extract_air' => $data['t_extract_air'],
                'q_supply_air_total' => $data['q_supply_air_total'],
                'q_extract_air_total' => $data['q_extract_air_total'],
                'comment' => $data['comment'],
                'last_editor_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrkRoomClimate $trk_room_climate): RedirectResponse
    {
        Log::info('User try to delete trk_room_climate',
            [
                'user' => Auth::user()->name,
                'trk_room_climate' => $trk_room_climate,
            ]);

        try {
            $trk_room_climate->update([
                'destroyer_id' => Auth::id(),
            ]);
            $trk_room_climate->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('trk_room_climates.index')->with('success', 'Данные удалены');
    }
}
