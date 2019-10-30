<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class VlrtransfController extends Controller
{
    public function Aciona() {
        return view('vlrtransf.index');
    }

    public function create()
    {
        $vlrtr = Pagamento::all();
        return View::make('vlrtransf.create')
            ->with('vlrtransf', $vlrtr);
    }

    public function store()
    {
        $input = Input::all();
        $sql = "Insert into vlrtransf (IdPagto, idConta, BCO, AGE, CTA, EMAIL) Values ( ";
        $sql = $sql.$input['txPagto'].', ';
        $sql = $sql.$input['txidConta'].', ';
        $sql = $sql.$input['BCO'].', ';
        $sql = $sql."'".$input['AGE']."', ";
        $sql = $sql."'".$input['CTA']."', ";
        $sql = $sql."'".$input['EMAIL']."')";
        DB::update($sql);

        // Obter o fornecedor do Pedido
        $qry = DB::table('pedidoItens')
            ->join('produtos', 'produtos.ID', '=', 'pedidoItens.idprod')
            ->select('produtos.Empresax_ID')
            ->where('pedidoItens.idPed', '=', $input['txPagto'])
            ->get();
        $ifForn = $qry[0]->Empresax_ID;
        $Teste=$Valor = $input['Teste'];
        $idEntrega = 0;
        $Valor = $input['VLRTOTAL'];
        if ($Teste==1) {
            $idEntrega = 1;
            $sql="Update notificacao set vizualizado = null, Confirmado = null ";
        } else {
            $idEntrega = DB::table('entrega')->max('id');
            $idTransf = DB::table('vlrtransf')->max('id');
            $sql = "Insert into notificacao (idPedido, idFornec, idTransf, Valor, Hora) Values ( ";
            $sql = $sql.$input['txPagto'].', ';     // idPedido
            $sql = $sql.$ifForn.', ';               // idFornec
            $sql = $sql.$idTransf.', ';             // idTransf
            $sql = $sql.$Valor.', ';                // Valor
            $sql = $sql.'now())';                   // Hora
        }
        DB::update($sql);
        /* $coment = $input['txMensagem'];
        if ($coment>'') {
            DB::update("Update pedido set Comentario = '".$coment."' where idPed = ".$input['txPagto']);
        } */
        return Redirect::to('/entrega/'.$idEntrega);
    }



    //CRIADO PARA REDIRECIONAMENTO DE PAAMENTO REALIZADO NO CARTÃO
    public function storeOthers(Request $request)
    {
        $Nome = $request->Nome;
        $ValorTotal = $request->ValorTotal;
        $compra = $request->compra;
        $Ped = $request->Ped;
        $User = $request->user;
        $id_carteira = $request->id_carteira;
        $parcelas = $request->parcelas;
        $securitycode = $request->securitycode;
        $expirationdate = $request->expirationdate;
        $cardnumber = $request->cardnumber;
        $valorEntrega = $request->valorEntrega;
        $sandbox = $request->sandbox;
        $idEntrega = $request->identrega;
        
        //return Redirect::to('/entrega/'.$idEntrega.$User.$Ped.$parcelas.$sandbox); //DEFINI UMA SIMULAÇÃO PARA O ID DA ENTREGA COM 1 EM PRODUÇÃO DEIXAR $idEntrega SOMENTE
    
        return redirect()->route('entregaothers', ['identrega' => $idEntrega,
                                                   'idped' => $Ped,
                                                   'valortotal' => $ValorTotal,
                                                   'compra' => $compra,
                                                   'user' => $User,
                                                   'parcelas' => $parcelas,
                                                   'sandbox' => $sandbox,
                                                   'valorentrega' => $valorEntrega,
                                                   'cardnumber' => $cardnumber,
                                                   'expirationdate' => $expirationdate,
                                                   'securitycode' => $securitycode ]); 
    
    }

}
