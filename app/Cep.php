<?php

namespace App;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Cep extends Model
{
    protected $table = 'cep';
    private $Lat = '';
    private $Long = '';
    private $NmCidade = '';
    private $idCidade = '';
    private $EsseCep = '';
    private $idPais = 0;
    private $debug = -1;
    private $idBai=0;
    private $Sigla = '';
    private $NomeBairro = '';
    private $cep_tmp = '';

    private function PeloGoogle($cep) {
        $cep_tmp = trim($cep);
        if (is_numeric($cep_tmp) == 0) {
            // echo 'Eh numerico'; die;
            $cep_tmp = str_replace(' ', '%20', $cep_tmp);
        }

        $nmPais="Brasil";
        $Siglapais = "BR";
        if ($this->idPais>1) {
            $nmPais = Session::get('nmPais');
            $Siglapais = strtoupper(Session::get('SiglaPais'));
        }
        $address = "'".$cep_tmp."'".",".$nmPais;

        // https://maps.googleapis.com/maps/api/directions/"+output+"?"+parameters + "&key=" + MY_API_KEY
        $request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address=" . $address . "&sensor=true" . "&key=AIzaSyDCCMjF6NOUUiFfDxqlnn6d4USOReRnOWY";

        $xml = simplexml_load_file($request_url) or die("url not loading"); // request do XML */

        $status = $xml->status; // pega o status do request, j� qe a API da google pode retornar v�rios tipos de respostas

        // REQUEST_DENIED

        if ($status == "OK") {

            $city = '';
            $this->setlat($xml->result->geometry->location->lat);
            $this->setLong($xml->result->geometry->location->lng);

            $tracks = $xml->result;
            $temp = '';
            $ok=false;
            foreach ($tracks as $key) {
                foreach ($key->address_component as $val) {
                    $this->loga("val->type = ".$val->type);
                    if ($val->type == "political") {
                        $this->SetBairro($val->long_name);

                    }
                    if ($val->type == "administrative_area_level_2") {
                        $this->NmCidade = $val->long_name;
                    }
                    if ($val->type == "administrative_area_level_1") {
                        $this->Sigla = $val->short_name;
                    }
                    if ($val->type == "country") {
                        $essePais=$val->short_name;
                        if ($Siglapais == $essePais) {
                            $ok=true;
                        }
                    }
                    if ($ok==true) {
                        $temp = $val->long_name;
                        if ($val->type == "locality") {
                            $city = $temp;
                            if ($this->debug==1) {echo 'city = '.$temp.'</Br>';}
                        }

                        /* if (address_component.types[0] == "country"){
                            console.log("País: " + address_component.long_name);
                        } */

                        if ($val->type == "country") {
                            $this->loga('country = '.$temp);
                            $this->loga('short_name = '.$val->short_name);
                        }
                    }
                }
            }
            if ($city > '') {

                $this->NmCidade = $city;
                // $this->idCidade = $city;

            }
        }
        if ($status == "ZERO_RESULTS") {
            //indica que o geocode funcionou mas nao retornou resutados.
            $status = Lang::get('cep.numdeu');
            // $status = "N�o Foi poss�vel encontrar o local";
        }
        if ($status == "OVER_QUERY_LIMIT") {
            //indica que sua cota di�ria de requests excedeu
            $status = Lang::get('cep.demais');
            // $status = "A cota do GoogleMaps excedeu o limite di�rio";
        }
        if ($status == "REQUEST_DENIED") {
            //indica que seu request foi negado, geralmente por falta de um 'parametro de sensor?'
            $status = Lang::get('cep.negado');
            // $status = "Acesso Negado";
        }
        if ($status == "INVALID_REQUEST") {
            // geralmente indica que a query (address or latlng) est� faltando.
            $status = Lang::get('cep.invalido');
            // $status = "Endere�o n�o est� preenchido corretamente";
        }
        if (($this->getLat() == - 25.2912987) && ($this->getLong() == - 57.6265412)) {
            $status = Lang::get('cep.erro');
            // $status = "CEP inv�liod ou Erro na API do Google";
        } else return $status;
    }

