<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CWG\Moip\MoipPagamento;
use App\Clientes;

require '../vendor/autoload.php';


class OthersOptionsController extends Controller
{
    //

    public function Aciona(Request $request){

        $Valor = $request->input('valor');
        $Descricao = $request->input('descricao');
        $Ped = $request->input('id_transacao');
        $User = $request->input('user');
        $id_carteira = $request->input('id_carteira');
        $sandbox = true;
        $setID = uniqid();
/*
        $Valor = $_POST['valor'];
        $Descricao = $_POST['descricao'];
        $Ped = $_POST['id_transacao'];
        $User = $_POST['user'];
        $Id_Carteira = $_POST['id_carteira'];
*/     

        /*$token =  'J27IIMSM0MWSQJIXT1MDUTHZFBWMV4W2';
        $key = 'IEVEAUWW0E4GX6FPYIEUHC7YTJEGOFNYXCEPKAER';
        $sandbox = true;*/
        

        /*$moipPag = new MoipPagamento($token, $key, $sandbox, $Ped, $id_carteira, $User );

        $scripts = $moipPag->setID($setID = uniqid())   //ID unico para identificar a compra
                                ->setPreco($Valor)   //Preço da compra
                                ->setDescricao($Descricao)
                                ->addFormaPagamento(MoipPagamento::CHECKOUT_CARTAO) //Libera forma de pagamento por cartão
                                ->getCheckoutTransparente();

        /*if(!$scripts){
            return '<link rel="stylesheet" href="css/app.css">
            <label style="font-size:20px; color:red; margin-left:28%; margin-top:20%;">
            Falha no pagamento, você será redirecionado a página anterior em 4 segundos</label>
            <script>setTimeout(function(){history.go(-1);}, 4000);</script>';
            
            
        }
        else{
            /*return view('produtos.index', [ 'id' => $setID, 
                                            'id_transacao' => $Ped, 
                                            'valor' => $Valor, 
                                            'descricao' => $Descricao,
                                            'user' => $User
                                            ]); //ESSA VIEW DEVE VIR DEPOIS DA COLOCAÇÃO DOS DADOS DO CARTÃO ABAIXO NA PÁGINA*/
            $Nome = Clientes::where('user', $User)->first()->Nome;

            return view('pagamento.index', ['id' => $setID, 
                                            'id_transacao' => $Ped, 
                                            'valor' => $Valor, 
                                            'descricao' => $Descricao,
                                            'User' => $User,
                                            'Nome' => $Nome,
                                            'id_carteira' => $id_carteira,
                                            'sandbox' => $sandbox
                                           ]); //ESSA VIEW DEVE SER USADA COM PRIORIDADE EM RELAÇÃO À DE CIMA PARA EXIBIR PÁINA ONDE O USUÁRIO INSERE INFORMAÇÕES DO CARTÂO


            //COMENTE EM PRODUÇÃO O COMANDO ABAIXO, EM TESTES COMENTE O SCRIPT DE CIMA
            //return 'Usuário: '.$User.'<br/>ID: '.$setID.'<br/>PEDIDO: '.$Ped.'<br/>VALOR R$: '.$Valor.'<br/>PRODUTO: '.$Descricao.'<br/>VENDEDOR: '.$Id_Carteira.'<br/>SANDBOX? '.$sandbox;
        
            //return 'sucesso';
            
    //}*/
    
    }

    public function Checkout(Request $request){
        //$id = $request->input('id');
        $Nome = $request->input('name');
        $Valor = $request->input('valor');
        $Descricao = $request->input('descricao');
        $Ped = $request->input('id_transacao');
        $User = $request->input('User');
        $id_carteira = $request->input('id_carteira');
        $sandbox = $request->input('sandbox');




        $token =  'J27IIMSM0MWSQJIXT1MDUTHZFBWMV4W2';
        $key = 'IEVEAUWW0E4GX6FPYIEUHC7YTJEGOFNYXCEPKAER';
        $sandbox = true;

        $moipPag = new MoipPagamento($token, $key, $sandbox, $Ped, $id_carteira, $User );

        $scripts = $moipPag->setID($setID = uniqid())   //ID unico para identificar a compra
                            ->setPreco($Valor)   //Preço da compra
                            ->setDescricao($Descricao)
                            ->addFormaPagamento(MoipPagamento::CHECKOUT_CARTAO) //Libera forma de pagamento por cartão
                            ->getCheckoutTransparente();

        If($scripts){
            $Nome = Clientes::where('user', $User)->first()->Nome;
            //COMENTE EM PRODUÇÃO O COMANDO ABAIXO, EM TESTES COMENTE O SCRIPT DE CIMA
            return 'Usuário: '.$User.'<br/>ID: '.$setID.'<br/>PEDIDO: '.$Ped.'<br/>VALOR R$: '.$Valor.'<br/>PRODUTO: '.$Descricao.'<br/>VENDEDOR: '.$id_carteira.'<br/>SANDBOX? '.$sandbox;
        
            //return 'sucesso';

            /*return view('produtos.index', [ 'id' => $setID, 
                                'id_transacao' => $Ped, 
                                'valor' => $Valor, 
                                'descricao' => $Descricao,
                                'user' => $User
                                ]); //ESSA VIEW DEVE VIR DEPOIS DA COLOCAÇÃO DOS DADOS DO CARTÃO ABAIXO NA PÁGINA*/
                                

          
        }
        else{
            return '<link rel="stylesheet" href="css/app.css">
            <label style="font-size:20px; color:red; margin-left:25%; margin-top:20%;">
            Falha no pagamento, você será redirecionado a página anterior em 4 segundos</label>
            <script>setTimeout(function(){history.go(-1);}, 4000);</script>';
            echo $Valor;
        } 
    
    }

}
