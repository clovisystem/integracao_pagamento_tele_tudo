<!DOCTYPE html>
<html>
<head>
	<title>Listagem de clientes</title>
	<script type="text/javascript" src="{{ URL::asset('js/jquery/jquery-1.6.2.min.js') }}"></script>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
	<script>

		//var query = $('#user').val();
      	//fetch_customer_data(query);

		
			/*window.onload = function(query){
				alert('q');
				$.ajax({
				url:"{{ route('produtos.index') }}",
				method:'GET',
				data:{query:query},
				dataType:'html',
				success:function(data)
				{
					$('#user').text(query);
				}
				});
				}*/

			window.onload = function(){
				setTimeout(function(){location.href="{!! route('produtos.index', array('user' => $pessoa->user, 'password' => $pessoa->password)) !!}";}, 3000);
			}
		
		  
	</script>
		
</head>
<body >
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
			<input type="hidden" id="user" name="user" value="{{ $pessoa->user }}"/>
		</p>
	</div>
	
</div>
</body>
</html>