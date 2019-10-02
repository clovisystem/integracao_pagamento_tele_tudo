<?php
$idUser = 0;
Session::put('SemDown',1);
?>
@extends('layouts.padrao')
<title>Convite para cadastro do site Tele-Tudo</title>
<link href="http://voky.com.ua/showcase/sky-forms/examples/css/sky-forms.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .normal {
        border-width: thin;
        width:330px; height:25px;
        border-color: #000000;
    }
</style>
@section('content')
<?php
Session::forget('SemChat');
if (isset($_REQUEST['id'])==false) {
    Session::put('SemLogo', 1);
    echo 'E necessiario um numero identificando o convite'; die;

    /* IP do Face
    66.220.151.91
    31.13.114.151 */

} else {

    $ClsLocation = new Location;
    $ObsIP = $ClsLocation->GetOpc();
    if ($ObsIP=='F') {
    // if (1==1) {
        Session::put('SemLogo', 1);
        echo "<img alt='Entre no Tele-Tudo' src='https://i.imgur.com/ExasZvu.png'>"; die;
    } else {
        $id = $_REQUEST['id'];
        $cCap = new Captador();
        $Convidou =$cCap->getNomeConvite($id);
        Session::put('idCaptador', $id);
        ?>
        <br><span style="font-size: large;">Este é um convite de cadastro para o site tele-tudo. Feito por <span style="color: #6f1a19; font-size: x-large;">{{$Convidou}}</span>.<br><br>Neste site voce pode.<br>
            &nbsp;&nbsp;&nbsp; - Comprar produtos por tele-entrega.<br>
            &nbsp;&nbsp;&nbsp; - Vender produtos por tele-entrega.<br>&nbsp;&nbsp;&nbsp; - Participar da rede social tele-tudo.<br>
            &nbsp;&nbsp;&nbsp; - Lucrar sendo representante do tele-tudo.</span>
        <Br><Br>
        <form name="formulario" action="https://www.tele-tudo.com/pessoa/create" method="get">
            {{  Form::submit('Cadastrar', array('class' => 'btn btn-lg btn-success btn-block')) }}
        </form>
        <?php
        $cSes = new Sessao();
        $url = $cSes->urlFace();
        echo "<a href=".$url." class='btn btn-lg btn-facebook btn-block'>Entrar pelo Facebook</a>";
    }
}
?>
@stop