    public function GetCoordenadas($cep, $ultcep, $idPais, $cepSes, $idUser, $tipo) {
        $status = '';
        $cep_tmp = str_replace('.', '', $cep);
        $cep_tmp = str_replace('-', '', $cep_tmp);
        $cep_tmp = str_replace(';', '', $cep_tmp);
        $cep_tmp = str_replace(' ', '', $cep_tmp);
        $this->EsseCep = $cep_tmp;
        if ($ultcep>'') {
            $this->loga('ULTCEP na Session');
            if ($cep_tmp == $ultcep) {
                $this->loga('ULTCEP = '.$cep_tmp);
                if (Session::has('LAT')) {
                    $this->setlat(Session::get('LAT'));
                    $this->setLong(Session::get('LONG'));
                    $status = 'OK';
                    $this->loga("status = 'OK'");
                }
            }
        }
        $this->idPais = $idPais;
        if ($status == '') {
            if (is_numeric($cep_tmp)==0) {
                // NÃO NUMÉRICO
                /* $status = $this->PeloGoogle($cep_tmp);
                $this->setCidade(); */
                $status = "NÃO OK[1]";

            } else {
                // NUMÉRICO

                // Session::forget('CID');
                $Conscep = DB::table('cep')
                    ->select('lat', 'lon', 'idBairro', 'idCidade')
                    ->where('NrCep', '=', $cep_tmp)

                    ->where('idPais', '=', 1)
                    // ->where('idPais', '=', $this->idPais)

                    ->first();
                $ok=0;
                if (is_null($Conscep)) {
                    $this->loga("NÃO ACHOU O CEP NA BASE DE DADOS[1]");
                    // echo "NÃO ACHOU O CEP NA BASE DE DADOS"; die;
                    $status = $this->Comunica($cep_tmp);
                    if ($status == "OK") {
                        // echo 'saiu da comunicação com OK'; die;
                        $cidade = $this->NmCidade;
                        $this->loga("InsereCep(".$cep_tmp." - ".$cidade.")");
                        $this->InsereCep($tipo,$cep_tmp, $cepSes);
                        $ok=1;
                        if ($tipo!='prod') {
                            Session::put('message','Não foi possível localizar informações sobre seu CEP');
                        }
                    }
                    // echo 'Não passou como OK';die;
                } else {
                    // ACHOU NA BASE DE DADPS
                    $this->loga("ACHOU O CEP NA BASE DE DADOS[2]");
                    $status = 'OK';
                    $ok=1;
                    $this->setlat($Conscep->lat);
                    $this->setLong($Conscep->lon);
                    $this->Incrementa($cep_tmp, $cepSes, $tipo, $idUser);
                    $this->setLocal($Conscep->idBairro, $Conscep->idCidade);
                    if ($Conscep->idCidade==null) {
                        $ConsBairro = DB::table('cep')
                            ->select('bairro.idcidade')
                            ->join('bairro', 'bairro.ID', '=', 'cep.idBairro')
                            ->where('NrCep', '=', $cep_tmp)
                            ->where('idPais', '=', $this->idPais)
                            ->first();

                        if ($ConsBairro!=null) {
                            $this->idcidade = $ConsBairro->idcidade;
                            // Session::put('CID', $ConsBairro->idcidade);
                        }
                    } else {
                        $this->idcidade = $Conscep->idCidade;
                        // Session::put('CID', $Conscep->idCidade);
                    }
                }
            }
            $this->cep_tmp = $cep_tmp;
        }
        return $status;
    }

    public function getIdCidade() {
        return $this->idcidade;
    }

    public function getcep_tmp() {
        return $this->cep_tmp;
    }

