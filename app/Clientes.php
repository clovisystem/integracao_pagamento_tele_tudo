<?php
    
namespace App;

use Illuminate\Database\Eloquent\Model;


class Clientes extends Model
{
    protected $table = 'users';
    private $idCliente = 0;

    /* public function getSaldo ($idCliente) {
        $this->idCliente=$idCliente;
        $Cliente = DB::table('clientes')
                  ->select('Saldo')
                  ->where('idCliente','=',$idCliente)
                  ->first();
        return $Cliente->Saldo;
    } */

    public function getIdCliente($idPessoa)
    {
        $cliente = DB::table('clientes')
            ->select('IdCliente')
            ->where('idPessoa','=',$idPessoa)
            ->first();
        if ($cliente!=null) {
            return $cliente->IdCliente;
        } else {
            return 0;
        }
    }

    public function EnderOK ($idUser) {
        $qry = DB::table('users')
            ->select('logradouro.adic')
            ->join('endereco', 'endereco.ID', '=', 'users.Endereco_ID')
            ->join('logradouro', 'logradouro.ID', '=', 'endereco.Logradouro_ID')
            ->where('users.id', '=', $idUser)
            ->first();
        if ($qry->adic==1) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getBairrosCidadeCliente($idUser) {
        $Cons = DB::table('users')
            ->select('cep_bairro.NomeBairro','cep_bairro.id_bairro as id')
            ->join('endereco', 'endereco.ID', '=', 'users.Endereco_ID')
            ->join('logradouro', 'logradouro.ID', '=', 'endereco.Logradouro_ID')
            ->join('bairro', 'cep_bairro.cidade_id', '=', 'logradouro.Cidade_ID')
            ->where('users.id','=',$idUser)
            ->orderBy('cep_bairro.NomeBairro')
            ->get();
        $ret = "<option value='0'>Escolha</option>";
        $i=0;
        foreach ($Cons as $reg) {
            $ret.="<option value='".$reg->id."'>".$reg->NomeBairro."</option>";
            $i++;
        }
        return $ret;
    }


}