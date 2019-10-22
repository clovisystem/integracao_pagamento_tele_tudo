<?php

namespace App;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CepProc extends Model
{
    protected $table = 'cep';

    /* private $Lat = '';
    private $Long = '';
    private $NmCidade = '';
    private $idCidade = '';
    private $EsseCep = '';
    private $idPais = 0;
    private $debug = -1;
    private $idBai=0;
    private $Sigla = '';
    private $NomeBairro = '';
    private $cep_tmp = ''; */

    public function BuscaEnderPeloCep($cep,$M) {
        $Logra='';
        $Sigla= '';
        $Cidade = '';
        $Bairro = '';
        $qtRuas=0;

        // Verifica se tem cep, em modo completo
        $ConsCep = DB::table('cep')
            ->select('cep.id',
                'cep_cidade.estado as Sigla', 'cep_cidade.cidade as NomeCidade'
                , 'cep_bairro.bairro as NomeBairro', 'cep_bairro.id_bairro as BairroID')
            ->join('cep_bairro', 'cep_bairro.id_bairro', '=', 'cep.bairro_id')
            ->join('cep_cidade', 'cep_cidade.id_cidade', '=', 'cep_bairro.cidade_id')
            ->where('Cep', '=', $cep)
            ->first();

        // $LograOK="<input type='text' name='txLogra' id='txLogra' style='display: block'>";
        $LograOK ="<div class='form-group'><select required data-live-search='true' >";
        $LograOK.="<option 'Escolha'>Clique aqui para escolher</option>";

        if ($ConsCep!=null) {

            $Sigla= $ConsCep->Sigla;
            $Cidade = $ConsCep->NomeCidade;
            $Bairro = $ConsCep->NomeBairro;

            // Selecionar os logradouros daquele bairro
            $sql = "Select DISTINCT logra.NomeLog, tplogradouro.nometplog ";
            $sql.= "From ";
            $sql.= "( SELECT DISTINCT Logradouro_ID ";
            $sql.= "FROM endereco ";
            $sql.= "Where idBairro = ".$ConsCep->BairroID;
            $sql.= " ) as X ";
            $sql.= "Inner Join logra on logra.ID = X.Logradouro_ID ";
            $sql.= "Inner Join tplogradouro on tplogradouro.ID = logra.TpLogradouro_ID ";
            $sql.= "Order by tplogradouro.nometplog, logra.NomeLog ";
            $qry_log = DB::select( DB::raw($sql));
            $Logra=$LograOK;
            foreach ($qry_log as $log) {
                $lugar = $log->nometplog." ".$log->NomeLog;
                $Logra.="<option data-tokens='$lugar'>$lugar</option>";
                $qtRuas++;
            }
            /* if ($qtRuas==0) {

            } */
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
                $ConsLog= DB::table('logra')
                    ->select('NomeLog','nometplog')
                    ->join('tplogradouro', 'tplogradouro.ID', '=', 'logra.TpLogradouro_ID')
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

}