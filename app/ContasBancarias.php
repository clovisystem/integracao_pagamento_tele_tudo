<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContasBancarias extends Model
{
    protected $table = 'contasbancarias';

    public function Aciona() {
        return view('contasbancarias.index');
    }

    public function Contas($idEmpresa) {
        $qry = DB::table('contasbancarias')
            ->join('bancos', 'bancos.cod', '=', 'contasbancarias.idBanco')
            ->select('contasbancarias.id','contasbancarias.idBanco',
                'bancos.banco',
                'contasbancarias.Agencia','contasbancarias.Conta')
            ->where('contasbancarias.idEmpresa', '=', $idEmpresa)
            ->get();
        return $qry;
    }

}