    private function Comunica($cep_tmp) {
        // $cep_tmp = "91793350";
        $url="https://www.google.com.br/maps/place/".$cep_tmp;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $results = curl_exec($ch);
        // var_dump($results); die;
        $pos = strpos($results, "staticmap?center");
        $Lat = substr($results, $pos+17, 11);
        $LatF = str_replace('.', ',', $Lat);
        curl_close($ch);

        // echo "LatF = ".$LatF; die;
        $posLat = strpos($LatF, "2C");

        if ($posLat==0) {
            // if (is_numeric($LatF)==0) {

            $this->Lat = substr($results, $pos+17, 11);
            $this->Long = substr($results, $pos+31, 11);
            $CoRet = $this->Lat.",".$this->Long;
            // $cO = "-30.1473547,-51.1514435";
            // "0x95199b6c35186d5f:0x3e45a9f4bd5f5cb","Restinga, Porto Alegre - RS, 91793-350",null,[null,null,-30.1473547,-51.1514435]
            $pocCo = strpos($results, $CoRet);
            $pedacoBairro = substr($results, $pocCo-100, $pocCo);
            $posPlace = strpos($pedacoBairro, "/place/");
            $IniBairro = $posPlace+7;
            $posVirgulaBairro= strpos($pedacoBairro, ",");
            $Bairro = substr($pedacoBairro, $IniBairro, $posVirgulaBairro+4);
            $TamBairro = strlen($Bairro);
            $pedacoCidade = substr($pedacoBairro, $IniBairro+$TamBairro+1, 50);
            $posVirgulaCidade = strpos($pedacoCidade, ",");
            $Cidade1 = substr($pedacoCidade, 1, $posVirgulaCidade);
            $TamCid1 = strlen($Cidade1);
            $Cidade2 = substr($Cidade1, 0, $TamCid1-6);
            $Cidade = str_replace('+', ' ', $Cidade2);
            $this->NmCidade = $Cidade;
            return "OK";
        } else {
            return "NÃO OK";
        }
    }

    private function PeloViaCep($cep) {
        $ret='';
        $request_url = "https://viacep.com.br/ws/".$cep."/xml";
        $xml = simplexml_load_file($request_url); // request do XML

        echo $xml; die;

        if ($xml->cep>'') {
            $this->NmCidade = $xml->localidade;
            $this->SetBairro($xml->bairro);
            $ret = "OK";
        }
        return $ret;
    }

    private function setLocal($idBairro, $idCidade) {
        if ($idBairro != null) {
            $ConsLocal = DB::table('bairro')
                ->join('cidade', 'cidade.ID', '=', 'bairro.idcidade')
                ->select('bairro.NomeBairro', 'cidade.NomeCidade', 'cidade.ID')
                ->where('bairro.id', '=', $idBairro)
                ->first();
            if ($ConsLocal!=null) {
                $ConsLocal = 'Localidade: '.$this->acento_para_html($ConsLocal->NomeBairro).' / '.$this->acento_para_html($ConsLocal->NomeCidade);
            }
            $this->idBai = $idBairro;
            if ($idCidade != '') {
                $this->idCidade = $idCidade;
            }
        } else {
            $this->idCidade = $idCidade;
            if ($idCidade != null) {
                $ConsLocal = DB::table('cidade')
                    ->select('NomeCidade')
                    ->where('ID', '=', $idCidade)
                    ->first();
                if ($ConsLocal != null) {
                    $ConsLocal = 'Localidade: Cidade de '.$this->acento_para_html($ConsLocal->NomeCidade);
                }
            } else {
                $ConsLocal = '';
            }
        }
        $this->NmCidade = $ConsLocal;
    }

    public function getLocal() {
        return $this->NmCidade;
    }

    private function Incrementa($cep_tmp, $tipo, $cepSes, $idUser) {
        $fazer = true;
        if (Auth::check()) {
            if ($idUser == 1) {
                $fazer = false;
            }
        }
        if ($cepSes>'') {
            $fazer = false;
        }
        if ($fazer == true) {
            DB::update('update cep set ' . $tipo . ' = ' . $tipo . ' + 1, data = NOW() where NrCep = "' . $cep_tmp . '" and idPais = '.$this->idPais);
        }
    }

