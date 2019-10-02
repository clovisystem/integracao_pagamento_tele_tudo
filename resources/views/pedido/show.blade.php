<?php $idUser = 0; ?>
@extends('layouts.padrao')
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<style>
    /* Always set the map height explicitly to define the size of the div
     * element that contains the map. */
    #map {
        height: 100%;
    }
    /* Optional: Makes the sample page fill the window. */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
</style>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
<?php
$idPedido = Session::get('idPedido');
?>
<title>{{ 'Resumo do Pedido nr. '.$idPedido }}></title>
@section('content')
    <style type="text/css">
        html { height: 75% }
        body { height: 100%; margin: 0%; padding: 0% }
    </style>
    <Br>
    <?php
    $cPed = new App\Pedido;
    $iduser = Session::get('iduser');
    $idForn = $cPed->getIdFornUser($iduser);
    $NrPedForn = $cPed->getNrVendas($idForn);
    $Cliente = $cPed->getCliente($pedido->idPed);
    $FoneCli = $cPed->getFoneCli();
    $Valor = $cPed->getTotal($pedido->idPed);
    $sValor = number_format($Valor,2,',','.');
    $QtdItens = $cPed->getQtdItens($pedido->idPed);
    $tpEnt = $cPed->tpEnt($idForn);
    $Endereco = $cPed->getEnderPedido();
    ?>
    <p>Resumo da Compra: {{$NrPedForn}}</p>
    <p>Comprador: <strong>{{$Cliente}}</strong></p>
    <p>Endereço: <strong>{{$Endereco}}</strong></p>
    <p>Telefone: <strong>{{$FoneCli}}</strong></p>
    <?php
    if ($tpEnt==0) {
        $Previsao = $cPed->getPrevisao($pedido->idPed);
        $NmMotoBoy = $cPed->getNmMotoBoy($pedido->idPed);
        $Placa = $cPed->getPlaca();
        $FoneBoy = $cPed->FoneBoy();
        ?>
        <p>MotoBoy: <strong>{{$NmMotoBoy}}</strong></p>
        <p>Placa: <strong>{{$Placa}}</strong></p>
        <p>Telefone: <strong>{{$FoneBoy}}</strong></p>
        <p>Previsão de chegada: <strong>{{$Previsao}}</strong></p>
        <p>Valor: <strong>{{$sValor}}</strong></p>
        <?php
    } else {
        $forma = $cPed->getFormaPagto();
        echo "<p>Forma de Pagamento: <strong>".$forma."</strong></p>";
        echo "<p>Valor: <strong>".$sValor."</strong></p>";
        if ($forma=='Dinheiro') {
            $Troco = $cPed->getTroco();
            echo "<p>Troco: <strong>".$Troco."</strong></p>";
        }
    }
    if ($cPed->getComentario()>'') {
        echo "<div class='alert alert-info'><h3>".$cPed->getComentario()."</h3></div>";
    }
    ?>
    <p>Quantidade de Itens diferentes: <strong>{{$QtdItens}}</strong></p>
    <br>
    <table class="table table-striped table-bordered">
        <tr>
            <td class="centro">Produto</td>
            <td class="centro">Quantidade</td>
        </tr>
        <?php echo $cPed->getItens($pedido->idPed); ?>
    </table>
    <?php
    $latC = $cPed->getlatC();
    $lonC = $cPed->getlonC();

    $cEntrega = new App\Entrega();
    $geoFor = $cEntrega->Coord_Fornecedor($idForn);
    $LatsFor = explode("|", $geoFor);

    $ClsEnderecos = new App\Enderecos;
    $idEndForn = $cPed->getidEndForn();
    $EndForn = $ClsEnderecos->GetEndereco($idEndForn, 1);
    ?>
    <div id='dist'></div>
    <div id="map"></div>
    <script>
        function initMap() {

            var CliLat = <?php echo $latC; ?>;
            var CliLon = <?php echo $lonC; ?>;
            var ForLat = <?php echo $LatsFor[0]; ?>;
            var ForLon = <?php echo $LatsFor[1]; ?>;
            var enderAte = <?php echo "'".$Endereco."'"; ?>;
            var enderDe = <?php echo "'".$EndForn."'"; ?>;

            var LatCen = ((CliLat + ForLat) / 2.0);
            var LonCen = ((CliLon + ForLon) / 2.0);

            if (CliLat>ForLat) {
                lat_max = CliLat;
                lat_min = ForLat;
            } else {
                lat_max = ForLat;
                lat_min = CliLat;
            }
            if (CliLon>ForLon) {
                lng_max=CliLon;
                lng_min=ForLon;
            } else {
                lng_max=ForLon;
                lng_min=CliLon;
            }

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: {lat: LatCen, lng: LonCen }
            });

            map.fitBounds(new google.maps.LatLngBounds(
                new google.maps.LatLng(lat_min, lng_min),
                new google.maps.LatLng(lat_max, lng_max)
            ));

            var directionsService, directionsRenderer;

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            var request = {
                origin:enderDe,
                destination:enderAte,
                travelMode: google.maps.DirectionsTravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC
            };

            directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(response);
                    $("#dist").append("<h2 style='color: #0000FF'>Distância: "+response.routes[0].legs[0].distance.text+"</h2>");
                }
            });

        }

    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-8eUFfwmlY-gOzlDw81q45EYmZeJwfVk&callback=initMap">
    </script>

@stop


