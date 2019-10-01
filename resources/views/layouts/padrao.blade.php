<!-- Arquivo app/views/layouts/padrao.blade.php -->
<!doctype html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />

    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">-->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<style type="text/css">
    .btn-facebook {
        color:#fff!important;
        background-color:#4863ae;
    }
    .Mouse {
        cursor:hand;
    }
</style>
<script Language="JavaScript">
    var Slogo = localStorage.getItem('SemLogo');
    var nav = navigator.appVersion;
    var A = nav.indexOf("Android");
    var Mobile = 0;
    if (A<1) {
        Mobile = 0;
        // document.write("<div class='container'>");
    } else {
        Mobile = 1;
    }
</script>
<?php
$UA = $_SERVER['HTTP_USER_AGENT'];

if (Session::has('site')) {
    $cPag = new App\Pagina();
    $Img = $cPag->fundo(Session::get('forn'));
} else {
    if (strrpos($UA, "Windows")) {
        $Img = asset('resources/assets/img/Natalia-Imagem-600.jpg');
    } else {
        $Img = asset('resources/assets/img/fundocel.jpg');
    }
}
?>

<body style="background-position: center center;
        background-image: url('{{$Img}}');
        background-attachment: scroll;
        background-repeat: no-repeat;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        background-size: cover;
        -o-background-size: cover;">

       
<script Language="JavaScript">

    if (A<1) {
        document.write("<div class='container'>");
    }
</script>

<?php

$A=''; $AL=''; $Cel = 0;
if (isset($_GET['A'])) {
    $AL='?A=1';
    $A='1';
} else {
    $UA = $_SERVER['HTTP_USER_AGENT'];

    if (strrpos($UA, "Android")) {
    // if (!strrpos($UA, "Windows")) {
        $Cel=1;
    } else {
        $AL='?A=1';
    }
}

// $Cel = 1;

if (Auth::check()) {
    $Nome = Session::get('Nome');
    $nmUser = $Nome;
} else {
    $nmUser="";
}

if (Session::has('site')) {
    $cor = $cPag->getCor();
} else {
    $cor="#000000";
}
$sValor = "R$ 0,00";
?>
<!--<form name="entrar" action="https://www.tele-tudo.com/public/entrar" method="POST">-->
{{ csrf_field() }}
@if ($Cel==1)
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	    <tbody>
	    <tr>
	        <td style="width: 16%"><a href="https://www.tele-tudo.com/" target="_blank">
	                <img border="0" src="https://www.tele-tudo.com/resources/assets/img/LOGOP.png" width="196" height="90"></a></td>
	        <td style="width: 8%">
	            <?php
	            if (Auth::check()) {
	            ?>
	            <a href="https://www.tele-tudo.com/sair">
	                <img border="0" src="https://www.tele-tudo.com/resources/assets/img/btsair.png" width="100" height="30" onclick="Sair()" style="cursor:hand">
	                <?php
	                } else {
	                ?>
	                <a href="https://www.tele-tudo.com/login">
	                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/brlogin.png" width="100" height="30" onclick="Sair()" style="cursor:hand">
	            <?php
	            }
	            ?>
	        </td>
	        <td>
	            <p align="center" style="width: 77px">
	                <img border="0" id="Carrinho" src="https://www.tele-tudo.com/resources/assets/img/carrinhopeqOK.png" width="74" height="63">
	                <br><b><label id="lbTotal" style='color: {{$cor}}'>{{$sValor}}</label></b>
	            </p></td>
	    </tr>
	    </tbody>
	</table>
