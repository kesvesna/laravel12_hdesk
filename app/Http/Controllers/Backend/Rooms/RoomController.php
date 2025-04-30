<?php

namespace App\Http\Controllers\Backend\Rooms;

use App\Http\Controllers\Controller;
use App\Http\Filters\Rooms\RoomFilter;
use App\Http\Requests\Rooms\RoomFilterRequest;
use App\Http\Requests\Rooms\StoreRoomFormRequest;
use App\Http\Requests\Rooms\UpdateRoomFormRequest;
use App\Models\Rooms\Room;
use App\Models\TrkRooms\TrkRoom;
use App\Services\Languages\LanguageCheckService\LanguageCheckService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function __construct(LanguageCheckService $languageCheckService)
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Room::class, 'room');
        $this->languageCheckService = $languageCheckService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RoomFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(RoomFilter::class, ['queryParams' => array_filter($data)]);

        $rooms = Room::filter($filter)
            ->orderBy('name')
            ->paginate(config('backend.room.pagination'));


        return \response()->view('backend.room.index', [
            'rooms' => $rooms,
            'old_filters' => $data,
            'all_rooms' => Room::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');

        return \response()->view('backend.room.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomFormRequest $request): RedirectResponse
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
                Room::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('room.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): Response
    {
        $this->authorize('view', $room);

        $trk_rooms = TrkRoom::where('room_id', $room->id)->get();

        return \response()->view('backend.room.show', [
            'room' => $room,
            'trk_rooms' => $trk_rooms,
            'room_names' => Room::orderBy('name')->get(),
            'language' => $this->languageCheckService->check_language($room->name),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room): Response
    {
        $this->authorize('edit', $room);

        return \response()->view('backend.room.edit', [
            'room' => $room,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomFormRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $room->update([
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
    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'room' => $room,

            ]);

        try {

            $room->update([
                'destroyer_id' => Auth::id(),
            ]);
            $room->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('room.index')->with('success', 'Данные удалены');

    }
}
