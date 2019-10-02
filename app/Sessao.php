<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

class Sessao extends Model
{
    protected $table = 'sessao';
    private $idSessao="";
    private $TocaSom=0;
    private $NrIdFace = 0;
    private $Nome='';

    public function Nova($IP) {
        DB::insert('insert into sessao (IP, data) values (?, ?)', [$IP, new DateTime]);
        $this->idSessao = DB::table('sessao')->max('idSessao');
    }

    /*    public function SisFin() {
            $sql = "SELECT Count(*) as Quant ";
            $sql.="FROM config ";
            $sql.="Where SUBTIME( Now( ) , '00:02:00' ) < dtAtuSisFin ";
            $Cons = DB::select( DB::raw($sql));
            echo "Cons[0]->Quant = ".$Cons[0]->Quant; die;
            return $Cons[0]->Quant;
        }*/

    public function Modo()
    {
        $cons = DB::table('config')
            ->select('Modo', 'NrIdFace')
            ->where('ID', '=', 1)
            ->first();
        $sModo="";
        $this->NrIdFace=$cons->NrIdFace;
        switch ($cons->Modo) {
            case 1:
                $sModo="Teste - Simulado";
                break;
            case 2:
                $sModo="Teste";
                break;
            case 3:
                $sModo="Produção";
                break;
        }
        return $sModo;
    }

    public function VeSf() {
        $sql = "SELECT dtON, SUBTIME( Now( ) , '00:02:30' ) DtX, Som ";
        $sql.="FROM config ";
        $sql.="Where ID = 1 ";
        $ConsA = DB::select( DB::raw($sql));
        $this->TocaSom = $ConsA[0]->Som;
        if ($ConsA[0]->dtON>$ConsA[0]->DtX) {
            return 1;
        } else {
            $sql = "SELECT dtON, SUBTIME( Now( ) , '00:02:30' ) DtX ";
            $sql.="FROM empresa ";
            $sql.="Where idEmpresa = 1 ";
            $ConsE = DB::select( DB::raw($sql));
            if ($ConsE[0]->dtON>$ConsE[0]->DtX) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function Som() {
        return $this->TocaSom;
    }

    /*public function getTpe($idPed) {
        $cons = DB::table('pedidoItens')
            ->select('empresa.tpEntrega')
            ->join('produtos', 'produtos.ID', '=', 'pedidoItens.idprod')
            ->join('empresa', 'empresa.idEmpresa', '=', 'produtos.Empresax_ID')
            ->where('pedidoItens.idped', '=', $idPed)
            ->first();
        return $cons->tpEntrega;
    }*/

    public function tpEntrega($idPedido) {
        $Cons = DB::table('pedido')
            ->select('Tpe')
            ->where('idPed','=',$idPedido)
            ->first();
        return $Cons->Tpe;
    }

    public function urlFace() {
        if ($this->NrIdFace==0) {
            $cons = DB::table('config')
                ->select('NrIdFace')
                ->where('ID', '=', 1)
                ->first();
            $this->NrIdFace=$cons->NrIdFace;
        }
        $app_secret = "9559a449eece386b90344842e4514f39";
        $app_id = "395697367529746";
        $autoriz = "public_profile";
        $redirect_uri = urlencode("https://www.tele-tudo.com/loginfb");

        $url = "http://www.facebook.com/dialog/oauth/?app_id=".$app_id.
            "&client_id=".$app_id.
            "&redirect_uri=".$redirect_uri.
            "&scope=".$autoriz.
            "&state=".$this->NrIdFace;
        return $url;
    }

    public function IncNrFace() {
        DB::update("update config set NrIdFace = NrIdFace + 1 where ID = 1");
    }

    /* public function getUser($idPessoa) {
        $Cons = DB::table('users')
            ->select('user','Nome')
            ->where('id','=',$idPessoa)
            ->first();
        $this->Nome=$Cons->Nome;
        return $Cons->user;
    } */

}