<?php $idUser = 0; ?>
@extends('layouts.padrao')
<title>Não tem ainda, compra por Sedex</title>
@section('content')
    <?php
    if (Auth::check()) {
        $idUser = Auth::id();
        $cPes = new Sessao();
        $dados = $cPes->getNmUser($idUser);
        $Nome = $dados[0];
        $email=$dados[1];
        $uf=$dados[2];
        $cidade=$dados[3];
        $UserTT=$dados[4];  // Verificar
        $telefone=$dados[5];
        $CEP=$dados[6];
    } else {
        echo 'Somente para usuários logados'; die;
    }
    ?>
    <form action="http://intonses.com.br/rede/loginTeleTudo.php" method="post" name="formRede" ENCTYPE="multipart/form-data">
        <input name="ID" type="hidden" value="{{$idUser}}">
        <input name="Tipo" type="hidden" value="usuario">
        <input name='Nome' type='hidden' value="{{$Nome}}">
        <input name='email' type='hidden' value="{{$email}}">
        <input name='uf' type='hidden' value="{{$uf}}">
        <input name='cidade' type='hidden' value="{{$cidade}}">
        <input name='UserTT' type='hidden' value='{{$UserTT}}'>
        <input name='telefone' type='hidden' value="{{$telefone}}">
        <input name='CEP' type='hidden' value="{{$CEP}}">
    </form>
    <script language="javascript" type="text/javascript">
        // document.formRede.submit();
    </script>
@stop