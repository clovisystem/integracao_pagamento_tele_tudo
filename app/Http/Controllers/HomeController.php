<?php

namespace App\Http\Controllers;

use Session;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;

class HomeController extends Controller
{

	public function entrar()
	{
		// echo 'aqui'; die;
		return view('home.entrar');
	}

    public function postEntrar()
    {
        // Op??o de lembrar do usu?rio
        $remember = false;
        if(Input::get('remember'))
        {
            $remember = true;
        }

        $user = Input::get('user');
        $senha = Input::get('senha');

        /* $pass = Hash::make($senha);
        DB::update("update pessoa set password = '".$pass."', user = '".$user."' where id = 285");
        // echo 'pass: '.$pass.'<p>';
        echo 'Alteração realizada';
        die; */

        // Autenticão
        if (Auth::attempt(array('user' => $user,'password' => $senha),$remember)) {

            $pessoas = DB::table('users')
                ->select('id', 'Nome')
                ->where('user','=',$user)
                ->first();

            DB::update('update users set contLogin = contLogin + 1 where id = '.$pessoas->id);

            Auth::loginUsingId($pessoas->id);

            $cookie = Cookie::make('Nome', $pessoas->Nome);
            $cookie = Cookie::make('iduser', $pessoas->id);

            Session::put('Nome', $pessoas->Nome);
            Session::put('iduser', $pessoas->id);

            $Nome = Session::get('Nome');

            $pag = '';
            if(isset($_COOKIE['pagina'])) {
                $pag = $_COOKIE['pagina'];
            }

            if ($pag > '') {
                $cookie = Cookie::make($pag, '');
echo '68'; die;
                return Redirect::to($pag);

            } else {

                $EhAdm = DB::table('pessoaperfil')->select(DB::raw('count(*) as Quant'))
                    ->where('idPessoa','=',$pessoas->id)
                    ->where('idPerfil', '=', 1)
                    ->first();
                if ($EhAdm->Quant>0) {
                    return Redirect::to('adm');
                } else {

                    // PEDIDO SENDO FEITO, INICIALMENTE SEM LOGIN
                    if (Session::has('PEDIDO')) {
                        $idPedido = Session::get('PEDIDO');

                        if ($idPedido==0) {
                            echo 'Session::get(PEDIDO)='.Session::get('PEDIDO'); die;
                        }

                        DB::update("update pedido set User = '".$pessoas->id."' where idPed = ".$idPedido);
                        Session::forget('PEDIDO');
                        $tpEnt=Session::get('TpEntrega');
                        return Redirect::to("confirma?IDPED=".$idPedido."&tpEnt=".$tpEnt);
                    } else {
                        $EhForn = DB::table('empresa')->select(DB::raw('count(*) as Quant'))
                            ->where('idPessoa','=',$pessoas->id)
                            ->first();

                        if ($EhForn->Quant>0) {
                            return Redirect::to('fornecedor');
                        } else {
                            $url="";
                            $url = Session::get('url');
                            $TamUrl=strlen($url);
                            if ($TamUrl==0) {
echo 'HomeController:108'; die;
                                return Redirect::to('/');
                            } else {
                                if ($url=='/') {
                                    $url="https://www.tele-tudo.com";
                                }
                                return Redirect::to($url);
                            }
                        }
                    }
                }
            }

        } else {
echo '120'; die;        
            return Redirect::to('entrar')
                ->with('flash_error', 1)
                ->withInput();

        }
    }

    public function getSair()
    {
        /* if (Session::Has('forn')) {
            DB::update("update empresa set dtON = null where idEmpresa = ".Session::get('forn'));
        }
        Session::forget('Nome');
        Session::forget('iduser');
        Session::forget('Debug');
        Session::forget('PEDIDO');
        $cookie = Cookie::forget('Nome');
        $cookie = Cookie::forget('iduser'); */
        Auth::logout();
        // return Redirect::to(Session::get('url'));
        return Redirect::to('/');
    }

    public function perfil()
    {
        return view('pessoa.perfil');
    }


}
