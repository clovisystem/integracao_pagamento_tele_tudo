<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use App\Pessoa;
use App\Produtos;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;


class PessoaController extends Controller
{

    private $idServico = 0;
    private $logradouro = '';
    private $LograNovo;

    public function CriaUser()
    {
        return view('pessoa.create')->with('user', '');
    }







    //CRIEI ESTES PROCEDIMENTOS SOMENTE PARA TESTES-------------------------------------------------------------

    public function layout()
    {
        return view('layouts.padrao');
    }

    public function teste()
    {
        return 'passou';
    }

    public function insert(Request $request)
    {
        //$pessoa = new Pessoa;

        $id = '';
        $user= $request->input('user');
        $Nome = $request->input('Nome');
        $email = $request->input('email');
        $fone = $request->input('fone');
        $password= $request->input('password');
        //$remember_token = $request->remember_token;
        $Cep = $request->input('txCep');
        /*$pessoa->Logra = $request->txLogra;
        $pessoa->Logra .= $request->cbLogra;*/

        $dados = array( 
                        'user' => $user,
                        'Nome' => $Nome,
                        'email'=> $email,
                        'fone' => $fone,
                        'password' => $password,
                        'Cep' => $Cep );




        $pessoa = DB::select('select * from users where user = ?', [$user]);

    

        if(count($pessoa) >= 1){
            echo'<script>function volta(){history.go(-1);}</script>';
            return '<p style="font-family:Helvetica, Arial, Sans-Serif; font-size:20px; padding-top:6px; padding-left:6px;  height:30px; background-color:red; border-radius:8px;">Esse usuário já está cadastrado no sistema.</p>'.'<script>setTimeout("volta()", 4000);</script>';
        
        }
        if(count($pessoa) == 0){
            $insert = DB::table('users')->insert($dados);
            $pessoa = DB::select('select * from users where user = ?', [$user]);
            return view ('pessoa.show')->withPessoa($pessoa[0]);
        }


        

    }



    //TIREI A FUNCAO SHOW DAQUI ONDE ESTAVA TESTANDO E PASSEI LÁ EMBAIXO 


    //CRIEI ESTES PROCEDIMENTOS SOMENTE PARA TESTES----------------------------------------------------









    public function index()
    {
        // get all the nerds
        $pessoa = Pessoa::all();

        // load the view and pass the nerds
        return View::make('pessoa.index')
            ->with('pessoa', $pessoa);
    }

    private function loga($texto) {
        DB::update("insert into LogDebug (Log) values ('PessoaController:".$texto."')");
    }

