<?php $idUser = 1; ?>


@extends('layouts.padrao')

@section('content')

    <script Language="JavaScript">
        var nav = navigator.appVersion;
        var A = nav.indexOf("Android");
        var nH = "h1";
        if (A<1) {
        } else {
            nH = "h4";
        }
        document.write("<"+nH+">Confirmação do acionamento da entrega</"+nH+">");
    </script>


    <?php

$Descricao = 'DVD Independence Day';
$Valor = '20.00';
//$Valor = $Valor * 100;
$Id_carteira = "xeviousbr@gmail.com";
$Nome = "Pagamento do Tele-Tudo.com, por compra realizada";
$user = 'teste';
$email = 'teste@teste.com';
    
    $cEntrega = new App\Entrega();
    $idPedido = $_POST['IDPED']; //MUDOU DE GET PARA POST
    $tpEnt = $_POST['tpEnt'];
    $VlrEntrega = $_POST['VlrEntrega'];
    Session::put('IDPED', $idPedido);
    Session::put('ENTREGA', $tpEnt);
    //$VlrEntrega = 0;
    if (Session::has('VlrEntrega')) {
        $VlrEntrega = Session::get('VlrEntrega');
    }
    $idEntrega = $tpEnt; //Session::put('ENTREGA');
    $cEntrega->setidEntrega($idEntrega);
    $cEntrega->setVlrEntrega($VlrEntrega,"pedido\index");
    $vValorTotal = $cEntrega->getValorTotal($idPedido);
    Session::put('VLRTOTAL', $vValorTotal);
    $ValorTotal = number_format($vValorTotal, 2, ',', '.');
    //$idPedido = 0;
    if (Session::has('PED')) {
        $idPedido = Session::get('PED');
    }
    $ValorEntrega = $cEntrega->getNossaCobranca($idPedido);
    Session::put('VlrEntrega', $ValorEntrega);
    ?>

    <div class="alert alert-success">Compras R$ {{ number_format($cEntrega->getCompras($idPedido), 2, ',', '.') }}</div>
    <div class="alert alert-success">Tele-Entrega R$ {{ $ValorEntrega }}</div>
    <div class="alert alert-success"><input id="ValorTotal" name="ValorTotal" type="text" value="Valor Total R$ {{ $ValorTotal }}" style="border:none; background:transparent;"/><!--Valor Total R$ {{ $ValorTotal }}--></div>
    <input id="IDPED" name="IDPED" type="text" hidden="hidden" value="{{ $idPedido }}" /></p>
    <input id="user" name="user" type="text"  hidden="hidden" value="{{ $user }}" /></p>
 
    <div>
        <table width="79%">
            <tr>
                <td><input type="submit" id="btAltera" value="Alterar a Compra" onclick="Alterar()" disabled class="btn btn-default" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td width="563px">&nbsp;</td>
                <td><input type="submit" id="btMaisItens" width="132px" value="Adicionar Mais Itens" onclick="Mais()" class="btn btn-warning" /></td>
            </tr>
            <tr>
                <form name="frmCanc" action="https://www.tele-tudo.com/produtos" method="get">
                    <td height="30px"><input type="submit" id="btCancelar" width="125px" value="Cancelar" disabled class="btn btn-default" /></td>
                </form>
                <td height="30px"></td>
                <td height="30px"></td>
                <td height="30px"></td>

                <td height="30px"><input type="submit" id="btPagamento" value="Finalização da compra" onclick="Pagar()" width="133px" text-align="center" class="btn btn-success" /></td>
            </tr>
        </table>
        <br/>
        <br />


        <?php
        $idPedido = $_POST['IDPED'];
        if (isset($_POST['tpEnt'])) {
            $tpEnt = $_POST['tpEnt'];
        } else {
            $cSessao = new App\Sessao;
            $tpEnt = $cSessao->tpEntrega($idPedido);
        }
        ?>
        <script>
            function Pagar() {
                var tpEnt = <?php echo $tpEnt; ?>;
                var idPedido = <?php echo $idPedido; ?>;

                // alert(tpEnt);
                if (tpEnt==0) {
                    // PLAY DELIVERY
                    document.location.assign("https://www.tele-tudo.com/formas?ped="+idPedido);
                } else {
                    // TELE-ENTREGA PRÓPRIA
                    //document.location.assign("https://www.tele-tudo.com/pagtodireto");SUBSTITUÍ PELO DEBAIXO PARA MINHHA MAQUINA LOCAL
                    //document.location.assign("{!! action('OthersOptionsController@Aciona') !!}");
                    document.location.assign("{!! route('cartao.index', ['user' => $user, 'IDPED' => $idPedido, 'valor' => $ValorTotal]) !!}");
                    // document.location.assign("http://www.tele-tudo.com/resumo");
                }
            }

            function Mais() {
                var ped = document.getElementById("hPd").value;
                document.location.assign("https://www.tele-tudo.com/produtos?ped="+ped);
            }

        </script>


@stop