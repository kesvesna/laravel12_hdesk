<?php

namespace App\Http\Controllers\Backend\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Filters\Tasks\TaskFilter;
use App\Http\Requests\Tasks\StoreSubTaskFormRequest;
use App\Http\Requests\Tasks\StoreTaskFormRequest;
use App\Http\Requests\Tasks\TaskFilterRequest;
use App\Http\Requests\Tasks\UpdateTaskDoneProgressFormRequest;
use App\Http\Requests\Tasks\UpdateTaskFormRequest;
use App\Jobs\Tasks\NewTaskEmailJob;
use App\Jobs\Tasks\TaskDoneProgressEmailJob;
use App\Models\Executables\Executable;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskPriority;
use App\Models\User;
use App\Models\UserNotifications\UserNotification;
use App\Services\Tasks\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TaskFilterRequest $request): Response
    {

        $data = $request->validated();

        $filter = app()->make(TaskFilter::class, ['queryParams' => array_filter($data)]);

        $tasks = null;

        if (Auth::user()->hasRole('sadmin')) {

            $tasks = Task::filter($filter)
                ->with(['responsible', 'priority'])
                ->orderBy('created_at', 'desc')
                ->paginate(config('backend.tasks.pagination'));

        } else {

            $tasks = Task::filter($filter)
                ->with(['responsible', 'priority'])
                ->where('author_id', Auth::id())
                ->orwhere('responsible_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(config('backend.tasks.pagination'));

        }

        return \response()->view('backend.tasks.index', [
            'tasks' => $tasks,
            'all_priorities' => TaskPriority::all(),
            'all_responsibles' => User::where('superior_id', Auth::id())->orwhere('id', Auth::id())->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.tasks.create', [
            'priorities' => TaskPriority::orderBy('sort_order')->get(),
            'responsibles' => User::orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskFormRequest $request, TaskService $taskService): RedirectResponse
    {
        if ($request->isMethod('post')) {

            Log::info('User try to store task.',
                [
                    'user_id' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                if (
                    Task::where('author_id', Auth::id())
                        ->where('responsible_id', $data['responsible_id'])
                        ->where('priority_id', $data['priority_id'])
                        ->where('description', $data['description'])
                        ->whereDate('deadline_at', '=', date('Y-m-d'))
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такая задача уже есть.');
                }

                $task = $taskService->createNewTask(
                    $data['description'],
                    $data['priority_id'],
                    $data['responsible_id'],
                    $data['deadline_at'],
                );

                $responsiblity_id = User::where('id', $data['responsible_id'])
                    ->pluck('id')
                    ->toArray();

                $want_email_ids = UserNotification::whereIn('user_id', $responsiblity_id)
                    ->where('task_to_user', 1)
                    ->pluck('user_id')
                    ->toArray();

                $emails = User::whereIn('id', $want_email_ids)
                    ->pluck('email')
                    ->toArray();

                NewTaskEmailJob::dispatch($emails, $task);

                return redirect()->route('tasks.index')->with('success', 'Данные сохранены.');

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
    public function show(Task $task): Response
    {
        //$this->authorize('view', $task);
        $subtasks = Task::where('parent_id', $task->id)->get();

        return \response()->view('backend.tasks.show', [
            'task' => $task,
            'subtasks' => $subtasks,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): Response
    {
        return \response()->view('backend.tasks.edit', [
            'task' => $task,
            'executors' => User::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function create_subtask(Task $task): Response
    {
        $this->authorize('create', $task);

        return \response()->view('backend.tasks.create_subtask', [
            'task' => $task,
            'responsibles' => User::all(),
            'priorities' => TaskPriority::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_subtask(StoreSubTaskFormRequest $request, Task $task, TaskService $taskService): RedirectResponse
    {
        $this->authorize('create', $task);

        if ($request->isMethod('post')) {

            Log::info('User try to store subtask.',
                [
                    'user_id' => Auth::user()->name,
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                if (
                    Task::where('author_id', Auth::id())
                        ->where('responsible_id', $data['responsible_id'])
                        ->where('priority_id', $data['priority_id'])
                        ->where('description', $data['description'])
                        ->whereDate('deadline_at', '=', date('Y-m-d'))
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такая задача уже есть.');
                }

                $task = $taskService->createSubTask(
                    $task,
                    $data['description'],
                    $data['priority_id'],
                    $data['responsible_id'],
                    $data['deadline_at'],
                );

                $responsiblity_id = User::where('id', $data['responsible_id'])->pluck('id')->toArray();

                $want_email_ids = UserNotification::whereIn('user_id', $responsiblity_id)->pluck('user_id')->toArray();

                $emails = User::whereIn('id', $want_email_ids)->pluck('email')->toArray();

                NewTaskEmailJob::dispatch($emails, $task);

                return redirect()->route('tasks.index')->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                Log::error($e);

                return redirect()->back()->with('error', 'Ошибка сохранения. Смотрите логи.');

            }
        }

        return redirect()->back()->with('error', 'Данные не сохранены.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function done_progress(Task $task): Response
    {
        $this->authorize('create', $task);

        return \response()->view('backend.tasks.done_progress', [
            'task' => $task,
            'executors' => User::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskFormRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update');

        Log::info('User try to update task',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $task->update([
                'deadline_at' => $data['deadline_at'],
                'description' => $data['description'],
                'executed_at' => $data['executed_at'],
                'executed_result' => $data['executed_result'],
                'done_progress' => $data['done_progress'],
                'last_editor_id' => Auth::id(),
            ]);

            $data['executors'] = array_unique($data['executors']);
            $executors = User::whereIn('name', $data['executors'])->get();

            Executable::where('executable_id', $task->id)->where('executable_type', 'App\\Models\\Tasks\\Task')->delete();

            foreach ($executors as $executor) {
                $executor->tasks()->save($task);
            }

            return redirect()->route('tasks.show', $task)->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_done_progress(UpdateTaskDoneProgressFormRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('create', $task);

        Log::info('User try to update task done progress',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {

            try {
                $data = $request->validated();

                if ($task->done_progress >= $data['done_progress']) {
                    return redirect()->back()->with('error', 'Прогресс должен быть больше ' . $task->done_progress . '%');
                }

                DB::beginTransaction();

                $task->update([
                    'executed_at' => $data['executed_at'],
                    'executed_result' => $data['executed_result'],
                    'done_progress' => $data['done_progress'],
                    'last_editor_id' => Auth::id(),
                ]);

                $data['executors'] = array_unique($data['executors']);
                $executors = User::whereIn('name', $data['executors'])->get();

                Executable::where('executable_id', $task->id)->where('executable_type', 'App\\Models\\Tasks\\Task')->delete();

                foreach ($executors as $executor) {
                    $executor->tasks()->save($task);
                }

                DB::commit();

                $responsiblity_id = User::where('id', $task->author_id)
                    ->pluck('id')
                    ->toArray();

                $want_email_ids = UserNotification::whereIn('user_id', $responsiblity_id)
                    ->where('task_from_user', 1)
                    ->pluck('user_id')
                    ->toArray();

                $emails = User::whereIn('id', $want_email_ids)->pluck('email')->toArray();

                TaskDoneProgressEmailJob::dispatch($emails, $task);

                switch ($data['operation_type']) {

                    case "save_and_create_spare_part_order":
                        return redirect()->route('orders.create_from_task', $task)->with('success', 'Данные сохранены.');

                    default:
                        return redirect()->route('tasks.show', $task)->with('success', 'Изменения сохранены');
                }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                return redirect()->back()->with('error', 'Изменения не сохранены, смотрите логи');
            }
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        Log::info('User try to delete task',
            [
                'user' => Auth::user()->name,
                'task' => $task,
            ]);

        try {
            DB::beginTransaction();

            $task->update([
                'destroyer_id' => Auth::id(),
            ]);

            $task->delete();

            $executables = Executable::where('executable_id', $task->id)
                ->where('executable_type', 'App\\Models\\Tasks\\Task')
                ->get();

            foreach ($executables as $executable) {
                $executable->update([
                    'destroyer_id' => Auth::id(),
                ]);
                $executable->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('tasks.index')->with('success', 'Данные удалены');
    }
}
