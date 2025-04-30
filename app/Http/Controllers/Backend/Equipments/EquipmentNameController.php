<?php

namespace App\Http\Controllers\Backend\Equipments;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentNames\EquipmentNameFilter;
use App\Http\Requests\EquipmentNames\EquipmentNameFilterRequest;
use App\Http\Requests\EquipmentNames\StoreEquipmentNameFormRequest;
use App\Http\Requests\EquipmentNames\UpdateEquipmentNameFormRequest;
use App\Models\Equipments\EquipmentName;
use App\Models\TrkEquipments\TrkEquipment;
use App\Services\Languages\LanguageCheckService\LanguageCheckService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class EquipmentNameController extends Controller
{
    public function __construct(LanguageCheckService $languageCheckService)
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(EquipmentName::class, 'equipment_name');
        $this->languageCheckService = $languageCheckService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EquipmentNameFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(EquipmentNameFilter::class, ['queryParams' => array_filter($data)]);

        $equipment_names = EquipmentName::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.equipment_names.pagination'));

        return \response()->view('backend.equipment_names.index', [
            'equipment_names' => $equipment_names,
            'all_equipments' => EquipmentName::orderBy('created_at', 'desc')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.equipment_names.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipmentNameFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new equipment name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {

            try {

                $data = $request->validated();

                if(!EquipmentName::withTrashed()->where('name', $data['name'])->restore())
                {
                    EquipmentName::create([
                        'name' => $data['name'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                return redirect()->route('equipment_names.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(EquipmentName $equipment_name): Response
    {
        $trk_equipments = TrkEquipment::where('equipment_name_id', $equipment_name->id)->get();

        return \response()->view('backend.equipment_names.show', [
            'equipment_name' => $equipment_name,
            'trk_equipments' => $trk_equipments,
            'equipment_names' => EquipmentName::orderBy('name')->get(),
            'language' => $this->languageCheckService->check_language($equipment_name->name),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentName $equipment_name): Response
    {
        return \response()->view('backend.equipment_names.edit', [
            'equipment_name' => $equipment_name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentNameFormRequest $request, EquipmentName $equipmentName): RedirectResponse
    {
        Log::info('User try to update equipment name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $equipmentName->update([
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
    public function destroy(EquipmentName $equipmentName): RedirectResponse
    {
        Log::info('User try to delete new brand',
            [
                'user' => Auth::user()->name,
                'equipment_name' => $equipmentName,
            ]);

        try {

            $equipmentName->update([
                'destroyer_id' => Auth::id(),
            ]);

            $equipmentName->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('equipment_names.index')->with('success', 'Данные удалены');
    }
}
