<?php $idUser=0; ?>
@extends('layouts.padrao')
<title>Tele Tudo - Produtos - Confirmação da Compra</title>
@section('content')
<?php
$tpEnt = $_REQUEST['t'];

// Compor o tipo de entrega, pelo produto
        
// 1 - Pegar o ID do Produto
// 2 - Descobrir o fornecedor
// 3 - Ver se o Fornecedor ta On-Line
// 4 - Deduzir o Tpe
// 5 - Retirar as outras programações a respeito do Tpe        

Session::put('TpEntrega', $tpEnt);
$forn = $_REQUEST['f'];
Session::put('FORN', $forn);
$idUser = Session::get('iduser');
$ClsPedido = new Pedido;
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
$clsEntrega = new Entrega();
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

echo 'idEntrega = '.$idEntrega; die;

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
    Session::put('ENTREGA', $idEntrega);
}
$Fazer=false;

if ($tpEnt==0) {
    $Token=$clsEntrega->Login($idEntrega);
    if ($Token>"")
    {
        Session::put('Token', $Token);
        $Fazer=true;
    }
} else {
    $Fazer=true;
}

echo 'Fazer = '.$Fazer; die;

if ($Fazer==true) {
    $QtdItens = $_REQUEST['Qtd'];
    $QtdItens++;
    for ($i=1;$i<$QtdItens;$i++) {
        if ($Teste==0) {
            $ClsItens = new PedidoItens;

            $q=$_REQUEST['q'.$i];
            $ClsItens->setQtd($q);

            $p=$_REQUEST['p'.$i];
            $ClsItens->setIdProd($p);

            $ClsItens->Add($idPedido);
        } else {
            $ClsItens = new PedidoItens;
            break;
        }
    }
    // Ver qual é a forma de tele-entrega do fornecedor
    $VlrOrc = $clsEntrega->PedeOrcamento($idPedido, $Teste, $tpEnt);
    $idFornProd = $ClsItens->getidFornProd();
    Session::put('Fornec',$idFornProd);

    if ($tpEnt>0) {
        Session::put('Kms',$clsEntrega->getKms());
        Session::put('TmpPrevisto',$clsEntrega->getTmpPrevisto());
        // $clsEntrega->EfetivaPedidoNoBDSemEntrega($idPedido, $idUser, $idFornProd);
    }
    $Nome = Session::get('Nome');

    if ($VlrOrc>0) {
        $VlrOk=True;
    } else {
        $VlrOk = $clsEntrega->getOrcFree();
    }

    if ($VlrOk>0) {
        ?>
        <div class="alert alert-info">{{ 'Usuario Logado: '.$Nome }}</div>
        <script language="javascript" type="text/javascript">
            document.location.assign("http://www.tele-tudo.com/confirma?IDPED="+{{$idPedido}});
        </script>
    <?php
    } else {
        if ($tpEnt==2) {
            echo "<div class='alert alert-danger'>No seu bairro não é possível realizar a entrega neste momento</div>";
        } else {
            echo 'Não foi possível obter a informação referente a Tele-Entrega[1]';
        }
    }
} else {
    echo 'Não foi possível realizar login na PlayDelivery<p>';
}
?>
@stop