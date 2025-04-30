<?php

namespace App\Http\Controllers\Backend\UserDivisionFunctions;

use App\Http\Controllers\Controller;
use App\Http\Filters\UserDivisionFunctions\UserDivisionFunctionFilter;
use App\Http\Requests\UserDivisionFunctions\StoreUserDivisionFunctionFormRequest;
use App\Http\Requests\UserDivisionFunctions\UserDivisionFunctionFilterRequest;
use App\Http\Requests\UserDivisionFunctions\UpdateUserDivisionFunctionFormRequest;
use App\Models\UserDivisionFunctions\UserDivisionFunction;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserFunctions\UserFunction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserDivisionFunctionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(TrkRoom::class, 'trk_room');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserDivisionFunctionFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(UserDivisionFunctionFilter::class, ['queryParams' => array_filter($data)]);

        $user_division_functions = UserDivisionFunction::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.user_division_functions.pagination'));

        return \response()->view('backend.user_division_functions.index', [
            'user_division_functions' => $user_division_functions,
            'old_filters' => $data,
            'all_divisions' => UserDivision::orderBy('name')->get(),
            'all_functions' => UserFunction::orderBy('name')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        return \response()->view('backend.user_division_functions.create', [
            'functions' => UserFunction::orderBy('name')->get(),
            'divisions' => UserDivision::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserDivisionFunctionFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new division/function',
            [
                'user' => Auth::user()->name,
                'request' => $request,
            ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                if (
                    UserDivisionFunction::where('user_division_id', $data['user_division_id'])
                        ->where('user_function_id', $data['user_function_id'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такая должность и подразделение уже есть');
                }

                UserDivisionFunction::create([
                    'user_division_id' => $data['user_division_id'],
                    'user_function_id' => $data['user_function_id'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('user_division_functions.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');

    }

    /**
     * Display the specified resource.
     */
    public function show(UserDivisionFunction $user_division_function): Response
    {
        return \response()->view('backend.user_division_functions.show', [
            'user_division_function' => $user_division_function,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDivisionFunction $user_division_function): Response
    {
        return \response()->view('backend.user_division_functions.edit', [
            'user_division_function' => $user_division_function,
            'functions' => UserFunction::orderBy('name')->get(),
            'divisions' => UserDivision::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserDivisionFunctionFormRequest $request, UserDivisionFunction $user_division_function): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $user_division_function->update([
                'user_division_id' => $data['user_division_id'],
                'user_function_id' => $data['user_function_id'],
                'last_editor_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDivisionFunction $user_division_function): RedirectResponse
    {
        try {
            $user_division_function->update([
                'destroyer_id' => Auth::id(),
            ]);
            $user_division_function->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('user_division_functions.index')->with('success', 'Данные удалены');

    }
}
