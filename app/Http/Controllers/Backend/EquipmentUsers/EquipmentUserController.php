<?php

namespace App\Http\Controllers\Backend\EquipmentUsers;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentUsers\EquipmentUserFilter;
use App\Http\Requests\EquipmentParameters\SetParametersToAnotherEquipmentFormRequest;
use App\Http\Requests\EquipmentUsers\EquipmentUserFilterRequest;
use App\Http\Requests\EquipmentUsers\StoreEquipmentUserFromEquipmentFormRequest;
use App\Http\Requests\EquipmentUsers\StoreEquipmentUserFromTrkRoomFormRequest;
use App\Http\Requests\EquipmentUsers\StoreFromCreateEquipmentUserFormRequest;
use App\Http\Requests\EquipmentUsers\UpdateEquipmentUserFormRequest;
use App\Models\Buildings\Building;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentUsers\EquipmentUser;
use App\Models\Floors\Floor;
use App\Models\Rooms\Room;
use App\Models\Systems\System;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(EquipmentUser::class, 'equipment_user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EquipmentUserFilterRequest $request): Response
    {
        //todo переделать таблицу потребителей в полиморфную, добавить туда поле user_class,
        // trk_room_id переделать в user_id
        // т.к. потребители могут быть как помещения (вентиляция, вода),
        // так и оборудование (для эл. щитов потребители другое оборудование)

        $data = $request->validated();

        $filter = app()->make(EquipmentUserFilter::class, ['queryParams' => array_filter($data)]);

        $equipment_users = EquipmentUser::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.equipment_users.pagination'));

        return \response()->view('backend.equipment_users.index', [
            'equipment_users' => $equipment_users,
            'old_filters' => $data,
            'trks' => Trk::orderBy('sort_order')->get(),
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
            'room_names' => Room::orderBy('name')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');


        return \response()->view('backend.equipment_users.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'systems' => System::where('visibility', 1)->orderBy('sort_order')->get(),
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'buildings' => Building::orderBy('name')->get(),
            'floors' => Floor::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFromCreateEquipmentUserFormRequest $request): RedirectResponse
    {
        Log::info('user try to store new equipment users', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
        ]);

        if ($request->isMethod('post')) {

            try {

                $data = $request->validated();

                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if(empty($trk_room->id))
                {
                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                    ->where('equipment_name_id', $data['equipment_name_id'])
                    ->first();

                if(empty($trk_equipment->id))
                {
                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);
                    $equipment = EquipmentName::find($data['equipment_name_id']);

                    return redirect()->back()->with('error', 'Нет такого оборудования: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name . ' - ' . $equipment->name)->withInput();
                }

                DB::beginTransaction();

                foreach($data['equipments'] as $key => $value)
                {
                    $old_room = TrkRoom::where('trk_id', $data['trk_id'])
                        ->where('building_id', $value['building'])
                        ->where('floor_id', $value['floor'])
                        ->where('room_id', $value['room'])
                        ->first();

                    if(!empty($old_room->id))
                    {
                        $old_user = EquipmentUser::where('trk_room_id', $old_room->id)
                            ->where('equipment_id', $trk_equipment->id)
                            ->first();

                        if(empty($old_user->id))
                        {
                            $new_user = EquipmentUser::create([
                                'trk_room_id' => $old_room->id,
                                'equipment_id' => $trk_equipment->id,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }
                }

                DB::commit();

                return redirect()->route('equipment_users.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e)
            {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_equipment(TrkEquipment $trk_equipment): Response
    {
        $trk_room = TrkRoom::find($trk_equipment->trk_room_id);
        $trk_rooms = TrkRoom::where('trk_id', $trk_room->trk_id)
            ->select('trk_rooms.*', 'rooms.name')
            ->join('rooms', 'rooms.id', '=', 'trk_rooms.room_id')
            ->orderBy('rooms.name')
            ->get();

        return \response()->view('backend.equipment_users.create_from_equipment', [
            'trk_equipment' => $trk_equipment,
            'trk_rooms' => $trk_rooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_equipment(StoreEquipmentUserFromEquipmentFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store user from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                if (
                    EquipmentUser::where('equipment_id', $data['equipment_id'])
                        ->where('trk_room_id', $data['trk_room_id'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Для этого оборудования такой потребитель уже есть. Пользуйтесь редактированием.');
                }


                EquipmentUser::create([
                    'equipment_id' => $data['equipment_id'],
                    'trk_room_id' => $data['trk_room_id'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('trk_equipments.show', $data['equipment_id'])->with('success', 'Данные сохранены');
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
        $trk_rooms = TrkRoom::where('trk_id', $trk_room->trk->id)->pluck('id')->toArray();

        $equipment_names = EquipmentName::orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();
        $floors = Floor::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $systems = System::orderBy('name')->get();

        return \response()->view('backend.equipment_users.create_from_trk_room', [
            'trk_equipments' => TrkEquipment::whereIn('trk_room_id', $trk_rooms)->get(),
            'trk_room' => $trk_room,
            'equipment_names' => $equipment_names,
            'buildings' => $buildings,
            'floors' => $floors,
            'rooms' => $rooms,
            'systems' => $systems,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreEquipmentUserFromTrkRoomFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store user from trk room',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $trk_id = TrkRoom::where('id', $data['trk_room_id'])->pluck('trk_id');

                $equipment_trk_room = TrkRoom::where('trk_id', $trk_id)
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->first();

                if(empty($equipment_trk_room->id))
                {
                    return redirect()->back()->with('error', 'Нет такого помещения')->withInput();
                }

                $equipment = TrkEquipment::where('trk_room_id', $equipment_trk_room->id)
                    ->where('system_id', $data['system_id'])
                    ->where('equipment_name_id', $data['equipment_name_id'])
                    ->first();

                if(empty($equipment->id))
                {
                    return redirect()->back()->with('error', 'Нет такого помещения')->withInput();
                }

                if (
                    EquipmentUser::where('equipment_id', $equipment->id)
                        ->where('trk_room_id', $data['trk_room_id'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Для этого помещения такой источник уже есть. Пользуйтесь редактированием.');
                }

                EquipmentUser::create([
                    'equipment_id' => $equipment->id,
                    'trk_room_id' => $data['trk_room_id'],
                    'comment' => $data['comment'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

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
    public function show(EquipmentUser $equipment_user): Response
    {
        return \response()->view('backend.equipment_users.show', [
            'equipment_user' => $equipment_user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentUser $equipment_user): Response
    {
        $trk_room = TrkRoom::find($equipment_user->trk_room_id);
        $trk_rooms = TrkRoom::where('trk_id', $trk_room->trk_id)->get();
        $trk_equipment = TrkEquipment::find($equipment_user->equipment_id);

        return \response()->view('backend.equipment_users.edit', [
            'equipment_user' => $equipment_user,
            'trk_rooms' => $trk_rooms,
            'trk_equipment' => $trk_equipment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentUserFormRequest $request, EquipmentUser $equipment_user): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update equipment_user from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'equipment_user' => $equipment_user,
                ]);

            try {

                $data = $request->validated();

                $equipment_user->update([
                    'equipment_id' => $data['equipment_id'],
                    'trk_room_id' => $data['trk_room_id'],
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);


                return redirect()->route('trk_equipments.show', $data['equipment_id'])->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function set_equipment_users_from_this_to_another_equipment(SetParametersToAnotherEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
    {
        if ($request->isMethod('patch')) {

            $data = $request->validated();

            $trk_room = TrkRoom::where('trk_id', $trk_equipment->trk_room->trk->id)
                ->where('building_id', $data['building_id'])
                ->where('floor_id', $data['floor_id'])
                ->where('room_id', $data['room_id'])
                ->first();

            if (empty($trk_room->id)) {
                return redirect()->back()->with('error', 'Такого помещения не существует');
            }

            $new_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                ->where('equipment_name_id', $data['equipment_name_id'])
                ->first();

            if (empty($new_equipment->id)) {
                return redirect()->back()->with('error', 'Такого оборудования не существует');
            }

            try {

                DB::beginTransaction();

                foreach ($trk_equipment->users as $equipment_user) {
                    if (
                        !EquipmentUser::where('equipment_id', $new_equipment->id)
                            ->where('trk_room_id', $equipment_user->trk_room->id)
                            ->exists()
                    ) {
                        $equipment_user->update([
                            'equipment_id' => $new_equipment->id,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                EquipmentUser::where('equipment_id', $trk_equipment->id)->delete();

                DB::commit();

                return redirect()->back()->with('success', 'Изменения сохранены');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);
            }

        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipmentUser $equipment_user): RedirectResponse
    {
        Log::info('User try to delete equipment_user',
            [
                'user' => Auth::user()->name,
                'equipment_user' => $equipment_user,
            ]);

        try {

            $trk_equipment = TrkEquipment::find($equipment_user->equipment_id);

            $equipment_user->update([
                'destroyer_id' => Auth::id(),
            ]);
            $equipment_user->forceDelete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }

        return redirect()->route('trk_equipments.show', $trk_equipment)->with('success', 'Данные удалены');

    }
}
