<?php

namespace App\Http\Controllers\Backend\TechActs;

use App\Exports\TechActs\TechActExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\TechActs\TechActFilter;
use App\Http\Requests\TechActs\StoreTechActFormRequest;
use App\Http\Requests\TechActs\TechActFilterRequest;
use App\Http\Requests\TechActs\UpdateTechActFormRequest;
use App\Models\DocCommunications\DocCommunication;
use App\Models\Executables\Executable;
use App\Models\OperationApplications\OperationApplication;
use App\Models\ResumeNames\ResumeName;
use App\Models\TechActs\TechAct;
use App\Models\TechActs\TechActResume;
use App\Models\TechActs\TechActSparePart;
use App\Models\Trks\Trk;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TechActController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(TechAct::class, 'tech_act');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TechActFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(TechActFilter::class, ['queryParams' => array_filter($data)]);

        $tech_acts = TechAct::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.tech_acts.pagination'));

        return \response()->view('backend.tech_acts.index', [
            'tech_acts' => $tech_acts,
            'all_tech_acts' => TechAct::orderBy('created_at', 'desc')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.tech_acts.create', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'users' => User::orderBy('name')->get(),
            'resumes' => ResumeName::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTechActFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new tech_act',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {

            try {

                $data = $request->validated();

                DB::beginTransaction();

                $tech_act = TechAct::create([
                    'write_at' => $data['write_at'],
                    'trk_id' => $data['trk_id'],
                    'inspection_at' => $data['inspection_at'],
                    'room_name' => $data['room_name'],
                    'equipment_name' => $data['equipment_name'],
                    'trouble_description' => $data['trouble_description'],
                    'reason_description' => $data['reason_description'],
                    'recovery_method_description' => $data['recovery_method_description'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                for ($i = 0; $i < count($data['spare_parts']); $i++) {

                    TechActSparePart::create([
                        'tech_act_id' => $tech_act->id,
                        'spare_part_name' => $data['spare_parts'][$i],
                        'price' => $data['prices'][$i],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $count = 1;

                foreach ($data['resumes'] as $resume_string)
                {
                    $resume = ResumeName::where('name', $resume_string)->first();

                    if(empty($resume->id))
                    {
                        return redirect()->back()->with('error', 'Нет такого решения комиссии: ' . $resume_string . ', выбирайте из тех что есть или попросите админа создать новые решения комиссии.')->withInput();
                    }

                    TechActResume::create([
                        'tech_act_id' => $tech_act->id,
                        'resume_name_id' => $resume->id,
                        'sort_order' => $count,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    $count++;

                }

                $data['executors'] = array_unique($data['users']);
                $executors = User::whereIn('name', $data['executors'])->get();

                Executable::where('executable_id', $tech_act->id)->where('executable_type', 'App\\Models\\TechActs\\TechAct')->delete();

                foreach ($executors as $executor) {

                    $executor->tech_acts()->save($tech_act);
                }

                DB::commit();

                switch ($data['operation_type']) {

                    case "save_and_create_repair": //TODO планирование ремонта из тех. акта
                        return redirect()->route('trk_repairs.create_from_tech_act', $tech_act)->with('success', 'Данные сохранены.');

                    case "save_and_create_avr":
                        return redirect()->route('avrs.create_from_tech_act', $tech_act)->with('success', 'Данные сохранены.');

                    default:
                        return redirect()->route('tech_acts.index', $tech_act)->with('success', 'Данные сохранены.');
                }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_from_operation_application(OperationApplication $operation_application): Response
    {
        $this->authorize('create');

        return \response()->view('backend.tech_acts.create_from_operation_application', [
            'trks' => Trk::orderBy('sort_order')->get(),
            'users' => User::orderBy('name')->get(),
            'resumes' => ResumeName::orderBy('name')->get(),
            'operation_application' => $operation_application,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store_from_operation_application(StoreTechActFormRequest $request, OperationApplication $operation_application): RedirectResponse
    {
        $this->authorize('create');

        Log::info('User try to store new tech_act from operation application',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
                'operation_application' => $operation_application,
            ]);

        if ($request->isMethod('post')) {

            try {

                $data = $request->validated();

                DB::beginTransaction();

                $tech_act = TechAct::create([
                    'write_at' => $data['write_at'],
                    'trk_id' => $data['trk_id'],
                    'inspection_at' => $data['inspection_at'],
                    'room_name' => $data['room_name'],
                    'equipment_name' => $data['equipment_name'],
                    'trouble_description' => $data['trouble_description'],
                    'reason_description' => $data['reason_description'],
                    'recovery_method_description' => $data['recovery_method_description'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                for ($i = 0; $i < count($data['spare_parts']); $i++) {
                    TechActSparePart::create([
                        'tech_act_id' => $tech_act->id,
                        'spare_part_name' => $data['spare_parts'][$i],
                        'price' => $data['prices'][$i],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $resume_ids = ResumeName::whereIn('name', $data['resumes'])->pluck('id')->toArray();

                $count = 1;
                foreach ($resume_ids as $resume_id) {
                    TechActResume::create([
                        'tech_act_id' => $tech_act->id,
                        'resume_name_id' => $resume_id,
                        'sort_order' => $count,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                    $count++;
                }

                $data['executors'] = array_unique($data['users']);
                $executors = User::whereIn('name', $data['executors'])->get();

                Executable::where('executable_id', $tech_act->id)->where('executable_type', 'App\\Models\\TechActs\\TechAct')->delete();

                foreach ($executors as $executor) {

                    $executor->tech_acts()->save($tech_act);
                }

                DocCommunication::create([
                    'from_id' => $operation_application->id,
                    'from_type' => get_class($operation_application),
                    'to_id' => $tech_act->id,
                    'to_type' => get_class($tech_act),
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                switch ($data['operation_type']) {

                    case "save_and_create_repair":
                        return redirect()->route('trk_repairs.create_from_tech_act', $tech_act)->with('success', 'Данные сохранены.');

                    case "save_and_create_avr":
                        return redirect()->route('avrs.create_from_tech_act', $tech_act)->with('success', 'Данные сохранены.');

                    default:
                        return redirect()->route('tech_acts.index', $tech_act)->with('success', 'Данные сохранены.');
                }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.')->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(TechAct $tech_act): Response
    {

        return \response()->view('backend.tech_acts.show', [
            'tech_act' => $tech_act,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TechAct $tech_act): Response
    {
        return \response()->view('backend.tech_acts.edit', [
            'tech_act' => $tech_act,
            'trks' => Trk::orderBy('sort_order')->get(),
            'users' => User::orderBy('name')->get(),
            'resumes' => ResumeName::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTechActFormRequest $request, TechAct $tech_act): RedirectResponse
    {
        Log::info('User try to update tech_act',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            try {
                $data = $request->validated();

                DB::beginTransaction();

                $tech_act->update([
                    'write_at' => $data['write_at'],
                    'trk_id' => $data['trk_id'],
                    'inspection_at' => $data['inspection_at'],
                    'room_name' => $data['room_name'],
                    'equipment_name' => $data['equipment_name'],
                    'trouble_description' => $data['trouble_description'],
                    'reason_description' => $data['reason_description'],
                    'recovery_method_description' => $data['recovery_method_description'],
                    'last_editor_id' => Auth::id(),
                ]);


                TechActSparePart::withTrashed()->where('tech_act_id', $tech_act->id)->forceDelete();

                for ($i = 0; $i < count($data['spare_parts']); $i++) {
                    TechActSparePart::create([
                        'tech_act_id' => $tech_act->id,
                        'spare_part_name' => $data['spare_parts'][$i],
                        'price' => $data['prices'][$i],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $resume_ids = ResumeName::whereIn('name', $data['resumes'])->pluck('id')->toArray();

                TechActResume::withTrashed()->where('tech_act_id', $tech_act->id)->forceDelete();

                foreach ($resume_ids as $resume_id) {
                    TechActResume::create([
                        'tech_act_id' => $tech_act->id,
                        'resume_name_id' => $resume_id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $data['executors'] = array_unique($data['users']);
                $executors = User::whereIn('name', $data['executors'])->get();

                Executable::where('executable_id', $tech_act->id)->where('executable_type', 'App\\Models\\TechActs\\TechAct')->delete();

                foreach ($executors as $executor) {
                    $executor->tech_acts()->save($tech_act);
                }

                DB::commit();

                return redirect()->back()->with('success', 'Изменения сохранены');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TechAct $tech_act): RedirectResponse
    {
        Log::info('User try to delete tech_act',
            [
                'user' => Auth::user()->name,
                'work_name' => $tech_act,
            ]);

        try {

            DB::beginTransaction();

            TechActSparePart::where('tech_act_id', $tech_act->id)->delete();

            TechActResume::where('tech_act_id', $tech_act->id)->delete();

            Executable::where('executable_id', $tech_act->id)
                ->where('executable_type', 'App\\Models\\TechActs\\TechAct')
                ->delete();

            $tech_act->update([
                'destroyer_id' => Auth::id(),
            ]);

            $tech_act->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('tech_acts.index')->with('success', 'Данные удалены');
    }

    public function export(TechAct $tech_act)
    {
        $this->authorize('read');

        return (new TechActExportPdf(
            $tech_act
        ))->export_pdf();
    }
}
