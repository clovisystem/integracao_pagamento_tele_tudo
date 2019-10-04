<!DOCTYPE html>
<html>
<head>
	<title>Listagem de clientes</title>
	<script type="text/javascript" src="{{ URL::asset('js/jquery/jquery-1.6.2.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/app.css">

</head>
<body onload = "submitform()">
<div class="container">

<nav class="navbar navbar-inverse">
	<div class="navbar-header">
		<a class="navbar-brand" href="{{ URL::to('pessoa') }}">Lista</a>
	</div>
	<ul class="nav navbar-nav">
		<li><a href="{{ URL::to('pessoa') }}">Todos</a></li>
		<li><a href="{{ URL::to('pessoa/create') }}">Novo</a>
	</ul>
</nav>

<h1>Showing {{ $pessoa->Nome }}</h1>

	<div class="jumbotron text-center">
		<h2>{{ $pessoa->Nome }}</h2>
		<p>
			<strong>Email:</strong> {{ $pessoa->email }}<br>
			
		</p>
	</div>

	<form name="formPessoaShow" action="produtos/index" method="post">
		{{ csrf_field() }}
		<input type="hidden" id="user" name="user" value="{{ $pessoa->user }}"/>
		<input type="hidden" id="password" name="password" value="{{ $pessoa->password }}"/>
		<input type="submit" id="enviar" name="enviar" value=""/>
	</form>
	<!--FUNÇÃO QUE GERA O SUBMIT AUTOMÁTICO DO FORM ESTÁ EM PUBLIC/JS/APP.JS COMO LINKADO NO CABEÇALHO DESTE ARQUIVO -->
</div>
</body>
</html>