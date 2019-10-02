<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pagina extends Model
{
    protected $table = 'pagina';

    private $CorLetraLoc='';

    public function fundo ($forn) {
        $cons = DB::table('pagina')
            ->select('fundo','CorLetra')
            ->where('idEmpresa', '=', $forn)
            ->first();
        if ($cons==null) {
            $this->CorLetraLoc = "#000000";
            $fundo = '';
        } else {
            $this->CorLetraLoc = $cons->CorLetra;
            $fundo = $cons->fundo;
        }
        return $fundo;
    }

    public function getCor() {
        return $this->CorLetraLoc;
    }

}