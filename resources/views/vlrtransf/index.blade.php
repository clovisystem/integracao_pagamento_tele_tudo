<?php $idUser = 0; ?>
<script Language="JavaScript">
    var nav = navigator.appVersion;
    var A = nav.indexOf("Android");
    var nH = "h1";
    if (A<1) {
        // sBanco="para o banco ";
    } else {
        nH = "h4";
        localStorage.setItem('SemMenu',1);
    }
</script>
@extends('layouts.padrao')
<title>Informar transfer&ecirc;ncia de valor</title>
@section('content')
<script Language="JavaScript">
    document.write("<"+nH+">Informar transferência de valor</"+nH+">");

    function clicou(dados) {
        var itens = dados.split(";");
        document.getElementById("txBanco").value=itens[0].trim();
        document.getElementById("txnr").value=itens[1];
        document.getElementById("txNrAg").value=itens[2];
        document.getElementById("txConta").value=itens[3];
        document.getElementById("txidConta").value=itens[4];
    }
</script>
<br>
<?php
$idForn =Session::get('FORN');
$Ban = $Nr = $Age = $Conta = $idConta = '';
$vValorTotal = Session::get('VLRTOTAL');
$ValorTotal = number_format($vValorTotal, 2, ',', '.');
$cCB = new App\ContasBancarias();
$qryConta = $cCB->Contas($idForn);
if ($qryConta == null) {
    echo "<div class='alert alert-danger'>Vendedor n&atilde;o tem conta banc&aacute;ria cadastrada</div>";
    die;
}
$cons=9.67;
$qtd = sizeof($qryConta);
if ($qtd==1) {
    $Ban = $qryConta[0]->banco;
    $Nr = $qryConta[0]->idBanco;
    $Age= $qryConta[0]->Agencia;
    $Conta = $qryConta[0]->Conta;
    $idConta = $qryConta[0]->id;
    $tmBan = strlen($Ban)*$cons;
    $tmNr = strlen($Nr)*$cons;
    $tmAge = strlen($Age)*$cons;
    $tmConta = strlen($Conta)*$cons;
} else {
    $MaiorBan='';
    $MaiorNr='';
    $MaiorAge='';
    $MaiorConta='';
    for ($i=0;$i<$qtd;$i++) {
        $Ban1 = trim($qryConta[$i]->banco);
        $Nr1 = $qryConta[$i]->idBanco;
        $Age1= $qryConta[$i]->Agencia;
        $Conta = $qryConta[$i]->Conta;

        // Não tenho certeza se ta certo isso
        $idConta = $qryConta[$i]->id;

        $MaiorBan=(strlen($Ban1)>strlen($MaiorBan)) ? $Ban1 : $MaiorBan;
        $MaiorNr=(strlen($Nr1)>strlen($MaiorNr)) ? $Nr1 : $MaiorNr;
        $MaiorAge=(strlen($Age1)>strlen($MaiorAge)) ? $Age1 : $MaiorAge;
        $MaiorConta=(strlen($Conta)>strlen($MaiorConta)) ? $Conta : $MaiorConta;
        $dados="\"".$Ban1.';'.$Nr1.';'.$Age1.';'.$Conta.';'.$qryConta[$i]->id."\"";
        echo "<div class='radio'><label>";
        echo "<input type='radio' name='rdBan' id='rdBan".$i."' onclick='clicou(".$dados.");' value='".$qryConta[$i]->banco."' > ".$qryConta[$i]->banco;
        echo "</label></div>";
    }
    $tmBan = strlen($MaiorBan)*$cons;
    $tmNr = strlen($MaiorNr)*$cons;
    $tmAge = strlen($MaiorAge)*$cons;
    $tmConta = strlen($MaiorConta)*$cons;
}
$Teste=Session::get('Teste');
?>
<strong><input name="txBanco" id="txBanco" type="text" value="{{$Ban}}" style="height: 22px; width: {{$tmBan}}px;" onclick="this.select()" readonly="readonly" >
    <input name="txnr" id="txnr" type="text" value="{{$Nr}}" style="height: 22px; width: {{$tmNr}}px;" onclick="this.select()" readonly="readonly" > , Agência
    <input name="txNrAg" id="txNrAg" type="text" value="{{$Age}}" style="height: 22px; width: {{$tmAge}}px;" onclick="this.select()" readonly="readonly" >, Conta
    <input name="txConta" id="txConta" type="text" value="{{$Conta}}" style="height: 22px; width: {{$tmConta}}px;" onclick="this.select()" readonly="readonly" ></strong>

<form action="https://tele-tudo.com/enviartransrf" method="post" >
<input type="hidden" name="VLRTOTAL" value="{{$ValorTotal}}">
<input type="hidden" name="Teste" value="{{$Teste}}">
{{ csrf_field() }}
<?php
$IdPagar = Session::get('IDPED');
$Bco="";
$Age="";
$Cta="";
$mail="";
if ($Teste==1) {
    $Bco="123";
    $Age="234";
    $Cta="345";
    $mail="teste@mail.com";
}
?>
<input name="txPagto" type="text" value="{{$IdPagar}}" style="display: none;" >
<div class="form-group">
    <script Language="JavaScript">
        document.write("<"+nH+"><label for='lbVlr'>Valor transferido R$ {{$ValorTotal}}</label></"+nH+">");
    </script>
</div>

<input name="txidConta" id="txidConta" type="hidden" value="{{$idConta}}">

<div class="form-group">
    <label for="BCO">Informe o número do Banco</label>
    <input id="BCO" type="text" class="form-control" name="BCO" required="required" value="{{$Bco}}">
</div>

<div class="form-group">
    <label for="AGE">Número da Agência</label>
    <input id="AGE" type="text" class="form-control" name="AGE" required="required" value="{{$Age}}">
</div>

<div class="form-group">
    <label for="CTA">Conta</label>
    <input id="CTA" type="text" class="form-control" name="CTA" required="required" value="{{$Cta}}">
</div>

<div class="form-group">
    <label for="EMAIL">Email</label>
    <input id="EMAIL" type="email" class="form-control" name="EMAIL" required="required" value="{{$mail}}">
</div>

<button type="submit" value="Registrar o pagamento" class="btn btn-primary" style="width: 143px">Enviar</button>

</form>
@stop