<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model {

    protected $table = 'empresa';
   
    private $id=0;
    private $Nome='';

    public function getFormas($empresa) {
        $this->id=$empresa;
        $qry = DB::table('formasforn')
            ->join('formaspag', 'formaspag.idformaspag', '=', 'formasforn.idforma')
            ->select('formaspag.Nome','formaspag.idformaspag')
            ->where('formasforn.idforn', '=', $empresa)
            ->get();

        // $ret="";
        // $ret="<form method='post' action='https://www.tele-tudo.com/pagtodireto'>";

        $bt=0;
        $tpbt='btn-primary';
        $din="<div id='dvTroco'></div>";
        $Cli='ClicouDin';
        foreach ($qry as $reg) {
            $bt++;

            // $ret.="<input type='button' id='bt".$bt."' onclick='".$Cli."(".$reg->idformaspag.")' value='".$reg->Nome."' text-align='center' class='btn-lg ".$tpbt."'/>";
            echo "<input type='button' id='bt".$bt."' onclick='".$Cli."(".$reg->idformaspag.")' value='".$reg->Nome."' text-align='center' class='btn-lg ".$tpbt."'/>";

            if ($bt==1) {
                $tpbt='btn-success';

                echo $din;
                // $ret.=$din;

                $Cli='ClicouForma';
            }
        }
        // $ret.="</form>";
        // return $ret;
    }

    public function getTempo() {
        $qry = DB::table('empresa')
            ->select('TempoEntrega','Empresa')
            ->where('idEmpresa', '=', $this->id)
            ->first();
        $tempo = $qry->TempoEntrega;
        $this->Nome=$qry->Empresa;
        if (substr($tempo,0,3)=="00:") {
            $tempo = substr($tempo,3). " minutos";
        } else {
            $tempo = "Mais de uma hora";
        }
        return $tempo;
    }

    public function getNome() {
        return $this->Nome;
    }
}