<?php

namespace App\Http\Controllers;

use Session;
use Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Hash;

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
        DB::update("update users set password = '".$pass."', user = '".$user."' where id = 282");
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
            Session::put('Nome', $pessoas->Nome);
            Session::put('iduser', $pessoas->id);
            $Nome = Session::get('Nome');
            $pag = '';
            if(isset($_COOKIE['pagina'])) {
                $pag = $_COOKIE['pagina'];
            }
            if ($pag > '') {
                $cookie = Cookie::make($pag, '');
                echo 'Home 68'; die;
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
                            return Redirect::to('fornecedor')
                                ->withCookie(Cookie::make('Nome', $pessoas->Nome))
                                ->withCookie(Cookie::make('iduser', $pessoas->id));
                            //->with('id', );
                            // return Redirect::to('fornecedor');
                            // return view('pagina.show')->with('message', 'Pagina Inexistente');
                        } else {
                            $url="";
                            $url = Session::get('url');
                            $TamUrl=strlen($url);
                            if ($TamUrl==0) {
                                echo 'HomeController:108'; die;
                                return Redirect::to('/');
                            } else {
                                if ($url=='/') {
                                    $url="https://tele-tudo.com";
                                }
                                return Redirect::to($url);
                            }
                        }
                    }
                }
            }
        } else {
            return Redirect::to('https://tele-tudo.com/?erro=1');
        }
    }

    public function getSair()
    {
        // return view('produtos/index')->with('sair', 'sim');
        return Redirect::to('https://tele-tudo.com/?sair=1');
    }

    public function perfil()
    {
        return view('pessoa.perfil');
    }

}