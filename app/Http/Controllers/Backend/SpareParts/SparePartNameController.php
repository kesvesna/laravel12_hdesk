<?php

namespace App\Http\Controllers\Backend\SpareParts;

use App\Http\Controllers\Controller;
use App\Http\Filters\SparePartNames\SparePartNameFilter;
use App\Http\Requests\SparePartNames\SparePartNameFilterRequest;
use App\Http\Requests\SparePartNames\StoreSparePartNameFormRequest;
use App\Http\Requests\SparePartNames\UpdateSparePartNameFormRequest;
use App\Models\SpareParts\SparePartName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SparePartNameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(SparePartName::class, 'spare_part_name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SparePartNameFilterRequest $request): Response
    {
        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(SparePartNameFilter::class, ['queryParams' => array_filter($data)]);

        $spare_part_names = SparePartName::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.spare_part_names.pagination'));


        return \response()->view('backend.spare_part_names.index', [
            'spare_part_names' => $spare_part_names,
            'old_filters' => $data,
            'all_spare_part_names' => SparePartName::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.spare_part_names.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSparePartNameFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new spare part name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                SparePartName::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('spare_part_names.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SparePartName $spare_part_name): Response
    {
        return \response()->view('backend.spare_part_names.show', [
            'spare_part_name' => $spare_part_name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SparePartName $spare_part_name): Response
    {
        return \response()->view('backend.spare_part_names.edit', [
            'spare_part_name' => $spare_part_name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSparePartNameFormRequest $request, SparePartName $spare_part_name): RedirectResponse
    {
        Log::info('User try to update spare part name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $spare_part_name->update([
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
    public function destroy(SparePartName $spare_part_name): RedirectResponse
    {
        Log::info('User try to delete new brand',
            [
                'user' => Auth::user()->name,
                'spare_part_name' => $spare_part_name,
            ]);

        try {
            $spare_part_name->update([
                'destroyer_id' => Auth::id(),
            ]);
            $spare_part_name->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('spare_part_names.index')->with('success', 'Данные удалены');
    }
}
