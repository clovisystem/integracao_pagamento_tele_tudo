<?php

namespace App\Http\Controllers;

use App\Empresa;
use Illuminate\Support\Facades\Redirect;

class PaginaController extends Controller {

    public function Aciona($site) {
        $forn = Empresa::where("site",$site)->first();
        if ($forn==null) {
            $sitete = "https://tele-tudo.com/".$site;
            $forn = Empresa::where("site",$sitete)->first();
        }
        if ($forn==null) {
            return view('pagina.show')->with('message', 'Pagina Inexistente');
        } else {            
	    return view('pagina.show', compact('forn'));
        }
    }

    public function edit() {
        return view('pagina.edit')->with('site', 1);
        // return View::make('pagina.edit');
    }

    public function salvapagina() {
        $IdEmpresa = Input::get('txIdEmpresa');
        $fundo="";
        $banner="";
        $CorLetra = Input::get('txCorLetra');
        $celular  = '';
        $face  = '';
        $whats  = '';
        if (isset($_GET['txImgFundo'])) {
            $fundo =  $_GET['txImgFundo'];
        }
        if (isset($_GET['txBanner'])) {
            $banner =  $_GET['txBanner'];
        }

        if (isset($_GET['txcelular'])) {
            $celular  =  $_GET['txcelular'];
        }
        if (isset($_GET['txface'])) {
            $face =  $_GET['txface'];
        }
        if (isset($_GET['txwhats'])) {
            $whats =  $_GET['txwhats'];
        }

        $pag = Pagina::where("IdEmpresa",$IdEmpresa)->first();
        if ($pag==null) {
            $pag = new Pagina;
            $pag->idempresa = $IdEmpresa;
            if ($fundo!="") {
                $pag->fundo =  $fundo;
            }
            if ($banner!="") {
                $pag->banner =  $banner;
            }
            $pag->CorLetra = $CorLetra;
            $pag->save();
        } else {
            DB::update("update pagina set fundo = '"
                .$fundo."', banner = '"
                .$banner."', CorLetra = '"
                .$CorLetra."', celular = '"
                .$celular."', face = '"
                .$face."', whats = '"
                .$whats."' "
                ." where IdEmpresa = ".$IdEmpresa);
        }
        return Redirect::to('/editapagina');
    }

}