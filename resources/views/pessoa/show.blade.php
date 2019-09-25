<!DOCTYPE html>
<html>
<head>
	<title>Listagem de clientes</title>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
</head>
<body>
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

</div>
</body>
</html>