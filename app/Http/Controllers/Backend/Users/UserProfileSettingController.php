<?php

namespace App\Http\Controllers\Backend\Users;

use App\Http\Controllers\Controller;
use App\Http\Filters\Users\UserFilter;
use App\Http\Requests\Profiles\StoreProfileFormRequest;
use App\Http\Requests\Profiles\UpdateProfileRequest;
use App\Http\Requests\Users\UserFilterRequest;
use App\Jobs\UserProfiles\NewUserProfileStoredEmailJob;
use App\Jobs\UserProfiles\UserProfileUpdatedEmailJob;
use App\Models\Organizations\Organization;
use App\Models\Towns\Town;
use App\Models\TrkRoles\TrkRole;
use App\Models\User;
use App\Models\UserDivisionFunctions\UserDivisionFunction;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserFunctions\UserFunction;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserProfileSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        //$this->authorizeResource(Document::class, 'document');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user = User::find(Auth::id());

        return \response()->view('backend.profile.index', [
            'user' => $user,
        ]);

    }

    public function role_setting(): Response
    {
        $first_division = UserDivision::orderBy('name')
            ->whereNot('visibility', 0)
            ->first();

        $user_function_ids = UserDivisionFunction::where('user_division_id', $first_division->id)->pluck('user_function_id');
        $functions = UserFunction::whereIn('id', $user_function_ids)->get();

        return response()->view('backend.profile.role-setting', [
            'towns' => Town::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
            'divisions' => UserDivision::orderBy('name')->whereNot('visibility', 0)->get(),
            'functions' => $functions,
        ])->withHeaders([
            "Pragma" => "no-cache",
            "Expires" => "Fri, 01 Jan 1990 00:00:00 GMT",
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return response()->view('backend.profile.create', [
            'towns' => Town::all(),
            'organizations' => Organization::all(),
            'divisions' => UserDivision::orderby('sort_order')
                ->get(),
            'functions' => UserFunction::orderby('sort_order')
                ->get(),
            'superiors' => User::all(),
            'user' => User::find(Auth::id()),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {

            Log::info('User try to store profile.',
                [
                    'user_id' => Auth::id(),
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                $user = User::find(Auth::id());

                $user->update([
                    'town_id' => $data['town_id'],
                    'organization_id' => $data['organization_id'],
                    'user_division_id' => $data['division_id'],
                    'user_function_id' => $data['function_id'],
                    'phone' => $data['phone'],
                ]);

                if (count($user->getRoleNames()) == 0) {
                    $user->assignRole('reader');
                }

                $role_id = Role::where('name', 'sadmin')->pluck('id')->first();

                $user_id = DB::table('model_has_roles')->where('role_id', $role_id)
                    ->where('model_type', 'App\\Models\\User')
                    ->pluck('model_id')
                    ->first();

                $emails = User::where('id', $user_id)->pluck('email')->toArray();

                NewUserProfileStoredEmailJob::dispatch($emails, $user);

                return redirect()->route('dashboard.index')->with('success', 'Роль сохранена.');

            } catch (\Exception $e) {

                Log::error($e);
                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }

        }

        return redirect()->back()->with('error', 'Роль не выбрана');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        return \response()->view('backend.profile.show', [
            'user' => $user,
            'user_trk_systems' => UserResponsibilityTrkSystem::where('user_id', $user->id)->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response|RedirectResponse
    {
        Log::info('User try to edit profile.', [
            'user' => Auth::user()->name,
            'request' => $user,
        ]);

        if (Auth::id() != $user->id && !Auth::user()->hasRole('sadmin')) {
            return redirect()->route('profile.index');
        }

        $first_division = UserDivision::where('id', $user->division->id)->first();
        $user_function_ids = UserDivisionFunction::where('user_division_id', $first_division->id)->pluck('user_function_id')->toArray();
        $functions = UserFunction::whereIn('id', $user_function_ids)->get();

        $superiors = User::whereNot('name', $user->name)
            ->orderBy('name')
            ->get();

        return \response()->view('backend.profile.edit', [
            'user' => $user,
            'towns' => Town::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
            'divisions' => UserDivision::orderBy('name')->get(),
            'functions' => $functions,
            'superiors' => $superiors,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, User $user): RedirectResponse
    {
        Log::info('User try to update profile.', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
        ]);

        if ($request->isMethod('patch') && (Auth::id() === $user->id || Auth::user()->hasRole('sadmin'))) {

            $data = $request->validated();

            $is_blocked = null;

            if (Auth::user()->hasRole('sadmin')) {
                $is_blocked = $data['is_blocked'];

            } else {

                $is_blocked = $user->is_blocked;
            }

            $old_user = [];
            $old_user['name'] = $user->name;
            $old_user['town'] = $user->town->name;
            $old_user['organization'] = $user->organization->name;
            $old_user['function'] = $user->function->name ?? null;
            $old_user['division'] = $user->division->name ?? null;
            $old_user['phone'] = $user->phone ?? null;
            $old_user['superior'] = $user->superior->name ?? 'отсутствует';

            try {

                $user->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'town_id' => $data['town_id'],
                    'organization_id' => $data['organization_id'],
                    'user_function_id' => $data['function_id'],
                    'user_division_id' => $data['division_id'],
                    'superior_id' => $data['superior_id'],
                    'is_blocked' => $is_blocked,
                ]);

                if (isset($data['role_name'])) {
                    $user->syncRoles($data['role_name']);
                }

                $role_id = Role::where('name', 'sadmin')->pluck('id')->first();

                $user_id = DB::table('model_has_roles')->where('role_id', $role_id)
                    ->where('model_type', User::class)
                    ->pluck('model_id')
                    ->first();

                $emails = User::where('id', $user_id)->pluck('email')->toArray();

                UserProfileUpdatedEmailJob::dispatch($emails, $user, $old_user);

            } catch (\Exception $e) {

                Log::error($e);
                return redirect()->back()->with('error', 'Ошибка при сохранении. Смотрите логи.');

            }

            return redirect()->back()->with('success', 'Данные сохранены');
        }

        return redirect()->back()->with('error', 'Данные не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        dd('not so fast');
    }

    public function all(UserFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(UserFilter::class, ['queryParams' => array_filter($data)]);

        $users = User::filter($filter)
            //->with(['trk', 'building', 'floor', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.users.pagination'));

        return \response()->view('backend.profile.all', [
            'users' => $users,
            'old_filters' => $data,
        ]);
    }
}