@else
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tbody>
	   <tr>	   	   
                <td width="20%"><a href="https://www.tele-tudo.com" target="_blank" >
                        <img border="0" src="https://www.tele-tudo.com/resources/assets/img/LOGOP.png" width="200" height="90"></a></td>
                    <td width="45%">
                        <p align="center">
                            <a href="{{ URL::to('aplicativo') }}">
                                <img border="0" src="https://www.tele-tudo.com/resources/assets/img/disponappeq.png" width="200" height="68"></a>
                            <a target="_blank" href="https://chat.whatsapp.com/FKgKLGaK648FLm8zkQG25B">
                                <img border="0" src="https://www.tele-tudo.com/resources/assets/img/Whatspeq.png" width="275" height="61"></a></p>
               </td>
                <?php
                if (Session::has('ValorPed')) {
                    $sValor = Session::get('ValorPed');
                }
                if (Auth::check()) {
                   if (Session::has('site')) {
                      $cor = $cPag->getCor();
                   } else {
                      $cor="#000000";
                   }
                ?>
                <td width="40%" class="text-center">
                    <br>
                    <div style='color: {{$cor}}' >
                        Seja Bem Vindo <b>{{$Nome}}</b><br>
                    </div>
                    <br>                    
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/brede.png" width="100" height="30" onclick="redeMenu()" style="cursor:hand">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/btServicos.png" width="100" height="30" onclick="servMenu()" style="cursor:hand">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/btsair.png" width="100" height="30" onclick="Sair()" style="cursor:hand">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <br>
                                        <img border="0" src="https://www.tele-tudo.com/resources/assets/img/btCaptador.png" width="100" height="30" onclick="CaptMenu()" style="cursor:hand">
                    <?php
                    $QryForn = DB::table('empresa')->select(DB::raw('count(*) as Quant'))
                        ->where('idPessoa','=',Session::get('iduser'))
                        ->first();
                    if ($QryForn->Quant>0) {
                    ?>
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/btForn.png" width="100" height="30" onclick="FornMenu()" style="cursor:hand">
                    <?php
                    }
                    ?>
                    <br>
                </td>
                <?php
                } else {
             
                ?>
                <td width="40%" class="text-center">
		    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/usuario.png" width="100" height="15">		    
		    
            <input type="text" name="user" id="user" value="{{ $_GET['user'] or '' }}" size="20"><br>
		    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/senha.png" width="100" height="15">
		    <input type="password" name="senha" value="{{ $_GET['password'] or '' }}" size="2">
		    <br>
		    <input checked="checked" name="remember" type="checkbox" value="remember">

                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/lembrar.png" width="100" height="15">
                    <br>
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <!--<img border="0" src="https://www.tele-tudo.com/resources/assets/img/btcadastrar.png" width="100" height="30" onclick="document.location.assign('https://www.tele-tudo.com/pessoa/create')" style="cursor:hand" >-->
                    <a href="/pessoas/create"><img border="0" src="https://www.tele-tudo.com/resources/assets/img/btcadastrar.png" width="100" height="30" style="cursor:hand"/></a>
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/btServicos.png" width="100" height="30" onclick="servMenu()" style="cursor:hand">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/fundo%20menu.png" width="10" height="10">
                    <img border="0" src="https://www.tele-tudo.com/resources/assets/img/btentrar.png" width="100" height="30" onclick="document.entrar.submit()" style="cursor:hand" >
                    <br>
		    
                </td>
                <?php
                }
                ?>
                <td>
                    <p align="center">
                        <img border="0" src="https://www.tele-tudo.com/resources/assets/img/carrinhopeq.png" width="74" height="63" id="Carrinho" style='cursor:hand' onclick='Enviar()'>
                        <br><b><label id="lbTotal" style='color: {{$cor}}'>{{$sValor}}</label></b>
                </td>
            </tr>
        </tbody>
    </table>
@endif
</form>

<script Language="JavaScript">
    if (Mobile==1) {
        document.write("<div class='container'>");
    }
</script>
<?php
$Img = Lang::get('menus.img');
?>
@yield('content')
<?php
$Chat=1;
if (Session::has('SemChat')) {
    $SemChat=Session::get('SemChat');
    if ($SemChat==1) {
        $Chat=0;
    }
}
?>
<script>
    function Logar() {
        document.location.assign("https://www.tele-tudo.com/login");
    }

    function Sair() {
        document.location.assign("https://www.tele-tudo.com/sair");
    }

    function redeMenu() {
        document.formularioRede.submit();
    }

    function servMenu() {
        document.location.assign("https://www.tele-tudo.com/public/servicos");
    }

    function CaptMenu() {
        document.location.assign("https://www.tele-tudo.com/captador");
    }

    function FornMenu() {
        document.location.assign("https://www.tele-tudo.com/fornecedor");
    }

        <?php
        if ($Chat==1) {
        ?>
    var $_Tawk_API = {}, $_Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/55a73bfb84d307454c01fcd3/default';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
    <?php
    }
    ?>
</script>
<form action="https://intonses.com.br/rede/loginTeleTudo.php" method="post" name="formularioRede" target="_blank" ENCTYPE="multipart/form-data">
    <input name="User" type="hidden" value="{{$nmUser}}">
    <input name="Tipo" type="hidden" value="usuario">
</form>
</body>
</html>