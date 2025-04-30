<?php

namespace App\Http\Controllers\Backend\ResumeNames;

use App\Http\Controllers\Controller;
use App\Http\Filters\ResumeNames\ResumeNameFilter;
use App\Http\Requests\ResumeNames\ResumeNameFilterRequest;
use App\Http\Requests\ResumeNames\StoreResumeNameFormRequest;
use App\Http\Requests\ResumeNames\UpdateResumeNameFormRequest;
use App\Models\ResumeNames\ResumeName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResumeNameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->authorizeResource(ResumeName::class, 'resume_name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ResumeNameFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(ResumeNameFilter::class, ['queryParams' => array_filter($data)]);

        $resume_names = ResumeName::filter($filter)
            ->orderBy('created_at', 'desc')
            ->orderBy('name')
            ->paginate(config('backend.resume_names.pagination'));

        return \response()->view('backend.resume_names.index', [
            'resume_names' => $resume_names,
            'all_resume_names' => ResumeName::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return \response()->view('backend.resume_names.create', [
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResumeNameFormRequest $request): RedirectResponse
    {
        Log::info('User try to store new resume name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('post')) {
            try {
                $data = $request->validated();
                ResumeName::create([
                    'name' => $data['name'],
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);
                return redirect()->route('resume_names.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ResumeName $resume_name): Response
    {
        return \response()->view('backend.resume_names.show', [
            'resume_name' => $resume_name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResumeName $resume_name): Response
    {
        return \response()->view('backend.resume_names.edit', [
            'resume_name' => $resume_name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResumeNameFormRequest $request, ResumeName $resume_name): RedirectResponse
    {
        Log::info('User try to update resume name',
            [
                'user' => Auth::user()->name,
                'request' => $request->all(),
            ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            $resume_name->update([
                'name' => $data['name'],
                'last_editor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Изменения сохранены');
        }
        return redirect()->back()->with('error', 'Изменения не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResumeName $resume_name): RedirectResponse
    {
        Log::info('User try to delete resume name',
            [
                'user' => Auth::user()->name,
                'work_name' => $resume_name,
            ]);

        try {
            $resume_name->update([
                'destroyer_id' => Auth::id(),
            ]);
            $resume_name->delete();
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('resume_names.index')->with('success', 'Данные удалены');
    }
}
