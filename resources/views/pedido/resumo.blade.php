<?php $idUser = 0; ?>
@extends('layouts.padrao')
<?php
$idPedido = Session::get('idPedido');
$tpEnt=0;
if (Session::has('TpEntrega')) {
    $tpEnt=Session::get('TpEntrega');

    $idCli = Auth::id();

    // Não tem que fazer a efetivação aqui
    // mas sim na pagina que chama esta

    // $clsEntrega = new Entrega();
    // $forn = Session::get('FORN');
    // $clsEntrega->Efetiva($idPedido);
    // $clsEntrega->EfetivaPedidoNoBDSemEntrega($idPedido, $idCli);
}
?>
<title>{{ 'Resumo do Pedido nr. '.$idPedido }}></title>
@section('content')
<style type="text/css">
    html { height: 75% }
    body { height: 100%; margin: 0%; padding: 0% }
</style>

<p>Resumo da Compra:</p>
<table>
    <?php
    $clsPedido = new App\Pedido;
    $lista = $clsPedido->getItensPedido($idPedido);
    foreach ($lista as $reg) {
        echo "<tr>";
        echo "<td width='494'>".$reg->Nome."</td>";
        echo "<td width='100'>R$ ".number_format($reg->Valor,2,',','.')."</td>";
        echo "<td width='100'>".$reg->quant."</td>";
        echo "<td width='100'>R$ ".number_format($reg->Valor*$reg->quant,2,',','.')."</td>";
        echo "</tr>";
    }
    $clsEntrega = new App\Entrega;
    $VlrCompras = $clsEntrega->getCompras($idPedido);
    $VlrEntrega = $clsEntrega->getVlrEntrega($idPedido)+$clsEntrega->getVlrNosso();
    $sCompras = number_format($VlrCompras,2,',','.');
    $sEntrega = number_format($VlrEntrega,2,',','.');
    $sTotal = number_format($VlrCompras+$VlrEntrega,2,',','.');
    ?>
    <tr></tr>
    <tr>
        <td width="494">Total</td>
        <td width="100"></td>
        <td width="100"></td>
        <td width="100">R$ {{$VlrCompras}}</td>
    </tr>
    <tr>
        <td width="494">Tele-Entrega</td>
        <td width="100"></td>
        <td width="100"></td>
        <td width="100">R$ {{$sEntrega }}</td>
    </tr>
    <tr>
        <td width="494">Valor Pago</td>
        <td width="100"></td>
        <td width="100"></td>
        <td width="100">R$ {{$sTotal}}</td>
    </tr>

</table>
</Br></Br>
<?php
if ($tpEnt==0) {

    $array=$clsEntrega->TempoDecorrido($idPedido);
    if ($clsEntrega->getErroCalcTempo()==0) {
        $deco = json_decode($array);
        $hIN=$deco->{'HoraIN'};
        $hFM=$deco->{'HoraFIM'};
        $dataH = date("d/m/Y");
        $data = substr($hIN, 0, 10);
        $dataN = new DateTime($data);
        $DataF = $dataN->format('d/m/Y');
        if ($DataF==$dataH) {
            $hINm=substr($hIN, 11, 8);
            $hFMm=substr($hFM, 11, 8);
        } else {
            $hINm = $hIN;
            $hFMm=$hFM;
        }
        $hDec=$deco->{'tempo'};
        $horas=0;
        $sHoras="";
        if ($hDec>3600) {
            $horas = floor($hDec / 3600);
            $sHoras = $horas." horas e ";
        }
        $minutos = floor(($hDec - ($horas * 3600)) / 60);
        echo "Tempo decorrido: ".$sHoras.$minutos." minutos (".$hINm." - ".$hFMm.")<br>";
    }
    $Ent = $clsEntrega->getEntregador($idPedido);
    $Entr = explode("|", $Ent);
    echo "Entregador: ".$Entr[0]."<br>";
    echo "Placa: ".$Entr[1]."<br>";
} else {
    $kms = Session::get("Kms");
    if ($kms>1) {
        $kms=intval($kms);
        $Dist = $kms." Kms";
    } else {
        $Dist = ($kms*1000)." mts";
    }
    $tmp=Session::get("TmpPrevisto");
    $Tempo=intval($tmp/60)+1;
    echo "Distância: ".$Dist."<p>";
    echo "Tempo Previsto:  <span style='font-size: large'>".$Tempo."</span> minutos<p>";
}
?>
<br>
<form method="post" style="vertical-align: top">
    Caso queira entrar em contato<textarea name="TextArea1" rows="2" style="width: 521px"></textarea>
    <button disabled="disabled" >Enviar</button>
    <br>
</form>
</Br></Br>
Informe aos seus amigos sobre a novidade:
<button disabled="disabled" >FaceBook</button>
<button disabled="disabled" >Twitter</button>
<button disabled="disabled" >G+</button>
</div>
</Br>
</body>

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
<?php
Session::forget('TpEntrega');
Session::forget('PEDIDO');
?>
@stop