    public function getLat() {
        return $this->Lat;
    }

    public function getLong() {
        return $this->Long;
    }

    public function setlat($Lat) {
        $this->Lat = $Lat;
        // Session::put('LAT', $Lat);
    }

    public function setLong($Long) {
        $this->Long = $Long;
        // Session::put('LONG', $Long);
    }

    private function setCidade() {
        if ($this->NmCidade >'') {
            $ConsCid = DB::table('cidade')
                ->select('ID')
                ->where('NomeCidade', '=', $this->NmCidade)
                ->first();
            if ($ConsCid != null) {
                Session::put('CID', $ConsCid->ID);
                $this->idCidade=$ConsCid->ID;
                $id = DB::table('cep')->max('id');
                DB::update('update cep set idCidade = '.$this->idCidade.' where id = '.$id);
                $ConsB = DB::table('bairro')
                    ->select('id')
                    ->where('NomeBairro', '=', $this->NomeBairro)
                    ->where('idcidade', '=', $id)
                    ->first();
                if ($ConsB==null) {
                    if ($this->NomeBairro>'') {
                        $this->idBai= $this->GravaBairro($this->NomeBairro,$this->idCidade);
                        DB::update('update cep set idBairro = '.$this->idBai.' where id = '.$id);
                    }
                } else {
                    DB::update('update cep set idCidade = '.$ConsB->id.' where id = '.$id);
                }
            } else {
                Session::put('CID', '0');
                $this->idCidade='';
            }
        } else {
            // Session::forget('CID');
            $this->idCidade='';
        }
    }

    public function GetCidadePelaLoc($lat, $long) {
        $localidade = '';
        $NomeCidade = '';

        $sql = 'select id, distancia, idCidade, idBairro ';
        $sql.='from ( ';
        $sql.='SELECT id, fn_distance ('.$lat.', '.$long.', cep.lat, cep.lon) distancia, idCidade, idBairro ';
        $sql.='from cep ) X ';
        $sql.='where idCidade is not null or idBairro is not null ';
        $sql.='order by distancia limit 1 ';
        $qry_cid = DB::select( DB::raw($sql));

        // echo $sql; die;

        $this->idBai=0;
        $idCid=0;
        foreach ($qry_cid as $cids) {
            $dist = $cids->distancia;

            if ($cids->idCidade!=null) {$idCid = $cids->idCidade; }
            if ($cids->idBairro!=null) {$this->idBai = $cids->idBairro; }
        }

        /*echo 'idCidade='.$idCid.'</p>';
        echo 'idBairro='.$this->idBai.'</p>';
        echo 'dist='.$dist;
        die; */

        if ($dist<10) {

            // echo 'dist='.$dist.'</p>';

            if ($idCid==0) {
                if ($this->idBai>0) {
                    $ConsBai = DB::table('bairro')
                        ->select('bairro.idcidade', 'cidade.NomeCidade')
                        ->join('cidade', 'cidade.ID', '=', 'bairro.idcidade')
                        ->where('bairro.id', '=', $this->idBai)
                        ->first();
                    $NomeCidade = $ConsBai->NomeCidade;
                    $idCid = $ConsBai->idcidade;
                }
            } else {
                $ConsCid = DB::table('cidade')
                    ->select('NomeCidade')
                    ->where('id', '=', $idCid)
                    ->first();
                $NomeCidade = $ConsCid->NomeCidade;
            }
            if ($NomeCidade > '') {
                if (is_numeric($this->EsseCep)) {
                    $localidade = 'Localidade: ' . $this->acento_para_html($NomeCidade);
                }
                $this->NmCidade = $localidade;
                Session::put('CID', $idCid);
            }

            /*echo 'NomeCidade='.$NomeCidade.'</p>';
            echo 'localidade='.$localidade.'</p>';*/

        }
        return $localidade;
    }