    public function store()
    {
        $idTpLogra=0;
        $this->loga('Entrou no Store');
        $tpEnder = Input::get('tpEnder');
        $erro='';
        if ($tpEnder=='E') {
            $this->loga('tpEnder=E 1');
            $endereco = Input::get('Endereco');
            $endereco = trim($endereco);
            $UltLetra = substr($endereco, -1);
            if ($UltLetra == ',') {
                $erro = "Complete o endereço";
                Session::put('Nrerro', 1);
            }
            $Endereco = Input::get('Endereco');
            $e1 = explode(',',$Endereco);
            $e2 = explode(' ',$e1[0]);
            $t1 = sizeof($e1);
            $TpLogra = $e2[0];
            $this->logradouro = $e2[1];
            $e3 = trim($e1[1]);
            $e4 = explode(' ',$e3);
            $e5 = sizeof($e4);
            if ($t1==3) {
                $Complemento = $e1[2];
                $numero=$e2[1];
            } else {
                $numero=$e4[0];
                $Complemento = '';
                if ($e5==2) {
                    $Complemento=$e4[1];
                }
            }
            $idTpLogra = $this->getTpLograE($TpLogra);
            $FaceID=Input::get('idFace');
            $Nome=Input::get('faceName');
        } else {
            $this->loga('tpEnder<>E');
            if ($tpEnder=='C') {
                // UM CEP POR CIDADE
                $numero = Input::get('txNumeroC');
                $cbLogra = Input::get('txLogra');
                if ($cbLogra=='') {
                    $erro = "Informe o endereço";
                }
            } else {
                // CEP POR RUAS
                $NumeroC = Input::get('txNumeroC1');
                if ($NumeroC>'') {
                    $numero = $NumeroC;
                } else {
                    $numero = Input::get('txNumeroR');
                }
                $cbLogra = Input::get('cbLogra');
                if ($cbLogra=='Clique aqui para escolher') {
                    $erro = "Informe o endereço";
                }
            }
            if ($numero=='') {
                $erro="Informe o numero ";
            }
            $Complemento = Input::get('txComplemento');
            $FaceID=0;
            $Nome= Input::get('Nome');

            $txLogra = Input::get('txLogra1');
            if ($txLogra=='') {
                $txLogra = Input::get('txLogra2');
            }
            if ($erro=='') {
                $idTpLogra = $this->getTpLogra($cbLogra, $txLogra);
            }
        }
        if ($idTpLogra==0) {
            $erro = 'Tipo de Logradouro não identificado = '.$txLogra;
        }
        if ($erro>'') {
            if ($tpEnder=='E') {
                Session::put('erro', $erro);
                return Redirect::to("ender?idFace=".Input::get('idFace').
                    "&cep=".Input::get('cep').
                    "&estado=".Input::get('estado').
                    "&sigla_estado=".Input::get('sigla_estado').
                    "&cidade=".Input::get('txCid').
                    "&Bairro=".Input::get('Bairro').
                    "&Endereco=".Input::get('Endereco').
                    "&fone=".Input::get('fone'));
            } else {
                echo '127:Erro = '.$erro; die;
                return Redirect::to('pessoa/create')
                    ->withErrors($erro)
                    ->withInput(Input::except('password'));
            }
        } else {

            // CADASTRO NORMAL
            $Estado = Input::get('txES');
            $idCidade = $this->getCidade(Input::get('txCid'), $Estado);
            $idBairro = $this->getBairro(Input::get('Bairro'), $idCidade);

            $idLogradouro = $this->gettidLogradouro($idCidade, $idTpLogra);
            // Endereço
            $scep = Input::get('txCep');
            $scep = $this->FiltraCep($scep);

            $idEndereco = $this->getEndereco($idBairro, $idLogradouro, $scep, $numero, $Complemento, $idCidade);
            // store
            $pessoa = new Pessoa;
            $pessoa->Nome     = $Nome;
            $pessoa->email    = Input::get('email');
            $pessoa->user     = Input::get('user');
            $idPedido = Input::get('idPedido');
            $pessoa->FaceID = $FaceID;
            if ($pessoa->FaceID>0) {

            }
            $idRede = Input::get('idRede');
            if ($idRede>0) {
                $pessoa->RedeID =$idRede;
            }
            $pessoa->fone = Input::get('fone');
            $pessoa->password = Hash::make(Input::get('password'));
            $pessoa->Cep      = Input::get('txCep');
            $pessoa->remember_token = Input::get('remember_token');
            $pessoa->Endereco_ID = $idEndereco;
            $pessoa->idCaptador =  Input::get('idCaptador');
            $pessoa->save();
            $ultPessoa = DB::table('users')->max('id');
            $cProd = new Produtos();
            $Tem = $cProd->VeSeTemCidDoCliente($pessoa->Cep, $idCidade);
            $compl = "?id=".$ultPessoa."&ln=".$this->LograNovo."&tl=".$Tem;
            if ($tpEnder=='E') {
                $this->loga('tpEnder=E 2');
                Auth::loginUsingId($ultPessoa);
                Session::put('iduser', $ultPessoa);

                // MUDAR AQUI
                Session::put('Nome',$Nome);
                if ($idPedido>0) {

                    $this->loga('idPedido>0');
                    // return Redirect::to('posface/');
                    return Redirect::to("posface?User=".$ultPessoa.
                        '&Ped='.$idPedido.
                        '&Tes='.Session::get('Teste'));

                    /*return Redirect::to("posface?User=".$ultPessoa.
                        '&Ped='.$idPedido.
                        '&Tpe='.$Tpe.
                        '&Tes='.Session::get('Teste'));*/

                } else {

                    $this->loga('idPedido = 0');
                    Session::put('url', $_SERVER ['REQUEST_URI']);
                    $url = '/';
                    if (Session::has('url')) {
                        $url = Session::get('url');
                    }
                    if ($url=='/pessoa') {
                        $url='perfil'.$compl;
                    }
                    return Redirect::to($url);
                }

            } else {
                $idPessoa = $this->SetaUserServico(Input::get('email'));
                if ($idPessoa>0) {
                    $this->loga('idPessoa>0 SetaUserServico');
                    Auth::loginUsingId($idPessoa);
                    $cookie = Cookie::make('Nome', Input::get('Nome'));
                    $cookie = Cookie::make('iduser', $idPessoa);
                    Session::put('Nome', Input::get('Nome'));
                    Session::put('iduser', $idPessoa);

                    Session::flash('message', 'Você pode editar seu anúncio');
                }
                if ($this->LograNovo==1) {
                    $MensAdicUser = 'Seu cadastro foi adicionado, mas seu endereço precisa ser confirmado<p>será avisado por email quando tiver ocorrido';
                } else {

                    // Session::put('Nome',$pessoa->Nome);
                    Auth::loginUsingId($ultPessoa);
                    $MensAdicUser = 'Usuário adicionado com sucesso!<Br>Voce já pode realizar suas compras';
                }
                Auth::loginUsingId($ultPessoa);
                return Redirect::to('perfil'.$compl);
            }
        }
    }
    //}

