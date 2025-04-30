<?php

namespace App\Http\Controllers\Backend\EquipmentParameters;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentParameters\EquipmentParameterFilter;
use App\Http\Requests\EquipmentParameters\EquipmentParameterFilterRequest;
use App\Http\Requests\EquipmentParameters\SetParametersToAnotherEquipmentFormRequest;
use App\Http\Requests\EquipmentParameters\StoreEquipmentParameterFormRequest;
use App\Http\Requests\EquipmentParameters\UpdateEquipmentParameterFormRequest;
use App\Models\EquipmentParameters\EquipmentParameter;
use App\Models\ParameterNames\ParameterName;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EquipmentParameterFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(EquipmentParameterFilter::class, ['queryParams' => array_filter($data)]);

        $equipment_parameters = EquipmentParameter::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.equipment_parameters.pagination'));

        return \response()->view('backend.equipment_parameters.index', [
            'equipment_parameters' => $equipment_parameters,
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

        return \response()->view('backend.equipment_parameters.create_from_equipment', [
            'trk_equipment' => $trk_equipment,
            'parameter_names' => ParameterName::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_equipment(StoreEquipmentParameterFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store parameter from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $parameter_name = ParameterName::where('name', $data['parameter_name'])->first();

                if (empty($parameter_name->id)) {
                    return redirect()->back()->with('error', 'Нет такого названия параметра, создайте его.');
                }

                if (
                    EquipmentParameter::where('equipment_id', $data['equipment_id'])
                        ->where('parameter_name_id', $parameter_name->id)
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Для этого оборудования такой параметр уже есть. Пользуйтесь редактированием.');
                }

                EquipmentParameter::create([
                    'equipment_id' => $data['equipment_id'],
                    'parameter_name_id' => $parameter_name->id,
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
    public function show(EquipmentParameter $equipment_parameter): Response
    {
        return \response()->view('backend.equipment_parameters.show', [
            'equipment_parameter' => $equipment_parameter,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentParameter $equipment_parameter): Response
    {
        return \response()->view('backend.equipment_parameters.edit', [
            'equipment_parameter' => $equipment_parameter,
            'parameter_names' => ParameterName::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentParameterFormRequest $request, EquipmentParameter $equipment_parameter): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update equipment_parameter from equipment',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'equipment_parameter' => $equipment_parameter,
                ]);

            try {

                $data = $request->validated();

                $parameter_name = ParameterName::where('name', $data['parameter_name'])->first();

                if (empty($parameter_name->id)) {
                    return redirect()->back()->with('error', 'Нет такого названия параметра, создайте его.');
                }

                $equipment_parameter->update([
                    'equipment_id' => $data['equipment_id'],
                    'parameter_name_id' => $parameter_name->id,
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
    public function set_parameters_from_this_to_another_equipment(SetParametersToAnotherEquipmentFormRequest $request, TrkEquipment $trk_equipment): RedirectResponse
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

                foreach ($trk_equipment->parameters as $parameter) {
                    if (
                        !EquipmentParameter::where('equipment_id', $new_equipment->id)
                            ->where('parameter_name_id', $parameter->parameter_name->id)
                            ->exists()
                    ) {
                        $parameter->update([
                            'equipment_id' => $new_equipment->id,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                EquipmentParameter::where('equipment_id', $trk_equipment->id)->delete();

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
    public function destroy(EquipmentParameter $equipment_parameter): RedirectResponse
    {
        Log::info('User try to delete equipment_parameter',
            [
                'user' => Auth::user()->name,
                'equipment_parameter' => $equipment_parameter,
            ]);

        try {

            $trk_equipment = TrkEquipment::find($equipment_parameter->equipment_id);

            $equipment_parameter->update([
                'destroyer_id' => Auth::id(),
            ]);
            $equipment_parameter->forceDelete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }

        return redirect()->route('trk_equipments.show', $trk_equipment)->with('success', 'Данные удалены');

    }
}
