<?php

namespace App\Http\Controllers\Backend\UsefulSofts\Shopping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;


class ShoppingSoftController extends Controller
{
    public function __invoke(): Response
    {
        return \response()->view('backend.useful_soft.shopping.price_for_one_kilo', [
        ]);
    }
}
