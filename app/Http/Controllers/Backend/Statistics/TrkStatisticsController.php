<?php

namespace App\Http\Controllers\Backend\Statistics;

use App\Http\Controllers\Backend\TrkRoomRepairs\TrkRoomRepairController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\EmployeeReportFilterRequest;
use App\Http\Requests\Reports\TrkReportFilterRequest;
use App\Http\Requests\Statistics\TrkStatisticFilterRequest;
use App\Models\EquipmentSpareParts\EquipmentSparePart;
use App\Models\SpareParts\SparePartName;
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

class TrkStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
//        $this->operation_applications_provider = new OperationApplicationsProvider();
    }

    /**
     * Display a listing of the resource.
     */
    public function spare_parts_index(): Response
    {
        return \response()->view('backend.statistics.trk.spare_parts.index', [
            'trks' => Trk::orderBy('sort_order')->get(),
        ]);
    }

    public function spare_parts_report(TrkStatisticFilterRequest $request): Response | RedirectResponse
    {
        $data = $request->validated();

        $trk_report = null;

        $trk_room_ids = TrkRoom::where('trk_id', $data['trk_id'])->pluck('id')->toArray();
        $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)->pluck('id')->toArray();

        $equipment_spare_parts = EquipmentSparePart::whereIn('equipment_id', $trk_equipment_ids)
            ->selectRaw(
                'equipment_spare_parts.*,
                spare_part_names.name as spare_part_name,
                sum(equipment_spare_parts.value) as sum',
            )
            ->join('spare_part_names', 'spare_part_names.id', '=', 'equipment_spare_parts.spare_part_id')
            ->groupBy(['spare_part_name', 'model'])
            ->orderBy('spare_part_name')
            ->orderBy('sum', 'desc')
            ->get();

        return \response()->view('backend.statistics.trk.spare_parts.report', [
            'trk' => Trk::find($data['trk_id']),
            'equipment_spare_parts' => $equipment_spare_parts,
        ]);
    }

    public function one_part_report(string $spare_part_name_id, string $model, string $trk_id): Response | RedirectResponse
    {

        $trk_room_ids = TrkRoom::where('trk_id', $trk_id)->pluck('id')->toArray();
        $trk_equipment_ids = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)->pluck('id')->toArray();

        $equipment_spare_parts = EquipmentSparePart::whereIn('equipment_id', $trk_equipment_ids)
           ->where('spare_part_id', $spare_part_name_id)
            ->where('model', $model)
            ->orderByRaw('CONVERT(value, SIGNED) desc')
            ->get();

        $spare_part_name = SparePartName::find($spare_part_name_id);

        return \response()->view('backend.statistics.trk.spare_parts.one_part_report', [
            'trk' => Trk::find($trk_id),
            'spare_part_name' => $spare_part_name ?? null,
            'model' => $model,
            'equipment_spare_parts' => $equipment_spare_parts,
        ]);
    }

}
