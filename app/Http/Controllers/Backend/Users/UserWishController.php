<?php

namespace App\Http\Controllers\Backend\Users;

use App\Http\Controllers\Controller;
use App\Http\Filters\Users\UserWishFilter;
use App\Http\Requests\Users\StoreUserWishFormRequest;
use App\Http\Requests\Users\UpdateUserWishFormRequest;
use App\Http\Requests\Users\UserWishFilterRequest;
use App\Models\Users\UserWish;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserWishController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Trk::class, 'trk');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserWishFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(UserWishFilter::class, ['queryParams' => array_filter($data)]);

        $user_wishes = UserWish::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.user_wishes.pagination'));

        return \response()->view('backend.user_wishes.index', [
            'user_wishes' => $user_wishes,
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.user_wishes.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserWishFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store user wish',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                if (
                    UserWish::where('wish_description', $data['wish_description'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Описание такого пожелание уже есть.');
                }


                UserWish::create([
                    'wish_description' => $data['wish_description'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('user_wishes.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserWish $user_wish): Response
    {
        return \response()->view('backend.user_wishes.show', [
            'user_wish' => $user_wish,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserWish $user_wish): Response
    {
        return \response()->view('backend.user_wishes.edit', [
            'user_wish' => $user_wish,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserWishFormRequest $request, UserWish $user_wish): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update user wish',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'user wish' => $user_wish,
                ]);

            try {

                $data = $request->validated();

                $user_wish->update([
                    'wish_description' => $data['wish_description'],
                    'resolution_description' => $data['resolution_description'],
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('user_wishes.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserWish $user_wish): RedirectResponse
    {
        Log::info('User try to delete user wish',
            [
                'user' => Auth::user()->name,
                'user_wish' => $user_wish,
            ]);

        try {

            $user_wish->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('user_wishes.index')->with('success', 'Данные удалены');
    }
}
