<?php $idUser=0; ?>
@extends('layouts.padrao')
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>
<?php
Session::put('TpEntrega', 1);
$idPedido = Session::get('idPedido');
$idCli = Auth::id();
$forn = Session::get('FORN');
$Comprou=0;
$idNotif=0;
if (Session::has('COMPROU')) {
    $Comprou=1;
    $idNotif = Session::get('COMPROU');
} else {
    if (isset($_REQUEST['forma'])) {
        $Comprou=1;
        $VlrEntrega = Session::get('VlrEntrega');
        $idEntrega = Session::get('ENTREGA');
        $cEntrega = new App\Entrega();
        $cEntrega->setidEntrega($idEntrega);
        $cEntrega->setidPedido($idPedido);
        $cEntrega->setVlrEntrega($VlrEntrega,"pagtodireto");
        $FornecRecebeu = $cEntrega->VeQtoFornRecebeu($idPedido);
        $cNotificacao = new App\Notificacao();
        $FormaPagto = $_REQUEST['forma'];
        $Troco = $_REQUEST['trc'];
        $idPesq=0;
        if (Session::has('idPesq')) {
            $idPesq = Session::get('idPesq');
            Session::forget('idPesq');
        }
        $Com = $_REQUEST['com'];
        $idNotif = $cNotificacao->InformaQueComprou($idPedido, $forn, $FornecRecebeu, $FormaPagto, $Troco, $idPesq, $Com);
        Session::put('COMPROU', $idNotif);
    }
}
$pessoas = DB::table('users')
    ->select('Endereco_ID')
    ->where('id','=',$idCli)
    ->first();
$ClsEnderecos = new App\Enderecos;
$ender = $ClsEnderecos->GetEndereco($pessoas->Endereco_ID, 1);
$valorJS = number_format(Session::get('VLRTOTAL'),2,'.',',');
$valor = str_replace('.',',',$valorJS);
$cEmpresa = new App\Empresa;
// $formas = $cEmpresa->getFormas($forn, $valor);
/*$veio = $_SERVER['HTTP_REFERER'];*/
$Tempo = $cEmpresa->getTempo();
$Empresa = $cEmpresa->getNome();
?>
<title>{{ 'Confirmação do Pedido nr. '.$idPedido }}></title>
@section('content')
<?php
    if (isset($_REQUEST['cancelou'])) {
        echo '<h1>Este pedido foi cancelado</h1>';
        ?>
@stop
        <?php
    }
?>
<style type="text/css">
    html { height: 75% }
    body { height: 100%; margin: 0%; padding: 0% }
    .formas {
        color: #008000;
        font-weight: bold;
    }
</style>

