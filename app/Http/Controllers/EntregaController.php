<?php

namespace App\Http\Controllers;

use App\Entrega;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /*public function Aciona() {
        // passar o id da entrega
        return View::make('entrega.show');
    }*/

    public function Aciona(Request $request) {

        $nome = $request->nome;
        $valortotal = $request->valortotal;
        $compra = $request->compra;
        $idped = $request->idped;
        $user = $request->user;
        $id_carteira = $request->id_carteira;
        $parcelas = $request->parcelas;
        $securitycode = $request->securitycode;
        $expirationdate = $request->expirationdate;
        $cardnumber = $request->cardnumber;
        $valorentrega = $request->valorentrega;
        $sandbox = $request->sandbox;
        $identrega = $request->identrega;



        return view('entrega.show', ['identrega' => $identrega,
                                     'nome' => $nome,
                                     'valortotal' => $valortotal,
                                     'compra' => $compra,
                                     'idped' => $idped,
                                     'user' => $user,
                                     'id_carteira' => $id_carteira,
                                     'parcelas' => $parcelas,
                                     'securitycode' => $securitycode,
                                     'expirationdate' => $expirationdate,
                                     'cardnumber' => $cardnumber,
                                     'valorentrega' => $valorentrega,
                                     'sandbox' => $sandbox]);

    }

    public function show($idUser, $idEntr)
    {
    
        $idPedidoEntr = DB::Table('entrega')->where("id",$idEntr)->first()->idPedido;


        return view('entrega.show', compact('idUser', 'idEntr', 'idPedidoEntr'));
    }




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