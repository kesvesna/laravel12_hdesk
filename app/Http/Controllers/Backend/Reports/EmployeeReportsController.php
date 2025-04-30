<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Backend\TrkRoomRepairs\TrkRoomRepairController;
use App\Http\Controllers\Controller;
use App\Http\Filters\Trks\TrkFilter;
use App\Http\Requests\Reports\EmployeeReportFilterRequest;
use App\Models\Counters\TrkRoomCounter;
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
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->operation_applications_provider = new OperationApplicationsProvider();
        $this->avrs_provider = new AvrsProvider();
        $this->tasks_provider = new TasksProvider();
        $this->repairs_provider = new TrkRoomRepairProvider();
        $this->balk_checklists_provider = new BalkChecklistsProvider();
        $this->air_duct_checklists_provider = new AirDuctChecklistsProvider();
        $this->air_diffuser_checklists_provider = new AirDiffuserChecklistsProvider();
        $this->air_supply_checklists_provider = new AirSupplyChecklistsProvider();
        $this->air_extract_checklists_provider = new AirExtractChecklistsProvider();
        $this->fancoil_checklists_provider = new FancoilChecklistsProvider();
        $this->conditioner_checklists_provider = new ConditionerChecklistsProvider();
        $this->counter_counts_provider = new CounterCountProvider();
        $this->period_works_provider = new PeriodWorkProvider();
        //$this->authorizeResource(EmployeeReports::class, 'avr');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return \response()->view('backend.reports.employee.index', [
            'users' => User::orderBy('name')->get(),
            'trks' => Trk::orderBy('sort_order')->get(),
            'divisions' => UserDivision::orderBy('name')
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::SECURITY)
                ->get(),
        ]);
    }

    public function general_report(EmployeeReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $user = User::where('name', 'like', '%' . $data['user'] . '%')
            ->where('user_division_id', $data['user_division_id'])
            ->first();

        if(empty($user->id))
        {
            return redirect()->route('employee_reports.general_report.index')->with('error', 'Нет такого сотрудника')->withInput();
        }

        $filter = app()->make(TrkFilter::class, ['queryParams' => array_filter($data)]);

        $trks = Trk::filter($filter)
                    ->orderBy('sort_order')
                    ->get();

        $user_report = null;

        foreach($trks as $trk)
        {
            $trk_room_ids = TrkRoom::where('trk_id', $trk->id)->pluck('id')->toArray();
            $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)->pluck('id')->toArray();
            $trk_room_counter_ids = TrkRoomCounter::where('trk_id', $trk->id)->pluck('id')->toArray();

            $trk_names[] = $trk->name;

            $user_report['avrs_count'][] = count($this->avrs_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_room_ids, $user->id));
            $user_report['closed_applications_count'][] = count($this->operation_applications_provider->getClosedBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk->id, $user->id));
            $user_report['closed_repairs_count'][] = count($this->repairs_provider->getClosedBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_room_ids, $user->id));

            $air_condition_checklists_count = 0;
            $air_condition_checklists_count += count($this->balk_checklists_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids,  $user->id));
            $air_condition_checklists_count += count($this->fancoil_checklists_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids, $user->id));
            $air_condition_checklists_count += count($this->conditioner_checklists_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids,  $user->id));
            $user_report['air_condition_checklists_count'][] = $air_condition_checklists_count;

            $air_recycle_checklists_count = 0;
            $air_recycle_checklists_count += count($this->air_supply_checklists_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids,  $user->id));
            $air_recycle_checklists_count += count($this->air_extract_checklists_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids,  $user->id));
            $air_recycle_checklists_count += count($this->air_duct_checklists_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids,  $user->id));
            $air_recycle_checklists_count += count($this->air_diffuser_checklists_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids,  $user->id));
            $user_report['air_recycle_checklists_count'][] = $air_recycle_checklists_count;

            $user_report['period_works_count'][] = count($this->period_works_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_equipment_ids, $user->id));

            $user_report['counter_counts'][] = count($this->counter_counts_provider->getBetweenDatesByTrkAndUser($data['start_date'], $data['finish_date'], $trk_room_counter_ids, $user->id));
        }

        return \response()->view('backend.reports.employee.general_report', [
            'user_report' => $user_report,
            'user' => $user,
            'trk_names' => $trk_names,
            'data' => $data,
            'trks' => Trk::orderBy('sort_order')->get(),
        ]);
    }

    public function operation_application_index(): Response
    {
        return \response()->view('backend.reports.employee.operation_application.index', [
            'users' => User::orderBy('name')->get(),
            'divisions' => UserDivision::orderBy('name')
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::SECURITY)
                ->get(),
        ]);
    }

    public function operation_application_report(EmployeeReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $user = User::where('name', 'like', '%' . $data['user'] . '%')
            ->where('user_division_id', $data['user_division_id'])
            ->first();

        if(empty($user->id))
        {
            return redirect()->route('employee_reports.operation_application.index')->with('error', 'Нет такого сотрудника');
        }

        $user_report['closed_operation_applications'] = $this->operation_applications_provider->getClosedBetweenDatesByUserWithTrks($data['start_date'], $data['finish_date'], $user);

        $user_report['in_process_operation_applications'] = $this->operation_applications_provider->getInProcessBetweenDatesByUserWithTrks($data['start_date'], $data['finish_date'], $user);

        $user_report['closed_applications_without_acts'] = $this->operation_applications_provider->getClosedBetweenDatesByUser($data['start_date'], $data['finish_date'], $user);

        $user_report['created_applications_by_this_user'] = $this->operation_applications_provider->getCreatedBetweenDatesByUser($data['start_date'], $data['finish_date'], $user);

        $user_report['trks'] = $this->operation_applications_provider->getClosedBetweenDatesByUserOrderedByTrkGroupByTrkName($data['start_date'], $data['finish_date'], $user);

        if(count($user_report['trks']) == 0 && count($user_report['created_applications_by_this_user']) > 0)
        {
            $user_report['trks'] = $this->operation_applications_provider->getCreatedBetweenDatesByUserOrderedByTrkGroupByTrkName($data['start_date'], $data['finish_date'], $user);
        }

        $user_report['closed_application_without_acts_count'] = 0;

        foreach($user_report['closed_applications_without_acts'] as $app)
        {
            if(isset($app->operation_application) && count($app->operation_application->avrs) == 0)
            {
                $user_report['closed_application_without_acts_count']++;
            }
        }

        return \response()->view('backend.reports.employee.operation_application.report', [
            'user_report' => $user_report,
            'user' => $user,
            'start_date' => $data['start_date'],
            'finish_date' => $data['finish_date'],
        ]);
    }

    public function avrs_index(): Response
    {
        return \response()->view('backend.reports.employee.avrs.index', [
            'users' => User::orderBy('name')->get(),
            'divisions' => UserDivision::orderBy('name')
                ->whereNot('name', UserDivision::RENTER)
                ->whereNot('name', UserDivision::DETK)
                ->whereNot('name', UserDivision::SECURITY)
                ->get(),
        ]);
    }

    public function avrs_report(EmployeeReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $user = User::where('name', 'like', '%' . $data['user'] . '%')
            ->where('user_division_id', $data['user_division_id'])
            ->first();

        if(empty($user->id))
        {
            return redirect()->route('employee_reports.avrs.index')->with('error', 'Нет такого сотрудника');
        }


        //$user_report['avrs'] = $this->avrs_provider->getCountBetweenDatesByUser($data['start_date'], $data['finish_date'], $user);

        $period = CarbonPeriod::create($data['start_date'], $data['finish_date']);

        foreach ($period as $date) {

            $user_report['avrs']['date'][] = $date->format('m-d');

            $user_report['avrs']['count'][] = $this->avrs_provider->getCountByDateByUser($date->format('Y-m-d'), $user);

        }


        return \response()->view('backend.reports.employee.avrs.report', [
            'user_report' => $user_report,
            'user' => $user,
            'start_date' => $data['start_date'],
            'finish_date' => $data['finish_date'],
        ]);
    }

}
