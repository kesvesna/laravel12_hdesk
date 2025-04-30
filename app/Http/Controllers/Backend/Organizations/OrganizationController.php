<?php

namespace App\Http\Controllers\Backend\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Filters\Organizations\OrganizationFilter;
use App\Http\Requests\Organizations\OrganizationFilterRequest;
use App\Http\Requests\Organizations\StoreOrganizationFormRequest;
use App\Http\Requests\Organizations\UpdateOrganizationFormRequest;
use App\Models\Organizations\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Organization::class, 'organization');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OrganizationFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(OrganizationFilter::class, ['queryParams' => array_filter($data)]);

        $organizations = Organization::filter($filter)
            ->orderBy('name')
            ->paginate(config('backend.organizations.pagination'));


        return \response()->view('backend.organizations.index', [
            'organizations' => $organizations,
            'all_organizations' => Organization::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.organizations.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new organization',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                Organization::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('organization.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization): Response
    {
        return \response()->view('backend.organizations.show', [
            'organization' => $organization,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization): Response
    {
        return \response()->view('backend.organizations.edit', [
            'organization' => $organization,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationFormRequest $request, Organization $organization): RedirectResponse
    {
        Log::info('User try to update organization',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $organization->update([
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
    public function destroy(Organization $organization): RedirectResponse
    {
        Log::info('User try to delete new brand',
            [
                'user' => Auth::user()->name,
                'organization' => $organization,
            ]);

        try {

            if(count($organization->user_profiles) > 0)
            {
                return redirect()->back()->with('error', 'Нельзя удалять организацию. Она используется в профилях юзеров.');
            }

            if(count($organization->trk_renters) > 0)
            {
                return redirect()->back()->with('error', 'Нельзя удалять организацию. Она используется в профилях арендаторов.');
            }

            if(count($organization->trk_counters) > 0)
            {
                return redirect()->back()->with('error', 'Нельзя удалять организацию. Она используется в счетчиках.');
            }

            if(count($organization->admin_apps) > 0)
            {
                return redirect()->back()->with('error', 'Нельзя удалять организацию. Она используется в заявках.');
            }

            $organization->update([
                'destroyer_id' => Auth::id(),
            ]);

            $organization->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('organization.index')->with('success', 'Данные удалены');

    }
}