    private function SetaUserServico($email) {
        $serv = DB::table('servicos')
            ->select('id')
            ->where('email','=',$email)
            ->first();

        if ($serv!=null) {
            $this->idServico = $serv->id;
            $pess = DB::table('users')
                ->select('id')
                ->where('email','=',$email)
                ->first();

            $servico = Servicos::find($this->idServico);
            $servico->idpessoa = $pess->id;
            $servico->save();
            return $pess->id;
        } else {
            return 0;
        }

    }

    public function show($id)
    {
        // get the nerd
        $pessoa = Pessoa::find($id);
       
        //SE O RETURN VIEW DEBAIXO NÃO FUNCIONAR COLOQUE ESTE:
        //return view('pessoa.show')->with('pessoa', $pessoa);

        // show the view and pass the nerd to it
        return View::make('pessoa.show')
            ->with('pessoa', $pessoa );
    }

    public function edit($id)
    {
        $pessoa = Pessoa::find($id);
        return View::make('pessoa.edit')
            ->with('pessoa', $pessoa);
    }

    public function update($id)
    {

        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'Nome'     => 'required',
            'email'    => 'required|email',
            'Cep'      => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('pessoa/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $pessoa = Pessoa::find($id);

            $pessoa->Nome     = Input::get('Nome');
            $pessoa->email    = Input::get('email');
            $pessoa->Cep      = Input::get('Cep');
            $pessoa->EnderDesc = Input::get('EnderDesc');

            // $pessoa->user     = Input::get('user');

            $senha='';
            $senha = Input::get('password');
            if ($senha>'') {

                /*$pessoa->password = Input::get('password');*/
                $pessoa->password = Hash::make(Input::get('password'));

                $pessoa->remember_token = Input::get('remember_token');
            }
            $pessoa->save();

            // redirect
            Session::flash('message', 'Cadastro Atualizado!');
            return Redirect::to('/');
        }
    }

    public function destroy($id)
    {
        // delete
        $pessoa = Pessoa::find($id);
        $pessoa->delete();

        // redirect
        Session::flash('message', 'Usuário Deletado!');
        return Redirect::to('pessoas');
    }

    private function getEstado($ES) {
        $Cons = DB::table('cep_estado')
            ->select('ID')
            ->where('sigla', '=', $ES)
            ->first();
        return $Cons->ID;
    }

    private function getCidade($Cid, $Sigla) {
        $Cons = DB::table('cep_cidade')
            ->select('id_cidade as ID')
            ->where('cidade', '=', $Cid)
            ->where('estado', '=', $Sigla)
            ->first();
        return $Cons->ID;
    }

    private function getBairro($Bairro, $idCidade) {
        $Cons = DB::table('cep_bairro')
            ->select('id_bairro as id')
            ->where('bairro', '=', $Bairro)
            ->where('cidade_id', '=', $idCidade)
            ->first();
        if ($Cons==null) {
            echo 'fazer a adição de bairro'; die;
            $idbairro = DB::table('bairro')->max('id_bairro')+1;
            DB::update("insert into bairro (id, NomeBairro, idCidade) values (".$idbairro.", '".$Bairro."', ".$idCidade.")");
        } else {
            $idbairro = $Cons->id;
        }
        return $idbairro;
    }

