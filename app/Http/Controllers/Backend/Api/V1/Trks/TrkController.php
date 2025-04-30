<?php

namespace App\Http\Controllers\Backend\Api\V1\Trks;

use App\Http\Controllers\Controller;
use App\Models\Trks\Trk;

class TrkController extends Controller
{
    public function index()
    {
        $data = Trk::orderBy('name')->get();

        return response()->json($data);
    }
}
