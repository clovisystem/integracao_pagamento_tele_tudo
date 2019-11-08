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

        $Valor = $request->valor; 
        $ValorTotal = $request->valorTotal; 
        $ValorEntrega = $request->valorEntrega; 
        $compra = $request->compra; 
        $IDPED = $request->IDPED;
        $User = $request->user;
        $id_carteira = $request->id_carteira;
        $tipoEntrega = $request->tipoEntrega;
        $nome = $request->nome;
        //$request->all();
        
        $sandbox = true;
        $setID = uniqid();

        //$IDPED = $request->idPedido;
       
        

             $Nome = Clientes::where('user', $User)->first()->Nome;

            return view('pagamento.index', ['id' => $setID,
                                            'User' => $User,
                                            'sandbox' => $sandbox,
                                            'IDPED' => $IDPED,
                                            'compra' => $compra,
                                            'valor' => $Valor,
                                            'id_carteira' => $id_carteira,
                                            'nome' => $nome,
                                            'tipoEntrega' => $tipoEntrega,
                                            'valorTotal' => $ValorTotal,
                                            'valorEntrega' => $ValorEntrega
                                            ]); //ESSA VIEW DEVE SER USADA COM PRIORIDADE EM RELAÇÃO À DE CIMA PARA EXIBIR PÁINA ONDE O USUÁRIO INSERE INFORMAÇÕES DO CARTÂO


            //COMENTE EM PRODUÇÃO O COMANDO ABAIXO, EM TESTES COMENTE O SCRIPT DE CIMA
            //return 'Usuário: '.$User.'<br/>ID: '.$setID.'<br/>PEDIDO: '.$Ped.'<br/>VALOR R$: '.$Valor.'<br/>PRODUTO: '.$Descricao.'<br/>VENDEDOR: '.$Id_Carteira.'<br/>SANDBOX? '.$sandbox;
        
            //return 'sucesso';
            
    //}*/
    
    }

    


    public function store(Request $request)
    {

        $Nome = $request->input('name');
        $Valor = $request->input('valor');
        $Descricao = $request->input('descricao');
        $Ped = $request->input('IDPED');
        $User = $request->input('User');
        $id_carteira = $request->input('id_carteira');
        $parcelas = $request->input('parcelas');
        $sandbox = $request->input('sandbox');
        $cardnumber = $request->input('cardnumber');
        $expirationdate = $request->input('expirationdate');
        $securitycode = $request->input('securitycode');
        //$request->all();
        //echo 'Está certo de realizar esta compra?<br><br>';
        //$resposta = 'nao';
        // 
        ?>
        <script>
            window.onload = function(){
                alert('Parabéns pela sua compra ;) contiue comprando com comodidade conosco!');
                <?php return redirect()->action('OthersOptionsController@Checkout'); ?>
            }
            /*function sim(){
                alert('sim');
                
                
            }
            function nao(){
                //alert('nao');
                history.go(-1);
            }*/
        </script>
        <?php

        /*echo $respostaSim ='<button name="Sim" value="botao" onclick="sim()">Sim</button>';
        echo $respostaNao ='<button name="Nao" value="botao" onclick="nao()">Não</button>';*/   
       

        
    }






    public function Checkout(Request $request){
        //$id = $request->input('id');
        /*$Nome = $request->Nome;
        $Valor = $request->Valor;
        $Descricao = $request->Descricao;
        $Ped = $request->Ped;
        $User = $request->user;
        $id_carteira = $request->id_carteira;
        $parcelas = $request->parcelas;
        $sandbox = $request->sandbox;*/

        $Nome = htmlspecialchars($request->input('name'));
        $ValorTotal = htmlspecialchars($request->input('valorTotal'));
        $ValorTotal = str_replace(',','.', $ValorTotal);
        $valorEntrega = htmlspecialchars($request->input('valorEntrega'));
        $compra = htmlspecialchars($request->input('compra'));
        $Ped = htmlspecialchars($request->input('IDPED'));
        $User = htmlspecialchars($request->input('User'));
        $id_carteira = htmlspecialchars($request->input('id_carteira'));
        $parcelas = htmlspecialchars($request->input('parcelas'));
        $sandbox = htmlspecialchars($request->input('sandbox'));
        $cardnumber = htmlspecialchars($request->input('cardnumber'));
        $expirationdate = htmlspecialchars($request->input('expirationdate'));
        $securitycode = htmlspecialchars($request->input('securitycode'));
        $identrega = htmlspecialchars($request->input('identrega'));
        $idUser = htmlspecialchars($request->input('id'));

        if($idUser != 1 || $identrega != 1){
            $idUser = 1;
            $identrega = 1;
        }

        $token =  'J27IIMSM0MWSQJIXT1MDUTHZFBWMV4W2';
        $key = 'IEVEAUWW0E4GX6FPYIEUHC7YTJEGOFNYXCEPKAER';
        $sandbox = true;

        $moipPag = new MoipPagamento($token, $key, $sandbox, $Ped, $id_carteira, $User, $parcelas );

        $scripts = $moipPag->setID($setID = uniqid())   //ID unico para identificar a compra
                            ->setPreco($ValorTotal)   //Preço da compra
                            ->setDescricao($compra)
                            ->addFormaPagamento(MoipPagamento::CHECKOUT_CARTAO) //Libera forma de pagamento por cartão
                            ->getCheckoutTransparente();

        If($scripts){
            $Nome = Clientes::where('user', $User)->first()->Nome;
            //COMENTE EM PRODUÇÃO O COMANDO ABAIXO, EM TESTES COMENTE O SCRIPT DE CIMA
            //return 'Usuário: '.$User.'<br/>ID: '.$setID.'<br/>PEDIDO: '.$Ped.'<br/>VALOR R$: '.$Valor.'<br/>PARCELAS: '.$parcelas.'<br/>PRODUTO: '.$Descricao.'<br/>VENDEDOR: '.$id_carteira.'<br/>SANDBOX? '.$sandbox;
         
            
            //return 'sucesso';
          
             //ESSA VIEW DEVE VIR DEPOIS DA COLOCAÇÃO DOS DADOS DO CARTÃO ABAIXO NA PÁGINA*/
                                
            //return view('produtos.index', ['id' => $setID,'id_transacao' => $Ped, 'Valor' => $Valor,'Descricao' => $Descricao, 'User' => $User]);
          
            //return view('produtos.index')->with(compact('setID','Ped','Valor','Descricao','User'));
   
            //return view('vlrtransf.index')->with(compact('setID','Ped','Valor','Descricao','User'));
        
            return redirect()->route('enviartransrfOthers', [$idUser,
                                                             $identrega,
                                                             $setID,
                                                             $Ped,
                                                             $ValorTotal,
                                                             $compra,
                                                             $User,
                                                             $valorEntrega, 
                                                             $parcelas,
                                                             $cardnumber,
                                                             $expirationdate,
                                                             $securitycode,
                                                             $valorEntrega]); 

        }
        else{
            /*return '<link rel="stylesheet" href="css/app.css">
            <label style="font-size:20px; color:red; margin-left:25%; margin-top:20%;">
            Falha no pagamento, você será redirecionado a página anterior em 4 segundos</label>
            <script>setTimeout(function(){history.go(-1);}, 4000);</script>';
            //echo $Valor;*/
            return false;
        } 
    
    }



}


