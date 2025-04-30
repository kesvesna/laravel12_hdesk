<?php

namespace App\Http\Controllers\Backend\EquipmentStatuses;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentStatuses\EquipmentStatusFilter;
use App\Http\Requests\EquipmentStatuses\EquipmentStatusFilterRequest;
use App\Http\Requests\EquipmentStatuses\StoreEquipmentStatusFormRequest;
use App\Http\Requests\EquipmentStatuses\UpdateEquipmentStatusFormRequest;
use App\Models\EquipmentStatuses\EquipmentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EquipmentStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(EquipmentStatus::class, 'equipment_status');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EquipmentStatusFilterRequest $request): Response
    {
        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(EquipmentStatusFilter::class, ['queryParams' => array_filter($data)]);

        $equipment_statuses = EquipmentStatus::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.equipment_statuses.pagination'));

        return \response()->view('backend.equipment_statuses.index', [
            'equipment_statuses' => $equipment_statuses,
            'all_equipment_statuses' => EquipmentStatus::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.equipment_statuses.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipmentStatusFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new equipment status',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                EquipmentStatus::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('equipment_statuses.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipmentStatus $equipment_status): Response
    {
        return \response()->view('backend.equipment_statuses.show', [
            'equipment_status' => $equipment_status,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipmentStatus $equipment_status): Response
    {
        return \response()->view('backend.equipment_statuses.edit', [
            'equipment_status' => $equipment_status,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentStatusFormRequest $request, EquipmentStatus $equipment_status): RedirectResponse
    {
        Log::info('User try to update equipment status',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $equipment_status->update([
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
    public function destroy(EquipmentStatus $equipment_status): RedirectResponse
    {
        Log::info('User try to delete equipment_status',
            [
                'user' => Auth::user()->name,
                'equipment_status' => $equipment_status,
            ]);

        try {
            $equipment_status->update([
                'destroyer_id' => Auth::id(),
            ]);
            $equipment_status->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('equipment_statuses.index')->with('success', 'Данные удалены');
    }
}
