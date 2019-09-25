<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateController extends Controller
{
    public function aciona(){
        return view('pessoas/create');
    }

    

    public function store(Request $request)
    {
        dd('Estou aqui em: CategoryController no método store()');
    }
}
