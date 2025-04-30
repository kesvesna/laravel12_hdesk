<?php

namespace App\Http\Controllers\Backend\Users;

use App\Http\Controllers\Controller;
use App\Http\Filters\Users\UserVacationFilter;
use App\Http\Requests\Users\StoreUserVacationFormRequest;
use App\Http\Requests\Users\UpdateUserVacationFormRequest;
use App\Http\Requests\Users\UserVacationFilterRequest;
use App\Models\User;
use App\Models\Users\UserVacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserVacationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Trk::class, 'trk');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserVacationFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(UserVacationFilter::class, ['queryParams' => array_filter($data)]);

        $user_vacations = UserVacation::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.user_vacations.pagination'));

        return \response()->view('backend.user_vacations.index', [
            'user_vacations' => $user_vacations,
            'users' => User::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        return \response()->view('backend.user_vacations.create', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserVacationFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store user vacation',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                if (
                    UserVacation::where('user_id', $data['user_id'])
                        ->whereBetween('start', [$data['start'], $data['finish']])
                        ->exists()
                    || UserVacation::where('user_id', $data['user_id'])
                        ->whereBetween('finish', [$data['start'], $data['finish']])
                        ->exists()
                    || UserVacation::where('user_id', $data['user_id'])
                        ->whereDate('start', '<', $data['start'])
                        ->whereDate('finish', '>', $data['finish'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'На эти даты отпуск уже есть. Пользуйтесь редактированием или удалите.');
                }

                $finish = \Carbon\Carbon::createFromFormat('Y-m-d', $data['finish']);
                $start = \Carbon\Carbon::createFromFormat('Y-m-d', $data['start']);

                $diff_in_days = $finish->diffInDays($start);

                DB::beginTransaction();

                UserVacation::create([
                    'user_id' => $data['user_id'],
                    'start' => $data['start'],
                    'finish' => $data['finish'],
                    'result' => $diff_in_days,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('user_vacations.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserVacation $user_vacation): Response
    {
        return \response()->view('backend.user_vacations.show', [
            'user_vacation' => $user_vacation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserVacation $user_vacation): Response
    {
        return \response()->view('backend.user_vacations.edit', [
            'user_vacation' => $user_vacation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserVacationFormRequest $request, UserVacation $user_vacation): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update user vacation',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $finish = \Carbon\Carbon::createFromFormat('Y-m-d', $data['finish']);
                $start = \Carbon\Carbon::createFromFormat('Y-m-d', $data['start']);

                $diff_in_days = $finish->diffInDays($start);

                DB::beginTransaction();

                $user_vacation->update([
                    'start' => $data['start'],
                    'finish' => $data['finish'],
                    'result' => $diff_in_days,
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('user_vacations.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserVacation $user_vacation): RedirectResponse
    {
        Log::info('User try to delete user vacation',
            [
                'user' => Auth::user()->name,
                'vacation' => $user_vacation,
            ]);

        try {

            $user_vacation->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('user_vacations.index')->with('success', 'Данные удалены');
    }
}
