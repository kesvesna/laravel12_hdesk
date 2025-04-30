<?php

namespace App\Http\Controllers\Backend\RoomChecks;

use App\Http\Controllers\Controller;
use App\Http\Filters\RoomChecks\RoomCheckFilter;
use App\Http\Requests\RoomChecks\RoomCheckFilterRequest;
use App\Http\Requests\RoomChecks\StoreRoomCheckByQrFormRequest;
use App\Http\Requests\RoomChecks\StoreRoomCheckFormRequest;
use App\Http\Requests\RoomChecks\UpdateRoomCheckFormRequest;
use App\Models\RoomChecks\RoomCheck;
use App\Models\Rooms\Room;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(RoomCheck::class, 'room_check');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RoomCheckFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(RoomCheckFilter::class, ['queryParams' => array_filter($data)]);

        $room_checks = RoomCheck::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.room_checks.pagination'));

        return \response()->view('backend.room_checks.index', [
            'room_checks' => $room_checks,
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $first_trk = Trk::orderBy('sort_order')->pluck('id')->first();
        $first_trk_room_ids = TrkRoom::where('trk_id', $first_trk)->pluck('room_id')->toArray();
        $rooms = Room::whereIn('id', $first_trk_room_ids)->orderBy('name')->get();

        return \response()->view('backend.room_checks.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => $rooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomCheckFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store new room check',
                [
                    'user' => Auth::user()->name,
                    'request' => $request->all(),
                ]);

            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])->where('room_id', $data['room_id'])->first();

                if (empty($trk_room)) {
                    return redirect()->back()->with('error', 'Нет такого помещения на этом ТРК. Создайте через ТРК/Помещения');
                }

                RoomCheck::create([
                    'trk_room_id' => $trk_room->id,
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('room_checks.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_by_qr(TrkRoom $trk_room): Response
    {
        return \response()->view('backend.room_checks.create_by_qr', [
            'trk_room' => $trk_room,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_by_qr(StoreRoomCheckByQrFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store new room check',
                [
                    'user' => Auth::user()->name,
                    'request' => $request->all(),
                ]);

            try {

                $data = $request->validated();

                $trk_room = TrkRoom::find($data['trk_room_id']);

                if (empty($trk_room)) {
                    return redirect()->back()->with('error', 'Нет такого помещения на этом ТРК. Создайте через ТРК/Помещения');
                }

                RoomCheck::create([
                    'trk_room_id' => $trk_room->id,
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('room_checks.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomCheck $room_check): Response
    {
        return \response()->view('backend.room_checks.show', [
            'room_check' => $room_check,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomCheck $room_check): Response
    {
        return \response()->view('backend.room_checks.edit', [
            'room_check' => $room_check,
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => Room::orderBy('created_at', 'desc')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomCheckFormRequest $request, RoomCheck $room_check): RedirectResponse
    {
        Log::info('User try to update room check',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
                'room_check' => $room_check,
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $room_check->update([
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
    public function destroy(RoomCheck $room_check): RedirectResponse
    {
        Log::info('User try to delete room check',
            [
                'user' => Auth::user()->name,
                'room_check' => $room_check,
            ]);

        try {
            $room_check->update([
                'destroyer_id' => Auth::id(),
            ]);
            $room_check->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('room_checks.index')->with('success', 'Данные удалены');

    }
}
