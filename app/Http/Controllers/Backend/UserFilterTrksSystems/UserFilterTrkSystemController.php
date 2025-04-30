<?php

namespace App\Http\Controllers\Backend\UserFilterTrksSystems;

use App\Http\Controllers\Controller;
use App\Models\UserFilterTrksSystems\UserFilterTrkSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserFilterTrkSystemController extends Controller
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
    public function show(UserFilterTrkSystem $userFilterTrkSystem): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserFilterTrkSystem $userFilterTrkSystem): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserFilterTrkSystem $userFilterTrkSystem): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserFilterTrkSystem $userFilterTrkSystem): RedirectResponse
    {
        //
    }
}
