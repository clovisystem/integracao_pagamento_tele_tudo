<?php $idUser = 0; ?>
@extends('layouts.padrao')
<title>Tele Tudo - Produtos - Continuação do Pedido - COM Login</title>
@section('content')
<?php

loga('ENTROU');

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
    loga('Tem Session Entrega');
    $idEntrega = Session::get('ENTREGA');
}

$idUser = $_REQUEST['User'];
$idPedido = $_REQUEST['Ped'];

echo 'idPedido:'.$idPedido.'<Br>';

$qTpe = DB::table('pedido')
    ->select('Tpe')
    ->where('idPed', '=', $idPedido)
    ->first();
$tpEnt = $qTpe->Tpe;

// $tpEnt = $_REQUEST['Tpe'];
$Teste = $_REQUEST['Tes'];
DB::update("update pedido set User = '".$idUser."' where idPed = ".$idPedido);

IF ($idEntrega==0) {
    loga('idEntrega==0');
    if ($idUser<1) {
        echo "idUser Vazio 1"; die;
    }

    $idEntrega = $clsEntrega->CriaRegistro($idPedido, $idUser, $Teste, $tpEnt);
    Session::put('ENTREGA', $idEntrega);
}

$Fazer=false;
if ($tpEnt==1) { // TpEntrega=1, Não precisa fazer login
    loga('tpEnt==1');
    $Fazer=true;
} else {
    loga('tpEnt<>1');
    $Token=$clsEntrega->Login($idEntrega);
    if ($Token>"")
    {
        loga('Token>');
        Session::put('Token', $Token);
        $Fazer=true;
    }
}

if ($Fazer) {
    loga('Fazer=true');
    $VlrOrc = $clsEntrega->PedeOrcamento($idPedido, $Teste, $tpEnt);

    if ($tpEnt>0) {
        loga('tpEnt>0');
        Session::put('Kms',$clsEntrega->getKms());
        Session::put('TmpPrevisto',$clsEntrega->getTmpPrevisto());
    }
    $Nome = Session::get('Nome');

    if ($VlrOrc>0) {
        loga('VlrOrc>0');
        Auth::loginUsingId($idUser);
        ?>
        <div class="alert alert-info">{{ 'Usuario Logado: '.$Nome }}</div>
        <script language="javascript" type="text/javascript">
            var idPedido = <?php echo $idPedido; ?>;
            var tpEnt = <?php echo $tpEnt; ?>;
            document.location.assign("https://www.tele-tudo.com/confirma?IDPED="+idPedido+"&tpEnt="+tpEnt);
        </script>
        <?php
    } else {
        echo 'Não foi possível obter a informação referente a Tele-Entrega[3]';
    }
} else {
    echo 'Não foi possível realizar login na PlayDelivery<p>';
}
?>
@stop
<?php
function loga($texto) {
    DB::update("insert into LogDebug (Log) values ('posface:".$texto."')");
}
?>