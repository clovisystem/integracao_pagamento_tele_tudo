<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class EntregaController extends Controller
{

    public function create()
    {
        return view('entrega.create');
    }

}
