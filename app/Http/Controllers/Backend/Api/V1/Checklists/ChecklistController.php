<?php

namespace App\Http\Controllers\Backend\Api\V1\Checklists;

use App\Http\Controllers\Controller;
use App\Models\Buildings\Building;
use App\Models\EquipmentUsers\EquipmentUser;
use App\Models\Floors\Floor;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use Illuminate\Http\Request;
use App\Models\Rooms\Room;

class ChecklistController extends Controller
{
    public function fetch_room_air_sources_by_trk(Request $request)
    {

        $trk_building_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $data['buildings'] = Building::whereIn("id", $trk_building_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $first_building = $data['buildings'][0];

        $trk_floor_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $first_building->id)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $data['floors'] = Floor::whereIn("id", $trk_floor_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $first_floor = $data['floors'][0];

        $trk_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $first_building->id)
            ->where('floor_id', $first_floor->id)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $trk_room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $first_room = $data['rooms'][0];

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $first_building->id)
            ->where('floor_id', $first_floor->id)
            ->where('room_id', $first_room->id)
            ->pluck('id')
            ->toArray();

        $equipment_user_ids = EquipmentUser::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->pluck('equipment_id')
            ->toArray();

        $data['equipments'] = TrkEquipment::select('trk_equipments.*', 'equipment_names.name')
            ->whereIn('trk_equipments.id', $equipment_user_ids)
            ->where('system_id', $request->system_id)
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->get();

        return response()->json($data);
    }

    public function fetch_room_air_sources_by_building(Request $request)
    {

        $trk_building_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $data['buildings'] = Building::whereIn("id", $trk_building_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $trk_floor_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->groupBy('floor_id')
            ->pluck('floor_id')
            ->toArray();

        $data['floors'] = Floor::whereIn("id", $trk_floor_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $first_floor = $data['floors'][0];

        $trk_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $first_floor->id)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $trk_room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $first_room = $data['rooms'][0];

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $first_floor->id)
            ->where('room_id', $first_room->id)
            ->pluck('id')
            ->toArray();

        $equipment_user_ids = EquipmentUser::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->pluck('equipment_id')
            ->toArray();

        $data['equipments'] = TrkEquipment::select('trk_equipments.*', 'equipment_names.name')
            ->whereIn('trk_equipments.id', $equipment_user_ids)
            ->where('system_id', $request->system_id)
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->get();

        return response()->json($data);
    }

    public function fetch_room_air_sources_by_floor(Request $request)
    {

        $trk_building_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->groupBy('building_id')
            ->pluck('building_id')
            ->toArray();

        $data['buildings'] = Building::whereIn("id", $trk_building_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

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
            ->where('floor_id', $request->floor_id)
            ->groupBy('room_id')
            ->pluck('room_id')
            ->toArray();

        $data['rooms'] = Room::whereIn("id", $trk_room_ids)
            ->orderBy('name')
            ->get(["name", "id"]);

        $first_room = $data['rooms'][0];

        $trk_equipment_room_ids = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->where('room_id', $first_room->id)
            ->pluck('id')
            ->toArray();

        $equipment_user_ids = EquipmentUser::whereIn('trk_room_id', $trk_equipment_room_ids)
            ->pluck('equipment_id')
            ->toArray();

        $data['equipments'] = TrkEquipment::select('trk_equipments.*', 'equipment_names.name')
            ->whereIn('trk_equipments.id', $equipment_user_ids)
            ->where('system_id', $request->system_id)
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->get();

        return response()->json($data);
    }

    public function fetch_room_air_sources_by_room(Request $request)
    {

        $trk_room = TrkRoom::where('trk_id', $request->trk_id)
            ->where('building_id', $request->building_id)
            ->where('floor_id', $request->floor_id)
            ->where('room_id', $request->room_id)
            ->first();

        $equipment_user_ids = EquipmentUser::where('trk_room_id', $trk_room->id)
            ->pluck('equipment_id')
            ->toArray();

        $data['equipments'] = TrkEquipment::select('trk_equipments.*', 'equipment_names.name')
            ->whereIn('trk_equipments.id', $equipment_user_ids)
            ->where('system_id', $request->system_id)
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->get();

        return response()->json($data);
    }
}
