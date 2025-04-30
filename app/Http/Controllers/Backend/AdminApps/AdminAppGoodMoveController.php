<?php

namespace App\Http\Controllers\Backend\AdminApps;

use App\Http\Controllers\Controller;
use App\Http\Filters\AdminAppGoodMoves\AdminAppGoodMoveFilter;
use App\Http\Requests\AdminAppGoodMoves\AdminAppGoodMoveFilterRequest;
use App\Http\Requests\AdminAppGoodMoves\ApproveAdminAppGoodMoveFormRequest;
use App\Http\Requests\AdminAppGoodMoves\RejectAdminAppGoodMoveFormRequest;
use App\Http\Requests\AdminAppGoodMoves\StoreAdminAppGoodMoveFormRequest;
use App\Http\Requests\AdminAppGoodMoves\UpdateAdminAppGoodMoveFormRequest;
use App\Jobs\AdminApps\ApproveAdminAppGoodMoveEmailJob;
use App\Jobs\AdminApps\NewAdminAppGoodMoveEmailJob;
use App\Jobs\AdminApps\RejectAdminAppGoodMoveEmailJob;
use App\Models\AdminAppGoods\AdminAppGood;
use App\Models\AdminApps\AdminAppGoodMove;
use App\Models\AdminApps\AdminAppStatus;
use App\Models\Brands\Brand;
use App\Models\Rooms\Room;
use App\Models\TareTypes\TareType;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserFunctions\UserFunction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminAppGoodMoveController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(AdminAppGood::class, 'admin_app_good');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AdminAppGoodMoveFilterRequest $request): Response
    {

        //$this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(AdminAppGoodMoveFilter::class, ['queryParams' => array_filter($data)]);

        $admin_app_good_moves = AdminAppGoodMove::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.admin_app_good_moves.pagination'));


        return \response()->view('backend.admin_app_good_moves.index', [
            'admin_app_good_moves' => $admin_app_good_moves,
            'old_filters' => $data,
            'all_statuses' => AdminAppStatus::orderBy('created_at')->get(),
            'all_trks' => Trk::orderBy('sort_order')->get(),
            'all_rooms' => Room::orderBy('name')->get(),
            'all_brand_names' => Brand::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //$this->authorize('create');

        $first_trk = Trk::orderBy('sort_order')->pluck('id')->first();
        $first_trk_room_ids = TrkRoom::where('trk_id', $first_trk)->pluck('room_id')->toArray();
        $first_trk_rooms = Room::whereIn('id', $first_trk_room_ids)->orderBy('name')->get();

        return \response()->view('backend.admin_app_good_moves.create', [
            'brands' => Brand::orderBy('name')->get(),
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => $first_trk_rooms,
            'tare_types' => TareType::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminAppGoodMoveFormRequest $request): RedirectResponse
    {
        //$this->authorize('store');

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])->where('room_id', $data['room_id'])->first();

                if (empty($trk_room)) {
                    return redirect()->back()->with('error', 'Нет такого помещения на выбранном ТРК');
                }

                $brand = Brand::where('name', $data['brand_name'])->first();

                if (empty($brand)) {
                    return redirect()->back()->with('error', 'Нет такой торговой марки');
                }

                $admin_app_status = AdminAppStatus::where('name', AdminAppStatus::NEW)->first();

                if (empty($admin_app_status)) {
                    return redirect()->back()->with('error', 'Нет такого статуса для заявки');
                }

                DB::beginTransaction();

                $admin_app = AdminAppGoodMove::create([
                    'start_at' => $data['start_at'],
                    'finish_at' => $data['finish_at'],
                    'trk_room_id' => $trk_room->id,
                    'operation_type' => $data['operation_type'],
                    'organization_id' => Auth::user()->organization->id,
                    'brand_id' => $brand->id,
                    'admin_app_status_id' => $admin_app_status->id,
                    'responsible_user' => $data['responsible_user'],
                    'gate_number' => $data['gate_number'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                for ($i = 0; $i < count($data['goods']['name']); $i++) {
                    AdminAppGood::create([
                        'admin_app_good_move_id' => $admin_app->id,
                        'name' => $data['goods']['name'][$i],
                        'tare_type_id' => $data['goods']['tare_type_id'][$i],
                        'value' => $data['goods']['value'][$i],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();

                $function_ids = UserFunction::where('name', UserFunction::ADMIN_TRK)
                    ->orWhere('name', UserFunction::CHIEF_ADMIN_TRK)
                    ->pluck('id')
                    ->toArray();

                $division_ids = UserDivision::where('name', UserDivision::SA_TRK)
                    ->pluck('id')
                    ->toArray();

                $emails = User::whereIn('user_function_id', $function_ids)
                    ->whereIn('user_division_id', $division_ids)
                    ->pluck('email')
                    ->toArray();

                NewAdminAppGoodMoveEmailJob::dispatch($emails, $admin_app);

                return redirect()->route('admin_app_good_moves.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();

    }

    /**
     * Display the specified resource.
     */
    public function show(AdminAppGoodMove $admin_app_good_move): Response
    {
        //$this->authorize('view', $admin_app_good_move);

        return \response()->view('backend.admin_app_good_moves.show', [
            'admin_app_good_move' => $admin_app_good_move,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminAppGoodMove $admin_app_good_move): Response
    {
        //$this->authorize('edit', $admin_app_good_move);

        $first_trk = Trk::orderBy('sort_order')->pluck('id')->first();
        $first_trk_room_ids = TrkRoom::where('trk_id', $first_trk)->pluck('room_id')->toArray();
        $first_trk_rooms = Room::whereIn('id', $first_trk_room_ids)->orderBy('name')->get();

        return \response()->view('backend.admin_app_good_moves.edit', [
            'admin_app_good_move' => $admin_app_good_move,
            'brands' => Brand::orderBy('name')->get(),
            'trks' => Trk::orderBy('sort_order')->get(),
            'rooms' => $first_trk_rooms,
            'tare_types' => TareType::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminAppGoodMoveFormRequest $request, AdminAppGoodMove $admin_app_good_move): RedirectResponse
    {
        //$this->authorize('update', $admin_app_good_move);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
                'admin_app_good_move' => $admin_app_good_move,
                'goods' => $admin_app_good_move->goods,

            ]);

        if ($request->isMethod('patch')) {
            try {
                $data = $request->validated();
                $trk_room = TrkRoom::where('trk_id', $data['trk_id'])->where('room_id', $data['room_id'])->first();

                if (empty($trk_room)) {
                    return redirect()->back()->with('error', 'Нет такого помещения на выбранном ТРК');
                }

                $brand = Brand::where('name', $data['brand_name'])->first();

                if (empty($brand)) {
                    return redirect()->back()->with('error', 'Нет такой торговой марки');
                }

                $admin_app_status = AdminAppStatus::where('id', $admin_app_good_move->admin_app_status_id)->first();

                if (empty($admin_app_status)) {
                    return redirect()->back()->with('error', 'Нет такого статуса для заявки');
                }

                DB::beginTransaction();

                $admin_app_good_move->update([
                    'start_at' => $data['start_at'],
                    'finish_at' => $data['finish_at'],
                    'trk_room_id' => $trk_room->id,
                    'operation_type' => $data['operation_type'],
                    'organization_id' => Auth::user()->organization->id,
                    'brand_id' => $brand->id,
                    'admin_app_status_id' => $admin_app_status->id,
                    'responsible_user' => $data['responsible_user'],
                    'gate_number' => $data['gate_number'],
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);

                AdminAppGood::where('admin_app_good_move_id', $admin_app_good_move->id)->forceDelete();

                for ($i = 0; $i < count($data['goods']['name']); $i++) {
                    AdminAppGood::create([
                        'admin_app_good_move_id' => $admin_app_good_move->id,
                        'name' => $data['goods']['name'][$i],
                        'tare_type_id' => $data['goods']['tare_type_id'][$i],
                        'value' => $data['goods']['value'][$i],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                DB::commit();
                return redirect()->route('admin_app_good_moves.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminAppGoodMove $admin_app_good_move): RedirectResponse
    {
        //$this->authorize('delete', $admin_app_good_move);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'admin_app_good_move' => $admin_app_good_move,

            ]);

        try {

            DB::beginTransaction();
            AdminAppGood::where('admin_app_good_move_id', $admin_app_good_move->id)->delete();
            $admin_app_good_move->update([
                'destroyer_id' => Auth::id(),
            ]);
            $admin_app_good_move->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('admin_app_good_moves.index')->with('success', 'Данные удалены');

    }


    public function approve(ApproveAdminAppGoodMoveFormRequest $request, AdminAppGoodMove $admin_app_good_move)
    {
        $this->authorize('update', $admin_app_good_move);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
                'admin_app_good_move' => $admin_app_good_move,
                'goods' => $admin_app_good_move->goods,

            ]);

        if ($request->isMethod('patch')) {
            try {

                $admin_app_status = AdminAppStatus::where('name', AdminAppStatus::APPROVE)->first();

                $data = $request->validated();

                $admin_app_good_move->update([
                    'admin_app_status_id' => $admin_app_status->id,
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);

                $emails = User::where('id', $admin_app_good_move->author_id)->pluck('email')->toArray();

                ApproveAdminAppGoodMoveEmailJob::dispatch($emails, $admin_app_good_move);

                return redirect()->route('admin_app_good_moves.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();
    }

    public function reject(RejectAdminAppGoodMoveFormRequest $request, AdminAppGoodMove $admin_app_good_move)
    {
        $this->authorize('update', $admin_app_good_move);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
                'admin_app_good_move' => $admin_app_good_move,
                'goods' => $admin_app_good_move->goods,

            ]);

        if ($request->isMethod('patch')) {
            try {

                $admin_app_status = AdminAppStatus::where('name', AdminAppStatus::REJECT)->first();

                $data = $request->validated();

                $admin_app_good_move->update([
                    'admin_app_status_id' => $admin_app_status->id,
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                ]);

                $emails = User::where('id', $admin_app_good_move->author_id)->pluck('email')->toArray();

                RejectAdminAppGoodMoveEmailJob::dispatch($emails, $admin_app_good_move);

                return redirect()->route('admin_app_good_moves.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();
    }

}