    public function getBairro() {
        return $this->idBai;
    }

    private function InsereCep($tipo,$cep_tmp) {
        if (strlen($cep_tmp)==8) {
            DB::insert('insert into cep (NrCep, lat, lon, ' . $tipo . ', data, idPais) values (?, ?, ?, ?, ?, ?)', [$cep_tmp, $this->getLat(), $this->getLong(), 1, new DateTime, $this->getPais()]);
        }
        $this->setCidade();
    }

    private function getPais() {
        if ($this->idPais==null) {
            return 1;
        } else {
            return $this->idPais;
        }
    }

    private function acento_para_html($umarray){
        $comacento = array('Á','á','Â','â','À','à','Ã','ã','É','é','Ê','ê','È','è','Ó','ó','Ô','ô','Ò','ò','Õ','õ','Í','í','Î','î','Ì','ì','Ú','ú','Û','û','Ù','ù','Ç','ç',);
        $acentohtml   = array('&Aacute;','&aacute;','&Acirc;','&acirc;','&Agrave;','&agrave;','&Atilde;','&atilde;','&Eacute;','&eacute;','&Ecirc;','&ecirc;','&Egrave;','&egrave;','&Oacute;','&oacute;','&Ocirc;','&ocirc;','&Ograve;','&ograve;','&Otilde;','&otilde;','&Iacute;','&iacute;','&Icirc;','&icirc;','&Igrave;','&igrave;','&Uacute;','&uacute;','&Ucirc;','&ucirc;','&Ugrave;','&ugrave;','&Ccedil;','&ccedil;');
        $umarray  = str_replace($comacento, $acentohtml, $umarray);
        return $umarray;
    }

