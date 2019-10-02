<?php

namespace App;

class Google
{

	// O ideal é colocar aqui todas as integrações do google
	// Mas se tiver muitas e poderem ser agrupadas
	   // então o ideal é criar uma pasta e classes para agrupamentos de funlções

    private $Kms=0;
    private $TmpPrevisto=0;

    public function PrevisaoGoogle($latF, $lonF, $latC, $lonC) {

        /* function CalculaDistancia() {
            var urlDistancematrix = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={0}&destinations={1}&mode=driving&language=pt-BR&sensor=false";
            //Adicionar endereço de origem
            urlDistancematrix = urlDistancematrix.replace("{0}", $("#txtOrigem").val());
            //Adicionar endereço de destino
            urlDistancematrix = urlDistancematrix.replace("{1}", $("#txtDestino").val());
            $('#litResultado').html('Aguarde...');
            //Pegar o retorno do distancematrix
            $.getJSON("webservice.ashx?url=" + escape(urlDistancematrix),
                function (data) {
                    if (data.status == "OK") {
                        if (data.rows[0].elements[0].status != "OK")
                            $('#litResultado').html(data.rows[0].elements[0].status);
                        else {
                            $('#litResultado').html("<strong>Origem</strong>: " + data.origin_addresses +
                                "<br /><strong>Destino:</strong> " + data.destination_addresses +
                                "<br /><strong>Distância</strong>: " + data.rows[0].elements[0].distance.text +
                                " <br /><strong>Duração</strong>: " + data.rows[0].elements[0].duration.text
                            );
//Atualizar o mapa
                            $("#map").attr("src", "https://maps.google.com/maps?saddr=" + data.origin_addresses +"&daddr=" + data.destination_addresses + "&output=embed");
                        }
                    }
                    else
                        $('#litResultado').html('Ocorreu um erro');
                }
            ).error(function () { $('#litResultado').html('Ocorreu um erro!'); });
        } */

        $strUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".
            $latF.",".$lonF."&destinations=".$latC.",".$lonC.
            "&mode=CAR&language=pt-BR&key=AIzaSyDCCMjF6NOUUiFfDxqlnn6d4USOReRnOWY";
        $response = $this->call($strUrl);

        var_dump($response); die;

        /* $ori="&origins=".$latF.",".$lonF;
        $dest="&destinations=".$latC.",".$lonC;

        $request_url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$ori."&destinations=".$dest."&mode=driving&language=pt-BR&sensor=false";
        $request_url.="key=AIzaSyDCCMjF6NOUUiFfDxqlnn6d4USOReRnOWY"; */

        /* $request_url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&".$ori.$dest;
        $request_url.="key=AIzaSyDCCMjF6NOUUiFfDxqlnn6d4USOReRnOWY"; */

        // echo $request_url; die;

        $data = file_get_contents($request_url);
        $data = json_decode($data);

        var_dump($data); die;

        $time = 0;
        $distance = 0;
        foreach($data->rows[0]->elements as $road) {
            $time += $road->duration->value;
            $distance += $road->distance->value;
        }
        $kms=$distance/1000;

/*        $this->Kms=$kms;
        $this->TmpPrevisto=$time; */
        $this->setKms($kms);
        $this->setTmpPrevisto($time);

    }

    private function call($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        return json_decode($response);
    }

    public function setKms($Kms) {
        $this->Kms=$Kms;
    }

    public function getKms() {
        return $this->Kms;
    }

    public function setTmpPrevisto($TmpPrevisto) {
        $this->TmpPrevisto=$TmpPrevisto;
    }

    public function getTmpPrevisto() {
        return $this->TmpPrevisto;
    }

}