<script>

    var idPedido = <?php echo $idPedido; ?>;
    var idAviso = <?php echo $idNotif; ?>;
    var VlrPed = <?php echo $valorJS; ?>;
    var Troco = 0;

    console.log('77 '+idAviso);
    if (idAviso>0) {
    	setInterval(VeSeOFornViu, 10000);
    }

    function ClicouDin() {
        var VLR = FmtValor(VlrPed);
        var TXT = "<h3>Pagamento em dinheiro</h3>";
        TXT+="Voce vai precisar de troco?";
/*
        TXT+="Sim&nbsp;<input id='rdSim' type='radio'>&nbsp;&nbsp;";
        TXT+="Não&nbsp;<input checked='true' id='rdNao' type='radio'>";
*/
        TXT+="<table style='width: 50%'>";
        TXT+="<tr>";
        TXT+="<td style='width: 145px' align='center'>";
        TXT+="<label>Valor</label>";
        TXT+="</td>";
        TXT+="<td style='width: 343px' align='center'>";
        TXT+="<label>Troco</label>";
        TXT+="</td>";
        TXT+="<td style='width: 200px' align='center'>";
        TXT+="</td>";
        TXT+="</tr>";
        TXT+="<tr>";
        TXT+="<td style='width: 145px'>";
        TXT+="<input id='txValor' type='text' onkeyup='Calcular()' onclick='this.select()' value=' "+VLR+"'>";
        TXT+="</td>";
        TXT+="<td style='width: 343px'>";
        TXT+="<input id='txTroco' type='text' readonly align='right' value=' 0,00'>";
        TXT+="</td>";
        TXT+="<td style='width: 200px' align='center'>";
        TXT+="<input type='submit' id='btEnviar' value='Enviar' onclick='PagtoDinheiro()' class='btn-lg btn-primary'/>";
        TXT+="</td>";
        TXT+="</tr>";
        TXT+="</table>";
        $("#dvTroco").append(TXT);
        $('#bt1').css({display:"none"});
    }

    function FmtValor(Valor) {
        var sValorO = numeral(Valor).format('0.00[0000]')+' ';
        var sValor = sValorO.replace(".", ",");
        return sValor;
    }

    function Calcular() {
        var sValorO = document.getElementById("txValor").value;
        var sValor = sValorO.replace(",", ".");
        var vTroco = sValor-VlrPed;
        var EnBt=true;
        var sTroco='';
        if (vTroco>0) {
            sTroco = FmtValor(vTroco);
            EnBt=false;
            Troco=vTroco;
            console.log(Troco);
        } else {
            Troco=0;
            if (vTroco<0) {
                EnBt=true;
            } else {
                EnBt=false;
            }
        }
        document.getElementById("txTroco").value=sTroco;
        document.getElementById('btEnviar').disabled=EnBt;
    }

    function voltar() {
        history.back();
    }

    function VeSeOFornViu() {
        console.log('idAviso = '+idAviso)
        $(function() {
            $.ajax({
                url: "https://www.tele-tudo.com/processo?op=8&idAviso="+idAviso,
                dataType: "html",
                success: function(result) {
                        console.log('result = '+result)
                        if (result=='1') {
                            setTimeout(VeSeOFornViu, 10000);
                        } else {
                            document.location.assign("https://www.tele-tudo.com/resumo?IDPED="+idPedido);
                    }
                }
            });
        });
    }

    function ClicouForma(forma) {
        var Coment = "'"+document.getElementById("txComentario").value+"'";
        document.location.assign("https://www.tele-tudo.com/pagtodireto?forma="+forma+'&trc=0'+'&com='+Coment);
    }

    function PagtoDinheiro() {
        var Coment = "'"+document.getElementById("txComentario").value+"'";
        document.location.assign("https://www.tele-tudo.com/pagtodireto?forma=1&trc="+Troco+'&com='+Coment);
    }

    function Cancelar() {
        if (confirm ("Tem certeza que deseja cancelar o pedido")) {
            alert("https://www.tele-tudo.com/processo?op=11&idPedido="+idPedido);
            $(function(){
                $.ajax({
                    url: "https://www.tele-tudo.com/processo?op=11&idPedido="+idPedido,
                    dataType: "html",
                    complete: function(msg){
                        document.location.assign("https://www.tele-tudo.com/pagtodireto?cancelou");
                    }
                });
            });
        }
    }

</script>
<h1 style="color: #0000FF">Confirmação da Compra:</h1>
<h2><p style="color: #FF0000"><span>Voce esta comprando de {{$Empresa}}</span></p></h2>
<h3>Confirmar a compra o entregador ir&aacute; at&eacute; seu endere&ccedil;o levar a compra e far&aacute; a cobran&ccedil;a no local.</h3>
<h2>Endere&ccedil;o: {{$ender}}</h2>
<h2>Valor: {{"R$ ".$valor}}</h2>
<h2>Tempo previsto de espera: {{$Tempo}}</h2>
@if ($Comprou==1)
    <div class="alert alert-info">
        <h3>Aguardando visualização do pedido pelo fornecedor</h3>
        <input name="btCanc" type="button" value="Cancelar" onclick="Cancelar()">
    </div>
@else
    <h2>Formas de pagamento aceitas:</h2>
    <?php
    $formas = $cEmpresa->getFormas($forn, $valor);
    ?>
    </Br>
    <h3>Comentário ao vendedor:</h3>
    <textarea id="txComentario" style="width: 460px; height: 44px"></textarea>
    </Br>
    <button type="button" onclick="voltar();" class="btn btn-warning btn-lg">Retornar</button>
@endif
@stop