    /*
    public function BuscaEnderPeloCep1($cep) {
        $Logra='';
        $Sigla= '';
        $Cidade = '';
        $Bairro = '';
        $qtRuas=0;

        // Verifica se tem cep, em modo completo
        $ConsCep = DB::table('cep')
            ->select('cep.id','Sigla', 'NomeCidade','NomeBairro', 'bairro.ID as BairroID')
            ->join('bairro', 'bairro.ID', '=', 'cep.idBairro')
            ->join('cidade', 'cidade.ID', '=', 'bairro.idcidade')
            ->join('estado', 'estado.ID', '=', 'cidade.Estado_ID')
            ->where('NrCep', '=', $cep)
            ->first();

        // $LograOK="<input type='text' name='txLogra' id='txLogra' style='display: block'>";
        $LograOK ="<div class='form-group'><select required data-live-search='true' >";
        $LograOK.="<option 'Escolha'>Clique aqui para escolher</option>";

        if ($ConsCep!=null) {

            $Sigla= $ConsCep->Sigla;
            $Cidade = $ConsCep->NomeCidade;
            $Bairro = $ConsCep->NomeBairro;

            // Selecionar os logradouros daquele bairro
            $sql = "Select DISTINCT logradouro.NomeLog, tplogradouro.nometplog ";
            $sql.= "From ";
            $sql.= "( SELECT DISTINCT Logradouro_ID ";
            $sql.= "FROM endereco ";
            $sql.= "Where idBairro = ".$ConsCep->BairroID;
            $sql.= " ) as X ";
            $sql.= "Inner Join logradouro on logradouro.ID = X.Logradouro_ID ";
            $sql.= "Inner Join tplogradouro on tplogradouro.ID = logradouro.TpLogradouro_ID ";
            $sql.= "Order by logradouro.NomeLog ";
            $qry_log = DB::select( DB::raw($sql));
            $Logra=$LograOK;
            foreach ($qry_log as $log) {
                $lugar = $log->nometplog." ".$log->NomeLog;
                $Logra.="<option data-tokens='$lugar'>$lugar</option>";
                $qtRuas++;
            }
            $Logra.="<option 'NÃO ESTA NA LISTA - IREI INFORMAR'>NÃO ESTA NA LISTA - IREI INFORMAR</option>";
            $Logra.="</select></div>";
            $BaiRO=" readonly='readonly' ";
            $OK=1;
        } else {

            // Verifica se a cep só pela cidade
            $ConsCep = DB::table('cep')
                ->select('cep.id','Sigla', 'NomeCidade','cidade.ID as CidadeID','Sigla')
                ->join('cidade', 'cidade.ID', '=', 'cep.idCidade')
                ->join('estado', 'estado.ID', '=', 'cidade.Estado_ID')
                ->where('NrCep', '=', $cep)
                ->first();

            if ($ConsCep!=null) {

                // Seleciona logradouros da cidade
                $ConsLog= DB::table('logradouro')
                    ->select('NomeLog','nometplog')
                    ->join('tplogradouro', 'tplogradouro.ID', '=', 'logradouro.TpLogradouro_ID')
                    ->where('Cidade_ID', '=', $ConsCep->CidadeID)
                    ->get();

                $Logra=$LograOK;
                foreach ($ConsLog as $log) {
                    $lugar = $log->nometplog." ".$log->NomeLog;
                    $Logra.="<option data-tokens='$lugar'>$lugar</option>";
                    $qtRuas++;
                }
                $Logra.="<option 'NÃO ESTA NA LISTA - IREI INFORMAR'>NÃO ESTA NA LISTA - IREI INFORMAR</option>";
                $Logra.="</select></div>";
                $Sigla= $ConsCep->Sigla;
                $Cidade = $ConsCep->NomeCidade;
                $Bairro = "";
                $BaiRO='';
                $OK=2;
            } else {
                if ($this->PeloGoogle($cep)=="OK") {
                    $Sigla= $this->Sigla;
                    $Cidade = $this->NmCidade;
                    $Bairro = $this->NomeBairro;
                    $OK=3;
                    $idCidade = $this->GravaCidade($Cidade, $Sigla);
                    if ($Bairro>'') {
                        $idBairro = $this->GravaBairro($Bairro, $idCidade);
                        $BaiRO=" readonly='readonly' ";
                    } else {
                        $idBairro = 'null';
                        $BaiRO='';
                    }

                    // CEP (pode ser que já tenha)
                    $Cons = DB::table('cep')
                        ->select('id')
                        ->where('NrCep', '=', $cep)
                        ->first();
                    if ($Cons==null) {
                        DB::insert("insert into cep (NrCep, lat, lon, data, idPais, idBairro, idCidade) values (?, ?, ?, ?, ?, ?, ?)", [$cep, $this->getLat(), $this->getLong(), new DateTime, 1, $idBairro, $idCidade]);
                    } else {
                        DB::update("update cep set idBairro = ".$idBairro.", idCidade = ".$idCidade." Where id = ".$Cons->id);
                    }
                } else {
                    $OK=0;
                }
            }
        }

        if ($BaiRO=='') { $tpEnder="C"; } else { $tpEnder="R"; }

        $JS = ['OK' => $OK,
            'Estado' => $Sigla,
            'Cidade' => $Cidade,
            'Bairro' => $Bairro,
            'Logra' => $Logra,
            'tpEnder' => $tpEnder,
            'qtRuas' => $qtRuas
        ];
        $results = json_encode($JS);
        return $results;
    }
    */
    public function BuscaEnderPeloCep($cep,$M) {
        $Logra='';
        $Sigla= '';
        $Cidade = '';
        $Bairro = '';
        $qtRuas=0;

        // Verifica se tem cep, em modo completo
        $ConsCep = DB::table('cep')
            ->select('cep.id','Sigla', 'NomeCidade','NomeBairro', 'bairro.ID as BairroID')
            ->join('bairro', 'bairro.ID', '=', 'cep.idBairro')
            ->join('cidade', 'cidade.ID', '=', 'bairro.idcidade')
            ->join('estado', 'estado.ID', '=', 'cidade.Estado_ID')
            ->where('NrCep', '=', $cep)
            ->first();

        // $LograOK="<input type='text' name='txLogra' id='txLogra' style='display: block'>";
        $LograOK ="<div class='form-group'><select required data-live-search='true' >";
        $LograOK.="<option 'Escolha'>Clique aqui para escolher</option>";

        if ($ConsCep!=null) {

            $Sigla= $ConsCep->Sigla;
            $Cidade = $ConsCep->NomeCidade;
            $Bairro = $ConsCep->NomeBairro;

            // Selecionar os logradouros daquele bairro
            $sql = "Select DISTINCT logradouro.NomeLog, tplogradouro.nometplog ";
            $sql.= "From ";
            $sql.= "( SELECT DISTINCT Logradouro_ID ";
            $sql.= "FROM endereco ";
            $sql.= "Where idBairro = ".$ConsCep->BairroID;
            $sql.= " ) as X ";
            $sql.= "Inner Join logradouro on logradouro.ID = X.Logradouro_ID ";
            $sql.= "Inner Join tplogradouro on tplogradouro.ID = logradouro.TpLogradouro_ID ";
            $sql.= "Order by logradouro.NomeLog ";
            $qry_log = DB::select( DB::raw($sql));
            $Logra=$LograOK;
            foreach ($qry_log as $log) {
                $lugar = $log->nometplog." ".$log->NomeLog;
                $Logra.="<option data-tokens='$lugar'>$lugar</option>";
                $qtRuas++;
            }
            $Logra.="<option 'NÃO ESTA NA LISTA - IREI INFORMAR'>NÃO ESTA NA LISTA - IREI INFORMAR</option>";
            $Logra.="</select></div>";
            $BaiRO=" readonly='readonly' ";
            $OK=1;
        } else {

            // Verifica se a cep só pela cidade
            $ConsCep = DB::table('cep')
                ->select('cep.id','Sigla', 'NomeCidade','cidade.ID as CidadeID','Sigla')
                ->join('cidade', 'cidade.ID', '=', 'cep.idCidade')
                ->join('estado', 'estado.ID', '=', 'cidade.Estado_ID')
                ->where('NrCep', '=', $cep)
                ->first();

            if ($ConsCep!=null) {

                // Seleciona logradouros da cidade
                $ConsLog= DB::table('logradouro')
                    ->select('NomeLog','nometplog')
                    ->join('tplogradouro', 'tplogradouro.ID', '=', 'logradouro.TpLogradouro_ID')
                    ->where('Cidade_ID', '=', $ConsCep->CidadeID)
                    ->get();

                $Logra=$LograOK;
                foreach ($ConsLog as $log) {
                    $lugar = $log->nometplog." ".$log->NomeLog;
                    $Logra.="<option data-tokens='$lugar'>$lugar</option>";
                    $qtRuas++;
                }
                $Logra.="<option 'NÃO ESTA NA LISTA - IREI INFORMAR'>NÃO ESTA NA LISTA - IREI INFORMAR</option>";
                $Logra.="</select></div>";
                $Sigla= $ConsCep->Sigla;
                $Cidade = $ConsCep->NomeCidade;
                $Bairro = "";
                $BaiRO='';
                $OK=2;
            } else {
                $BaiRO='';
                $OK=0;

                /* if ($this->PeloGoogle($cep)=="OK") {
                    $Sigla= $this->Sigla;
                    $Cidade = $this->NmCidade;
                    $Bairro = $this->NomeBairro;
                    $OK=3;
                    $idCidade = $this->GravaCidade($Cidade, $Sigla);
                    if ($Bairro>'') {
                        $idBairro = $this->GravaBairro($Bairro, $idCidade);
                        $BaiRO=" readonly='readonly' ";
                    } else {
                        $idBairro = 'null';
                        $BaiRO='';
                    }

                    // CEP (pode ser que já tenha)
                    $Cons = DB::table('cep')
                        ->select('id')
                        ->where('NrCep', '=', $cep)
                        ->first();
                    if ($Cons==null) {
                        DB::insert("insert into cep (NrCep, lat, lon, data, idPais, idBairro, idCidade) values (?, ?, ?, ?, ?, ?, ?)", [$cep, $this->getLat(), $this->getLong(), new DateTime, 1, $idBairro, $idCidade]);
                    } else {
                        DB::update("update cep set idBairro = ".$idBairro.", idCidade = ".$idCidade." Where id = ".$Cons->id);
                    }
                } else {
                    $OK=0;
                } */

            }
        }

        $EM='';
        if ($M==1) {
            $EM='<Br>';
            // $EM='XXX';
        }

        if ($BaiRO=='') { $tpEnder="C"; } else { $tpEnder="R"; }

        $EstCidBai ="<label for='txES'>Estado</label>&nbsp;<input class='form-row' type='text' name='txES' id='Estado' value='".$Sigla."' style='width: 33px' readonly='readonly' >&nbsp;";
        $EstCidBai.="<label for='txCid'>Cidade</label>&nbsp;<input class='form-row' type='text' name='txCid' id='txCid' value='".$Cidade."' readonly='readonly' style='width: 110px' >&nbsp;".$EM;
        $EstCidBai.="<label for='Bairro'>Bairro</label>&nbsp;<input class='form-row' name='Bairro' type='text' id='Bairro' required value='".$Bairro."' ".$BaiRO.">";
        $EstCidBai.="<input name='tpEnder' type='text' id='tpEnder' value='".$tpEnder."' style='visibility: hidden; display: none'>";

        $JS = ['OK' => $OK,
            'EstCidBai' => $EstCidBai,
            'Logra' => $Logra,
            'qtRuas' => $qtRuas
        ];
        $results = json_encode($JS);
        return $results;
    }

