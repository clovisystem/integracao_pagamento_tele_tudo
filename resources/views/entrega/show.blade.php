@extends('layouts.padrao')
<title>Acompanhamento da Entrega</title>
@section('content')

    <style type="text/css">
        html { height: 75% }
        body { height: 100%; margin: 0%; padding: 0% }
        #map { height: 100%; margin: 10%; padding: 0% }
    </style>


    <?php
    $clsEntrega = new App\Entrega();

    //{{ $identrega; }}
    //$idPedido = $entr->first()->idPedido;
    //$idPedido = Entrega::where('id', $id)->first()->idPedido;
    //$idPedido = $idped;
    //$idPedido = $clsEntrega->getPedido();
    $idPedido = $idPedidoEntr;
    $idUser = $idUser;
    $EsperaPagamento=1;
    $Modo=$clsEntrega->getModo();
    $Teste=0;
  
    if (Session::has('Teste')) {
        $Teste=1;
    }
    $Token = Session::get('Token');
    $NomeFor = $clsEntrega->GetNomeFor($idPedido);
    $FoneFor = $clsEntrega->GetFoneFor();
    $emailFor = $clsEntrega->GetEmailFor();
    ?>
<script>
    var Contador=0; // MostrouQNaoTemMotoboy
    var ok=0;
    var map = null;
    var latOLD = 0;
    var lonOLD = 0;
    var CliLat = 0;
    var CliLon = 0;
    var ForLat = 0;
    var ForLon = 0;
    var lat_max = 0;
    var lat_min = 0;
    var lng_max = 0;
    var lng_min = 0;
    var OldeSts = -2;

    var EsperaPagamento = <?php echo $EsperaPagamento; ?>;
    var Teste = <?php echo $Teste; ?>;
    var Modo = <?php echo $Modo; ?>;

    var idPedido = <?php echo $idPedido; ?>;

    var Token = "<?php echo $Token; ?>";

    var NomeFor = "<?php echo $NomeFor; ?>";
    var FoneFor = "<?php echo $FoneFor; ?>";
    var emailFor = "<?php echo $emailFor; ?>";

    var sTeste="";
    var Tempo = 30000;
    if (Modo<3) {
        Tempo=4000;
    }
    if (Teste==1) {
        sTeste="&Teste=1";
        Tempo=4000;
    }

	var nav = navigator.appVersion;
	var A = nav.indexOf("Android");
	var nH = "h1";
	var n2 = "h2";

	if (A<1) {
	} else {
            nH = "h4";
            n2 = nH
	}
        if (EsperaPagamento==0) {
            $('#criando').css({display:"block"});
            Solicita();
        } else {
            setInterval(VeSePagou(0), Tempo);
        }

        function Solicita() {

            // url: "http://www.tele-tudo.com/processo?op=1"+sTeste,

            $(function(){
                $.ajax({
                    url: "https://www.tele-tudo.com/operacoes?op=1&idPedido="+idPedido+"&Token="+Token,
                    dataType: "html",
                    success: function(result){
                        $("#solicitou").append(result);
                        // Loga("processo?op=1");
                        setInterval(VerMotora(Contador), Tempo);
                    },
                    beforeSend: function(){
                        $('#criando').css({display:"block"});
                    },
                    complete: function(msg){
                        $('#criando').css({display:"none"});
                    }
                });
            });
        }

        function Efetiva() {

	        // url: "https://www.tele-tudo.com/processo?op=6",

            $(function(){
                $.ajax({
                    url: "https://www.tele-tudo.com/processo?op=6&idPedido="+idPedido,
                    dataType: "html",
                    success: function(result){
			            Loga('efetivado no BD');
                    }
                });
            });
        }

        function VeSePagou(JaFez) {

            // url: "http://www.tele-tudo.com/processo?op=4"+sTeste,

            $(function(){
                $.ajax({
                    url: "https://www.tele-tudo.com/processo?op=4&idPedido="+idPedido+sTeste,
                    dataType: "html",
                    success: function(result){
                        if (result=='0') {
                            if (JaFez==0) {
                                Loga('não pagou');
                                $("#pagAguarda").append("<"+nH+">Aguardando verificação do pagamento</"+nH+">");
                                $("#pagCanc").append("<input name='btpagCanc' type='button' value='Cancelar Informação de pagamento' onclick='CancelaPagto()'>");
                                ok=1;
                            }
                            setInterval(VeSePagou(1), Tempo);
                        } else {
                            Loga('pagou');
                            ok=1;
                            $("#pagConfir").append("<"+nH+">Pagamento Confirmado</"+nH+">");
                            $('#criando').css({display:"block"});
                            $('#pagAguarda').css({display:"none"});
                            $('#pagCanc').css({display:"none"});
                            Solicita();
                        }
                    }
                });
            });
        }

        function VerMotora(Contador) {
            $(function(){
                $.ajax({
                    url: "https://www.tele-tudo.com/processo?op=2&vez="+Contador+"&Token="+Token,
                    dataType: "html",
                    success: function(result){
                        var ret = eval('('+result+')');
                        Contador++;
                        Loga('VerMotora');
                        if (ret.status=='not_matched') {
                            Loga('not_matched MQNTM');
                            if (Contador==0) {
                                $("#motora").append("<"+nH+">Aguardando Motoboy</"+nH+">");
                                ok=1;
                            }
                            setInterval(VerMotora(Contador), Tempo);
                        } else {
                            Loga('ret.status = '+ret.status);
                            ok=1;
                            $("#motora").append("<"+nH+">Motoboy Aceitou Entrega</"+nH+">");
                            Loga('Motoboy Aceitou Entrega');
                            setTimeout(MostraOCara, Tempo);
                        }
                    },
                    beforeSend: function(){
                        // Loga('beforeSend ok = '+ok);
                        if (ok==1) {
                            $('#solicitou').css({display:"block"});
                        }
                    },
                    complete: function(msg){
                        // Loga('complete ok = '+ok);
                        if (ok==1) {
                            $('#solicitou').css({display:"none"});
                        }
                    }
                });
            });
        }

        function OndeEleTa() {
            $(function() {
                $.ajax({
                    url: "https://www.tele-tudo.com/processo?op=3&vez=" + Contador+sTeste+"&Token="+Token,
                    dataType: "html",
                    success: function(result) {
                    	Loga('OndeEleTa');
                        var ret = eval('(' + result + ')');
                        var status = ret.status;
                        if (status == 'finished') {
                            // var idPedido = <?php echo $idPedido; ?>;

                            document.location.assign("https://www.tele-tudo.com/resumo?IDPED="+idPedido);
                            // document.location.assign("http://www.tele-tudo.com/pedido/"+idPedido);

                        } else {
                            if (status == 'delivering') {
	                        if (ret.last_delivery_point_finished!=OldeSts) {
	                            switch(ret.last_delivery_point_finished) {
	                                case -1:    // SE DESLOCANDO PARA O FORNECEDOR
	                                    var Dive = "<"+nH+">Em deslocamento para o fornecedor</"+nH+">";
	                                    $("#indofor").append(Dive);
	                                    break;
	                                case 0: // CHEGOU NO FORNECEDOR
                                        console.log('Chegou no fornecedor');
                                        $('#btCanc').css({visibility:"hidden"});
	                                    $('#indofor').css({visibility:"hidden"});
	                                    $('#indocli').css({visibility:"visible"});
	                                    var Dive = "<"+nH+">Em deslocamento para o cliente</"+nH+">";
	                                    $("#indocli").append(Dive);
	                                    Efetiva();
	                                    break;
	                                default:    // 1 PRONTO PARA ENTREGA
	                                    // REDIRECIONAR PARA A PÁGINA DE CONCLUSÃO
	                                    $('#indocli').css({visibility:"hidden"});
	                                    var Dive = "<"+nH+">Pronto para realizar a entrega</"+nH+">";
	                                    $('#pronto').css({visibility:"visible"});
	                                    $("#pronto").append(Dive);
	                                    break;
	                            }
	                            OldeSts = ret.last_delivery_point_finished;
	                        }
	                            Contador++;
	                            var foo = JSON.stringify(ret.deliveryboy);
	                            var boy = eval('(' + foo + ')');
	                            var MotLat = boy.latitude;
	                            var MotLon = boy.longitude;
	                            if (latOLD == 0) {

	                                var LatCen = ((CliLat + ForLat + MotLat) / 3.0);
	                                var LonCen = ((CliLon + ForLon + MotLon) / 3.0);
	                                map = new google.maps.Map(document.getElementById('map'), {
	                                    zoom: 14, center: { lat: LatCen, lng: LonCen }
	                                });

	                                if (MotLat > lat_max) { lat_max = MotLat; }
	                                if (MotLat < lat_max) { lat_min = MotLat; }
	                                if (MotLon > lng_max) { lng_max = MotLon; }
	                                if (MotLon < lng_max) { lng_max = MotLon; }
	                                map.fitBounds(new google.maps.LatLngBounds(
	                                    new google.maps.LatLng(lat_min, lng_min),
	                                    new google.maps.LatLng(lat_max, lng_max)
	                                ));
	                                // map.
	                                Marca(map, {lat: CliLat, lng: CliLon }, 'Cliente');
	                                Marca(map, {lat: ForLat, lng: ForLon }, 'Fornecedor');
	                                Marca(map, {lat: MotLat, lng: MotLon }, 'MotoBoy');
	                            } else {
	                                Marca(map, {lat: MotLat, lng: MotLon}, 'MotoBoy');
	                            }
	                            latOLD = MotLat;
	                            lonOLD = MotLon;
	                        }
	                    }
	                }
                });
            });
        }

        function MostraOCara() {
            $(function(){
                $.ajax({
                    url: "https://www.tele-tudo.com/processo?op=3&vez="+Contador+"&Token="+Token,
                    dataType: "html",
                    success: function(result){
                        var ret = eval('('+result+')');
                        var status = ret.status;
                        var foo = JSON.stringify(ret.deliveryboy);
                        var boy = eval('('+foo+')');
                        Loga('MostraOCara status = '+status);
                        $("#entregador").append("<"+nH+"><b>"+boy.name+"</b></"+nH+">"+
                            "<img src='"+boy.photo_url+"'>"+
                            "<"+nH+">Fone: <b>"+boy.phone+"</"+nH+"></b>"+
                            "<"+nH+">Placa: <b>"+boy.license_plate+"</b></"+nH+">");
                        Loga('Colocar inf do motoboy');
                    },
                    complete: function(msg){
                        $('#motora').css({display:"none"});
                        $('#entregador').css({visibility:"visible"});
                        setInterval(OndeEleTa, Tempo);
                        Loga('Acionou timer OndeEleTa');
                    }
                });
            });
        }

        function Loga(Texto) {
           console.log(Contador+' '+Texto);
        }

        function CancelaPagto() {
            var textoA = "A TELE-TUDO.COM não poderá estornar seu pagamento\n";
            textoA+= "Voce precisará entrar em contato com o fornecedor para ser ressarcido\n";
            textoA+= "Confirma o cancelamento?\n\n";
            textoA+= "Fornecedor: "+NomeFor+"\n";
            textoA+= "Telefone: "+FoneFor+"\n";
            textoA+= "email: "+emailFor;
            if (confirm (textoA)) {

                $(function(){
                    $.ajax({
                        url: "https://www.tele-tudo.com/processo?op=11&idPedido="+idPedido,
                        dataType: "html",
                        complete: function(msg){
                            $('#pagAguarda').css({display:"none"});
                            $('#pagCanc').css({display:"none"});
                            var textoB = "<h1>Compra foi cancelada</h1><h4>Entre em contato com o fornecedor para tratar de ressarcimento<Br>";
                            textoB+="Pedido: "+idPedido+"<Br>";
                            textoB+="Fornecedor: "+NomeFor+"<Br>";
                            textoB+= "Telefone: "+FoneFor+"<Br>";
                            textoB+= "email: "+emailFor;
                            $("#CancelouTransf").append(textoB);
                        }
                    });
                });

            }
        }

        document.write("<"+h2+">Acompanhamento da entrega   <input id='btCanc' type='button' onclick='Cancelar()' value='Clique aqui para cancelar'></"+h2+">");

    </script>
    </Br>
    <div id="criando" style="visibility: hidden">
        <script>
    	    document.write("<"+nH+">Criando solicitação de entrega</"+nH+">");
    	</script>
    </div>
    <div id="pronto" style="visibility: visible"></div>
    <div id="indocli" style="visibility: visible"></div>
    <div id="indofor" style="visibility: visible"></div>
    <div id="solicitou"></div>
    <div id="pagAguarda"></div>
    <div id="pagCanc"></div>
    <div id="CancelouTransf"></div>
    <div id="pagConfir"></div>
    <div id="motora"></div>
    <div id="entregador" style="visibility: hidden"></div>
