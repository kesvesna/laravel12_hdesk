<?php

namespace App\Http\Controllers\Backend\Users;

use App\Http\Controllers\Controller;
use App\Http\Filters\Users\UserBugReportFilter;
use App\Http\Requests\Users\StoreUserBugReportFormRequest;
use App\Http\Requests\Users\UpdateUserBugReportFormRequest;
use App\Http\Requests\Users\UserBugReportFilterRequest;
use App\Models\Users\UserBugReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserBugReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Trk::class, 'trk');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserBugReportFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(UserBugReportFilter::class, ['queryParams' => array_filter($data)]);

        $user_bug_reports = UserBugReport::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.user_bug_reports.pagination'));

        return \response()->view('backend.user_bug_reports.index', [
            'user_bug_reports' => $user_bug_reports,
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.user_bug_reports.create', [

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserBugReportFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {
            Log::info('User try to store user bug report',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                if (
                    UserBugReport::where('trouble_description', $data['trouble_description'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Описание такого бага уже есть.');
                }


                UserBugReport::create([
                    'trouble_description' => $data['trouble_description'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('user_bug_reports.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserBugReport $user_bug_report): Response
    {
        return \response()->view('backend.user_bug_reports.show', [
            'user_bug_report' => $user_bug_report,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserBugReport $user_bug_report): Response
    {
        return \response()->view('backend.user_bug_reports.edit', [
            'user_bug_report' => $user_bug_report,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserBugReportFormRequest $request, UserBugReport $user_bug_report): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update user bug report',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                    'bug report' => $user_bug_report,
                ]);

            try {

                $data = $request->validated();

                $user_bug_report->update([
                    'trouble_description' => $data['trouble_description'],
                    'result_description' => $data['result_description'],
                    'last_editor_id' => Auth::id(),
                ]);

                return redirect()->route('user_bug_reports.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserBugReport $user_bug_report): RedirectResponse
    {
        Log::info('User try to delete user bug report',
            [
                'user' => Auth::user()->name,
                'user_bug_report' => $user_bug_report,
            ]);

        try {

            $user_bug_report->delete();

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('user_bug_reports.index')->with('success', 'Данные удалены');
    }
}
