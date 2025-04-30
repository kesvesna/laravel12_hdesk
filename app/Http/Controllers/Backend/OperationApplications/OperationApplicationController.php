<?php

namespace App\Http\Controllers\Backend\OperationApplications;

use App\Exports\OperationApplications\OperationApplicationsExport;
use App\Exports\OperationApplications\OperationApplicationsExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\OperationApplications\OperationApplicationFilter;
use App\Http\Requests\OperationApplications\OperationApplicationFilterRequest;
use App\Http\Requests\OperationApplications\StoreOperationApplicationFormRequest;
use App\Http\Requests\OperationApplications\UpdateOperationApplicationDoneProgressFormRequest;
use App\Http\Requests\OperationApplications\UpdateOperationApplicationFormRequest;
use App\Http\Requests\OperationApplications\UpdateOperationApplicationRedirectFormRequest;
use App\Jobs\OperationApplications\NewOperationApplicationEmailJob;
use App\Jobs\OperationApplications\OperationApplicationDoneProgressUpdateEmailJob;
use App\Jobs\OperationApplications\OperationApplicationRedirectToAnotherDivisionEmailJob;
use App\Models\Avrs\Avr;
use App\Models\DocCommunications\DocCommunication;
use App\Models\Executables\Executable;
use App\Models\OperationApplications\OperationApplication;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserNotifications\UserNotification;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use App\Services\OperationApplications\OperationApplicationService;
use App\Services\UserDivisions\UserDivisionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class OperationApplicationController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(OperationApplication::class, 'operation_application');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OperationApplicationFilterRequest $request): Response
    {
        $data = $request->validated();

        $user_trks = UserResponsibilityTrkSystem::where('user_id', Auth::id())->groupBy('trk_id')->pluck('trk_id')->toArray();

        $show_trk_column = true;

        if(count($user_trks) == 1)
        {
            $data['trk_id'] = $user_trks[0];
            $show_trk_column = false;
        }

        $filter = app()->make(OperationApplicationFilter::class, ['queryParams' => array_filter($data)]);

        $operation_applications = OperationApplication::filter($filter)
            ->with(['trk', 'division'])
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.operation_applications.pagination'));

        return \response()->view('backend.operation_applications.index', [
            'operation_applications' => $operation_applications,
            'all_trks' => Trk::orderBy('sort_order')->get(),
            'all_divisions' => UserDivision::where('visibility', 1)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::CONTRACTOR)
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::SECURITY)
                ->orderBy('name')
                ->get(),
            'old_filters' => $data,
            'show_trk_column' => $show_trk_column,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        return \response()->view('backend.operation_applications.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'divisions' => UserDivision::orderBy('created_at')
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::CONTRACTOR)
                ->whereNot('name', UserDivision::SECURITY)
                ->get(),
        ]);
    }

    public function create_by_microphone(): Response
    {
        return \response()->view('backend.operation_applications.create_by_microphone', [
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
    public function store(StoreOperationApplicationFormRequest $request, OperationApplicationService $applicationService, UserDivisionService $divisionService): RedirectResponse
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

                if (
                    OperationApplication::where('trk_id', $data['trk_id'])
                        ->where('division_id', $data['division_id'])
                        ->where('trouble_description', $data['trouble_description'])
                        ->whereDate('created_at', '=', date('Y-m-d'))
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такая заявка уже есть.');
                }

                $operation_application = $applicationService->createNewApplication(
                    $data['trk_id'],
                    $data['division_id'],
                    $data['trouble_description']
                );

                $division_user_ids = $divisionService->getUsersIdsByDivisionId($data['division_id']);

                $get_responsible_user_ids = UserResponsibilityTrkSystem::whereIn('user_id', $division_user_ids)
                    ->where('trk_id', $data['trk_id'])
                    ->pluck('user_id')
                    ->toArray();

                $responsiblity_ids = User::whereIn('id', $get_responsible_user_ids)
                    ->pluck('id')
                    ->toArray();

                $want_email_ids = UserNotification::whereIn('user_id', $responsiblity_ids)
                    ->where('app_to_user_division', 1)
                    ->pluck('user_id')
                    ->toArray();

                $emails = User::whereIn('id', $want_email_ids)->pluck('email')->toArray();

                NewOperationApplicationEmailJob::dispatch($emails, $operation_application);

                return redirect()->route('operation_applications.index')->with('success', 'Данные сохранены.');

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
    public function show(OperationApplication $operationApplication): Response
    {
        return \response()->view('backend.operation_applications.show', [
            'operation_application' => $operationApplication,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OperationApplication $operationApplication): Response
    {
        Log::info('User try to edit renter_trk_room_brand.',
            [
                'user' => Auth::user()->name,
                'request' => $operationApplication,
            ]
        );

        $trk_room_ids = TrkRoom::where('trk_id', $operationApplication->trk_id)->pluck('id')->toArray();

        $avrs = Avr::whereDate('date', date('Y-m-d', strtotime($operationApplication->done_at)))
            ->whereIn('trk_room_id', $trk_room_ids)
            ->get();

        return \response()->view('backend.operation_applications.edit', [
            'operation_application' => $operationApplication,
            'trks' => Trk::all(),
            'divisions' => UserDivision::all(),
            'executors' => User::orderBy('name')->get(),
            'avrs' => $avrs,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function redirect_to_another_division(OperationApplication $operation_application): Response
    {
        $this->authorize('done_progress_update', $operation_application);

        Log::info('User try redirect_to_another_division.',
            [
                'user' => Auth::user()->name,
                'request' => $operation_application,
            ]
        );

        return \response()->view('backend.operation_applications.redirect_to_another_division', [
            'operation_application' => $operation_application,
            'divisions' => UserDivision::whereNot('id', $operation_application->division_id)
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::CONTRACTOR)
                ->whereNot('name', UserDivision::SECURITY)
                ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function done_progress(OperationApplication $operation_application): Response
    {
        $this->authorize('done_progress_update', $operation_application);

        Log::info('User make done progress of operation_application',
            [
                'user' => Auth::user()->name,
                'request' => $operation_application,
            ]
        );

        return \response()->view('backend.operation_applications.done_progress', [
            'operation_application' => $operation_application,
            'executors' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperationApplicationFormRequest $request, OperationApplication $operationApplication): RedirectResponse
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

                DB::beginTransaction();

                $operationApplication->update([
                    'trk_id' => $data['trk_id'],
                    'division_id' => $data['division_id'],
                    'trouble_description' => $data['trouble_description'],
                    'last_editor_id' => Auth::id(),
                    'result_description' => $data['done_description'],
                    'done_percents' => $data['done_percents'],
                    'done_author_id' => Auth::id(),
                    'done_at' => $data['done_at'],
                ]);

                $data['executors'] = array_unique($data['executors']);
                $executors = User::whereIn('name', $data['executors'])->get();

                Executable::where('executable_id', $operationApplication->id)->where('executable_type', 'App\\Models\\OperationApplications\\OperationApplication')->delete();

                foreach ($executors as $executor) {
                    $executor->operation_applications()->save($operationApplication);
                }

                if(isset($data['avr_id']))
                {

                    $avr = Avr::find($data['avr_id']);

                    $doc_communication = DocCommunication::where('from_id', $operationApplication->id)
                        ->where('from_type', OperationApplication::class)
                        ->where('to_type', Avr::class)
                        ->where('to_id', $data['avr_id'])
                        ->first();

                    if(empty($doc_communication->id) && !empty($avr->id))
                    {
                        DocCommunication::create([
                            'from_type' => OperationApplication::class,
                            'from_id' => $operationApplication->id,
                            'to_type' => Avr::class,
                            'to_id' => $data['avr_id'],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('operation_applications.show', $operationApplication)->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                Log::error($e);
                DB::rollBack();

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }

        }

        return redirect()->back()->with('error', 'Данные не сохранены.');

    }

    /**
     * Update the specified resource in storage.
     */
    public function redirect_to_another_division_update(UpdateOperationApplicationRedirectFormRequest $request, OperationApplication $operationApplication): RedirectResponse
    {
        $this->authorize('done_progress_update', $operationApplication);

        if ($request->isMethod('patch')) {

            Log::info('User try redirect_to_another_division_update.',
                [
                    'user' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                $operationApplication->update([
                    'division_id' => $data['division_id'],
                    'last_editor_id' => Auth::id(),
                ]);

                $responsiblity_ids = User::where('id', $operationApplication->author_id)
                    ->pluck('id')
                    ->toArray();

                $want_email_ids = UserNotification::whereIn('user_id', $responsiblity_ids)
                    ->where('app_from_user', 1)
                    ->pluck('user_id')
                    ->toArray();

                $emails = User::whereIn('id', $want_email_ids)->pluck('email')->toArray();

                OperationApplicationRedirectToAnotherDivisionEmailJob::dispatch($emails, $operationApplication);

                return redirect()->route('operation_applications.show', $operationApplication)->with('success', 'Заявка перенаправлена.');

            } catch (\Exception $e) {

                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }

        }

        return redirect()->back()->with('error', 'Данные не сохранены.');

    }

    /**
     * Update the specified resource in storage.
     */
    public function done_progress_update(UpdateOperationApplicationDoneProgressFormRequest $request, OperationApplication $operation_application): RedirectResponse
    {
        $this->authorize('done_progress_update', $operation_application);

        if ($request->isMethod('patch')) {

            Log::info('User try to update operation_application.',
                [
                    'user' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            if ($data['done_percents'] <= $operation_application->done_percents) {
                return redirect()
                    ->back()
                    ->with('error', 'Процент выполнения должен быть больше ' . $operation_application->done_percents . '%')
                    ->withInput();
            }

            try {

                DB::beginTransaction();

                $operation_application->update([
                    'last_editor_id' => Auth::id(),
                    'result_description' => $data['done_description'],
                    'done_percents' => $data['done_percents'],
                    'done_author_id' => Auth::id(),
                    'done_at' => $data['done_at'],
                ]);

                $data['executors'] = array_unique($data['executors']);
                $executors = User::whereIn('name', $data['executors'])->get();

                Executable::where('executable_id', $operation_application->id)->where('executable_type', 'App\\Models\\OperationApplications\\OperationApplication')->delete();

                foreach ($executors as $executor) {
                    $executor->operation_applications()->save($operation_application);
                }

                DB::commit();

                $responsiblity_ids = User::where('id', $operation_application->author_id)
                    ->pluck('id')
                    ->toArray();

                $want_email_ids = UserNotification::whereIn('user_id', $responsiblity_ids)
                    ->where('app_from_user', 1)
                    ->pluck('user_id')
                    ->toArray();

                $emails = User::whereIn('id', $want_email_ids)->pluck('email')->toArray();

                OperationApplicationDoneProgressUpdateEmailJob::dispatch($emails, $operation_application);

                switch ($data['operation_type']) {

                    case "save_and_create_repair":
                        return redirect()->route('trk_repairs.create_from_operation_application', $operation_application)->with('success', 'Данные сохранены.');

                    case "redirect_application":
                        return redirect()->route('operation_applications.redirect_to_another_division', $operation_application)->with('success', 'Данные сохранены.');

                    case "save_and_create_repair":
                        return redirect()->route('trk_repairs.create_from_operation_application', $operation_application)->with('success', 'Данные сохранены.');

                    case "save_and_create_tech_act":
                        return redirect()->route('tech_acts.create_from_operation_application', $operation_application)->with('success', 'Данные сохранены.');

                    case "save_and_create_avr":
                        return redirect()->route('avrs.create_from_operation_application', $operation_application)->with('success', 'Данные сохранены.');

                    default:
                        return redirect()->route('operation_applications.show', $operation_application)->with('success', 'Данные сохранены.');
                }


            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }

        }

        return redirect()->back()->with('error', 'Данные не сохранены.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OperationApplication $operationApplication): RedirectResponse
    {
        Log::info('User try to delete renter_trk_room_brand.',
            [
                'user' => Auth::user()->name,
                'request' => $operationApplication,
            ]
        );

        $operationApplication->update([
            'destroyer_id' => Auth::id(),
        ]);

        $operationApplication->delete();

        return redirect()->route('operation_applications.index')->with('success', 'Данные удалены.');

    }

    public function export(OperationApplicationFilterRequest $request)
    {
        $data = $request->validated();

        $filter = app()->make(OperationApplicationFilter::class, ['queryParams' => array_filter($data)]);

        $applications = OperationApplication::filter($filter)
            //->whereBetween('created_at', [$data['start_date'], $data['finish_date']])
            ->whereDate('created_at', '>=', $data['start_date'])
            ->whereDate('created_at', '<=', $data['finish_date'])
            ->orderBy('created_at', 'desc')
            ->get();

        $status = OperationApplication::NEW;

        switch ($data['status'])
        {

            case 'in_progress':
                $applications = $applications->filter(function ($application) {
                    return $application->done_percents > 0 && $application->done_percents < 100;
                });
                $status = OperationApplication::IN_PROGRESS;
                break;

            case 'closed':
                $applications = $applications->filter(function ($application) {
                    return $application->done_percents == 100;
                });
                $status = OperationApplication::CLOSED;
                break;

            default:
                $applications = $applications->filter(function ($application) {
                    return $application->done_percents == 0;
                });
                break;
        }

        if (count($applications) > 1000) {

            return redirect()->back()->with('error', 'Заявок для выгрузки ' . count($applications) . ', 1000 это максимум для комфортной работы приложения, попробуйте сузить фильтр для уменьшения количества.');
        }

        $applications = $applications->sortByDesc('date');

        if (count($applications) == 0) {

            return redirect()->back()->with('error', 'Нет таких заявок в базе');
        }

        switch ($data['file_type']) {

            case '.pdf':

                return (new OperationApplicationsExportPdf(
                    $applications,
                    $data,
                    $status,
                ))->export_pdf();

            case '.html':
                return Excel::download(new OperationApplicationsExport(
                    $applications,
                    $data,
                    $status,
                ), 'Заявки__' . $data['start_date'] . '__' . $data['finish_date'] . '__' . $status . '.html');

            default:
                return Excel::download(new OperationApplicationsExport(
                    $applications,
                    $data,
                    $status,
                ), 'Заявки__' . $data['start_date'] . '__' . $data['finish_date'] . '__' . $status . '.xlsx');
        }
    }
}
