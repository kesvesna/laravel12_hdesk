<?php

namespace App\Http\Controllers\Backend\RoomPurposes;

use App\Http\Controllers\Controller;
use App\Http\Filters\RoomPurposes\RoomPurposeFilter;
use App\Http\Requests\RoomPurposes\RoomPurposeFilterRequest;
use App\Http\Requests\RoomPurposes\StoreRoomPurposeFormRequest;
use App\Http\Requests\RoomPurposes\UpdateRoomPurposeFormRequest;
use App\Models\RoomPurposes\RoomPurpose;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomPurposeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(RoomPurpose::class, 'room_purpose');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RoomPurposeFilterRequest $request): Response
    {
        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(RoomPurposeFilter::class, ['queryParams' => array_filter($data)]);

        $room_purposes = RoomPurpose::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.room_purposes.pagination'));

        return \response()->view('backend.room_purposes.index', [
            'room_purposes' => $room_purposes,
            'all_room_purposes' => RoomPurpose::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.room_purposes.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomPurposeFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new room purpose',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                RoomPurpose::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('room_purposes.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomPurpose $room_purpose): Response
    {
        return \response()->view('backend.room_purposes.show', [
            'room_purpose' => $room_purpose,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomPurpose $room_purpose): Response
    {
        return \response()->view('backend.room_purposes.edit', [
            'room_purpose' => $room_purpose,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomPurposeFormRequest $request, RoomPurpose $room_purpose): RedirectResponse
    {
        Log::info('User try to update room_purpose',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $room_purpose->update([
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
    public function destroy(RoomPurpose $room_purpose): RedirectResponse
    {
        Log::info('User try to delete room_purpose',
            [
                'user' => Auth::user()->name,
                'room_purpose' => $room_purpose,
            ]);

        try {
            $room_purpose->update([
                'destroyer_id' => Auth::id(),
            ]);
            $room_purpose->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('room_purposes.index')->with('success', 'Данные удалены');
    }
}
