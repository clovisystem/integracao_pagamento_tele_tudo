<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Enderecos extends Model
{
    protected $table = 'endereco';

    public function GetEndereco($id, $red) {
        $sql = "Select endereco.Numero, endereco.cep, endereco.complemento, endereco.CEP,  
                  logra.NomeLog, 
                  tplogradouro.nometplog, 
                  cep_cidade.cidade as NomeCidade, cep_cidade.estado, 
                  cep_bairro.bairro as NomeBairro    
                from endereco 
                inner join logra on logra.ID = endereco.Logradouro_ID 
                inner join tplogradouro on tplogradouro.ID = logra.TpLogradouro_ID 
                left join cep_bairro on cep_bairro.id_bairro = endereco.idBairro 
                inner join cep_cidade on cep_cidade.id_cidade = logra.Cidade_ID 
                where endereco.ID = ".$id;
        $sql = $sql." limit 1 ";
        $enders = DB::select($sql);
        $ret = '';
        foreach ($enders as $ender) {
            $Numero = $ender->Numero;
            $NomeLog = $ender->NomeLog;
            $nometplog = $ender->nometplog;
            $NomeCidade = $ender->NomeCidade;
            $bairro = $ender->NomeBairro;
            $Sigla = $ender->estado;
            $NrCep = $ender->CEP;
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