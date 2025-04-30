<?php

namespace App\Http\Controllers\Backend\Dropdowns;

use App\Http\Controllers\Controller;
use App\Models\Buildings\Building;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentUsers\EquipmentUser;
use App\Models\Floors\Floor;
use App\Models\Systems\System;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use App\Models\UserDivisionFunctions\UserDivisionFunction;
use App\Models\UserFunctions\UserFunction;
use App\Providers\TrkEquipments\TrkEquipmentProvider;
use App\Providers\TrkRooms\TrkRoomProvider;
use Illuminate\Http\Request;
use App\Models\Rooms\Room;

class DropdownController extends Controller
{

    public function __construct(TrkRoomProvider $trkRoomProvider, TrkEquipmentProvider $trkEquipmentProvider)
    {
        $this->trkRoomProvider = $trkRoomProvider;
        $this->trkEquipmentProvider = $trkEquipmentProvider;
    }

    public function fetch_rooms(Request $request)
    {

        $trk_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->pluck('id')
            ->toArray();

        $trk_room_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
            ->where('system_id', $request->system_id)
            ->pluck('trk_room_id')
            ->toArray();

        $room_ids = TrkRoom::whereIn("id", $trk_room_equipment_ids)
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $building_ids = TrkRoom::whereIn("id", $trk_room_equipment_ids)
            ->pluck('building_id')
            ->toArray();

        $data['buildings'] = Building::whereIn("id", $building_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $floor_ids = TrkRoom::whereIn("id", $trk_room_equipment_ids)
            ->pluck('floor_id')
            ->toArray();

        $data['floors'] = Floor::whereIn("id", $floor_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_system_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
            ->groupBy('system_id')
            ->pluck('system_id')
            ->toArray();

        $data['systems'] = System::whereIn('id', $trk_system_ids)->orderBy('name')->get(["name", "id"]);

        $trk_first_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('room_id', $data['rooms'][0]['id'])
            ->pluck('id')
            ->toArray();

        $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_first_room_ids)
            ->where('system_id', $request->system_id)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $trk_equipment_ids)->orderBy('name')->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_equipments(Request $request)
    {

        $trk_building_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $data['buildings'] = Building::whereIn("id", $trk_building_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_floor_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $data['floors'] = Floor::whereIn("id", $trk_floor_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $trk_room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->where('room_id', $request->room_id)
            ->pluck('id')
            ->toArray();

        $system_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->where('system_id', $request->system_id)
            ->pluck('equipment_name_id')
            ->toArray();

        $equipment_name_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->where('system_id', $request->system_id)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $equipment_name_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_buildings(Request $request)
    {

        $trk_building_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $data['buildings'] = Building::whereIn("id", $trk_building_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_floor_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->whereIn('building_id', $trk_building_ids)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $data['floors'] = Floor::whereIn("id", $trk_floor_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->whereIn('building_id', $trk_building_ids)
            ->whereIn('floor_id', $trk_floor_ids)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $trk_room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->whereIn('room_id', $trk_room_ids)
            ->pluck('id')
            ->toArray();

        $system_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->groupBy('system_id')
            ->pluck('system_id')
            ->toArray();

        $data['systems'] = System::whereIn("id", $system_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $equipment_name_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $equipment_name_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_floors(Request $request)
    {

        $trk_floor_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $data['floors'] = Floor::whereIn("id", $trk_floor_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->whereIn('floor_id', $trk_floor_ids)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $trk_room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->whereIn('room_id', $trk_room_ids)
            ->pluck('id')
            ->toArray();

        $system_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->groupBy('system_id')
            ->pluck('system_id')
            ->toArray();

        $data['systems'] = System::whereIn("id", $system_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $equipment_name_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $equipment_name_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_rooms_by_floor(Request $request)
    {
        $trk_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $trk_room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->whereIn('room_id', $trk_room_ids)
            ->pluck('id')
            ->toArray();

        $system_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->groupBy('system_id')
            ->pluck('system_id')
            ->toArray();

        $data['systems'] = System::whereIn("id", $system_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $equipment_name_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $equipment_name_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_equipments_by_room(Request $request)
    {

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->where('room_id', $request->room_id)
            ->pluck('id')
            ->toArray();

        $system_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->groupBy('system_id')
            ->pluck('system_id')
            ->toArray();

        $data['systems'] = System::whereIn("id", $system_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $equipment_name_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $equipment_name_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_equipments_by_system(Request $request)
    {
        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->where('room_id', $request->room_id)
            ->pluck('id')
            ->toArray();

        $equipment_name_ids = TrkEquipment::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->where('system_id', $request->system_id)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $equipment_name_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_room_air_sources(Request $request)
    {

        $trk_room = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->where('room_id', $request->room_id)
            ->first();

        $equipment_ids = EquipmentUser::where('trk_room_id', $trk_room->id)
            ->pluck('equipment_id')
            ->toArray();

        $trk_equipment_ids = TrkEquipment::whereIn('id', $equipment_ids)
            ->where('system_id', $request->system_id)
            ->pluck('equipment_name_id')
            ->toArray();

        $data['equipment_names'] = EquipmentName::whereIn('id', $trk_equipment_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function fetch_functions(Request $request)
    {

        $function_ids = UserDivisionFunction::where("user_division_id", $request->user_division_id)->pluck('user_function_id')->toArray();

        $data['functions'] = UserFunction::whereIn("id", $function_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        return response()->json($data);
    }
}