    private function getTpLogra($cbLogra, $txLogra) {
        if ($txLogra>'') {
            // DIGITADO
            $Texto=$txLogra;
        } else {
            // ESCOLHIDO NA LISTA
            $Texto=$cbLogra;
        }
        $posE = strpos($Texto, " ");
        $TpLogra = strtolower(substr($Texto, 0, $posE));

        // ->where(LOWER('nometplog'), '=', $TpLogra)
        /* $Cons = DB::table('tplogradouro')
            ->select('ID')
            ->where('nometplog', '=', $TpLogra)
            ->first(); */

        /*$sql = "select ID ";
        $sql.="from tplogradouro ";
        $sql.="where LOWER(nometplog) = '".$TpLogra."'";
        $qry = DB::select( DB::raw($sql));*/
        $qry = $this->ConsTpLogr($TpLogra);
        if ($qry!=null) {
            $tam = strlen($Texto);
            $this->logradouro = ltrim(substr($Texto, $posE, $tam-$posE+1));
            return $qry[0]->ID;
        } else {
            echo "Tipo de Logradouro imprevisto"; die;
        }
    }

    private function ConsTpLogr($TpLogra) {
        $sql = "select ID ";
        $sql.="from tplogradouro ";
        $sql.="where LOWER(nometplog) = '".$TpLogra."'";
        $qry = DB::select( DB::raw($sql));
        return $qry;
    }

    private function getTpLograE($TpLogra) {
        $qry = $this->ConsTpLogr($TpLogra);
        if ($qry!=null) {
            return $qry[0]->ID;
        } else {
            echo "Tipo de Logradouro imprevisto"; die;
        }
    }

    private function gettidLogradouro($idCidade, $idTpLogra) {
        $sql = "select ID ";
        $sql.="from logra ";
        $sql.="where NomeLog = '".$this->logradouro;
        $sql.="' and Cidade_ID = ".$idCidade;
        $qry = DB::select( DB::raw($sql));
        if ($qry==null) {
            DB::insert('insert into logra (NomeLog, TpLogradouro_ID, Cidade_ID) values (?, ?, ?)', [
                $this->logradouro,
                $idTpLogra,
                $idCidade
            ]);
            $idLogradouro = DB::table('logra')
                ->max('ID');
            $this->LograNovo=1;
            // $this->MensAdicUser = 'Seu cadastro foi adicionado, mas seu endereço precisa ser confirmado<p>será avisado por email quando tiver ocorrido';
        } else {
            $idLogradouro= $qry[0]->ID;
            $this->LograNovo=0;
            // $this->MensAdicUser = 'Usuário adicionado com sucesso!<Br>Voce já pode realizar sua compra';
        }
        return $idLogradouro;
    }

    private function getEndereco($idBairro, $idLogra, $scep, $Numero, $Complemento, $idCidade) {
        $idCep = $this->getCep($scep, $idBairro, $idCidade);
        $sql = "select ID ";
        $sql.= "from endereco ";
        $sql.= "where Logradouro_ID = ".$idLogra;
        $sql.= " and Numero = '".$Numero;
        $tamC = strlen($Complemento);
        if ($tamC>0) {
            $sql.= "' and Complemento = '".$Complemento."'";
        } else {
            $sql.= "' and Complemento is null ";
        }
        $qry = DB::select( DB::raw($sql));
        if ($qry!=null) {
            $idEnder = $qry[0]->ID;
        } else {
            if ($tamC>0) {
                DB::insert('insert into endereco (idBairro, Logradouro_ID, CEP, Numero, Complemento) values (?, ?, ?, ?, ?)', [
                    $idBairro,
                    $idLogra,
                    $idCep,
                    $Numero,
                    $Complemento
                ]);
            } else {
                DB::insert('insert into endereco (idBairro, Logradouro_ID, CEP, Numero) values (?, ?, ?, ?)', [
                    $idBairro,
                    $idLogra,
                    $idCep,
                    $Numero
                ]);
            }
            $idEnder = DB::table('endereco')->max('id');
        }
        return $idEnder;
    }

    private function FiltraCep($scep) {
        $scep = str_replace('.', '', $scep);
        $scep = str_replace('-', '', $scep);
        $scep = str_replace(';', '', $scep);
        $scep = str_replace(' ', '', $scep);
        return $scep;
    }

    private function getCep($scep, $idbairro, $idCidade) {
        $ConsC = DB::table('cep')
            ->select('id')
            ->where('cep', '=', $scep)
            ->first();
        if ($ConsC==null) {
            $cCep = new Cep;
            $cCep->GetCoordenadas($scep, 'cad');
            $idCep = DB::table('cep')->max('id');
        } else {
            $idCep = $ConsC->id;
        }
        return $idCep;
    }

    public function create() {
        // não faz nada no create
    }

}