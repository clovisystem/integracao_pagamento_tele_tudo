<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CWG\Moip\MoipPagamento;
  

require '../vendor/autoload.php';


class OthersOptionsController extends Controller
{
    //

    public function Aciona(){

        $Valor = $_POST['valor'];
        $Descricao = $_POST['descricao'];
        $Id_Transacao = $_POST['id_transacao'];
        $Id_Transacao = $_POST['id_transacao'];
        $User = $_POST['user'];

        $token =  'J27IIMSM0MWSQJIXT1MDUTHZFBWMV4W2';
        $key = 'IEVEAUWW0E4GX6FPYIEUHC7YTJEGOFNYXCEPKAER';
        $sandbox = true;

        $moipPag = new MoipPagamento($token, $key, $sandbox);

        $scripts = $moipPag->setID($setID = uniqid())   //ID unico para identificar a compra
                                ->setPreco($Valor)   //Preço da compra
                                ->setDescricao($Descricao)
                                ->addFormaPagamento(MoipPagamento::CHECKOUT_CARTAO) //Libera forma de pagamento por cartão
                                ->getCheckoutTransparente();

        if($scripts){
            //return 'Sucesso '.$Valor.' '.$Descricao;
            return view('produtos.index', [ 'id' => $setID, 
                                            'id_transacao' => $Id_Transacao, 
                                            'valor' => $Valor, 
                                            'descricao' => $Descricao,
                                            'user' => $User
                                          ]);
            //return view('pedido.index', compact('setID', 'Id_Transacao', 'Valor', 'Descricao'));
        }
        else{
            return 'falha';
        }
    
    }
}
