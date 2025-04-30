<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserSubscriptionEntitiesEvents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserSubscriptionEtitiesEventsController extends Controller
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
    public function show(UserSubscriptionEntitiesEvents $userSubscriptionEntitiesEvents): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserSubscriptionEntitiesEvents $userSubscriptionEntitiesEvents): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserSubscriptionEntitiesEvents $userSubscriptionEntitiesEvents): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserSubscriptionEntitiesEvents $userSubscriptionEntitiesEvents): RedirectResponse
    {
        //
    }
}