</div>
<div id="map" name="map"></div>
<?php
$iduser=0;

/*if (Session::has('iduser')) {
    $iduser=Session::get('iduser');
} else {
    echo 'Sem Session:iduser'; die;
}
*/

if (isset($idUser)) {
    $iduser = $idUser;
} else {
    echo 'Sem Session: {{ $iduser }}'; die;
}
// $clsEntrega = new Entrega();
$geoCli = $clsEntrega->CoordCliente($iduser);
$LatsCli = explode("|", $geoCli);

$geoFor = $clsEntrega->CoordFornecedor();
$LatsFor = explode("|", $geoFor);

// echo 'token:'.Session::get('Token');

?>
<script>

    function initMap() {

        CliLat = <?php echo $LatsCli[0]; ?>;
        CliLon = <?php echo $LatsCli[1]; ?>;
        ForLat = <?php echo $LatsFor[0]; ?>;
        ForLon = <?php echo $LatsFor[1]; ?>;

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

        // var
        map = new google.maps.Map(document.getElementById('map'), {
             zoom: 14,
             center: {lat: LatCen, lng: LonCen }
         });

         Marca(map, { lat: CliLat, lng: CliLon}, 'Cliente');
         Marca(map, { lat: ForLat, lng: ForLon}, 'Fornecedor');
         map.fitBounds(new google.maps.LatLngBounds(
            new google.maps.LatLng(lat_min, lng_min),
            new google.maps.LatLng(lat_max, lng_max)
         ));
    }

     function Marca(map, posicao, texto) {
         var marker = new google.maps.Marker({
         	position: posicao,
         	map: map
         });
         attachSecretMessage(marker, texto);
     }

     function attachSecretMessage(marker, secretMessage) {
         var infowindow = new google.maps.InfoWindow({
            content: secretMessage
         });

         marker.addListener('click', function() {
            infowindow.open(marker.get('map'), marker);
         });
     }
</script>

    <?php
    // echo $clsEntrega->Cancelar(Session::get('Token'));
    ?>

    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDZwCFnu2HgH2wczPbUQxVeJVHTQ13vjTo&callback=initMap">

@stop