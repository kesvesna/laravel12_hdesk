<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\UpdatePasswordRequest;
use App\Http\Requests\Profiles\UpdateUserFilterTrkSystemRequest;
use App\Http\Requests\Profiles\UpdateUserResponsibilityTrkSystemRequest;
use App\Http\Requests\Profiles\UpdateUserSubscriptionEntityEventRequest;
use App\Models\Organizations\Organization;
use App\Models\Systems\System;
use App\Models\Towns\Town;
use App\Models\Trks\Trk;
use App\Models\UserFilterTrksSystems\UserFilterTrkSystem;
use App\Models\UserNotifications\UserNotification;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserFunctions\UserFunction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return \response()->view('backend.settings.index', [
            'user' => Auth::user(),
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
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        Log::info('User try to edit settings', [
            'user' => Auth::user()->name,
            'user_to_edit_id' => $id,
        ]);

        if ((string)$id !== (string)Auth::id()) {
            return redirect()->back()->with('error', 'Невозможно редактировать настройки другого пользователя');
        }

        $user = User::where('id', $id)->with(['town', 'organization', 'subordinates'])->first();

        $user_responsibility_trks_systems = [];

        foreach (UserResponsibilityTrkSystem::where('user_id', $id)->get() as $responsible) {
            $user_responsibility_trks_systems[$responsible->trk_id][] = $responsible->system_id;
        }

        $user_filter_trks_systems = [];

        foreach (UserFilterTrkSystem::where('user_id', $id)->get() as $filter) {
            $user_filter_trks_systems[$filter->trk_id][] = $filter->system_id;
        }

        $renter_trk_rooms_brands = [];

//        foreach(UserRentTrkRoomBrand::where('user_id', $id)->get() as $rent_room)
//        {
//            $renter_trk_rooms_brands[$rent_room->trk_id][] = $rent_room->room_id;
//        }

        return view('backend.settings.edit',
            [
                'user' => User::findOrFail($id),
                'towns' => Town::orderBy('name')->get(),
                'trks' => Trk::orderBy('sort_order')->get(),
                'divisions' => UserDivision::orderBy('name')->get(),
                'functions' => UserFunction::orderBy('name')->get(),
                'organizations' => Organization::orderBy('name')->get(),
                'superiors' => User::whereNot('id', $id)->get(),
                'systems' => System::orderBy('name')->get(),
                'user_responsibility_trks_systems' => $user_responsibility_trks_systems,
                'user_filter_trks_systems' => $user_filter_trks_systems,
                'renter_trk_rooms_brands' => $renter_trk_rooms_brands,
            ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        //
    }

    public function update_password(UpdatePasswordRequest $request, User $user)
    {
        Log::info('User try to change password', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
        ]);

        if ($request->isMethod('patch')) {

            if (Hash::check($request->old_password, $user->password)) {

                if (!Hash::check($request->new_password, $user->password)) {

                    $user = User::find($user->id);
                    $user->fill([
                        'password' => Hash::make($request->new_password)
                    ])->save();

                    return redirect()->route('settings.edit', $user)->with('success', 'Пароль изменен');

                } else {

                    return redirect()->route('settings.edit', $user)->with('error', 'Старый пароль совпадает с новым');
                }

            } else {

                return redirect()->route('settings.edit', $user)->with('error', 'Старый пароль другой');

            }
        }

        return redirect()->route('settings.edit', $user)->with('error', 'Не удалось изменить пароль');
    }

    public function update_responsibility_trks_systems(UpdateUserResponsibilityTrkSystemRequest $request, string $id): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            try {

                $data = $request->validated();

                if (!empty($data)) {

                    DB::beginTransaction();

                    UserResponsibilityTrkSystem::where('user_id', $id)->forceDelete();

                    foreach ($data['trks_systems'] as $trk_id => $systems) {


                        foreach ($systems as $system_id => $value) {

                            $responsibility = new UserResponsibilityTrkSystem();
                            $responsibility->fill([
                                'user_id' => $id,
                                'trk_id' => $trk_id,
                                'system_id' => $system_id,
                                'last_editor_id' => Auth::id(),
                                'author_id' => Auth::id(),
                            ])->save();

                        }

                    }

                    DB::commit();

                    return redirect()->back()->with('success', 'Изменения сохранены');

                } else {

                    UserResponsibilityTrkSystem::where('user_id', $id)->forceDelete();

                    return redirect()->back()->with('success', 'Все данные удалены');

                }

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка записи в базу. Смотрите логи');

            }
        }
    }

    public function update_event_subscription(UpdateUserSubscriptionEntityEventRequest $request, User $user): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            try {

                $data = $request->validated();

                $user_notification = UserNotification::where('user_id', $user->id)->first();

                if (empty($user_notification->id)) {
                    UserNotification::create([
                        'user_id' => $user->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                        'task_from_user' => $data['task_from_user'] ?? 0,
                        'task_to_user' => $data['task_to_user'] ?? 0,
                        'app_from_user' => $data['app_from_user'] ?? 0,
                        'app_to_user_division' => $data['app_to_user_division'] ?? 0,
                        'tasks_need_to_do_till_weekend' => $data['tasks_need_to_do_till_weekend'] ?? 0,
                        'repairs_need_to_do_till_weekend' => $data['repairs_need_to_do_till_weekend'] ?? 0,
                        'periodical_works_need_to_do_till_weekend' => $data['periodical_works_need_to_do_till_weekend'] ?? 0,
                    ]);
                } else {
                    $user_notification->update([
                        'last_editor_id' => Auth::id(),
                        'task_from_user' => $data['task_from_user'] ?? 0,
                        'task_to_user' => $data['task_to_user'] ?? 0,
                        'app_from_user' => $data['app_from_user'] ?? 0,
                        'app_to_user_division' => $data['app_to_user_division'] ?? 0,
                        'tasks_need_to_do_till_weekend' => $data['tasks_need_to_do_till_weekend'] ?? 0,
                        'repairs_need_to_do_till_weekend' => $data['repairs_need_to_do_till_weekend'] ?? 0,
                        'periodical_works_need_to_do_till_weekend' => $data['periodical_works_need_to_do_till_weekend'] ?? 0,
                    ]);
                }
                return redirect()->back()->with('success', 'Изменения сохранены');

            } catch (\Exception $e) {

                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка записи в базу. Смотрите логи');

            }
        }

    }

    public function update_trks_systems_filter(UpdateUserFilterTrkSystemRequest $request, User $user): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            try {

                $data = $request->validated();

                if (!empty($data)) {

                    DB::beginTransaction();

                    UserFilterTrkSystem::where('user_id', $user->id)->forceDelete();

                    foreach ($data['trks_systems'] as $trk_id => $systems) {


                        foreach ($systems as $system_id => $value) {

                            $responsibility = new UserFilterTrkSystem();
                            $responsibility->fill([
                                'user_id' => $user->id,
                                'trk_id' => $trk_id,
                                'system_id' => $system_id,
                                'last_editor_id' => Auth::id(),
                                'author_id' => Auth::id(),
                            ])->save();

                        }

                    }

                    DB::commit();

                    return redirect()->back()->with('success', 'ТРК/Системы - фильтры, изменения сохранены');

                } else {

                    UserFilterTrkSystem::where('user_id', $user->id)->forceDelete();

                    return redirect()->back()->with('success', 'ТРК/Системы - фильтры, все данные удалены');

                }

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка записи в базу. Смотрите логи');

            }
        }
    }
}
