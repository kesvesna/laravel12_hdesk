<?php

namespace App\Http\Controllers\Backend\AdminApps;

use App\Http\Controllers\Controller;
use App\Http\Filters\AdminAppStatuses\AdminAppStatusFilter;
use App\Http\Requests\AdminAppStatuses\AdminAppStatusFilterRequest;
use App\Http\Requests\AdminAppStatuses\StoreAdminAppStatusFormRequest;
use App\Http\Requests\AdminAppStatuses\UpdateAdminAppStatusFormRequest;
use App\Models\AdminApps\AdminAppStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAppStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(AdminAppStatus::class, 'admin_app_status');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AdminAppStatusFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(AdminAppStatusFilter::class, ['queryParams' => array_filter($data)]);

        $admin_app_statuses = AdminAppStatus::filter($filter)
            ->orderBy('name')
            ->paginate(config('backend.admin_app_statuses.pagination'));


        return \response()->view('backend.admin_app_statuses.index', [
            'statuses' => $admin_app_statuses,
            'old_filters' => $data,
            'all_statuses' => AdminAppStatus::orderBy('created_at')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');

        return \response()->view('backend.admin_app_statuses.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminAppStatusFormRequest $request): RedirectResponse
    {
        $this->authorize('store');

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                AdminAppStatus::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('admin_app_statuses.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(AdminAppStatus $admin_app_status): Response
    {
        $this->authorize('view', $admin_app_status);

        return \response()->view('backend.admin_app_statuses.show', [
            'admin_app_status' => $admin_app_status,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminAppStatus $admin_app_status): Response
    {
        $this->authorize('edit', $admin_app_status);

        return \response()->view('backend.admin_app_statuses.edit', [
            'admin_app_status' => $admin_app_status,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminAppStatusFormRequest $request, AdminAppStatus $admin_app_status): RedirectResponse
    {
        $this->authorize('update', $admin_app_status);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
                'admin_app_status' => $admin_app_status,

            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $admin_app_status->update([
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
    public function destroy(AdminAppStatus $admin_app_status): RedirectResponse
    {
        $this->authorize('delete', $admin_app_status);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'admin_app_status' => $admin_app_status,

            ]);

        try {

            $admin_app_status->update([
                'destroyer_id' => Auth::id(),
            ]);
            $admin_app_status->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('admin_app_statuses.index')->with('success', 'Данные удалены');

    }
}
