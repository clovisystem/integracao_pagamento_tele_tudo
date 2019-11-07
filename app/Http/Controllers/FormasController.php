<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Clientes;

use DB; 




class FormasController extends Controller
{
	public function Aciona($ped, $id = '21') { //21 É O USUÁRIO DE TESTE DEIXAR VAZIO EM PRODUÇÃO

		if($id == ''){
			return '<link rel="stylesheet" href="css/app.css">
					<label style="font-size:20px; color:red; margin-left:36%; margin-top:20%; text-align:center;">
					Logue-se para comprar!<br/><br/>Redirecionando em 4 segundos</label>
					<script>setTimeout(function(){history.go(-1);}, 4000);</script>';
		}
		else{
		//$user = Clientes::find($id);
		$user = Clientes::where('id', $id )->first()->user; // PEGA SÓ O VaLOR DO CAMPO 'USER'
		
		return view('formas.index')->with(array('user' => $user, 'ped' => $ped));

		}

	}


	public function index(Request $request) { //21 É O USUÁRIO DE TESTE DEIXAR VAZIO EM PRODUÇÃO

		
		$user = $request->user;
		$id_carteira = $request->id_carteira;
		$Descricao = $request->Descricao;
		$idPed = $request->idPed;
		$tpEnt = $request->tpEnt;
		$valor = $request->valor;
		$produtos = $request->produtos;
		

		
		$id = DB::table('users')->where('user',$user)->first()->id;

		if($id == ''){
			return '<link rel="stylesheet" href="css/app.css">
					<label style="font-size:20px; color:red; margin-left:36%; margin-top:20%; text-align:center;">
					Logue-se para comprar!<br/><br/>Redirecionando em 4 segundos</label>
					<script>setTimeout(function(){history.go(-1);}, 4000);</script>';
		}
		else{
		//$user = Clientes::find($id);
		$user = Clientes::where('id', $id )->first()->user; // PEGA SÓ O VaLOR DO CAMPO 'USER'
		
		return view('formas.index')->with(array('user' => $user,
												'ped' => $idPed,
												'id_carteira' => $id_carteira,
												'Valor' => $valor,
												'Descricao' => $Descricao,
												'tpEnt' => $tpEnt,
												'IDPED' => $idPed,
												'produtos' => $produtos));

		}

	}
}
