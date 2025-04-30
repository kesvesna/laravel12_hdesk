<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EquipmentWorkPeriods\EquipmentWorkPeriod;
use App\Models\OperationApplications\OperationApplication;
use App\Models\Systems\System;
use App\Models\Tasks\Task;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRoomRepairs\TrkRoomRepair;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $user;
    private $userTrkIds;
    private $engineerTrkIds;
    private $engineerSystemIds;
    private $trkEquipmentIds;

    public function __construct()
    {
        //$this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
    }

    public function index(): Response|RedirectResponse
    {

        return response()->view('backend.dashboard.index', [
            'info' => 'Dashboard page',
        ]);


        $this->user = Auth::user();

        if (!$this->hasCompleteProfile()) {
            return redirect()->route('profile.role_setting');
        }

        $this->loadUserTrkData();

        return response()->view('backend.dashboard.index', [
            'tasks' => $this->getTasksData(),
            'operation_applications' => $this->getOperationApplicationsData(),
            'user_trks' => UserResponsibilityTrkSystem::where('user_id', $this->user->id)->get(),
            'trks' => $this->getUserTrks(),
        ]);
    }

    private function hasCompleteProfile(): bool
    {
        return $this->user->town_id
            && $this->user->organization_id
            && $this->user->user_function_id
            && $this->user->user_division_id;
    }

    private function loadUserTrkData(): void
    {
        $this->userTrkIds = UserResponsibilityTrkSystem::join('trks', 'trks.id', '=', 'trk_system_responsibles.trk_id')
            ->where('user_id', $this->user->id)
            ->orderBy('trks.sort_order')
            ->pluck('trk_system_responsibles.trk_id')
            ->toArray();

        $this->engineerTrkIds = UserResponsibilityTrkSystem::where('user_id', $this->user->id)
            ->groupBy('trk_id')
            ->pluck('trk_id')
            ->toArray();

        $this->engineerSystemIds = UserResponsibilityTrkSystem::where('user_id', $this->user->id)
            ->groupBy('system_id')
            ->pluck('system_id')
            ->toArray();

        $trkRoomIds = TrkRoom::whereIn('trk_id', $this->engineerTrkIds)
            ->pluck('id')->toArray();

        $this->trkEquipmentIds = TrkEquipment::whereIn('trk_room_id', $trkRoomIds)
            ->whereIn('system_id', $this->engineerSystemIds)
            ->pluck('id')->toArray();
    }

    private function getTasksData(): array
    {
        $tasksToUser = $this->getTasksByResponsible($this->user->id);
        $tasksFromUser = $this->getTasksByAuthor($this->user->id);

        return [
            'to_user' => $tasksToUser,
            'from_user' => $tasksFromUser,
            'counts' => [
                'to_user' => $this->getTasksCounts($tasksToUser),
                'from_user' => $this->getTasksCounts($tasksFromUser),
            ],
            'show_tasks_to_user' => $this->hasTasksToShow($tasksToUser),
            'show_tasks_from_user' => $this->hasTasksToShow($tasksFromUser),
        ];
    }

    private function getTasksByResponsible(int $userId): array
    {
        $query = $this->baseTaskQuery()->where('responsible_id', $userId);

        return [
            'new' => $this->getNewTasks($query),
            'in_process' => $this->getInProcessTasks($query),
            'expired' => $this->getExpiredTasks($query),
        ];
    }

    private function getTasksByAuthor(int $userId): array
    {
        $query = $this->baseTaskQuery()
            ->where('author_id', $userId)
            ->whereNot('responsible_id', $userId);

        return [
            'new' => $this->getNewTasks($query),
            'in_process' => $this->getInProcessTasks($query),
            'expired' => $this->getExpiredTasks($query),
        ];
    }

    private function baseTaskQuery()
    {
        return Task::with([
            'author:id,name',
            'priority:id,name',
            'last_editor:id,name',
            'destroyer:id,name',
            'responsible:id,name',
            'orders:id,title'
        ])->orderBy('created_at', 'desc');
    }

    private function getNewTasks($query)
    {
        return (clone $query)
            ->where('done_progress', 0)
            ->where('deadline_at', '>=', now())
            ->get();
    }

    private function getInProcessTasks($query)
    {
        return (clone $query)
            ->where('done_progress', '>', 0)
            ->where('done_progress', '<', 100)
            ->where('deadline_at', '>=', now())
            ->get();
    }

    private function getExpiredTasks($query)
    {
        return (clone $query)
            ->where('done_progress', '<', 100)
            ->where('deadline_at', '<', now())
            ->get();
    }

    private function hasTasksToShow(array $tasks): bool
    {
        return $tasks['new']->isNotEmpty()
            || $tasks['in_process']->isNotEmpty()
            || $tasks['expired']->isNotEmpty();
    }

    private function getTasksCounts(array $tasks): array
    {
        return [
            'new_count' => $tasks['new']->count(),
            'in_process_count' => $tasks['in_process']->count(),
            'expired_count' => $tasks['expired']->count(),
            'total_count' => $tasks['new']->count() + $tasks['in_process']->count() + $tasks['expired']->count()
        ];
    }

    private function getOperationApplicationsData(): array
    {
        $fromUser = $this->getOperationApplicationsFromUser();

        return [
            'from_user' => $fromUser,
            'show_from_user' => $fromUser['new']->isNotEmpty() || $fromUser['in_process']->isNotEmpty(),
        ];
    }

    private function getOperationApplicationsFromUser(): array
    {
        $query = OperationApplication::where('author_id', $this->user->id)
            ->orderBy('created_at', 'desc');

        return [
            'new' => (clone $query)
                ->where('done_percents', 0)
                ->get(),
            'in_process' => (clone $query)
                ->with([
                    'trk',
                    'division',
                    'author',
                    'done_author',
                    'last_editor',
                    'destroyer',
                    'executors',
                    'repairs',
                    'avrs',
                    'tech_acts',
                    'logs'
                ])
                ->where('done_percents', '>', 0)
                ->where('done_percents', '<', 100)
                ->get(),
        ];
    }

    private function getUserTrks()
    {
        return UserResponsibilityTrkSystem::where('user_id', $this->user->id)
            ->join('trks', 'trks.id', '=', 'trk_system_responsibles.trk_id')
            ->select('trks.*')
            ->orderBy('trks.sort_order')
            ->distinct()
            ->get();
    }

    public function worksThisMonth()
    {
        $this->user = Auth::user();
        $this->loadUserTrkData();

        return view('backend.dashboard.partials.works_this_month', [
            'works_this_month' => $this->getWorksByMonth(date('Y-m'))
        ]);
    }

    public function worksNextMonth()
    {
        $this->user = Auth::user();
        $this->loadUserTrkData();

        return view('backend.dashboard.partials.works_next_month', [
            'works_next_month' => $this->getWorksByMonth(Carbon::parse(now())->addMonth()->format('Y-m')),
            'next_month' => Carbon::parse(now())->addMonth()->format('Y-m'),
        ]);
    }

    public function worksExpired()
    {
        $this->user = Auth::user();
        $this->loadUserTrkData();

        return view('backend.dashboard.partials.works_expired', [
            'works_expired' => $this->getExpiredWorks(),
            'prev_month' => (new Carbon('last day of last month'))->format('Y-m-d'),
        ]);
    }

    private function getWorksByMonth(string $month)
    {
        $query = $this->baseWorkPeriodQuery();
        return $this->processPeriodWorks(
            (clone $query)->where('next_to_be_at', 'like', $month . '%')->get()
        );
    }

    private function getExpiredWorks()
    {
        $query = $this->baseWorkPeriodQuery();
        return $this->processPeriodWorks(
            (clone $query)->whereDate('next_to_be_at', '<', (new Carbon('last day of last month'))->format('Y-m-d'))->get()
        );
    }

    private function baseWorkPeriodQuery()
    {
        return EquipmentWorkPeriod::with(['work_name', 'trk_equipment.equipment_name', 'trk_room.trk'])
            ->whereIn('equipment_id', $this->trkEquipmentIds)
            ->join('trk_equipments', 'trk_equipments.id', '=', 'equipment_work_periods.equipment_id')
            ->join('systems', 'systems.id', '=', 'trk_equipments.system_id')
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_equipments.trk_room_id')
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->select(
                'equipment_work_periods.*',
                'trks.id as trk_id',
                'trks.name as trk_name',
                'systems.name as system_name',
                'systems.id as system_id',
            )
            ->orderBy('next_to_be_at');
    }

    private function processPeriodWorks($works): array
    {
        $sortedWorks = $works->sortBy(['trk_name', 'system_name']);
        $trkCounts = $sortedWorks->countBy('trk_name');
        $trks = array_keys($trkCounts->toArray());
        $systems = array_keys($sortedWorks->countBy('system_name')->toArray());

        $grouped = [];
        foreach ($trks as $trk) {
            $trkId = Trk::where('name', $trk)->first();
            $grouped[$trk]['trk_id'] = $trkId->id;

            foreach ($systems as $system) {
                $counter = 0;
                $systemId = System::where('name', $system)->first();

                foreach ($sortedWorks as $work) {
                    if ($work->trk_name == $trk && $work->system_name == $system) {
                        $grouped[$trk]['systems'][$systemId->id][$system] = ++$counter;
                    }
                }
            }
        }

        return [
            'works' => $sortedWorks,
            'grouped' => $grouped,
            'trk_counts' => $trkCounts,
            'total_count' => $sortedWorks->count(),
        ];
    }

    public function repairsSection()
    {
        $this->user = Auth::user();
        $this->loadUserTrkData();

        return view('backend.dashboard.partials.repairs', [
            'repairs' => $this->getRepairsData()
        ]);
    }

    private function getRepairsData(): array
    {
        $query = TrkRoomRepair::whereIn('equipment_id', $this->trkEquipmentIds)
            ->with(['operation_application', 'trk_room', 'trk_equipment', 'executors', 'orders'])
            ->join('trk_rooms', 'trk_rooms.id', '=', 'trk_room_repairs.trk_room_id')
            ->join('trks', 'trks.id', '=', 'trk_rooms.trk_id')
            ->orderBy('trks.sort_order')
            ->select('trk_room_repairs.*');

        $newRepairs = (clone $query)
            ->where('done_progress', '=', 0)
            ->get();

        $inProgressRepairs = (clone $query)
            ->where('done_progress', '>', 0)
            ->where('done_progress', '<', 100)
            ->get();

        return [
            'new' => $newRepairs,
            'in_progress' => $inProgressRepairs,
            'show' => $newRepairs->isNotEmpty() || $inProgressRepairs->isNotEmpty(),
        ];
    }

    public function appsToYourDivision()
    {
        $this->user = Auth::user();
        $this->loadUserTrkData();

        $toDivision = $this->getOperationApplicationsToDivision();

        $operation_applications['to_division'] = $toDivision;
        $operation_applications['show_to_division'] = $toDivision['new_count'] > 0 || $toDivision['in_process_count'] > 0;

        return view('backend.dashboard.partials.apps_to_your_division', [
            'apps_to_your_division' => $operation_applications,
        ]);
    }

    private function getOperationApplicationsToDivision(): array
    {
        $query = OperationApplication::where('division_id', $this->user->user_division_id)
            ->whereIn('trk_id', $this->userTrkIds)
            ->join('trks', 'trks.id', '=', 'operation_applications.trk_id')
            ->orderBy('trks.sort_order')
            ->select('operation_applications.*', 'trks.name as trk_name')
            ->distinct();

        $newApps = (clone $query)
            ->where('done_percents', 0)
            ->get();

        $inProcessApps = (clone $query)
            ->where('done_percents', '>', 0)
            ->where('done_percents', '<', 100)
            ->get();

        return [
            'new' => $this->groupByTrk($newApps),
            'new_count' => $newApps->count(),
            'in_process' => $this->groupByTrk($inProcessApps),
            'in_process_count' => $inProcessApps->count(),
        ];
    }

    private function groupByTrk($applications): array
    {
        $grouped = [];
        foreach ($applications as $app) {
            $grouped[$app->trk_name][] = $app;
        }
        // Сортируем по порядку TRK из userTrkIds
        $sorted = [];
        $trkOrder = Trk::whereIn('id', $this->userTrkIds)
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->toArray();

        foreach ($trkOrder as $trkName) {
            if (isset($grouped[$trkName])) {
                $sorted[$trkName] = $grouped[$trkName];
            }
        }

        return $sorted;
    }
}
