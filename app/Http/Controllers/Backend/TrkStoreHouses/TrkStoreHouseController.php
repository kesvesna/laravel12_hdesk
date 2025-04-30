<?php

namespace App\Http\Controllers\Backend\TrkStoreHouses;

use App\Http\Controllers\Controller;
use App\Http\Filters\TrkStoreHouses\TrkStoreHouseFilter;
use App\Http\Requests\TrkStoreHouses\StoreTrkStoreHouseFormRequest;
use App\Http\Requests\TrkStoreHouses\TrkStoreHouseFilterRequest;
use App\Http\Requests\TrkStoreHouses\UpdateTrkStoreHouseFormRequest;
use App\Models\EquipmentSpareParts\EquipmentSparePart;
use App\Models\SpareParts\SparePartName;
use App\Models\StoreHouses\StoreHouseName;
use App\Models\Trks\Trk;
use App\Models\TrkStoreHouses\TrkStoreHouse;
use App\Models\TrkStoreHouses\TrkStoreHouseUser;
use App\Models\User;
use App\Services\Profiles\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrkStoreHouseController extends Controller
{
    public function __construct(ProfileService $profileService)
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->profileService = $profileService;
        $this->authorizeResource(TrkStoreHouse::class, 'trk_store_house');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TrkStoreHouseFilterRequest $request): Response
    {

        $data = $request->validated();

        $filter = app()->make(TrkStoreHouseFilter::class, ['queryParams' => array_filter($data)]);

        $user_trk_store_houses = TrkStoreHouseUser::where('user_id', Auth::id())
            ->where('division_id', Auth::user()->user_division_id)
            ->orWhere('author_id', Auth::id())
            ->get();

        $trk_store_house_ids = '';

        foreach ($user_trk_store_houses as $user_trk_store_house) {

            $ids = TrkStoreHouse::where('trk_id', $user_trk_store_house->trk_id)
                ->where('store_house_name_id', $user_trk_store_house->store_id)
                ->pluck('id')
                ->toArray();

            $ids = implode("', '", $ids);
            $trk_store_house_ids .= $ids;
        }

        $trk_store_houses = TrkStoreHouse::filter($filter)
            ->where(function ($query) use ($trk_store_house_ids) {
                $query->whereRaw("id in ('$trk_store_house_ids')")
                    ->orWhere('author_id', Auth::id());
            })
            ->with(['trk'])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.trk_store_houses.pagination'));

        $trks = Trk::orderBy('sort_order')
            ->get();

        $store_house_names = StoreHouseName::orderBy('created_at', 'desc')
            ->orderBy('name')->get();

        $spare_part_names = SparePartName::orderBy('name')->get();

        $model_names = TrkStoreHouse::orderBy('spare_part_model')->pluck('spare_part_model');

        return \response()->view('backend.trk_store_houses.index', [
            'trk_store_houses' => $trk_store_houses,
            'trks' => $trks,
            'store_house_names' => $store_house_names,
            'spare_part_names' => $spare_part_names,
            'model_names' => $model_names,
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response|RedirectResponse
    {

        if (!$this->profileService->checkUserProfileComplete(User::find(Auth::id()))) {
            return redirect()->back()->with('error', 'Не хватает данных, профиль пользователя не заполнен.');
        }

        $trks = Trk::orderBy('sort_order')->get();

        $store_houses = StoreHouseName::orderBy('name')->get();

        $spare_parts = SparePartName::orderBy('name')->get();

        return \response()->view('backend.trk_store_houses.create', [
            'trks' => $trks,
            'store_houses' => $store_houses,
            'spare_parts' => $spare_parts,
            'users' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkStoreHouseFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new trk_store_house',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if (!$this->profileService->checkUserProfileComplete(User::find(Auth::id()))) {
            return redirect()->back()->with('error', 'Не хватает данных, профиль пользователя не заполнен.');
        }

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                if (
                    TrkStoreHouse::where('trk_id', $data['trk_id'])
                        ->where('store_house_name_id', $data['store_house_name_id'])
                        ->where('spare_part_name_id', $data['spare_part_name_id'])
                        ->where('spare_part_model', $data['model_name'])
                        ->where('user_division_id', Auth::user()->user_division_id)
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такая деталь, такой модели уже есть на этом складе.');
                }

                TrkStoreHouse::withTrashed()->updateOrCreate([
                    'trk_id' => $data['trk_id'],
                    'store_house_name_id' => $data['store_house_name_id'],
                    'spare_part_name_id' => $data['spare_part_name_id'],
                    'spare_part_model' => $data['model_name'],
                    'value' => $data['value'],
                    'min_required_value' => $data['min_required_value'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'comment' => $data['comment'],
                    'user_division_id' => Auth::user()->user_division_id,
                ])->restore();

                return redirect()->route('trk_store_houses.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrkStoreHouse $trk_store_house): Response
    {

        $trk_equipments_use_this = EquipmentSparePart::where('model', 'like', '%' . $trk_store_house->spare_part_model . '%')
            ->join('trk_equipments', 'trk_equipments.id', '=', 'equipment_spare_parts.equipment_id')
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_equipments.trk_room_id')
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->join('floors', 'floors.id', '=', 'trk_rooms.floor_id')
            ->join('equipment_names', 'equipment_names.id', '=', 'trk_equipments.equipment_name_id')
            ->orderBy('trks.sort_order')
            ->orderBy('floors.name')
            ->orderBy('equipment_names.name')
            ->get();

        return \response()->view('backend.trk_store_houses.show', [
            'trk_store_house' => $trk_store_house,
            'trk_equipments_use_this' => $trk_equipments_use_this,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrkStoreHouse $trk_store_house): Response|RedirectResponse
    {
        if (!$this->profileService->checkUserProfileComplete(User::find(Auth::id()))) {
            return redirect()->back()->with('error', 'Не хватает данных, профиль пользователя не заполнен.');
        }

        $trks = Trk::orderBy('sort_order')
            ->get();

        $store_house_names = StoreHouseName::orderBy('created_at', 'desc')
            ->orderBy('name')->get();

        $spare_part_names = SparePartName::orderBy('created_at', 'desc')
            ->orderBy('name')->get();

        return \response()->view('backend.trk_store_houses.edit', [
            'trk_store_house' => $trk_store_house,
            'trks' => $trks,
            'store_houses' => $store_house_names,
            'spare_parts' => $spare_part_names,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkStoreHouseFormRequest $request, TrkStoreHouse $trk_store_house): RedirectResponse
    {
        Log::info('User try to update trk_store_house',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);


        if (!$this->profileService->checkUserProfileComplete(User::find(Auth::id()))) {
            return redirect()->back()->with('error', 'Не хватает данных, профиль пользователя не заполнен.');
        }

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $trk_store_house->update([
                'trk_id' => $data['trk_id'],
                'store_house_name_id' => $data['store_house_name_id'],
                'spare_part_name_id' => $data['spare_part_name_id'],
                'spare_part_model' => $data['spare_part_model'],
                'value' => $data['value'],
                'min_required_value' => $data['min_required_value'],
                'author_id' => Auth::id(),
                'comment' => $data['comment'],
                'user_division_id' => Auth::user()->user_division_id,
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrkStoreHouse $trk_store_house): RedirectResponse
    {
        Log::info('User try to delete new brand',
            [
                'user' => Auth::user()->name,
                'trk_store_house' => $trk_store_house,
            ]);

        try {

            $trk_store_house->update([
                'destroyer_id' => Auth::id(),
            ]);

            $trk_store_house->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('trk_store_houses.index')->with('success', 'Данные удалены');
    }
}
