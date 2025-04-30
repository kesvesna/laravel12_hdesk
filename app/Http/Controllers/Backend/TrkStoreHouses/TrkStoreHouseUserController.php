<?php

namespace App\Http\Controllers\Backend\TrkStoreHouses;

use App\Http\Controllers\Controller;
use App\Http\Filters\TrkStoreHouseUsers\TrkStoreHouseUserFilter;
use App\Http\Requests\TrkStoreHouseUsers\StoreTrkStoreHouseUserFormRequest;
use App\Http\Requests\TrkStoreHouseUsers\TrkStoreHouseUserFilterRequest;
use App\Http\Requests\TrkStoreHouseUsers\UpdateTrkStoreHouseUserFormRequest;
use App\Models\StoreHouses\StoreHouseName;
use App\Models\Trks\Trk;
use App\Models\TrkStoreHouses\TrkStoreHouse;
use App\Models\TrkStoreHouses\TrkStoreHouseUser;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrkStoreHouseUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TrkStoreHouseUser::class, 'trk_store_house_users');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TrkStoreHouseUserFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(TrkStoreHouseUserFilter::class, ['queryParams' => array_filter($data)]);

        $trk_store_houses = TrkStoreHouseUser::filter($filter)
            ->where('author_id', Auth::id())
            ->with(['trk', 'store_house'])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.trk_store_houses.pagination'));

        $trk_ids = TrkStoreHouse::where('author_id', Auth::id())
            ->where('user_division_id', Auth::user()->user_division_id)
            ->groupBy('trk_id')
            ->pluck('trk_id')
            ->toArray();

        $trks = Trk::whereIn('id', $trk_ids)->orderBy('sort_order')->get();

        $store_house_name_ids = TrkStoreHouse::where('author_id', Auth::id())
            ->where('user_division_id', Auth::user()->user_division_id)
            ->groupBy('store_house_name_id')
            ->pluck('store_house_name_id')
            ->toArray();

        $store_house_names = StoreHouseName::whereIn('id', $store_house_name_ids)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')->get();

        $users = User::where('user_division_id', Auth::user()->user_division_id)->get();

        return \response()->view('backend.trk_store_house_users.index', [
            'trk_store_houses' => $trk_store_houses,
            'trks' => $trks,
            'store_house_names' => $store_house_names,
            'users' => $users,
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $trk_ids = TrkStoreHouse::where('author_id', Auth::id())
            //->where('user_division_id', Auth::user()->user_division_id)
            ->groupBy('trk_id')
            ->pluck('trk_id')
            ->toArray();

        $trks = Trk::whereIn('id', $trk_ids)->orderBy('sort_order')->get();

        $store_house_name_ids = TrkStoreHouse::where('author_id', Auth::id())
            //->where('user_division_id', Auth::user()->user_division_id)
            ->groupBy('store_house_name_id')
            ->pluck('store_house_name_id')
            ->toArray();

        $store_houses = StoreHouseName::whereIn('id', $store_house_name_ids)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->get();

        return \response()->view('backend.trk_store_house_users.create', [
            'trks' => $trks,
            'store_houses' => $store_houses,
            'users' => User::where('user_division_id', Auth::user()->user_division_id)
                ->whereNot('name', 'like', Auth::user()->name)
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrkStoreHouseUserFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new trk_store_house',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                if($data['user_id'] == Auth::id())
                {
                    return redirect()->back()->with('error', 'Нет смысла создавать самому себе доступ, он и так есть.');
                }

                $store_house = TrkStoreHouse::where('trk_id', $data['trk_id'])
                    ->where('store_house_name_id', $data['store_house_name_id'])
                    ->where('author_id', Auth::id())
                    ->where('user_division_id', Auth::user()->user_division_id)
                    ->first();

                if(empty($store_house->id))
                {
                    $trk = Trk::find($data['trk_id']);
                    $store_name = StoreHouseName::find($data['store_house_name_id']);
                    $division = UserDivision::find(Auth::user()->user_division_id);

                    return redirect()->back()->with('error', 'У Вас нет такого склада: ' . $trk->name . ', ' . $store_name->name . ', ' . $division->name);
                }

                if (
                    TrkStoreHouseUser::where('trk_id', $data['trk_id'])
                        ->where('store_id', $data['store_house_name_id'])
                        ->where('user_id', $data['user_id'])
                        ->where('division_id', Auth::user()->user_division_id)
                        ->where('author_id', Auth::id())
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'У этого пользователя уже есть доступ к этому складу.');
                }

                TrkStoreHouseUser::withTrashed()->updateOrCreate([
                    'trk_id' => $data['trk_id'],
                    'store_id' => $data['store_house_name_id'],
                    'user_id' => $data['user_id'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                    'division_id' => Auth::user()->user_division_id,
                ])->restore();

                return redirect()->route('trk_store_house_users.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(TrkStoreHouseUser $trk_store_house_user): Response
    {
        return \response()->view('backend.trk_store_house_users.show', [
            'trk_store_house_user' => $trk_store_house_user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrkStoreHouseUser $trk_store_house_user): Response
    {
        $trk_ids = TrkStoreHouse::where('author_id', Auth::id())
            ->where('user_division_id', Auth::user()->user_division_id)
            ->groupBy('trk_id')
            ->pluck('trk_id')
            ->toArray();

        $trks = Trk::whereIn('id', $trk_ids)->orderBy('sort_order')->get();

        $store_house_name_ids = TrkStoreHouse::where('author_id', Auth::id())
            ->where('user_division_id', Auth::user()->user_division_id)
            ->groupBy('store_house_name_id')
            ->pluck('store_house_name_id')
            ->toArray();

        $store_houses = StoreHouseName::where('id', $store_house_name_ids)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->get();

        return \response()->view('backend.trk_store_house_users.edit', [
            'trk_store_house_user' => $trk_store_house_user,
            'trks' => $trks,
            'store_houses' => $store_houses,
            'users' => User::where('user_division_id', Auth::user()->user_division_id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrkStoreHouseUserFormRequest $request, TrkStoreHouseUser $trk_store_house_user): RedirectResponse
    {
        Log::info('User try to update trk_store_house',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            try {

                $data = $request->validated();

                $trk_store_house_user->update([
                    'trk_id' => $data['trk_id'],
                    'store_id' => $data['store_house_name_id'],
                    'user_id' => $data['user_id'],
                    'comment' => $data['comment'],
                    'last_editor_id' => Auth::id(),
                    'division_id' => Auth::user()->user_division_id,
                ]);

                return redirect()->route('trk_store_house_users.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrkStoreHouseUser $trk_store_house_user): RedirectResponse
    {
        Log::info('User try to delete trk_store_house_user',
            [
                'user' => Auth::user()->name,
                'trk_store_house_user' => $trk_store_house_user,
            ]);

        try {

            $trk_store_house_user->update([
                'destroyer_id' => Auth::id(),
            ]);

            $trk_store_house_user->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('trk_store_house_users.index')->with('success', 'Данные удалены');
    }
}
