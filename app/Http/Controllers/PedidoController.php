<?php

namespace App\Http\Controllers;

use App\Pedido;

/* use Illuminate\Http\Request;
use App\Http\Requests; */

class PedidoController extends Controller
{

    public function Pagtodireto()
    {
        return view('pedido.pagtodireto');
    }

    public function Criapedido()
    {
        return view('pedido.criapedido');
    }

    public function Aciona() {
        return view('pedido.index');
    }

    public function show($id)
    {
        $pedido = DB::table('pedido')
            ->where('idPed', '=', $id)
            ->first();
        return view('pedido.show')
            ->with('pedido', $pedido);
    }

    public function portaldaluz()
    {
        return view('/portaldaluz.com');
    }

}
