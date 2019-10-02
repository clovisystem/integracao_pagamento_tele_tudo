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

        $token =  'J27IIMSM0MWSQJIXT1MDUTHZFBWMV4W2';
        $key = 'IEVEAUWW0E4GX6FPYIEUHC7YTJEGOFNYXCEPKAER';
        $sandbox = true;

        $moipPag = new MoipPagamento($token, $key, $sandbox);

        $scripts = $moipPag->setID(uniqid())   //ID unico para identificar a compra
                                ->setPreco($Valor)   //Preço da compra
                                ->setDescricao($Descricao)
                                ->addFormaPagamento(MoipPagamento::CHECKOUT_CARTAO) //Libera forma de pagamento por cartão
                                ->getCheckoutTransparente();

        if($scripts){
            return 'Sucesso '.$Valor.' '.$Descricao;
        }
        else{
            return 'falha';
        }
    
    }
}
