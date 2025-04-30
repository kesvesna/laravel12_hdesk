<?php

namespace App\Http\Controllers\Backend\WorkNames;

use App\Http\Controllers\Controller;
use App\Http\Filters\WorkNames\WorkNameFilter;
use App\Http\Requests\WorkNames\StoreWorkNameFormRequest;
use App\Http\Requests\WorkNames\UpdateWorkNameFormRequest;
use App\Http\Requests\WorkNames\WorkNameFilterRequest;
use App\Models\Avrs\AvrWork;
use App\Models\EquipmentWorkPeriods\EquipmentWorkPeriod;
use App\Models\WorkNames\WorkName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkNameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(WorkName::class, 'work_name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(WorkNameFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(WorkNameFilter::class, ['queryParams' => array_filter($data)]);

        $work_names = WorkName::filter($filter)
            //->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.work_names.pagination'));

        return \response()->view('backend.work_names.index', [
            'work_names' => $work_names,
            'all_work_names' => WorkName::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.work_names.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkNameFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new work name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {

                $data = $request->validated();

                WorkName::withTrashed()->updateOrCreate([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ])->restore();

                return redirect()->route('work_names.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkName $work_name): Response
    {
        $avr_works = AvrWork::where('work_name_id', $work_name->id)->get();
        $period_works = EquipmentWorkPeriod::where('work_name_id', $work_name->id)->get();

        return \response()->view('backend.work_names.show', [
            'work_name' => $work_name,
            'work_names' => WorkName::orderBy('name')->get(),
            'avr_works' => $avr_works,
            'period_works' => $period_works,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkName $work_name): Response
    {
        return \response()->view('backend.work_names.edit', [
            'work_name' => $work_name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkNameFormRequest $request, WorkName $work_name): RedirectResponse
    {
        Log::info('User try to update new work name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {

            $data = $request->validated();

                $work_name->update([
                    'name' => $data['name'],
                    'visibility' => $data['visibility'],
                    'last_editor_id' => Auth::id(),
                ]);


            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkName $work_name): RedirectResponse
    {
        Log::info('User try to delete work name',
            [
                'user' => Auth::user()->name,
                'work_name' => $work_name,
            ]);

        try {

            $avr_works = AvrWork::where('work_name_id', $work_name->id)->get();

            if (count($avr_works) > 0) {
                return redirect()->route('work_names.show', $work_name->id)->with('error', 'Невозможно удалить, есть акты с этим типом работ');
            }

            $period_works = EquipmentWorkPeriod::where('work_name_id', $work_name->id)->get();

            if (count($period_works) > 0) {
                return redirect()->route('work_names.show', $work_name->id)->with('error', 'Невозможно удалить, есть тех. мероприятия с этим типом работ');
            }

            $work_name->update([
                'destroyer_id' => Auth::id(),
            ]);

            $work_name->delete();

            return redirect()->route('work_names.index')->with('success', 'Данные удалены');

        } catch (\Exception $e) {

            Log::error($e);

        }

        return redirect()->back()->with('error', 'Данные не удалены');
    }
}
