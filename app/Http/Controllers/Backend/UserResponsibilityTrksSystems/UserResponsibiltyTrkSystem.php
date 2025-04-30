<?php

namespace App\Http\Controllers\Backend\UserResponsibilityTrksSystems;

use App\Http\Controllers\Controller;
use App\Models\UserResponsibilityTrksSystems\UserResponsibilityTrkSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserResponsibiltyTrkSystem extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        // $this->authorizeResource(UserResponsibilityTrkSystem::class, 'user_responsibility_trk_system');
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
    public function show(UserResponsibilityTrkSystem $userResponsibilityTrkSystem): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserResponsibilityTrkSystem $userResponsibilityTrkSystem): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserResponsibilityTrkSystem $userResponsibilityTrkSystem): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserResponsibilityTrkSystem $userResponsibilityTrkSystem): RedirectResponse
    {
        //
    }
}
