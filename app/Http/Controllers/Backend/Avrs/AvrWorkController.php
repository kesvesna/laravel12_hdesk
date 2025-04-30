<?php

namespace App\Http\Controllers\Backend\Avrs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Avrs\UpdateWorkInAvrsFromWorkNameFormRequest;
use App\Models\Avrs\AvrWork;
use App\Models\WorkNames\WorkName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AvrWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AvrWork $avrWork): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AvrWork $avrWork): Response
    {
        //
    }

    public function change_work_name_in_avrs(UpdateWorkInAvrsFromWorkNameFormRequest $request, WorkName $work_name): RedirectResponse
    {
        //$this->authorize('change_work_name_in_avrs');

        Log::info('user try to change work name in avrs from work name show page', [

            'user' => Auth::user()->name,
            'request' => $request->all(),
            'work_name' => $work_name,

        ]);

        if ($request->isMethod('patch')) {

            try {

                $data = $request->validated();

                $avr_works = AvrWork::where('work_name_id', $work_name->id)->get();

                foreach ($avr_works as $avr_work) {
                    foreach ($data['works'] as $key => $value) {

                        $new_avr_work = AvrWork::where('work_name_id', $key)
                            ->where('trk_equipment_id', $avr_work->trk_equipment_id)
                            ->where('avr_id', $avr_work->avr_id)
                            ->first();

                        if ($new_avr_work === null) {

                            $new_avr_work = AvrWork::create([
                                'avr_id' => $avr_work->avr_id,
                                'trk_equipment_id' => $avr_work->trk_equipment_id,
                                'description' => $avr_work->description,
                                'author_id' => $avr_work->author_id,
                                'last_editor_id' => Auth::id(),
                                'work_name_id' => $key,
                            ]);

                        }
                    }

                    $avr_work->delete();
                }

                return redirect()->route('work_names.show', $work_name->id)->with('success', 'Данные сохранены.');

            } catch (\Exception $e) {

                Log::error($e);
            }
        }

        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AvrWork $avrWork): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AvrWork $avrWork): RedirectResponse
    {
        //
    }


}
