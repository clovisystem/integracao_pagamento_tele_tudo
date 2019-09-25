<!-- app/views/nerds/show.blade.php -->

<!DOCTYPE html>
<html>
<head>
	<title>
        Tele Tudo - Servicos
	</title>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">

<nav class="navbar navbar-inverse">
	<ul class="nav navbar-nav">
		<li><a href="{{ URL::to('produtos') }}">Produtos</a></li>
		<li><a href="{{ URL::to('/servicos') }}">Servi&ccedil;os</a></li>
    <li><a href="{{ URL::to('/') }}">Login</a>
	</ul>
</nav>

<h1>Nome: {{ $servico->nome }}</h1>

</div>
</body>
</html>