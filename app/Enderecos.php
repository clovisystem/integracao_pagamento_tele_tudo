<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Enderecos extends Model
{
    protected $table = 'endereco';

    public function GetEndereco($id, $red) {

        $sql = 'select endereco.Numero, logradouro.NomeLog, tplogradouro.nometplog, cidade.NomeCidade, estado.Sigla, bairro.NomeBairro, cep.NrCep, endereco.complemento';
        $sql = $sql.' from endereco ';
        $sql = $sql.' left join cep on cep.id = endereco.idCep';
        $sql = $sql.' inner join logradouro on logradouro.ID = endereco.Logradouro_ID';
        $sql = $sql.' inner join tplogradouro on tplogradouro.ID = logradouro.TpLogradouro_ID';
        $sql = $sql.' left join bairro on bairro.id = endereco.idBairro ';
        $sql = $sql.' inner join cidade on cidade.id = logradouro.Cidade_ID';
        $sql = $sql.' inner join estado on estado.ID = cidade.Estado_ID';
        $sql = $sql.' where endereco.ID = '.$id;
        $sql = $sql.' limit 1 ';

        // if(Auth::check()) { echo $sql; die; }

        $enders = DB::select($sql);

        $ret = '';

        foreach ($enders as $ender) {
            $Numero = $ender->Numero;
            $NomeLog = $ender->NomeLog;
            $nometplog = $ender->nometplog;
            $NomeCidade = $ender->NomeCidade;
            $bairro = $ender->NomeBairro;
            $Sigla = $ender->Sigla;
            $NrCep = $ender->NrCep;

            if ($NomeLog==null) {
                if ($bairro==null) {
                    $ret = $NomeCidade.', '.$Sigla;
                } else {
                    $ret = $bairro.', '.$NomeCidade.', '.$Sigla;
                }
            } else {
                $Compl = $ender->complemento;
                if ($Compl==null) {
                    $Compl='';
                } else {
                    $Compl=' '.$Compl;
                }
                if ($bairro!=null) {
                    $bairro = $bairro;
                }

                if ($red==0) {
                    // 'Acesso D, 3753, Restinga, Porto Alegre, RS';
                    $ret = $nometplog.' '.$NomeLog.', '.$Numero.$Compl.', '.$bairro.', '.$NomeCidade.', '.$Sigla;
                    if ($NrCep!=null) {
                        $ret=$ret.'</p>Cep: '.$NrCep;
                    }
                } else {
                    $ret = $nometplog.' '.$NomeLog.', '.$Numero.$Compl.', '.$bairro;
                }
            }

        }

        return $ret;

    }

}