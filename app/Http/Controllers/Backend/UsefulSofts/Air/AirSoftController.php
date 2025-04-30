<?php

namespace App\Http\Controllers\Backend\UsefulSofts\Air;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;


class AirSoftController extends Controller
{
    public function __invoke(): Response
    {
        return \response()->view('backend.useful_soft.air.air_flow', [
        ]);
    }
}
