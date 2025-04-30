<?php

namespace App\Http\Controllers\Backend\Executables;

use App\Http\Controllers\Controller;
use App\Models\Executables\Executable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExecutableController extends Controller
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
    public function show(Executable $executable): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Executable $executable): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Executable $executable): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Executable $executable): RedirectResponse
    {
        //
    }
}
