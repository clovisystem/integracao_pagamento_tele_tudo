<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class VlrtransfController extends Controller
{
    public function Aciona() {
        return view('vlrtransf.index');
    }
}
