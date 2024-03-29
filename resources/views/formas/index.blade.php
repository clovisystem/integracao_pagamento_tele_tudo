<script language="javascript" src="js/jquery/jquery-1.6.2.min.js"></script>
<script language="javascript" src="js/jquery/cycle/jquery.cycle.all.js"></script>
    <script language="javascript">
        jquery(document).ready(function(){
            jquery("#bandeiras ul").cycle({
            fx:'scrollHorz',
            speed:1000
            });
        });
    </script>

<?php $idUser=0; ?>
@extends('layouts.padrao')


@section('content')



@if($user == '')
    <h1 class="alert alert-warning">Informe seu nome!<h1><script>setTimeout(function(){history.back(-1);}, 3000)</script>
@elseif($user == null)
    <h1 class="alert alert-danger">Logue-se para comprar!<h1><script>setTimeout(function(){history.back(-1);}, 3000)</script>
@else
    <h1>Escolha a forma de pagamento, {{ $user }}.<h1>
@endif


@if($Valor == '')
    <h1 class="alert alert-warning">Você esqueceu de escolher o produto!<h1><script>setTimeout(function(){history.back(-1);}, 3000)</script>
@endif



<script Language="JavaScript">
    function Transfere() {
        document.location.assign("https://www.tele-tudo.com/vlrtransf");
    }
</script>

<?php

$Teste=0;
if (Session::has('Teste')) {
    $Teste=1;
}

$cSessao = new App\Sessao;

// $OnLine = $cSessao->VeSf();
$OutrDisab='disabled';
//if ($Teste==1) { //FOI COMENTADO PORQUE ESTÁ EM TESTE E HABILITA O SUBMIT
if ($Teste==0) { 
    // $OnLine=1;
    $OutrDisab='';
}

//CRIADO PARA FINS DE TESTES COM O ChECKOUT SEGURO
/*$OutrDenab='enabled';
if ($Teste==1) {
    // $OnLine=1;
    $OutrDenab='';
}*/
$Nome = DB::table('users')->where('user',$user)->first()->Nome;

{{ $vlrEntr = isset($_POST['vlrEntr'])?$_POST['vlrEntr']:'20.00'; }}

if($_POST['produtos']=='' || $_POST['produtos']=='nada'){
    echo '<script>history.back(-1);</script>';
}


/*if ($OnLine==0) {
    echo "<a class='btn btn-small btn-success btn-lg btn-block' disabled >Transferência Bancária</a>";
    echo "<p><div class='alert alert-danger'><font size='5'>Sistema Financeiro não disponível no momento</font><font size='2'>  Não será possível colocar a loja On-Line</font></div><p>";
} else {*/
    // echo "<a class='btn btn-small btn-success btn-lg btn-block' href=".URL::to('vlrtransf/create/').">Transferência Bancária</a>";

    echo "<a class='btn btn-small btn-success btn-lg btn-block' href='https://www.tele-tudo.com/vlrtransf'>Transferência Bancária</a>";

// }

?>

<!--<form action="https://www.moip.com.br/PagamentoMoIP.do" method="POST">-->
<!--<form action="/pagamentos/credito" method="POST">FOI DESATIVADO TEMPORARIAMENTE PARA TESTAR A ACTION DE CONFIRMAÇÃO DE TELE-ENTREGA-->
<form action="/confirma" method="POST">
    {!! Csrf_Field() !!}
    
    {{ method_field('POST') }}

    <!--Sua identificação no MoIP. Pode ser seu e-mail principal, celular verificado ou login.	Alfanumérico	45-->
    <input type="hidden" name="id_carteira" value="{{$id_carteira}}">

    <!--O valor da transação, sem vírgulas e identificador da moeda	Numérico (inteiro)	9-->
    <input type="hidden" name="valor"  value="{{$Valor}}">

    <input type="hidden" name="descricao"  value="{{$Descricao}}">

    <input type="hidden" name="tpEnt"  value="{{$tpEnt}}">

    <input type="hidden" name="VlrEntrega"  value="{{$vlrEntr}}">

    <input type="hidden" name="IDPED"  value="{{$IDPED}}">

   
    <input type="hidden" name="user"  value="{{$user}}">

    <!--Razão do pagamento	Razão do pagamento a ser mostrado na página do MoIP, durante o processo de confirmação (nome do produto/serviço)	Alfanumérico	64-->
    <input type="hidden" name="nome"  value="{{$Nome}}">

    <input type="submit" class="btn btn-info btn-block btn-lg btn-primary {{$OutrDisab}}" value="Cartões de crédito">
    

    <!--<a href="http://desenvolvedor.moip.com.br/sandbox/" target="_blank"><img src="http://desenvolvedor.moip.com.br/sandbox/imgs/banner_5_1.jpg" border="0"></a>-->

</form>

<!--list-style-type:none; border-radius:12px;-->


 <style>
     #bandeiras{overflow:hidden; height:36px; margin-left:20%; margin-top:0px; padding-left:8px;}
 </style>
    <div id="bandeiras" >

          <img src={{ asset('bandeiras_wirecard/visa.png') }} width="60" height="30" alt="visa"/>
          <img src={{ asset('bandeiras_wirecard/mastercard.jpg') }} width="60" height="30" alt="mastercard"/>
          <img src={{ asset('bandeiras_wirecard/hiper_menor.png') }} width="60" height="30" alt="hiper"/>
          <img src={{ asset('bandeiras_wirecard/elo.png') }} width="60" height="30" alt="elo"/>
          <img src={{ asset('bandeiras_wirecard/Hipercard.png') }} width="60" height="30" alt="hipercard"/>
          <img src={{ asset('bandeiras_wirecard/itaucard.jpg') }} width="60" height="30" alt="itaucard"/>
          <img src={{ asset('bandeiras_wirecard/amex.png') }} width="60" height="30" alt="amex"/>
          <img src={{ asset('bandeiras_wirecard/BancoDoBrasil.png') }} width="60" height="30" alt="banco do brasil"/>
          <img src={{ asset('bandeiras_wirecard/banco-santander.png') }} width="60" height="30" alt="santander"/>
    </div>
@stop