<?php

namespace App\Http\Controllers\Backend\Api\V1\Applications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationApplications\OperationApplication;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {


        return response()->json("ok, application received", 201);
    }
}
