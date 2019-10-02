<?php $idUser = 0; ?>
@extends('layouts.padrao')
<title>Cadastro de Clientes e Colaboradores do Teletudo</title>
@section('content')
<h1>Cadastro</h1>

{{ HTML::ul($errors->all() )}}

    {{ Form::model($pessoa, array('action' => array('PessoaController@update', $pessoa->id), 'method' => 'PUT')) }}

    <h1>Usuário: {{ $pessoa->user }} </h1>

    <div class="form-group">
		{{ Form::label('Nome', 'Nome') }}
		{{ Form::text('Nome', $pessoa->Nome, array('class' => 'form-control')) }}
	</div>

	<div class="form-group">
		{{ Form::label('email', 'Email') }}
		{{ Form::email('email', $pessoa->email, array('class' => 'form-control')) }}
	</div>

	<div class="form-group">
		{{ Form::label('password', 'Senha') }}
		{{ Form::text('password', '', array('class' => 'form-control', 'placeholder'=>'Digite apenas caso queira alterar')) }}
	</div>			

	<div class="form-group">
		{{ Form::label('remember_token', 'Lembrete de Senha') }}
		{{ Form::text('remember_token', '', array('class' => 'form-control', 'placeholder'=>'Digite apenas caso queira alterar')) }}
	</div>				
	
	<div class="form-group">
		{{ Form::label('Cep', 'Cep') }}
		{{ Form::text('Cep', $pessoa->Cep, array('class' => 'form-control')) }}
	</div>

    <div class="form-group">
        {{ Form::label('Ender', 'Endereço') }}
        {{ Form::text('EnderDesc', $pessoa->EnderDesc, array('class' => 'form-control','required' => 'required')) }}
    </div>

    <!--	<div class="form-group">
            {{ Form::label('bitcoin', 'Chave de indentificação BitCoin') }}
            {{ Form::text('bitcoin', Input::old('bitcoin'), array('class' => 'form-control')) }}
        </div>
    -->
	{{ Form::submit('Salvar cadastro', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}

</div>

@if (Auth::check()==0)
<script type="text/javascript">
    var $_Tawk_API = {}, $_Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/55a73bfb84d307454c01fcd3/default';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
@endif
@stop