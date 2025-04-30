<?php

namespace App\Http\Controllers\Backend\Users;

use App\Exports\UserTimeSheets\UserTimeSheetExport;
use App\Exports\UserTimeSheets\UserTimeSheetExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Filters\Users\UserResultTimeSheetFilter;
use App\Http\Requests\Exports\ExportUserTimeSheetFormRequest;
use App\Http\Requests\Users\StoreUserTimeSheetFormRequest;
use App\Http\Requests\Users\UpdateUserTimeSheetFormRequest;
use App\Http\Requests\Users\UserResultTimeSheetFilterRequest;
use App\Models\User;
use App\Models\Users\UserResultTimeSheet;
use App\Models\Users\UserTimeSheet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UserTimeSheetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserResultTimeSheetFilterRequest $request): Response
    {
        $data = $request->validated();

        $filter = app()->make(UserResultTimeSheetFilter::class, ['queryParams' => array_filter($data)]);

        $user_result_time_sheets = UserResultTimeSheet::filter($filter)
            ->orderBy('created_at', 'desc')
            ->paginate(config('backend.user_time_sheets.pagination'));

        $years = [];
        $current_year = date('Y');
        $years[] = $current_year - 1;
        $years[] = $current_year;
        $years[] = $current_year + 1;

        $months = [
            '01' => 'январь',
            '02' => 'февраль',
            '03' => 'март',
            '04' => 'апрель',
            '05' => 'май',
            '06' => 'июнь',
            '07' => 'июль',
            '08' => 'август',
            '09' => 'сентябрь',
            '10' => 'октябрь',
            '11' => 'ноябрь',
            '12' => 'декабрь',
        ];

        return \response()->view('backend.user_time_sheets.index', [
            'user_result_time_sheets' => $user_result_time_sheets,
            'years' => $years,
            'months' => $months,
            'users' => User::orderBy('name')->get(),
            'old_filters' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {

        $years = [];
        $current_year = date('Y');
        $years[] = $current_year - 1;
        $years[] = $current_year;
        $years[] = $current_year + 1;

        $months = [
            '01' => 'январь',
            '02' => 'февраль',
            '03' => 'март',
            '04' => 'апрель',
            '05' => 'май',
            '06' => 'июнь',
            '07' => 'июль',
            '08' => 'август',
            '09' => 'сентябрь',
            '10' => 'октябрь',
            '11' => 'ноябрь',
            '12' => 'декабрь',
        ];


        return \response()->view('backend.user_time_sheets.create', [
            'years' => $years,
            'months' => $months,
            'users' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserTimeSheetFormRequest $request): RedirectResponse
    {

        if ($request->isMethod('post')) {
            Log::info('User try to store time sheet',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                if (
                    UserResultTimeSheet::where('user_id', $data['user_id'])
                        ->where('year', $data['year'])
                        ->where('month', $data['month'])
                        ->exists()
                ) {
                    return redirect()->back()->with('error', 'Такой табель уже существует. Воспользуйтесь редактированием.');
                }

                $days_in_this_month = cal_days_in_month(CAL_JULIAN, (int)$data['month'], (int)$data['year']);

                $time_sheet = [];
                $time_sheets = [];

                for ($i = 1; $i <= $days_in_this_month; $i++) {
                    $time_sheet['date'] = $data['year'] . '-' . $data['month'] . '-' . $i;

                    $weekday = (int)date('N', strtotime($time_sheet['date']));

                    switch ($weekday) {
                        case 1:
                            $time_sheet['finish'] = '18:30:00';
                            $time_sheet['start'] = '09:30:00';
                            $start = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['start']);
                            $end = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['finish']);
                            $duration = $end->diffInSeconds($start) - 3600;
                            $time_sheet['result'] = gmdate('H:i', $duration);
                            $time_sheet['is_holiday'] = 0;
                            break;
                        case 2:
                            $time_sheet['finish'] = '18:30:00';
                            $time_sheet['start'] = '09:30:00';
                            $start = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['start']);
                            $end = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['finish']);
                            $duration = $end->diffInSeconds($start) - 3600;
                            $time_sheet['result'] = gmdate('H:i', $duration);
                            $time_sheet['is_holiday'] = 0;
                            break;
                        case 3:
                            $time_sheet['finish'] = '18:30:00';
                            $time_sheet['start'] = '09:30:00';
                            $start = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['start']);
                            $end = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['finish']);
                            $duration = $end->diffInSeconds($start) - 3600;
                            $time_sheet['result'] = gmdate('H:i', $duration);
                            $time_sheet['is_holiday'] = 0;
                            break;
                        case 4:
                            $time_sheet['finish'] = '18:30:00';
                            $time_sheet['start'] = '09:30:00';
                            $start = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['start']);
                            $end = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['finish']);
                            $duration = $end->diffInSeconds($start) - 3600;
                            $time_sheet['result'] = gmdate('H:i', $duration);
                            $time_sheet['is_holiday'] = 0;
                            break;
                        case 5:
                            $time_sheet['finish'] = '17:30:00';
                            $time_sheet['start'] = '09:30:00';
                            $start = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['start']);
                            $end = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['finish']);
                            $duration = $end->diffInSeconds($start);
                            $time_sheet['result'] = gmdate('H:i', $duration);
                            $time_sheet['is_holiday'] = 0;
                            break;
                        case 6:
                            $time_sheet['finish'] = '00:00:00';
                            $time_sheet['start'] = '00:00:00';
                            $start = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['start']);
                            $end = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['finish']);
                            $duration = $end->diffInSeconds($start);
                            $time_sheet['result'] = gmdate('H:i', $duration);
                            $time_sheet['is_holiday'] = 1;
                            break;
                        case 7:
                            $time_sheet['finish'] = '00:00:00';
                            $time_sheet['start'] = '00:00:00';
                            $start = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['start']);
                            $end = Carbon::parse($time_sheet['date'] . ' ' . $time_sheet['finish']);
                            $duration = $end->diffInSeconds($start);
                            $time_sheet['result'] = gmdate('H:i', $duration);
                            $time_sheet['is_holiday'] = 1;
                            break;
                    }

                    $time_sheets[] = $time_sheet;
                }

                $result_time = null;

                DB::beginTransaction();

                foreach ($time_sheets as $item) {
                    UserTimeSheet::create([
                        'user_id' => $data['user_id'],
                        'date' => $item['date'],
                        'start' => $item['start'],
                        'finish' => $item['finish'],
                        'is_holiday' => $item['is_holiday'],
                        'result' => $item['result'],
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                    $result_time += (int)$item['result'];
                }

                UserResultTimeSheet::create([
                    'user_id' => $data['user_id'],
                    'year' => $data['year'],
                    'month' => $data['month'],
                    'result' => $result_time,
                    'overtime' => 0,
                    'author_id' => Auth::id(),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('user_time_sheets.index')->with('success', 'Данные сохранены');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, string $year, string $month): Response
    {
        $user_time_sheets = UserTimeSheet::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        $user_result_time_sheet = UserResultTimeSheet::where('user_id', $user->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        return \response()->view('backend.user_time_sheets.show', [
            'user_time_sheets' => $user_time_sheets,
            'user' => $user,
            'year' => $year,
            'month' => $month,
            'user_result_time_sheet' => $user_result_time_sheet,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, string $year, string $month): Response
    {

        $user_time_sheets = UserTimeSheet::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        $user_result_time_sheet = UserResultTimeSheet::where('user_id', $user->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        return \response()->view('backend.user_time_sheets.edit', [
            'user_time_sheets' => $user_time_sheets,
            'user' => $user,
            'year' => $year,
            'month' => $month,
            'user_result_time_sheet' => $user_result_time_sheet,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserTimeSheetFormRequest $request, UserTimeSheet $user_time_sheet): RedirectResponse
    {
        if ($request->isMethod('patch')) {
            Log::info('User try to update time sheet',
                [
                    'user' => Auth::user()->name,
                    'request' => $request,
                ]);

            try {

                $data = $request->validated();

                $result_time = null;
                $result_over_time = null;

                DB::beginTransaction();

                foreach ($data['user_time_sheets'] as $key => $value) {

                    $over_time = 0;

                    $user_time_sheet = UserTimeSheet::where('id', $key)
                        ->first();

                    $start = Carbon::parse($value['start']);
                    $end = Carbon::parse($value['finish']);

                    $work_time_in_minutes = $end->diffInMinutes($start);

                    if ( // если не выходной и не пятница отнимаем один час из-за обеда
                        !$user_time_sheet->is_holiday
                        && date('w', strtotime($user_time_sheet->date)) != 5
                    ) {
                        $work_time_in_minutes -= 60;
                    }

                    // если больше восьми часов и не выходной, считаем переработку
                    if ($work_time_in_minutes > 480 && !$user_time_sheet->is_holiday) {
                        $over_time = $work_time_in_minutes - 480;
                    }

                    // если выходной то все часы переработка
                    if ($user_time_sheet->is_holiday) {
                        $over_time = $work_time_in_minutes;
                    }

                    $user_time_sheet->update([
                        'start' => $value['start'],
                        'finish' => $value['finish'],
                        'result' => intdiv($work_time_in_minutes, 60) . ':' . ($work_time_in_minutes % 60),
                        'overtime' => intdiv($over_time, 60) . ':' . ($over_time % 60),
                    ]);

                    $result_time += $work_time_in_minutes;
                    $result_over_time += $over_time;
                }

                $user_time_sheet = UserResultTimeSheet::find($data['user_result_time_sheet_id']);

                $user_time_sheet->update([
                    'result' => intdiv($result_time, 60) . ':' . ($result_time % 60),
                    'overtime' => intdiv($result_over_time, 60) . ':' . ($result_over_time % 60),
                    'last_editor_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('user_time_sheets.index')->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e);

            }
        }
        return redirect()->back()->with('error', 'Ошибка сохранения данных, смотрите логи.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, string $year, string $month): RedirectResponse
    {
        Log::info('User try to delete user time sheet',
            [
                'user' => Auth::user()->name,
                'user_time_sheet' => $user,
                'year' => $year,
                'month' => $month,
            ]);

        try {

            DB::beginTransaction();

            UserResultTimeSheet::where('user_id', $user->id)
                ->where('year', $year)
                ->where('month', $month)
                ->delete();

            UserTimeSheet::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', 'Данные не удалены');
        }
        return redirect()->route('user_time_sheets.index')->with('success', 'Данные удалены');
    }

    public function export(ExportUserTimeSheetFormRequest $request)
    {
        $data = $request->validated();

        if (
            !UserResultTimeSheet::where('user_id', $data['user_id'])
                ->where('year', $data['year'])
                ->where('month', $data['month'])
                ->exists()
        ) {
            return redirect()->back()->with('error', 'Нет такого табеля в базе');
        }

        switch ($data['file_type']) {
            case '.pdf':

                return (new UserTimeSheetExportPdf(
                    $data['user_id'],
                    $data['year'],
                    $data['month'],
                    $data['file_type'],
                ))->export_pdf();

            case '.html':
                return Excel::download(new UserTimeSheetExport(
                    $data['user_id'],
                    $data['year'],
                    $data['month'],
                    $data['file_type'],
                ), 'Tабель__' . $data['year'] . '__' . $data['month'] . $data['file_type']);

            default:
                return Excel::download(new UserTimeSheetExport(
                    $data['user_id'],
                    $data['year'],
                    $data['month'],
                    $data['file_type'],
                ), 'Tабель__' . $data['year'] . '__' . $data['month'] . '.xlsx');

        }

    }
}
