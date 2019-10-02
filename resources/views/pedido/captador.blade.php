<?php
$idUser = 0;
Session::put('SemDown',1);
?>
@extends('layouts.padrao')
<title>Painel do Captador</title>
<link href="http://voky.com.ua/showcase/sky-forms/examples/css/sky-forms.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .normal {
        border-width: thin;
        width:330px; height:25px;
        border-color: #000000;
    }
</style>
@section('content')
<?php
if (Auth::check()==false) {
    echo 'esta Area e apenas para Usuarios Registrados'; die;
}
$idUser = Auth::id();
$cCap = new Captador();
$aReceber = $cCap->getSaldo($idUser);
$sReceber = number_format($aReceber, 2, ',', '.');
?>
<label style="font-size: medium">Captador é nosso representante. Se dedica a trazer mais fornecedores para o tele-tudo<Br>
E lucra com as vendas realizadas por eles</label>
<div class="col-md-9">
<div class="table-responsive">
<div class="alert alert-info"><h3>Valor já recebido: R$ 0,00</h3></div>
<div class="alert alert-info"><h3>Valor a receber: R$ {{$sReceber}}</h3></div>
<h3>Use este endereço para convidar pessoas para o tele-tudo<Br>e já fazerem parte da sua equipe</h3>
<textarea class="form-control" id="txConvite" rows="1" onclick="this.select();" readonly="readonly" dir="ltr" autocorrect="off" spellcheck="false" id="lbConvite" style="max-width:100%; font-size: x-large; cursor:pointer" onmousemove="PassouMouse();" ></textarea>
<div id="Dvinfo" visible="false" style="text-align:center; color:#00FF12; font-size: medium;" > </div>
<h1>Fornecedores<h1>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Valor Total</th>
            <th>A Resgatar</th>
            <th>Resgatado</th>
            <th>Ultima Venda</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
        <h1>Sua <b>equipe</b> de captadores<h1>
        <?php
        $IdCaptador = $cCap->getIdCaptador($idUser);
        if ($cCap->getCaptados($IdCaptador)>0) {
            if ($cCap->getQtqEquipe($IdCaptador)==0) {
                echo "<h4 style='color: #800000'>Há pessoas cadastradas com seu convite, mas não houve ainda algum captador</h4>";
            }
        }
        ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Valor Total</th>
                    <th>A Resgatar</th>
                    <th>Resgatado</th>
                    <th>Ultima Venda</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>

@if (Session::has('message'))
    <div class="alert alert-info"><h3>{{ Session::get('message') }}</h3></div>
@endif
</div>
<script>
    var idCaptador = "<?php echo $idUser; ?>";
    var Passou = false;
    document.getElementById("txConvite").innerHTML="https://www.tele-tudo.com/convite?id="+idCaptador;
    function PassouMouse() {
        if (Passou==false) {
            document.getElementById("Dvinfo").innerText="Copie para memória";
            Passou = true;
        }
    }
</script>
@stop
