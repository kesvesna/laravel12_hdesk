<?php

namespace App\Http\Controllers\Backend\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response()->view('backend.logs.index', []
        );
    }

}
