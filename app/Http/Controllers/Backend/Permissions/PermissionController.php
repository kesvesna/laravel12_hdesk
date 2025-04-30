<?php

namespace App\Http\Controllers\Backend\Permissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\StorePermissionFormRequest;
use App\Http\Requests\Permissions\UpdatePermissionFormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Log::info('User viewAny permissions.',
            [
                'user_id' => Auth::id(),
            ]
        );

        $permissions = Permission::orderBy('name')
            ->paginate(config('backend.permissions.pagination'));

        return \response()->view('backend.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.permissions.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new permission.',
            [
                'user_id' => Auth::id(),
                'request' => $request->all(),
            ]
        );

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();

                Permission::create([
                    'name' => $data['name'],
                    //'alias' => Str::slug($data['name']),
                    // 'author_id' => Auth::id(),
                    //'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('permissions.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission): Response
    {
        Log::info('User try to view permission.',
            [
                'user_id' => Auth::id(),
                'permission' => $permission,
            ]
        );

        $permission = Permission::findOrFail($permission->id);

        return \response()->view('backend.permissions.show', [
            'permission' => $permission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission): Response
    {
        Log::info('User try to edit permission.',
            [
                'user_id' => Auth::id(),
                'permission' => $permission,
            ]
        );

        $permission = Permission::findOrFail($permission->id);

        return \response()->view('backend.permissions.edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionFormRequest $request, Permission $permission): RedirectResponse
    {
        Log::info('User try to update permission.',
            [
                'user_id' => Auth::id(),
                'permission' => $permission,
                'request' => $request->all(),
            ]
        );

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $role = Permission::findOrFail($permission->id);

            $role->update([
                'name' => $data['name'],
                //'last_editor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        Log::info('User try to destroy permission.',
            [
                'user_id' => Auth::id(),
                'permission' => $permission,
            ]
        );

        try {
            $permission->delete();
        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('permissions.index')->with('success', 'Данные удалены');

    }
}
