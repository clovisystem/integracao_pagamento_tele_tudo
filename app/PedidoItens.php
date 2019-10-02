<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PedidoItens extends Model
{
    protected $table = 'pedidoItens';
    private $qtd = 0;
    private $idProd=0;
    private $idFornProd=0;

    public function setIdProd($idProd)
    {
        $this->idProd = $idProd;

        $qry = DB::table('produtos')
            ->select('Empresax_ID')
            ->where('ID', '=', $idProd)
            ->first();
        $this->idFornProd=$qry->Empresax_ID;

    }

    public function getIdProd()
    {
        return $this->idProd;
    }

    public function Add($idPed) {
    
    	$sql = "INSERT INTO pedidoItens (idped, idprod, quant, Valor) ";
    	$sql = $sql."SELECT ".$idPed." as idped, ";
    	$sql = $sql."ID as idprod, ";
    	$sql = $sql.$this->getQtd()." as quant, ";
    	$sql = $sql." Valor From produtos ";
    	$sql = $sql." Where ID = ".$this->getIdProd();    	
    	
    	DB::update($sql);
    	
        /* DB::insert('insert into pedidoItens (idped, idprod, quant, Valor) values (?, ?, ?, ?)', [
            $idPed,
            $this->getIdProd(),
            $this->getQtd(),
            $this->getVlr()
        ]); */
    }

    public function setQtd($qtd)
    {
        $this->qtd = $qtd;
    }

    public function getQtd()
    {
        return $this->qtd;
    }

    public function getidFornProd() {
        return $this->idFornProd;
    }

}