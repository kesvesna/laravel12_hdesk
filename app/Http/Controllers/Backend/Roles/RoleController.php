<?php

namespace App\Http\Controllers\Backend\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\StoreRoleFormRequest;
use App\Http\Requests\Roles\UpdateRoleFormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Log::info('User viewAny roles.',
            [
                'user_id' => Auth::id(),
            ]
        );

        $roles = Role::whereNot('name', 'sadmin')
            ->orderBy('name')
            ->paginate(config('backend.roles.pagination'));

        return \response()->view('backend.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.roles.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new role.',
            [
                'user_id' => Auth::id(),
                'request' => $request->all(),
            ]
        );

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();

                Role::create([
                    'name' => $data['name'],
                    //'alias' => Str::slug($data['name']),
                    // 'author_id' => Auth::id(),
                    //'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('roles.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): Response
    {
        Log::info('User try to view role.',
            [
                'user_id' => Auth::id(),
                'role' => $role,
            ]
        );

        $role = Role::whereNot('name', 'sadmin')->findOrFail($role->id);
        $permissions = Permission::orderby('name')->get();

        return \response()->view('backend.roles.show', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): Response
    {
        Log::info('User try to edit role.',
            [
                'user_id' => Auth::id(),
                'role' => $role,
            ]
        );

        $role = Role::whereNot('name', 'sadmin')->findOrFail($role->id);

        return \response()->view('backend.roles.edit', [
            'role' => $role,
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleFormRequest $request, Role $role): RedirectResponse
    {
        Log::info('User try to update role.',
            [
                'user_id' => Auth::id(),
                'role' => $role,
                'request' => $request->all(),
            ]
        );

        if ($request->isMethod('patch') && Auth::user()->hasRole('sadmin')) {

            $data = $request->validated();

            $role = Role::whereNot('name', 'sadmin')->findOrFail($role->id);

            $role->update([
                'name' => $data['name'],
                //'last_editor_id' => Auth::id(),
            ]);

            $role->syncPermissions($data['permissions']);

            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        Log::info('User try to destroy role.',
            [
                'user_id' => Auth::id(),
                'role' => $role,
            ]
        );

        try {

            if (Auth::user()->hasRole('sadmin')) {
                $role = Role::whereNot('name', 'sadmin')->findOrFail($role->id);

                $role->delete();
            }
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('roles.index')->with('success', 'Данные удалены');

    }
}
