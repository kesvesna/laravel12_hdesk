<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Backend\TrkRoomRepairs\TrkRoomRepairController;
use App\Http\Controllers\Controller;
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

class TrkReportsController extends Controller
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
        return \response()->view('backend.reports.trk.index', [
            'trks' => Trk::orderBy('sort_order')->get(),
        ]);
    }

    public function general_report(TrkReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $divisions = UserDivision::whereNot('name', UserDivision::SECURITY)
            ->whereNot('name', UserDivision::DETK)
            ->whereNot('name', UserDivision::RENTER)
            ->orderBy('name')
            ->get();

        $trk_room_ids = TrkRoom::where('trk_id', $data['trk_id'])->pluck('id')->toArray();
        $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)->pluck('id')->toArray();
        $trk_report = null;
        $division_names = null;

        foreach($divisions as $division)
        {
            $author_ids = User::where('user_division_id', $division->id)->pluck('id')->toArray();

            if($division->name == UserDivision::SA_TRK)
            {
                $division->name = 'Эксплуатация';
            }

            $division_names[] = $division->name;
            $trk_report['avrs_count'][] = count($this->avrs_provider->getBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_room_ids, $author_ids));
            $trk_report['closed_applications_count'][] = count($this->operation_applications_provider->getClosedBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $data['trk_id'], $division->id, $author_ids));
            $trk_report['closed_applications'][] = $this->operation_applications_provider->getClosedBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $data['trk_id'], $division->id, $author_ids);
            $trk_report['closed_repairs_count'][] = count($this->repairs_provider->getBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_room_ids, $division->id));
            $trk_report['period_works_count'][] = count($this->period_works_provider->getBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_equipment_ids, $author_ids));
        }

        return \response()->view('backend.reports.trk.general_report', [
            'trk_report' => $trk_report,
            'trk' => Trk::find($data['trk_id']),
            'division_names' => $division_names,
            'start_date' => $data['start_date'],
            'finish_date' => $data['finish_date'],
        ]);
    }

    public function operation_application_index(): Response
    {
        return \response()->view('backend.reports.trk.operation_application.index', [
            'trks' => Trk::orderBy('sort_order')->get(),
        ]);
    }

    public function operation_application_report(TrkReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $divisions = UserDivision::whereNot('name', UserDivision::SECURITY)
            ->whereNot('name', UserDivision::DETK)
            ->whereNot('name', UserDivision::RENTER)
            ->orderBy('name')
            ->get();

        $trk_report = null;
        $division_names = null;

        foreach($divisions as $division)
        {
            $author_ids = User::where('user_division_id', $division->id)->pluck('id')->toArray();

            if($division->name == UserDivision::SA_TRK) {
                $division->name = 'Эксплуатация';
            }

            $division_names[] = $division->name;
            $trk_report['closed_applications_count'][] = count($this->operation_applications_provider->getClosedBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $data['trk_id'], $division->id, $author_ids));
            $trk_report['new_applications_count'][] = count($this->operation_applications_provider->getNewBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $data['trk_id'], $division->id));
            $trk_report['created_applications_count'][] = count($this->operation_applications_provider->getCreatedBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $data['trk_id'], $division->id));
            $trk_report['in_progress_applications_count'][] = count($this->operation_applications_provider->getInProgressBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $data['trk_id'], $division->id));
            $trk_report['motionless_applications_count'][] = count($this->operation_applications_provider->getMotionlessBetweenDatesByTrkAndDivision($data['trk_id'], $division->id));

        }

        $trk_report['motionless_applications'] = $this->operation_applications_provider->getAllMotionlessBetweenDatesByTrk($data['trk_id']);

        return \response()->view('backend.reports.trk.operation_application.report', [
            'trk_report' => $trk_report,
            'trk' => Trk::find($data['trk_id']),
            'division_names' => $division_names,
            'start_date' => $data['start_date'],
            'finish_date' => $data['finish_date'],
        ]);
    }

    public function repair_index(): Response
    {
        return \response()->view('backend.reports.trk.repair.index', [
            'trks' => Trk::orderBy('sort_order')->get(),
        ]);
    }

    public function repair_report(TrkReportFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $divisions = UserDivision::whereNot('name', UserDivision::SECURITY)
            ->whereNot('name', UserDivision::DETK)
            ->whereNot('name', UserDivision::RENTER)
            ->orderBy('name')
            ->get();

        $trk_rooms = TrkRoom::where('trk_id', $data['trk_id'])->pluck('id')->toArray();

        $trk_report = null;
        $division_names = null;

        foreach($divisions as $division)
        {
            $author_ids = User::where('user_division_id', $division->id)->pluck('id')->toArray();

            if($division->name == UserDivision::SA_TRK) {
                $division->name = 'Эксплуатация';
            }

            $division_names[] = $division->name;
            $trk_report['closed_repairs_count'][] = count($this->repairs_provider->getClosedBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_rooms, $division->id));
            $trk_report['new_repairs_count'][] = count($this->repairs_provider->getNewBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_rooms, $division->id));
            $trk_report['in_progress_repairs_count'][] = count($this->repairs_provider->getInProgressBetweenDatesByTrkAndDivision($data['start_date'], $data['finish_date'], $trk_rooms, $division->id));
            $trk_report['motionless_repairs_count'][] = count($this->repairs_provider->getMotionlessBetweenDatesByTrkAndDivision($trk_rooms, $division->id));

        }

        $trk_report['motionless_repairs'] = $this->repairs_provider->getAllMotionlessBetweenDatesByTrk($trk_rooms);

        return \response()->view('backend.reports.trk.repair.report', [
            'trk_report' => $trk_report,
            'trk' => Trk::find($data['trk_id']),
            'division_names' => $division_names,
            'start_date' => $data['start_date'],
            'finish_date' => $data['finish_date'],
        ]);
    }

}
