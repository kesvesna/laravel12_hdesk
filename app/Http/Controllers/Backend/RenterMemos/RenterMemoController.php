<?php

namespace App\Http\Controllers\Backend\RenterMemos;

use App\Http\Controllers\Controller;
use App\Http\Filters\RenterMemos\RenterMemoFilter;
use App\Http\Requests\RenterMemos\RenterMemoFilterRequest;
use App\Http\Requests\RenterMemos\StoreRenterMemoFormRequest;
use App\Http\Requests\RenterMemos\UpdateRenterMemoFormRequest;
use App\Models\RenterMemos\RenterMemo;
use App\Models\RenterMemos\RenterMemoDivision;
use App\Models\Trks\Trk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RenterMemoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(RenterMemo::class, 'renter_memo');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RenterMemoFilterRequest $request): Response
    {

        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(RenterMemoFilter::class, ['queryParams' => array_filter($data)]);

        $renter_memos = RenterMemo::filter($filter)
            ->orderBy('name')
            ->paginate(config('backend.renter_memos.pagination'));


        return \response()->view('backend.renter_memos.index', [
            'renter_memos' => $renter_memos,
            'old_filters' => $data,
            'all_types' => RenterMemo::orderBy('created_at')->get(),
            'trks' => Trk::orderBy('sort_order')->get(),
            'divisions' => RenterMemoDivision::orderBy('name')->get(),
            'all_functions' => RenterMemo::orderBy('function')->distinct('function')->get(),
            'all_names' => RenterMemo::orderBy('name')->distinct('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');

        return \response()->view('backend.renter_memos.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'divisions' => RenterMemoDivision::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRenterMemoFormRequest $request): RedirectResponse
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

                RenterMemo::create([
                    'trk_id' => $data['trk_id'],
                    'division_id' => $data['division_id'],
                    'name' => $data['name'],
                    'function' => $data['function'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('renter_memos.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(RenterMemo $renter_memo): Response
    {
        $this->authorize('view', $renter_memo);

        return \response()->view('backend.renter_memos.show', [
            'renter_memo' => $renter_memo,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RenterMemo $renter_memo): Response
    {
        $this->authorize('edit', $renter_memo);

        return \response()->view('backend.renter_memos.edit', [
            'renter_memo' => $renter_memo,
            'trks' => Trk::orderBy('sort_order')->get(),
            'divisions' => RenterMemoDivision::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRenterMemoFormRequest $request, RenterMemo $renter_memo): RedirectResponse
    {
        $this->authorize('update', $renter_memo);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $renter_memo->update([
                'trk_id' => $data['trk_id'],
                'division_id' => $data['division_id'],
                'name' => $data['name'],
                'function' => $data['function'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'last_editor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RenterMemo $renter_memo): RedirectResponse
    {
        $this->authorize('delete', $renter_memo);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'renter_memo' => $renter_memo,

            ]);

        try {

            $renter_memo->update([
                'destroyer_id' => Auth::id(),
            ]);
            $renter_memo->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('renter_memos.index')->with('success', 'Данные удалены');

    }
}
