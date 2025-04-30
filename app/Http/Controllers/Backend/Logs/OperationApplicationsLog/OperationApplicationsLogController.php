<?php

namespace App\Http\Controllers\Backend\Logs\OperationApplicationsLog;

use App\Http\Controllers\Controller;
use App\Http\Filters\Logs\OperationApplicationsLog\LogOperationApplicationFilter;
use App\Http\Requests\Logs\OperationApplicationsLog\LogOperationApplicationFilterRequest;
use App\Http\Requests\Logs\OperationApplicationsLog\UpdateLogOperationApplicationFormRequest;
use App\Http\Requests\OperationApplications\StoreOperationApplicationFormRequest;
use App\Http\Requests\OperationApplications\UpdateOperationApplicationFormRequest;
use App\Models\Avrs\Avr;
use App\Models\DocCommunications\DocCommunication;
use App\Models\Executables\Executable;
use App\Models\Logs\LogOperationApplication;
use App\Models\OperationApplications\OperationApplication;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationApplicationsLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(LogOperationApplication::class, 'operation_application_log');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(LogOperationApplicationFilterRequest $request): Response
    {
        $data = $request->validated();

        $all_trks = Trk::orderBy('sort_order')->get();

        $filter = app()->make(LogOperationApplicationFilter::class, ['queryParams' => array_filter($data)]);

        $operation_applications =LogOperationApplication::filter($filter)
            ->with(['trk', 'division'])
            ->orderBy('updated_at', 'asc')
            ->paginate(config('backend.operation_applications.pagination'));

        return \response()->view('backend.logs.operation_applications_log.index', [
            'operation_applications_log' => $operation_applications,
            'all_trks' => $all_trks,
            'all_divisions' => UserDivision::where('visibility', 1)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::CONTRACTOR)
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::SECURITY)
                ->orderBy('name')
                ->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.logs.operation_applications_log.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'divisions' => UserDivision::orderBy('created_at')
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::CONTRACTOR)
                ->whereNot('name', UserDivision::SECURITY)
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperationApplicationFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {

            Log::info('User try to store operation application.',
                [
                    'user_id' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {






                return redirect()->route('logs.operation_applications_log.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }
        }

        return redirect()->back()->with('error', 'Данные не сохранены.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LogOperationApplication $operation_applications_log): Response
    {
        return \response()->view('backend.logs.operation_applications_log.show', [
            'operation_application' => $operation_applications_log,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LogOperationApplication $operation_applications_log): Response
    {
        Log::info('User try to edit $operation_applications_log.',
            [
                'user' => Auth::user()->name,
                'request' => $operation_applications_log,
            ]
        );

        $trk_room_ids = TrkRoom::where('trk_id', $operation_applications_log->trk_id)->pluck('id')->toArray();

        $avrs = Avr::whereDate('date', date('Y-m-d', strtotime($operation_applications_log->done_at)))
            ->whereIn('trk_room_id', $trk_room_ids)
            ->get();

        $users = User::orderBy('name')->get();

        return \response()->view('backend.logs.operation_applications_log.edit', [
            'operation_application' => $operation_applications_log,
            'trks' => Trk::all(),
            'divisions' => UserDivision::all(),
            'executors' => $users,
            'avrs' => $avrs,
            'authors' => $users,
            'done_authors' => $users,
            'last_editors' => $users,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLogOperationApplicationFormRequest $request, LogOperationApplication $operation_applications_log): RedirectResponse
    {
        if ($request->isMethod('patch')) {

            Log::info('User try to update operation_application.',
                [
                    'user' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                $operation_applications_log->update([
                    'trk_id' => $data['trk_id'],
                    'division_id' => $data['division_id'],
                    'trouble_description' => $data['trouble_description'],
                    'last_editor_id' => $data['last_editor_id'],
                    'result_description' => $data['done_description'],
                    'done_percents' => $data['done_percents'],
                    'done_author_id' => $data['done_author_id'],
                    'done_at' => $data['done_at'],
                    'created_at' => $data['created_at'],
                ]);

                return redirect()->route('operation_applications_log.show', $operation_applications_log)->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                Log::error($e);
                DB::rollBack();

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }

        }

        return redirect()->back()->with('error', 'Данные не сохранены.');

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LogOperationApplication $operation_applications_log): RedirectResponse
    {
        Log::info('User try to delete operation application log.',
            [
                'user' => Auth::user()->name,
                'request' => $operation_applications_log,
            ]
        );

        $operation_applications_log->update([
            'destroyer_id' => Auth::id(),
        ]);

        $operation_applications_log->delete();

        return redirect()->route('operation_applications_log.index')->with('success', 'Данные удалены.');

    }

}