    private function GravaCidade($Cidade, $ES) {
        $Cons = DB::table('estado')
            ->select('cidade.ID')
            ->join('cidade', 'cidade.Estado_ID', '=', 'estado.ID')
            ->where('estado.Sigla', '=', $ES)
            ->where('cidade.NomeCidade', '=', $Cidade)
            ->first();
        if ($Cons!=null) {
            $id=$Cons->ID;
            echo "idCidade = ".$id."<Br>";
        } else {
            $Cons = DB::table('estado')
                ->select('ID')
                ->where('estado.Sigla', '=', $ES)
                ->first();
            DB::insert('insert into cidade (NomeCidade, Estado_ID) values (?, ?)', [$Cidade, $Cons->ID]);
            $id = DB::table('cidade')->max('id');
            echo "Cidadegravado id = ".$id."<Br>";
        }
        return $id;
    }

    private function GravaBairro($Bairro, $idCid) {
        $sql ="select id ";
        $sql.="from bairro ";
        $sql.="where NomeBairro  = '".$Bairro;
        $sql.="' and idcidade = ".$idCid;
        $qry_log = DB::select( DB::raw($sql));
        if ($qry_log!=null) {
            foreach ($qry_log as $reg) {
                $id=$reg->id;
                break;
            }
        } else {
            $id = DB::table('bairro')->max('id')+1;
            DB::insert('insert into bairro (id, NomeBairro, idcidade) values (?, ?, ?)', [$id,$Bairro, $idCid]);
        }
        return $id;
    }

    private function SetBairro($Bairro) {
        $nr = strrpos($Bairro,"'");
        if ($nr>0) {
            $tm = strlen($Bairro);
            $c = $tm-$nr;
            $B1 = substr($Bairro, 0, $nr);
            $B2 = substr($Bairro, $nr+1, $c);
            $this->NomeBairro = $B1.$B2;
        } else {
            $this->NomeBairro = $Bairro;
        }
    }

    private function loga($texto) {
        if ($this->debug==-1) {
            $ConsConfig = DB::table('config')
                ->select('Debug')
                ->where('ID','=',1)
                ->first();
            $this->debug=$ConsConfig->Debug;
        }
        if ($this->debug==1) {
            echo $texto.'<Br>';
        }
    }

}
