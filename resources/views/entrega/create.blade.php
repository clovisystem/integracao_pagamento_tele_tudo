<?php $idUser=0; 
?>
@extends('layouts.padrao')
<title>Tele Tudo - Produtos - Confirmação da Compra</title>
@section('content')
<?php
$tpEnt = $_REQUEST['t'];
Session::put('TpEntrega', $tpEnt);
$forn = $_REQUEST['f'];
Session::put('FORN', $forn);
$idUser = Session::get('iduser');
$ClsPedido = new App\Pedido;
$Teste=0;
if (Session::get('iduser')==21) {
    $Teste = 1;
}
if (isset($_REQUEST['ped'])) {
    $idPedido = $_REQUEST['ped'];
} else {
    $ClsPedido->CriaPedido($idUser, $Teste, $tpEnt);
    $idPedido = $ClsPedido->getIdPedido($Teste);
    echo "idPedido:".$idPedido."<p>";
}
Session::put('PED', $idPedido);
$clsEntrega = new App\Entrega();
echo "<h1>Tele-Entrega</h1>";
echo "<div class='alert alert-info'>Solicitando orçamento de tele-entrega</div>";

// Arrumar, mais pra frente
// $Peso=1;

$Valor = 0;

// PlayDelovery como única empresa de tele-enrtega
// $clsEntrega->idEntregadora = 1;
$idEntrega = 0;
if (Session::has('ENTREGA')) {
    $idEntrega = Session::get('ENTREGA');
}
IF ($idEntrega==0) {
    $Teste=Session::get('Teste');
    if ($idUser<1) {
        /*$idRede=Session::get('id');
        if ($idRede>0) {
            echo "Usuario da Rede"; die;
        } else {*/
            echo "idUser Vazio 2"; die;
        // }
    }
    $idEntrega = $clsEntrega->CriaRegistro($idPedido, $idUser, $Teste, $tpEnt);
    Session::put('idPedido', $idPedido);
    Session::put('ENTREGA', $idEntrega);
}
if ($tpEnt==0) {
    $Token=$clsEntrega->Login($idEntrega);
    if ($Token>"")
    {
        Session::put('Token', $Token);
        $Fazer=true;
    }
} else {
    // tpEnt: 3 = Sem Tele-Entrega
    $Fazer=true;
}
if ($Fazer==true) {
    $QtdItens = $_REQUEST['Qtd'];
    $QtdItens++;
    for ($i=1;$i<$QtdItens;$i++) {
        if ($Teste==0) {
            $ClsItens = new App\PedidoItens;

            $q=$_REQUEST['q'.$i];
            $ClsItens->setQtd($q);

            $p=$_REQUEST['p'.$i];
            $ClsItens->setIdProd($p);

            $ClsItens->Add($idPedido);
        } else {
            $ClsItens = new App\PedidoItens;
            break;
        }
    }
    // Ver qual é a forma de tele-entrega do fornecedor
    $VlrEntrega = 0;
    if (Session::has('VlrEntrega')) {
        $VlrEntrega = Session::get('VlrEntrega');
    }
    $cep = $_REQUEST['c'];
    $VlrOrc = $clsEntrega->PedeOrcamento($idPedido, $Teste, $tpEnt, $VlrEntrega, $cep);
    Session::put('VlrEntrega', $clsEntrega->getVlrEntrega($idPedido));
    $idFornProd = $ClsItens->getidFornProd();
    Session::put('Fornec',$idFornProd);
    if ($tpEnt>0) {
        Session::put('Kms',$clsEntrega->getKms());
        Session::put('TmpPrevisto',$clsEntrega->getTmpPrevisto());
        // $clsEntrega->EfetivaPedidoNoBDSemEntrega($idPedido, $idUser, $idFornProd);
    }
    $Nome = Session::get('Nome');
    if ($tpEnt==3) {
        echo "<div class='alert alert-sucess'>Voce será direcionado a página do vendedor[2]</div>";
        // echo 'Chamar a página do Fornecedor'; die;
    } else {
        if ($VlrOrc>0) {
            $VlrOk=True;
        } else {
            $idFornProd = Session::get('Fornec');
            $clsEntrega->setidFornProd($idFornProd);
            $VlrOk = $clsEntrega->getOrcFree();
        }

        if ($VlrOk>0) {
        ?>
        <div class="alert alert-info">{{ 'Usuario Logado: '.$Nome }}</div>
        <script language="javascript" type="text/javascript">
            document.location.assign("https://www.tele-tudo.com/confirma?IDPED="+{{$idPedido}});

        </script>
        <?php
        } else {
            if ($tpEnt==2) {
                echo "<div class='alert alert-danger'>No seu bairro não é possível realizar a entrega neste momento</div>";
            } else {
                echo 'Não foi possível obter a informação referente a Tele-Entrega[1]';
            }
        }
    }

} else {
    echo 'Não foi possível realizar login na PlayDelivery<p>';
}
?>
@stop