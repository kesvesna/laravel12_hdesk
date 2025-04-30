<?php

namespace App\Http\Controllers\Backend\RenterMemos;

use App\Http\Controllers\Controller;
use App\Http\Filters\RenterMemoDivisions\RenterMemoDivisionFilter;
use App\Http\Requests\RenterMemoDivisions\RenterMemoDivisionFilterRequest;
use App\Http\Requests\RenterMemoDivisions\StoreRenterMemoDivisionFormRequest;
use App\Http\Requests\RenterMemoDivisions\UpdateRenterMemoDivisionFormRequest;
use App\Models\RenterMemos\RenterMemoDivision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RenterMemoDivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(RenterMemoDivision::class, 'renter_memo_division');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RenterMemoDivisionFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(RenterMemoDivisionFilter::class, ['queryParams' => array_filter($data)]);

        $renter_memo_divisions = RenterMemoDivision::filter($filter)
            ->orderBy('name')
            ->paginate(config('backend.renter_memo_divisions.pagination'));


        return \response()->view('backend.renter_memo_divisions.index', [
            'types' => $renter_memo_divisions,
            'old_filters' => $data,
            'all_types' => RenterMemoDivision::orderBy('created_at')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');

        return \response()->view('backend.renter_memo_divisions.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRenterMemoDivisionFormRequest $request): RedirectResponse
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
                RenterMemoDivision::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('renter_memo_divisions.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(RenterMemoDivision $renter_memo_division): Response
    {
        $this->authorize('view', $renter_memo_division);

        return \response()->view('backend.renter_memo_divisions.show', [
            'renter_memo_division' => $renter_memo_division,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RenterMemoDivision $renter_memo_division): Response
    {
        $this->authorize('edit', $renter_memo_division);

        return \response()->view('backend.renter_memo_divisions.edit', [
            'renter_memo_division' => $renter_memo_division,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRenterMemoDivisionFormRequest $request, RenterMemoDivision $renter_memo_division): RedirectResponse
    {
        $this->authorize('update', $renter_memo_division);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $renter_memo_division->update([
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
    public function destroy(RenterMemoDivision $renter_memo_division): RedirectResponse
    {
        $this->authorize('delete', $renter_memo_division);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'renter_memo_division' => $renter_memo_division,

            ]);

        try {

            $renter_memo_division->update([
                'destroyer_id' => Auth::id(),
            ]);
            $renter_memo_division->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('renter_memo_divisions.index')->with('success', 'Данные удалены');

    }
}
