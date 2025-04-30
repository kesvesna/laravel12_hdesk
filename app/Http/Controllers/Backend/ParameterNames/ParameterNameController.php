<?php

namespace App\Http\Controllers\Backend\ParameterNames;

use App\Http\Controllers\Controller;
use App\Http\Filters\ParameterNames\ParameterNameFilter;
use App\Http\Requests\ParameterNames\ParameterNameFilterRequest;
use App\Http\Requests\ParameterNames\StoreParameterNameFormRequest;
use App\Http\Requests\ParameterNames\UpdateParameterNameFormRequest;
use App\Models\ParameterNames\ParameterName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParameterNameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(ParameterName::class, 'parameter_name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ParameterNameFilterRequest $request): Response
    {
        $this->authorize('viewAny');

        $data = $request->validated();

        $filter = app()->make(ParameterNameFilter::class, ['queryParams' => array_filter($data)]);

        $parameters = ParameterName::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.parameter_names.pagination'));


        return \response()->view('backend.parameter_names.index', [
            'parameter_names' => $parameters,
            'old_filters' => $data,
            'all_parameter_names' => ParameterName::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create');

        return \response()->view('backend.parameter_names.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParameterNameFormRequest $request): RedirectResponse
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
                ParameterName::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('parameter_names.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParameterName $parameter_name): Response
    {
        $this->authorize('view', $parameter_name);

        return \response()->view('backend.parameter_names.show', [
            'parameter_name' => $parameter_name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParameterName $parameter_name): Response
    {
        $this->authorize('edit', $parameter_name);

        return \response()->view('backend.parameter_names.edit', [
            'parameter_name' => $parameter_name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParameterNameFormRequest $request, ParameterName $parameter_name): RedirectResponse
    {
        $this->authorize('update', $parameter_name);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),

            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $parameter_name->update([
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
    public function destroy(ParameterName $parameter_name): RedirectResponse
    {
        $this->authorize('delete', $parameter_name);

        Log::info(get_class($this) . ', method: ' . __FUNCTION__,
            [
                'user' => Auth::user()->name,
                'parameter_name' => $parameter_name,

            ]);

        try {

            $parameter_name->update([
                'destroyer_id' => Auth::id(),
            ]);
            $parameter_name->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');

        }
        return redirect()->route('parameter_names.index')->with('success', 'Данные удалены');
    }
}
