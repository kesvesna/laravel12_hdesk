<?php

namespace App\Http\Controllers\Backend\Renters;

use App\Http\Controllers\Controller;
use App\Http\Requests\Renters\StoreRenterFormRequest;
use App\Http\Requests\Renters\UpdateRenterFormRequest;
use App\Models\Organizations\Organization;
use App\Models\Towns\Town;
use App\Models\TrkRoles\TrkRole;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\UserFunctions\UserFunction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RenterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(Document::class, 'document');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $trk_role_id = UserDivision::where('alias', 'renter')->pluck('id')->first();

        $renters = User::where('user_division_id', $trk_role_id)->paginate(config('backend.renters.pagination'));

        return \response()->view('backend.renters.index', [
            'renters' => $renters,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return response()->view('backend.renters.create', [
            'towns' => Town::all(),
            'organizations' => Organization::all(),
            'divisions' => UserDivision::where('alias', 'renter')
                ->orderby('sort_order')
                ->get(),
            'functions' => UserFunction::orderBy('name')
                ->orderby('sort_order')
                ->get(),
            'superiors' => User::whereNot('id', Auth::id())->get(),
            'user' => User::find(Auth::id()),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRenterFormRequest $request): RedirectResponse
    {
        if ($request->isMethod('post')) {

            Log::info('User try to store profile.',
                [
                    'user_id' => Auth::id(),
                    'request' => $request->all(),
                ]
            );

            $data = $request->validated();

            try {

                $renter_role_id = TrkRole::where('alias', TrkRole::RENTER)->firstOrFail()->id;

                $new_renter = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'town_id' => $data['town_id'],
                    'trk_role_id' => $renter_role_id,
                    'password' => Hash::make($data['password']),
                    'organization_id' => $data['organization_id'],
                    'user_function_id' => $data['user_function_id'],
                    'user_division_id' => UserDivision::where('alias', 'arendator')->firstOrFail()->id,
                ]);

                return redirect()->route('renters.index')->with('success', 'Данные сохранены.');

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
    public function show(string $id): Response
    {
        $renter = User::find($id);

        return \response()->view('backend.renters.show', [
            'renter' => $renter,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response|RedirectResponse
    {
        Log::info('User try to edit profile.', [
            'user' => Auth::user()->name,
            'request' => $id,
        ]);

        $user = User::find($id);

        $divisions = UserDivision::where('trk_role_alias', TrkRole::RENTER)->orderBy('name')->get();
        $functions = UserFunction::where('trk_role_alias', TrkRole::RENTER)->orderBy('name')->get();
        $superiors = User::whereNot('name', Auth::user()->name)->orderBy('name')->get();

        return \response()->view('backend.renters.edit', [
            'user' => $user,
            'towns' => Town::orderBy('name')->get(),
            'organizations' => Organization::orderBy('name')->get(),
            'divisions' => $divisions,
            'functions' => $functions,
            'superiors' => $superiors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRenterFormRequest $request, string $id): RedirectResponse
    {
        Log::info('User try to update profile.', [
            'user' => Auth::user()->name,
            'request' => $request->all(),
        ]);

        if ($request->isMethod('patch')) {
            $data = $request->validated();

            try {

                $user = User::where('id', $id)->firstOrFail();

                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'] ?? $user->email,
                    'phone' => $data['phone'],
                    'town_id' => $data['town_id'],
                    'organization_id' => $data['organization_id'],
                    'user_function_id' => $data['user_function_id'],
                    'password' => $data['password'] ? Hash::make($data['password']) : $user['password'],
                ]);

                return redirect()->back()->with('success', 'Данные сохранены');

            } catch (\Exception $e) {

                Log::error($e);
                return redirect()->back()->with('error', 'Ошибка при сохранении. Смотрите логи.');

            }
        }

        return redirect()->back()->with('error', 'Данные не сохранены');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        dd('not so fast');
    }
}
