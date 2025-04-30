<?php

namespace App\Http\Controllers\Backend\RenterTrkRoomBrands;

use App\Exports\Renters\RentersExport;
use App\Exports\Renters\RentersExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\RenterTrkRoomBrands\RenterTrkRoomBrandFilter;
use App\Http\Requests\RenterTrkRoomBrands\RenterTrkRoomBrandFilterRequest;
use App\Http\Requests\RenterTrkRoomBrands\StoreRenterTrkRoomBrandFormRequest;
use App\Http\Requests\RenterTrkRoomBrands\StoreRenterTrkRoomBrandFromTrkRoomFormRequest;
use App\Http\Requests\RenterTrkRoomBrands\UpdateRenterTrkRoomBrandFormRequest;
use App\Models\Brands\Brand;
use App\Models\Buildings\Building;
use App\Models\Counters\TrkRoomCounter;
use App\Models\Floors\Floor;
use App\Models\Organizations\Organization;
use App\Models\RenterTrkRoomBrands\RenterTrkRoomBrand;
use App\Models\Rooms\Room;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RenterTrkRoomBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(RenterTrkRoomBrand::class, 'renter_trk_room_brand');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RenterTrkRoomBrandFilterRequest $request): Response
    {

        $data = $request->validated();

        $user_trks = UserResponsibilityTrkSystem::where('user_id', Auth::id())
            ->pluck('trk_id')
            ->toArray();

        $all_trks = Trk::orderBy('sort_order')->get();

        if (count($user_trks) > 0 && !auth()->user()->hasRole('sadmin')) {
            $data['trk_ids'] = $user_trks;
            $all_trks = Trk::whereIn('id', $user_trks)->get();
        }

        $filter = app()->make(RenterTrkRoomBrandFilter::class, ['queryParams' => array_filter($data)]);

        $renter_trk_room_brands = RenterTrkRoomBrand::filter($filter)
            ->with(['trk_room', 'organization', 'brand', 'floor'])
            ->select([
                'trk_rooms.*',
                'floors.name',
                'trks.sort_order',
                'rooms.name',
                'trk_room_renters.*'
            ])
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_room_renters.trk_room_id')
            ->join('floors', 'floors.id', '=', 'trk_rooms.floor_id')
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->join('rooms', 'rooms.id', '=', 'trk_rooms.room_id')
            ->orderBy('floors.name', 'asc')
            ->orderBy('trks.sort_order', 'asc')
            ->orderBy('rooms.name', 'asc')
            ->paginate(config('backend.renter_trk_room_brands.pagination'));

        return \response()->view('backend.renter_trk_room_brands.index', [
            'renter_trk_room_brands' => $renter_trk_room_brands,
            'all_trks' => $all_trks,
            'all_brands' => Brand::orderBy('name')->get(),
            'all_organizations' => Organization::orderBy('name')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        return \response()->view('backend.renter_trk_room_brands.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'all_buildings' => Building::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRenterTrkRoomBrandFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {

            Log::info('User try to store renter_trk_room_brand.',
                [
                    'user_id' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                $brand = Brand::where('name', $data['brand_name'])->first();

                if (empty($brand)) {
                    return redirect()->back()->with('error', 'Нет такого бренда ' . $data['brand_name'] . '. Добавьте в разделе Архитектура/Бренды.');
                }

                $organization = Organization::where('name', $data['organization_name'])->first();

                if (empty($organization)) {
                    return redirect()->back()->with('error', 'Нет такой организации ' . $data['organization_name'] . '. Добавьте в разделе Архитектура/Организации.');
                }

                $trk_room_id = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->where('room_id', $data['room_id'])
                    ->pluck('id')
                    ->first();

                if (empty($trk_room_id)) {
                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                if (
                    !empty($trk_room_id) &&
                    RenterTrkRoomBrand::where('trk_room_id', $trk_room_id)
                        ->where('brand_id', $brand->id)
                        ->where('organization_id', $organization->id)
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такая арендатор уже есть.')->withInput();
                }

                if (!empty($trk_room_id)) {
                    RenterTrkRoomBrand::withTrashed()->updateOrCreate([
                        'trk_room_id' => $trk_room_id,
                        'brand_id' => $brand->id,
                        'organization_id' => $organization->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ])->restore();

                    return redirect()->route('renter_trk_room_brands.index')->with('success', 'Данные сохранены.');
                }

            } catch (\Exception $e) {

                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }

        }

        return redirect()->back()->with('error', 'Данные не сохранены.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_trk_room(TrkRoom $trk_room): Response
    {
        $this->authorize('create');

        return \response()->view('backend.renter_trk_room_brands.create_from_trk_room', [
            'trk_room' => $trk_room,
            'brands' => Brand::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_trk_room(StoreRenterTrkRoomBrandFromTrkRoomFormRequest $request): RedirectResponse
    {
        $this->authorize('create');

        if ($request->isMethod('post')) {
            Log::info('User try to store trk renter from trk room',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $not_exists_room = '';

                $trk_room = TrkRoom::find($data['trk_room_id']);

                $organization = Organization::where('name', $data['organization_name'])->first();

                if (empty($organization->id)) {
                    return redirect()->back()->with('error', 'Нет такой организации в базе. Создайте ее через раздел Архитектура.')->withInput();
                }

                if (!empty($trk_room)) {

                    RenterTrkRoomBrand::withTrashed()
                        ->where('trk_room_id', $trk_room->id)
                        ->where('brand_id', $data['brand_id'])
                        ->where('organization_id', $organization->id)
                        ->restore();

                    RenterTrkRoomBrand::updateOrCreate([
                        'trk_room_id' => $trk_room->id,
                        'brand_id' => $data['brand_id'],
                        'organization_id' => $organization->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }


                $message = '';

                if (strlen($not_exists_room) > 0) {
                    $message .= 'Помещения: ' . $not_exists_room . ' отсутствуют на этом ТРК. Создайте их в разделе ТРК/Помещения. ';
                }

                if (strlen($message) > 0) {
                    return redirect()->route('trk_room.show', $data['trk_room_id'])->with('alert', 'Сохранена часть. ' . $message);
                }

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
    public function show(RenterTrkRoomBrand $renter_trk_room_brand): Response
    {

        $organization = $renter_trk_room_brand->organization->id ?? null;

        $counters = TrkRoomCounter::where('trk_id', $renter_trk_room_brand->trk_room->trk->id)
            ->where('brand_id', $renter_trk_room_brand->brand->id)
            ->where('organization_id', $organization)
            ->get();

        return \response()->view('backend.renter_trk_room_brands.show', [
            'renter_trk_room_brand' => $renter_trk_room_brand,
            'counters' => $counters ?? null,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RenterTrkRoomBrand $renter_trk_room_brand): Response
    {
        Log::info('User try to edit renter_trk_room_brand.',
            [
                'user' => Auth::user()->name,
                'renter_trk_room_brand' => $renter_trk_room_brand,
            ]
        );

        return \response()->view('backend.renter_trk_room_brands.edit', [
            'renter_trk_room_brand' => $renter_trk_room_brand,
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'all_buildings' => Building::orderBy('name')->get(),
            'all_floors' => Floor::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRenterTrkRoomBrandFormRequest $request, RenterTrkRoomBrand $renter_trk_room_brand): RedirectResponse
    {
        if ($request->isMethod('patch')) {

            Log::info('User try to update renter_trk_room_brand.',
                [
                    'user' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                $brand = Brand::where('name', $data['brand_name'])->first();

                if (empty($brand)) {
                    return redirect()->back()->with('error', 'Нет такого бренда ' . $data['brand_name'] . '. Добавьте в разделе Аренда/Бренды.');
                }

                $organization = Organization::where('name', $data['organization_name'])->first();

                if (empty($organization)) {
                    return redirect()->back()->with('error', 'Нет такой организации ' . $data['organization_name'] . '. Добавьте в разделе Аренда/Организации.');
                }

                $trk_room_id = TrkRoom::where('trk_id', $data['trk_id'])
                    ->where('room_id', $data['room_id'])
                    ->where('building_id', $data['building_id'])
                    ->where('floor_id', $data['floor_id'])
                    ->pluck('id')
                    ->first();


                if (empty($trk_room_id)) {
                    $trk = Trk::find($data['trk_id']);
                    $building = Building::find($data['building_id']);
                    $floor = Floor::find($data['floor_id']);
                    $room = Room::find($data['room_id']);

                    return redirect()->back()->with('error', 'Нет такого помещения: ' . $trk->name . ', ' . $building->name . ', ' . $floor->name . ', ' . $room->name)->withInput();
                }

                if (
                    !empty($trk_room_id) &&
                    RenterTrkRoomBrand::where('trk_room_id', $trk_room_id)
                        ->where('brand_id', $brand->id)
                        ->where('organization_id', $organization->id)
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такая запись уже есть.')->withInput();
                }

                $renter_trk_room_brand->update([
                    'trk_room_id' => $trk_room_id,
                    'brand_id' => $brand->id,
                    'organization_id' => $organization->id,
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('renter_trk_room_brands.show', $renter_trk_room_brand)->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }

        }

        return redirect()->back()->with('error', 'Данные не сохранены.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {

        Log::info('User try to delete renter_trk_room_brand.',
            [
                'user' => Auth::user()->name,
                'request' => $id,
            ]
        );

        $renter_trk_room_brand = RenterTrkRoomBrand::findOrFail($id);

        $renter_trk_room_brand->update([
            'destroyer_id' => Auth::id(),
        ]);

        $renter_trk_room_brand->delete();

        return redirect()->route('renter_trk_room_brands.index')->with('success', 'Данные удалены.');
    }

    public function export(RenterTrkRoomBrandFilterRequest $request)
    {
        $data = $request->validated();

        $data['floor_id'] = $data['floor_id_2'];

        $filter = app()->make(RenterTrkRoomBrandFilter::class, ['queryParams' => array_filter($data)]);

        $renters = RenterTrkRoomBrand::filter($filter)
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_room_renters.trk_room_id')
            ->join('floors', 'floors.id', '=', 'trk_rooms.floor_id')
            ->join('buildings', 'buildings.id', '=', 'trk_rooms.building_id')
            ->join('rooms', 'rooms.id', '=', 'trk_rooms.room_id')
            ->orderBy('buildings.name')
            ->orderByRaw('CONVERT(floors.name, SIGNED) asc')
            ->orderBy('rooms.name')
            ->get();

        $trk = Trk::find($data['trk_id']);

        if (count($renters) == 0) {

            return redirect()->back()->with('error', 'Нет таких арендаторов');
        }

        switch ($data['file_type']) {
            case '.pdf':

                return (new RentersExportPdf(
                    $renters,
                    $data,
                ))->export_pdf();

            case '.html':
                return Excel::download(new RentersExport(
                    $renters,
                    $data,
                ), 'Арендаторы__' . $trk->name . '__' . date('d-m-Y') . '.html');

            default:
                return Excel::download(new RentersExport(
                    $renters,
                    $data,
                ), 'Арендаторы__' . $trk->name . '__' . date('d-m-Y') .  '.xlsx');
        }
    }
}
