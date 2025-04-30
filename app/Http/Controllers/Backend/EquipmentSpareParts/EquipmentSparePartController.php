<?php

namespace App\Http\Controllers\Backend\EquipmentSpareParts;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentSpareParts\EquipmentSparePartFilter;
use App\Http\Requests\EquipmentSpareParts\EquipmentSparePartFilterRequest;
use App\Http\Requests\EquipmentSpareParts\SetSparePartsToAnotherEquipmentFormRequest;
use App\Http\Requests\EquipmentSpareParts\StoreEquipmentSparePartFormRequest;
use App\Http\Requests\EquipmentSpareParts\UpdateEquipmentSparePartFormRequest;
use App\Models\EquipmentSpareParts\EquipmentSparePart;
use App\Models\SpareParts\SparePartName;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use App\Models\TrkStoreHouses\TrkStoreHouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentSparePartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EquipmentSparePartFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(EquipmentSparePartFilter::class, ['queryParams' => array_filter($data)]);

        $equipment_spare_parts = EquipmentSparePart::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.equipment_spare_parts.pagination'));

        $spare_part_names = SparePartName::orderBy('created_at', 'desc')
            ->orderBy('name')->get();

        return \response()->view('backend.equipment_spare_parts.index', [
            'equipment_spare_parts' => $equipment_spare_parts,
            'spare_part_names' => $spare_part_names,
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_equipment(TrkEquipment $trk_equipment): Response
    {
        return \response()->view('backend.equipment_spare_parts.create_from_equipment', [
            'trk_equipment' => $trk_equipment,
            'spare_parts' => SparePartName::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_equipment(StoreEquipmentSparePartFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store equipment spare part from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                if (
                    EquipmentSparePart::where('equipment_id', $data['equipment_id'])
                        ->where('spare_part_id', $data['spare_part_name_id'])
                        ->where('model', $data['model'])
                        ->where('value', $data['value'])
                        ->where('comment', $data['comment'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Для этого оборудования такая запчасть уже есть. Пользуйтесь редактированием.');
                }

                EquipmentSparePart::create([
                    'equipment_id' => $data['equipment_id'],
                    'spare_part_id' => $data['spare_part_name_id'],
                    'model' => $data['model'],
                    'value' => $data['value'],
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
     * Display the specified resource.
     */
    public function show(EquipmentSparePart $equipment_spare_part): Response
    {
       $trk_store_houses = collect();

       if($equipment_spare_part->model != '')
       {
           $trk_store_houses = TrkStoreHouse::where('spare_part_model', 'like', '%' . $equipment_spare_part->model . '%')
               ->where('user_division_id', Auth::user()->user_division_id)
               ->get();
       }

        return \response()->view('backend.equipment_spare_parts.show', [
            'equipment_spare_part' => $equipment_spare_part,
            'trk_store_houses' => $trk_store_houses,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentSparePart $equipment_spare_part): Response
    {
        $trk_equipment = TrkEquipment::find($equipment_spare_part->equipment_id);

        return \response()->view('backend.equipment_spare_parts.edit', [
            'equipment_spare_part' => $equipment_spare_part,
            'spare_parts' => SparePartName::orderBy('name')->get(),
            'trk_equipment' => $trk_equipment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentSparePartFormRequest $request, EquipmentSparePart $equipment_spare_part): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update equipment spare part from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'equipment_spare_part' => $equipment_spare_part,
                ]);

            try {

                $data = $request->validated();

                $equipment_spare_part->update([
                    'equipment_id' => $data['equipment_id'],
                    'spare_part_id' => $data['spare_part_name_id'],
                    'model' => $data['model'],
                    'value' => $data['value'],
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
    public function set_spare_parts_from_this_to_another_equipment(SetSparePartsToAnotherEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
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
                ->where('system_id', $data['system_id'])
                ->first();

            if (empty($new_equipment->id)) {
                return redirect()->back()->with('error', 'Такого оборудования не существует');
            }

            try {

                DB::beginTransaction();

                foreach ($trk_equipment->spare_parts as $spare_part) {
                    $spare_part->update([
                        'equipment_id' => $new_equipment->id,
                        'last_editor_id' => Auth::id(),
                    ]);
                }

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
    public function destroy(EquipmentSparePart $equipment_spare_part): RedirectResponse
    {
        Log::info('User try to delete equipment_spare_part',
            [
                'user' => Auth::user()->name,
                'equipment_spare_part' => $equipment_spare_part,
            ]);

        try {

            $trk_equipment = TrkEquipment::find($equipment_spare_part->equipment_id);

            $equipment_spare_part->update([
                'destroyer_id' => Auth::id(),
            ]);
            $equipment_spare_part->forceDelete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }

        return redirect()->route('trk_equipments.show', $trk_equipment)->with('success', 'Данные удалены');
    }
}
