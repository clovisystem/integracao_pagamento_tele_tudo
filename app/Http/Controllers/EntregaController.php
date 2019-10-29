<?php

namespace App\Http\Controllers;

use App\Entrega;
use App\Http\Requests;
use Illuminate\Http\Request;

class EntregaController extends Controller
{

    public function index()
    {
        return view('entrega.create');
    }

    public function create()
    {
        return view('entrega.create');
    }

    public function Aciona() {
        // passar o id da entrega
        return View::make('entrega.show');
    }

    /* public function aciona($id) {
        return view('entrega.show')
            ->with('identrega', $id);
    } */

    public function show($id)
    {
        $entr = Entrega::where("id",$id)->first();
        return view('entrega.show', compact('entr'));
    }

    // $entrega = Entrega::find($id);
    /* return view('entrega.show')
        ->with('idEntrega', $id); */


    public function Processa()
    {
        echo 'AQUI(Processa)'; die;
        return view('entrega.Processa');
    }

    public function Propria()
    {
        $entrega = Entrega::find(1);
        return view('entrega.resumo')
            ->with('entrega', $entrega);
    }

    public function store() {
        echo 'Entrega.Store'; die;
    }


}