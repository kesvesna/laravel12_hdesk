<?php

namespace App\Http\Controllers\Backend\StoreHouses;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHouseNames\StoreStoreHouseNameFormRequest;
use App\Http\Requests\StoreHouseNames\UpdateStoreHouseNameFormRequest;
use App\Models\StoreHouses\StoreHouseName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreHouseNameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(StoreHouseName::class, 'store_house_name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $store_house_names = StoreHouseName::orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.store_house_names.pagination'));

        return \response()->view('backend.store_house_names.index', [
            'store_house_names' => $store_house_names,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.store_house_names.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreHouseNameFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new storehouse name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                StoreHouseName::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('store_house_names.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreHouseName $store_house_name): Response
    {
        return \response()->view('backend.store_house_names.show', [
            'store_house_name' => $store_house_name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StoreHouseName $store_house_name): Response
    {
        return \response()->view('backend.store_house_names.edit', [
            'store_house_name' => $store_house_name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreHouseNameFormRequest $request, StoreHouseName $store_house_name): RedirectResponse
    {
        Log::info('User try to update storehouse name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $store_house_name->update([
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
    public function destroy(StoreHouseName $store_house_name): RedirectResponse
    {
        Log::info('User try to delete new brand',
            [
                'user' => Auth::user()->name,
                'store_house_name' => $store_house_name,
            ]);

        try {
            $store_house_name->update([
                'destroyer_id' => Auth::id(),
            ]);
            $store_house_name->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('store_house_names.index')->with('success', 'Данные удалены');
    }
}
