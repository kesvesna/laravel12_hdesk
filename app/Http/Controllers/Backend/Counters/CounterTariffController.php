<?php

namespace App\Http\Controllers\Backend\Counters;

use App\Http\Controllers\Controller;
use App\Models\Counters\CounterTariff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CounterTariffController extends Controller
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
    public function show(CounterTariff $counterTariff): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CounterTariff $counterTariff): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CounterTariff $counterTariff): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CounterTariff $counterTariff): RedirectResponse
    {
        //
    }
}
