<?php

namespace App\Http\Controllers;

use App\Pedido;
use Illuminate\Http\Request;

/* use Illuminate\Http\Request;
use App\Http\Requests; */

class PedidoController extends Controller
{


    /*public function Index() {
        return view('pedido.index');
    }*/ //COLOCADO PARA TESTES

    public function Pagtodireto()
    {
        return view('pedido.pagtodireto');
    }

    public function Criapedido()
    {
        return view('pedido.criapedido');
    }

    public function Aciona(Request $request) {

        $id_carteira = $request->id_carteira;

        $valor = $request->valor;
    
        $descricao = $request->descricao;
    
        $tpEnt = $request->tpEnt;
    
        $idPed = $request->IDPED;
    
        //$VlrEntrega = $request->VlrEntrega;
    
        $user = $request->user;
    
        $nome = $request->nome;

        return view('pedido.index')
            ->with(['id_carteira' => $id_carteira,
                    'valor' => $valor,
                    'descricao' => $descricao,
                    'tpEnt' => $tpEnt,
                    'idPed' => $idPed,
                    'user' => $user,
                    'nome' => $nome]
                );
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



