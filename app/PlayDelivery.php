<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PlayDelivery extends Model
{
    private $AMB = "";
    private $VlrEntrega=0;
    private $status="";
    private $idEntrega=0;
    private $delivery_id=0; 

    // OPERAÇÕES PRINCIPAIS

    public function Login() {
        $headers = ['Accept: application/json', 'Content-Type: application/json'];
        $dados = ['user' => ['email' => "xeviousbr@gmail.com", 'password' => "ufrs3753"], 'no_redirection' => '1', ];
        $results = $this->Comunica("POST", json_encode($dados), "login", $headers);
        $deco = json_decode($results);
        $this->status = $deco->{'status'};
        $Token = $deco->{'authentication_token'};
        return $Token;
    }

    public function SolicitaEntrega($idPedido, $Token, $qry1, $ConsF, $ConsP) {
        $i = 0;
        foreach ($qry1 as $forns) {
            if ($i == 0) {
                $lat_0 = $ConsF->lat;
                $long_0 = $ConsF->lon;
                $title_0 = $ConsF->Empresa;
                $street_0 = $ConsF->nometplog.' '.$ConsF->NomeLog;
                $number_0 = $ConsF->Numero;
                $compl_0 = $ConsF->Complemento;
                $phone_0 = $ConsF->Telefone;
                $task_0 = "Coletar";
                $district_0 = $ConsF->NomeBairro;
                $city_0 = $ConsF->NomeCidade;
                $state_0 = $ConsF->NomeEstado;
                $country_0 = $ConsF->NomePais;
            } else {
                $lat_1 = $ConsF->lat;
                $long_1 = $ConsF->lon;
                $title_1 = $ConsF->Empresa;
                $street_1 = $ConsF->nometplog.' '.$ConsF->NomeLog;
                $number_1 = $ConsF->Numero;
                $compl_1 = $ConsF->Complemento;
                $phone_1 = $ConsF->Telefone;
                $task_1 = "Coletar";
                $district_1 = $ConsF->NomeBairro;
                $city_1 = $ConsF->NomeCidade;
                $state_1 = $ConsF->NomeEstado;
                $country_1 = $ConsF->NomePais;
            }
            $i++;
            // $idCliente = $forns->User;
        }

        // Dados do Comprador 2
        // $ConsP = $this->DadosCliente($idCliente);

        if ($i == 1) {
            $lat_1 = $ConsP->lat;
            $long_1 = $ConsP->lon;
            $title_1 = $ConsP->Nome;
            $street_1 = $ConsP->nometplog.' '.$ConsP->NomeLog;
            $number_1 = $ConsP->Numero;
            $compl_1 = $ConsP->Complemento;
            $district_1 = $ConsP->NomeBairro;
            $city_1 = $ConsP->NomeCidade;
            $state_1 = $ConsF->NomeEstado;
            $country_1 = $ConsP->NomePais;
            $phone_1 = $ConsP->fone;
            $task_1 = "Entregar";
        } else {
            $lat_2 = $ConsP->lat;
            $long_2 = $ConsP->lon;
            $title_2 = $ConsP->Nome;
            $street_2 = $ConsP->nometplog.' '.$ConsP->NomeLog;
            $number_2 = $ConsP->Numero;
            $district_2 = $ConsP->NomeBairro;
            $city_2 = $ConsP->NomeCidade;
            $compl_2 = $ConsP->Complemento;
            $state_2 = $ConsF->NomeEstado;
            $country_2 = $ConsP->NomePais;
            $phone_2 = $ConsP->fone;
            $task_2 = "Entregar";
        }
        $dados ="delivery_id=".$this->getdelivery_id();

        $dados.="&qty_points=".($i + 1);
        $dados.="&lat_0=".$lat_0;
        $dados.="&long_0=".$long_0;
        $dados.="&title_0=".$title_0;
        $dados.="&street_0=".$street_0;
        $dados.="&number_0=".$number_0;
        $dados.="&compl_0=".$compl_0;
        $dados.="&district_0=".$district_0;
        $dados.="&phone_0=".$phone_0;
        $dados.="&task_0=".$task_0;
        $dados.="&city_0=".$city_0;
        $dados.="&state_0=".$state_0;
        $dados.="&country_0=".$country_0;

        $dados.="&lat_1=".$lat_1;
        $dados.="&long_1=".$long_1;
        $dados.="&title_1=".$title_1;
        $dados.="&street_1=".$street_1;
        $dados.="&number_1=".$number_1;
        $dados.="&compl_1=".$compl_1;
        $dados.="&district_1=".$district_1;
        $dados.="&phone_1=".$phone_1;
        $dados.="&city_1=".$city_1;
        $dados.="&task_1=".$task_1;
        $dados.="&state_1=".$state_1;
        $dados.="&country_1=".$country_1;
        if ($i == 2) {
            $dados.="&lat_2=".$lat_2;
            $dados.="&long_2=".$long_2;
            $dados.="&title_2=".$title_2;
            $dados.="&street_2=".$street_2;
            $dados.="&number_2=".$number_2;
            $dados.="&compl_2=".$compl_2;
            $dados.="&district_2=".$district_2;
            $dados.="&phone_2=".$phone_2;
            $dados.="&city_2=".$city_2;
            $dados.="&task_2=".$task_2;
            $dados.="&state_2=".$state_2;
            $dados.="&country_2=".$country_2;
        }
        $dados.="&back_to_point=0";
        $dados.="&delivery_size=1";
        $dados.="&version=3.1.4";
        $dados.="&op_system=4";

        // Campos que devem ser passados, mas estão fixos por testes
        $dados.="&delivery_size=1";     // 1=Documentos. 2=Pacote Médio, 3=Pacote Grande
        $dados.="&payment_method=5";    // 1=Dinheiro, 2=Cartão de Crédito, 5=Pós-Pago
        $dados.="&payment_point=1";     // Sem descrição ou opções
        $dados.="&priority=1";          // Sem descrição ou opções

        $headers = $this->MontaHeader($Token);

        $cSes = new Sessao();
        $lcModo=$cSes->Modo();
        if ($lcModo=="Teste - Simulado") {
            $resultado = "success";
        } else {

            // echo 'Bah meu foi quase!'; die;

            $results= $this->Comunica("GET", $dados, "/api/requestor/create_delivery", $headers);
            $deco = json_decode($results);
            $resultado = $deco->{'status'};
        }

        /* if ($this->getModo()==1) {
            $resultado = "success";
        } else {
            $results= $this->Comunica("GET", $dados, "/api/requestor/create_delivery", $headers);
            $deco = json_decode($results);
            $resultado = $deco->{'status'};
        } */

        if ($resultado== "success") {
            DB::update("update pedido set status = 1 where idPed = " .$idPedido);
            return true;
        } else {
            return false;
        }
    }

    public function Logof() {
        $headers = ['Accept: application/json', 'Content-Type: application/json'];
        $dados = ['X-User-Email' => "xeviousbr@gmail.com", 'X-User-Token' => $this->getToken()];

        $this->Comunica("DELETE", json_encode($dados), "logout", $headers);
    }

    public function Cancelar($Token) {
        $headers = $this->MontaHeader($Token);
        $dados="&delivery_id=".$this->getdelivery_id();
        $dados.="&requestor_delivery_cancel_reason_type_id=4";
        $dados.="&requestor_delivery_cancel_reason_input=tele-tudo";
        $results= $this->Comunica("GET", $dados, "/api/requestor/cancel_delivery.json", $headers);
        return $results;
    }

    // OBTENÇÃO DE DADOS

    public function OrcamentoPlay($qry1, $ConsF, $ConsP, $Teste) {
        $ret=0;
        $i = 0;
        foreach ($qry1 as $forns) {
            if ($i == 0) {
                $lat_0 = $ConsF->lat;
                $long_0 = $ConsF->lon;
                $title_0 = $ConsF->Empresa;
                $street_0 = $ConsF->nometplog.' '.$ConsF->NomeLog;
                $number_0 = $ConsF->Numero;
                $compl_0 = $ConsF->Complemento;
                $phone_0 = $ConsF->Telefone;
                $task_0 = "Coletar";
                $district_0 = $ConsF->NomeBairro;
                $city_0 = $ConsF->NomeCidade;
                $state_0 = $ConsF->NomeEstado;
                $country_0 = $ConsF->NomePais;
            } else {
                $lat_1 = $ConsF->lat;
                $long_1 = $ConsF->lon;
                $title_1 = $ConsF->Empresa;
                $street_1 = $ConsF->nometplog.' '.$ConsF->NomeLog;
                $number_1 = $ConsF->Numero;
                $compl_1 = $ConsF->Complemento;
                $phone_1 = $ConsF->Telefone;
                $task_1 = "Coletar";
                $district_1 = $ConsF->NomeBairro;
                $city_1 = $ConsF->NomeCidade;
                $state_1 = $ConsF->NomeEstado;
                $country_1 = $ConsF->NomePais;
            }
            $i++;
            if ($i>2) {
                $i==2;
                break;
            }
            $idCliente = $forns->User;
        }

        // Dados do Comprador 1
        // $ConsP = $this->DadosCliente($idCliente);
        if ($i == 1) {
            $lat_1 = $ConsP->lat;
            $long_1 = $ConsP->lon;
            $title_1 = $ConsP->Nome;
            $street_1 = $ConsP->nometplog.' '.$ConsP->NomeLog;
            $number_1 = $ConsP->Numero;
            $compl_1 = $ConsP->Complemento;
            $district_1 = $ConsP->NomeBairro;
            $city_1 = $ConsP->NomeCidade;
            $state_1 = $ConsF->NomeEstado;
            $country_1 = $ConsP->NomePais;
            $phone_1 = $ConsP->fone;
            $task_1 = "Entregar";
        } else {
            $lat_2 = $ConsP->lat;
            $long_2 = $ConsP->lon;
            $title_2 = $ConsP->Nome;
            $street_2 = $ConsP->nometplog.' '.$ConsP->NomeLog;
            $number_2 = $ConsP->Numero;
            $district_2 = $ConsP->NomeBairro;
            $city_2 = $ConsP->NomeCidade;
            $compl_2 = $ConsP->Complemento;
            $state_2 = $ConsF->NomeEstado;
            $country_2 = $ConsP->NomePais;
            $phone_2 = $ConsP->fone;
            $task_2 = "Entregar";
        }
        $dados ="delivery_id=-1";
        $dados.="&qty_points=".($i + 1);
        $dados.="&lat_0=".$lat_0;
        $dados.="&long_0=".$long_0;
        $dados.="&title_0=".$title_0;
        $dados.="&street_0=".$street_0;
        $dados.="&number_0=".$number_0;
        $dados.="&compl_0=".$compl_0;
        $dados.="&district_0=".$district_0;
        $dados.="&phone_0=".$phone_0;
        $dados.="&task_0=".$task_0;
        $dados.="&city_0=".$city_0;
        $dados.="&state_0=".$state_0;
        $dados.="&country_0=".$country_0;

        $dados.="&lat_1=".$lat_1;
        $dados.="&long_1=".$long_1;
        $dados.="&title_1=".$title_1;
        $dados.="&street_1=".$street_1;
        $dados.="&number_1=".$number_1;
        $dados.="&compl_1=".$compl_1;
        $dados.="&district_1=".$district_1;
        $dados.="&phone_1=".$phone_1;
        $dados.="&city_1=".$city_1;
        $dados.="&task_1=".$task_1;
        $dados.="&state_1=".$state_1;
        $dados.="&country_1=".$country_1;
        if ($i == 2) {
            $dados.="&lat_2=".$lat_2;
            $dados.="&long_2=".$long_2;
            $dados.="&title_2=".$title_2;
            $dados.="&street_2=".$street_2;
            $dados.="&number_2=".$number_2;
            $dados.="&compl_2=".$compl_2;
            $dados.="&district_2=".$district_2;
            $dados.="&phone_2=".$phone_2;
            $dados.="&city_2=".$city_2;
            $dados.="&task_2=".$task_2;
            $dados.="&state_2=".$state_2;
            $dados.="&country_2=".$country_2;
        }
        $dados.="&back_to_point=0";
        $dados.="&delivery_size=1";
        $dados.="&version=3.1.4";
        $dados.="&op_system=4";

        $headers = ['Accept: application/json', 'Cache-Control: no-cache'];

        $cSes = new Sessao();
        $lcModo=$cSes->Modo();
        if ($Teste==1) {
            if ($lcModo=="Produção") {
                // SE FOR PRODUÇÃO, ENTÃO PASSA PRA MODO TESTE SIMULADO
                $lcModo="Produção";
                $ret=1;
            }
        }

        if ($lcModo=="Teste - Simulado") {
            $resultado = "success";
            $delivery_id = 2774;
            $this->setVlrEntrega(18.0, "OrcamentoPlay:teste");
            $ret=1;
        } else {
            $results= $this->Comunica("GET", $dados, "/api/requestor/delivery_budget", $headers);
            $deco = json_decode($results);
            $resultado = $deco->{'status'};
            $delivery_id = $deco->{'delivery_id'};
            $this->setVlrEntrega($deco->{'price'}, "OrcamentoPlay:Prod");
            $ret=1;
        }

        /* {
            "status": "success",
            "delivery_id": 2627,
            "first_point_city": "Porto Alegre",
            "distance": 1227,
            "price": "18.0",
            "priority_price": "9.0",
            "exclusive_price": "9.0",
            "playdelivery_price": "18.0",
            "signature_price_per_point": "1.0",
            "flag_number": 3
        } */

        $this->setdelivery_id($delivery_id);

        if ($resultado=="success") {
            /*            $valor = $this->getVlrEntrega()+$this->getVlrNosso();
                        DB::update("update entrega set Valor = ".$this->getVlrEntrega()." where id = " .$this->getidEntrega());
                        echo 'Valor relativo a Tele-Entrega = '.number_format($valor, 2, ',', '.');*/
            return $ret;
        } else {
            echo 'Não foi possível obter cotação da tele-entrega';
            return false;
        }
    }

    public function Motorista($Token, $vez) {
        $headers = $this->MontaHeader($Token);
        $dados="request_id=".$vez."&delivery_id=2769";
        $results= $this->Comunica("GET", $dados, "/api/requestor/wait_for_deliveryboy", $headers);
        return $results;
    }

    public function OndeEleTa($Token, $vez) {
        $headers = $this->MontaHeader($Token);
        $dados = "request_id=".$vez;
        $dados.="&delivery_id=2769";
        $results= $this->Comunica("GET", $dados, "/api/requestor/follow_delivery", $headers);
        return $results;
    }

    // ACESSÓRIAS DE COMUNICAÇÃO

    private function setdelivery_id($delivery_id) {
        $this->delivery_id = $delivery_id;
    }

    private function setVlrEntrega($VlrEntrega, $lugar) {
        $this->VlrEntrega=$VlrEntrega;
        DB::update("update entrega set Valor = ".$VlrEntrega." where id = " .$this->idEntrega);
    }

    public function getVlrEntrega() {
        return $this->VlrEntrega;
    }

    public function getstatus() {
        return $this->status;
    }

    // BAIXO NIVEL

    private function MontaHeader($Token) {
        $headers = [
            'Accept: application/json',
            'Cache-Control: no-cache',
            'X-User-Email: xeviousbr@gmail.com',
            'X-User-Token: '.$Token
        ];
        return $headers;
    }

    private function Comunica($Operacao, $Dados, $Pag, $headers) {
        if ($this->AMB=="D") {
            $url="https://devplaydelivery.herokuapp.com/";
        } else {
            $url="https://playdelivery.herokuapp.com/";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $url.$Pag.".json");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $Operacao);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $Dados);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $results = curl_exec($ch);
        curl_close($ch);

        $head = json_encode($headers);
        DB::insert('insert into Log (Caminho, Header, Tipo, Envio, Rec) values (?, ?, ?, ?, ?)', [$Pag, $head, $Operacao, $Dados, $results]);
        return $results;
    }

}