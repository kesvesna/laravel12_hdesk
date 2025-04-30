<?php

namespace App\Http\Controllers\Backend\UserDivisions;

use App\Http\Controllers\Controller;
use App\Models\UserDivisions\UserDivision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserDivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(UserDivision::class, 'user_division');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
//        $data = UserDivision::orderBy('name')
//            ->whereNotIn('name', ['Подрядчик', 'ДЭТК', 'Охрана', 'Арендатор', 'ТСО'])
//            ->get();
//
//        return response()->json($data);
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
    public function show(UserDivision $division): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDivision $division): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserDivision $division): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDivision $division): RedirectResponse
    {
        //
    }
}
