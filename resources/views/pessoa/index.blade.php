@extends('layouts.padrao')
<title>Cadastro de Clientes e Colaboradores do Teletudo</title>
@section('content')
<Br>
<div class="alert alert-success">Seu cadastro foi realizado com sucesso</div>
<?php
$cep = Session::get('CEP');
$cProd = new Produtos();
$Tem = $cProd->VeSeTemCidDoCliente($cep, 0);
if ($Tem>0) {
    echo "<div class='alert alert-success'>Estamos felizes em informar que EXISTEM fornecedores nossos que atendem a sua regiãoo</div>";
} else {
    echo "<div class='alert alert-info'>Assim que existirem fornecedores na sua área de abrangência, será avisado por email</div>";
}
?>

@stop