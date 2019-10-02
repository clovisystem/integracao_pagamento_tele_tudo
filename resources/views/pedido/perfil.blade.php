<?php
$idUser = 0;
Session::put('SemDown',1);
?>
@extends('layouts.padrao')
<title>Definição de Perfis do usuário Tele-Tudo</title>
<link href="http://voky.com.ua/showcase/sky-forms/examples/css/sky-forms.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .normal {
        border-width: thin;
        width:330px; height:25px;
        border-color: #000000;
    }
</style>

<script>
    var OpcOrig = [0, 0];
    var OpcNovo = [0, 0];

    function Mudou(Nr) {
        var ObjCk = "ckOpc" + Nr;
        var Checado = document.getElementById(ObjCk).checked;
        var valor = 0;
        if (Checado) {
            valor = 1;
        }
        OpcNovo[Nr] = valor;
        var igual=true;
        for (i=0;i<2;i++) {
            if (OpcNovo[i]!=OpcOrig[i]) {
                igual=false;
                break;
            }
        }
        if (igual) {
            document.getElementById('btSalvar').disabled=true;
        } else {
            document.getElementById('btSalvar').disabled=false;
        }
    }

    function Aciona() {
        document.location.assign("https://www.tele-tudo.com/fornecedor/create");
    }

    function Compras() {
        document.location.assign("https://www.tele-tudo.com");
    }

</script>

@section('content')
<label style="font-size: medium">No tele-tudo voce pode fazer muitas coisas</label>
<form class="sky-form boxed" style="width: 344px">
    <Br>
    <label>
        &nbsp;&nbsp;<input type="checkbox" value="" checked="checked" disabled="disabled">
        <span style="font-size: large">Cliente</span>
    </label>
    <Br>
    <label>
        &nbsp;&nbsp;<input type="checkbox" value="" checked="checked" disabled="disabled">
        <span style="font-size: large">Perfil na Rede Tele-Tudo</span>
    </label>
    <Br>
    <label>
        &nbsp;&nbsp;<input type="checkbox" value="" checked="checked" name="ckOpc1" id="ckOpc1" disabled="disabled">
        <span style="font-size: large">Captador</span>
    </label>
    <Br>
    <label>
        &nbsp;&nbsp;<input type="checkbox" onchange="Mudou(0)" name="ckOpc0" id="ckOpc0" value="">
        <span style="font-size: large">Fornecedor</span>
    </label>

   {{-- <Br>
    <label>
        &nbsp;&nbsp;<input type="checkbox" onchange="Mudou(0)" name="ckOpc0" id="ckOpc0" value="">
        <span style="font-size: large">Motorista</span>
    </label>--}}

    <Br>
    <Br>
    <td >
        <button type="button" name="btSalvar" id="btSalvar" disabled onclick="Aciona()" class="btn btn-success btn-lg btn-block">Salvar</button>
    </td>
    <Br>
    <td >
        <button type="button" name="btSalvar" id="btSalvar" onclick="Compras()" class="btn btn-success btn-lg btn-block">Ir às compras</button>
    </td>
    <Br>
    @if (Session::has('message'))
        <div class="alert alert-info"><h3>{{ Session::get('message') }}</h3></div>
    @endif
</form>
@stop
