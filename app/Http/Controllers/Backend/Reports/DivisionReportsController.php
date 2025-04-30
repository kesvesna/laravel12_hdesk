<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Backend\TrkRoomRepairs\TrkRoomRepairController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\DivisionEmployeesReportFilterRequest;
use App\Http\Requests\Reports\DivisionReportFilterRequest;
use App\Http\Requests\Reports\EmployeeReportFilterRequest;
use App\Http\Requests\Reports\TrkReportFilterRequest;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Providers\Avrs\AvrsProvider;
use App\Providers\Checklists\AirDiffuserChecklistsProvider;
use App\Providers\Checklists\AirDuctChecklistsProvider;
use App\Providers\Checklists\AirExtractChecklistsProvider;
use App\Providers\Checklists\AirSupplyChecklistsProvider;
use App\Providers\Checklists\BalkChecklistsProvider;
use App\Providers\Checklists\ConditionerChecklistsProvider;
use App\Providers\Checklists\FancoilChecklistsProvider;
use App\Providers\CounterCounts\CounterCountProvider;
use App\Providers\OperationApplications\OperationApplicationsProvider;
use App\Providers\PeriodWorks\PeriodWorkProvider;
use App\Providers\Tasks\TasksProvider;
use App\Providers\TrkRepairs\TrkRoomRepairProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DivisionReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->operation_applications_provider = new OperationApplicationsProvider();
        $this->avrs_provider = new AvrsProvider();
        $this->tasks_provider = new TasksProvider();
        $this->repairs_provider = new TrkRoomRepairProvider();
        $this->period_works_provider = new PeriodWorkProvider();
        $this->balk_checklists_provider = new BalkChecklistsProvider();
        $this->air_duct_checklists_provider = new AirDuctChecklistsProvider();
        $this->air_diffuser_checklists_provider = new AirDiffuserChecklistsProvider();
        $this->air_supply_checklists_provider = new AirSupplyChecklistsProvider();
        $this->air_extract_checklists_provider = new AirExtractChecklistsProvider();
        $this->fancoil_checklists_provider = new FancoilChecklistsProvider();
        $this->conditioner_checklists_provider = new ConditionerChecklistsProvider();
        $this->counter_counts_provider = new CounterCountProvider();
    }

    /**
     * Display a listing of the resource.
     */
    public function all_trk_index(): Response
    {
        return \response()->view('backend.reports.division.all_trk.index', [
            'divisions' => UserDivision::whereNot('name', UserDivision::SECURITY)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::RENTER)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function all_trk_report(DivisionReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $division = UserDivision::find($data['user_division_id']);
        $author_ids = User::where('user_division_id', $division->id)->pluck('id')->toArray();

        $trks = Trk::orderBy('sort_order')->get();

        $division_report = null;

        foreach($trks as $trk)
        {
            $trk_room_ids = TrkRoom::where('trk_id', $trk->id)->pluck('id')->toArray();
            $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)->pluck('id')->toArray();

            $trk_names[] = $trk->name;
            $division_report['avrs_count'][] = count($this->avrs_provider->getBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_room_ids, $author_ids));
            $division_report['created_applications_count'][] = count($this->operation_applications_provider->getCreatedBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk->id, $division->id));
            $division_report['closed_applications_count'][] = count($this->operation_applications_provider->getClosedBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk->id, $division->id, $author_ids));
            $division_report['closed_repairs_count'][] = count($this->repairs_provider->getBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_room_ids, $division->id));
            $division_report['period_works_count'][] = count($this->period_works_provider->getBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_equipment_ids, $author_ids));
        }

        return \response()->view('backend.reports.division.all_trk.report', [
            'division_report' => $division_report,
            'trk_names' => $trk_names,
            'division' => $division,
            'start_date' => $data['start_date'],
            'finish_date' => $data['finish_date'],
            'axis_x_type' => $data['axis_x_type'],
        ]);
    }

    public function employees_index(): Response
    {
        return \response()->view('backend.reports.division.employees.index', [
            'executors' => User::where('superior_id', Auth::id())->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function employees_report(DivisionEmployeesReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $division_report = null;

        foreach ($data['executors'] as $executor)
        {
            $user = User::where('name', 'like', $executor . '%')->first();
            $division_report['users'][] = $user;
        }

        foreach($division_report['users'] as $user)
        {

            $division_report['user_names'][] = $user->name;
           $division_report['avrs_count'][] = count($this->avrs_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
           $division_report['closed_applications_count'][] = count($this->operation_applications_provider->getClosedBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
           $division_report['closed_repairs_count'][] = count($this->repairs_provider->getClosedBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));

            $air_condition_checklists_count = 0;
            $air_condition_checklists_count += count($this->balk_checklists_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
            $air_condition_checklists_count += count($this->fancoil_checklists_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
            $air_condition_checklists_count += count($this->conditioner_checklists_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
            $user_report['air_condition_checklists_count'] = $air_condition_checklists_count;

            $air_recycle_checklists_count = 0;
            $air_recycle_checklists_count += count($this->air_supply_checklists_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
            $air_recycle_checklists_count += count($this->air_extract_checklists_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
            $air_recycle_checklists_count += count($this->air_duct_checklists_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
            $air_recycle_checklists_count += count($this->air_diffuser_checklists_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
            $user_report['air_recycle_checklists_count'] = $air_recycle_checklists_count;

           $division_report['checklists_count'][] = $user_report['air_condition_checklists_count'] + $user_report['air_recycle_checklists_count'];
           $division_report['period_works_count'][] = count($this->period_works_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));
           $division_report['counters_count'][] = count($this->counter_counts_provider->getBetweenDatesByUser($data['start_date'], $data['finish_date'], $user));

        }

        return \response()->view('backend.reports.division.employees.report', [
            'division_report' => $division_report,
            'start_date' => $data['start_date'],
            'finish_date' => $data['finish_date'],
            'axis_x_type' => $data['axis_x_type'],
        ]);
    }

}
