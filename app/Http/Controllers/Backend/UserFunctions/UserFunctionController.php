<?php

namespace App\Http\Controllers\Backend\UserFunctions;

use App\Http\Controllers\Controller;
use App\Models\UserFunctions\UserFunction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserFunctionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        //$this->authorizeResource(UserFunction::class, 'user_function');
    }

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
    public function show(UserFunction $userFunction): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserFunction $userFunction): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserFunction $userFunction): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserFunction $userFunction): RedirectResponse
    {
        //
    }
}
