@extends('layouts.padrao')
<title>
    <?php

    Session::forget('ENTREGA');
    Session::forget('ULTCEP');
    Session::forget('COMPROU');
    Session::forget('site');
    Session::forget('ValorPed');
    /* Session::get('VlrEntrega');
    Session::get('tpEnt'); */

    $funciona = true;
    if (App::getLocale()=='') {
        $ClsLocation = new App\Location;
        $funciona = $ClsLocation->SetaLocal();
    }
    echo Lang::get('produtos.titulo');
    $CliSemEnder = 0;
    $CepDoCli="";
    $idUser = 0;
    $EnderOK = 1;
    if (Auth::check()) {
        $idUser = Auth::id();
        $pessoas = DB::table('users')
            ->select('Endereco_ID','Cep')
            ->where('id','=',$idUser)
            ->first();
        if ($pessoas->Endereco_ID==null) {
            $CliSemEnder = 1;
        }
        $CepDoCli=$pessoas->Cep;
        $cCli = new App\Clientes;
        $EnderOK = $cCli->EnderOK($idUser);

    }
    $idRede = 0;
    if (isset($_GET['id'])) {
        $idRede = $_GET['id'];
    }
    ?>
</title>

@section('content')
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>
    <script>

        var txtenviar="";
        var VlrPedAnt = 0;
        var PedAnt="";
        var JaSelecionou=0;

        // RETIRAR QUANDO O ENDEREÇO DO CLIENTE ESTIVER COMPLETO
        // var CliSemEnder=0;
        var NumCompra=0;
        var CliSemEnder = <?php echo $CliSemEnder; ?>;

        // RETIRAR QUANDO PERMITIR MAIS DE UM FORNECEDOR POR PEDIDO
        var DoisForn =0;
        var EsseFor = 0;
        var sValor = "";

        var nav = navigator.appVersion;
        var A = nav.indexOf("Android");
        var nH = "h3"; // h1
        var Logado = <?php echo $idUser; ?>;

        var idrede = <?php echo $idRede; ?>;
        // A=1; // Altera de PC para Celular e vice-versa
        // Mas agora tem outro ponto também, mais abaixo $UA = $_SERVER['HTTP_USER_AGENT'];

        // Endereço não confirmado ainda não pode comprar
        var EnderOK = <?php echo $EnderOK; ?>;

        var ArrIts = [];

        if (A<1) {
            document.cookie = "BRO=PC";
        } else {
            nH = "h4";
            document.cookie = "BRO=AN";
        }

        function AtualizaTotal(Total) {
            sValor = numeral(Total).format('0.00[0000]');
            lbTotal.textContent = "R$ " + sValor;
        }

        function ATM(id, click) {
            var Total = 0;
            var QtdUn = 0;
            var Valor = 0;
            var VltItem = 0;
            var sValor = '';
            var ii = 0;
            var ObjQt="";
            var ObjVl="";
            var Objid="";
            var ObjTpe="";
            var sValor2="";
            var idProd="";
            var ObjFor="";
            var Tpe=0;
            var Qtd = document.getElementById("txQtdItens").value;

            // RETIRAR QUANDO PERMITIR MAIS DE UM FORNECEDOR POR PEDIDO
            var ForAtu=0;
            var lcDoisForn=0;

            txtenviar="";
            Qtd++;
            EsseFor=0;
            for (i = 1; i < Qtd; i++) {
                ObjQt = "txQt" + i;
                ObjVl = "txVlr" + i;
                Objid = "txID"+i;
                ObjTpe = "txTpe"+i;
                ObjFor = "txFor"+i;
                sValor = document.getElementById(ObjVl).innerText;
                var res = sValor.split(",");

                // sValor2 = res[0].substring(2,res[0].length+"."+res[1]);
                sValor2 = res[0].substring(2,res[0].length)+"."+res[1];

                Valor = Number.parseFloat(sValor2);
                QtdUn = document.getElementById(ObjQt).value;
                idProd = document.getElementById(Objid).value;
                Tpe = document.getElementById(ObjTpe).value;

                if (QtdUn>0) {
                    ii++;
                    VltItem = QtdUn * Valor;
                    Total = Total + VltItem;
                    if (click==0) {
                        if (nH == "h3") { // if (nH == "h1") {
                            AjustaClick(i, VltItem);
                        }
                    }
                    txtenviar=txtenviar+"&q"+ii+"="+QtdUn;
                    txtenviar=txtenviar+"&p"+ii+"="+idProd;
                    EsseFor = document.getElementById(ObjFor).value;
                    if (ForAtu==0) {
                        ForAtu= EsseFor;
                    } else {
                        if (ForAtu!= EsseFor) {
                            lcDoisForn=1;
                        }
                    }
                }
            }
            txtenviar=txtenviar+"&Qtd="+ii;
            txtenviar=txtenviar+"&t="+Tpe;
            Total+=VlrPedAnt;
            if (lcDoisForn==1) {
                $('#MesmoFor').css({display:"block"});
                DoisForn=1;
            } else {
                if (DoisForn==1) {
                    $('#MesmoFor').css({display:"none"});
                    DoisForn=0;
                }
            }
            AtualizaTotal(Total);
        }

        function Logar() {
            document.location.assign("http://tele-tudo.com/login");
        }

        function aciona() {
            document.formPesq.submit();
        }

        function ClicCat(CatProd) {
            document.getElementById("CatProd").value = CatProd;
            aciona();
        }

        function Marca(nrLin) {
            var nmObjID = "txID"+nrLin;
            var nmObjIM = "txIm"+nrLin;
            var nmObjQT = "txQt"+nrLin;
            var nmObjFor = "txFor"+nrLin;
            Tpe = document.getElementById("txTpe"+nrLin).value;
            var ID = document.getElementById(nmObjID).value;
            EsseFor = document.getElementById(nmObjFor).value;
            var posId =Procura(ID);
            if (posId>-1) {
                document.getElementById(nmObjQT).value = 0;
                document.getElementById(nmObjIM).src="https://tele-tudo.com/resources/assets/img/carrinho.png";
                TiraElem(ID);
            } else {
                Adiciona(nrLin, ID, 1);
                document.getElementById(nmObjIM).src="https://tele-tudo.com/resources/assets/img/retirar-do-carrinho.jpg";
                document.getElementById(nmObjQT).value = 1;
            }
            Totaliza();

        }

        $(document).keypress(function handleEnter(e, func) {
            if (e.keyCode == 13 || e.which == 13) {
                var CEP = document.getElementById('CEP').value;
                var PESQ = document.getElementById('PESQ').value;
                if ((CEP>'') && (PESQ>'')) {
                    aciona();
                }
            }
        });

        function Procura (id) {
            const pesq = ArrIts.find( obj => obj.cod === id )
            if (pesq === undefined ) {
                return -1;
            } else {
                return pesq.pos;
            }
        }

        function Adiciona(nrLin, ID, quant) {
            // alert("nrLin = "+nrLin);
            var ObjVl = "txVlr" + nrLin;
            var ObjTpe = "txTpe" + nrLin;
            var sValor = document.getElementById(ObjVl).innerText;
            var res = sValor.split(",");
            var sValor2 = res[0].substring(2,res[0].length)+"."+res[1];
            var Valor = Number.parseFloat(sValor2);
            var vTpe = document.getElementById(ObjTpe).value;
            ArrIts.push({pos:ArrIts.length, cod:ID, qua:quant, vlr:Valor , Tpe:vTpe});
            document.getElementById('Carrinho').src="https://tele-tudo.com/resources/assets/img/carrinhopeqOK.png";
        }

        function Totaliza() {
            var TamArr = ArrIts.length;
            var Tot=0;
            var VlrIt=0;
            var qt=0;
            var VTotIt=0;
            for (var i = 0; i < TamArr; i++) {
                VlrIt = ArrIts[i].vlr;
                qt = ArrIts[i].qua;
                VTotIt = VlrIt * qt;
                Tot+=VTotIt;
            }
            sValor = numeral(Tot).format('0.00[0000]');
            lbTotal.textContent = "R$ " + sValor;
        }

        function AtuQuant(nrLin) {
            var nmObjID = "txID"+nrLin;
            var ID = document.getElementById(nmObjID).value;
            var posId =Procura(ID);
            var nmObjQT = "txQt"+nrLin;
            var QT = document.getElementById(nmObjQT).value;
            ArrIts[posId].qua=QT;
            Totaliza();
        }

        function TiraElem(ID) {
            var TamArr = ArrIts.length;
            var newArr =[];
            for (i = 0; i < TamArr; i++) {
                if (ArrIts[i].cod!==ID) {
                    newArr.push({pos:i, cod:ArrIts[i].cod, qua:ArrIts[i].qua, vlr:ArrIts[i].vlr });
                }
            }
            ArrIts = newArr;
            if (newArr.length==0) {
                document.getElementById('Carrinho').src="https://tele-tudo.com/resources/assets/img/carrinhopeq.png";
            }
        }

    </script>
    <?php
    // $mostrar=false;
    $adm=false;
    $cep=$CepDoCli;
    $pesq='';
    $lat = '';
    $long = '';
    $debug = 0;
    $req = '';
    $qtItens=0;
    Session::put('url', $_SERVER ['REQUEST_URI']);
    $Teste = 0;
    $logado=0;

    if (Session::has('Nome')) {
        $Nome = Session::get('Nome');
        if ($Nome>"") {
            $logado = 1;
            if (Session::get('iduser')==21) {
                $Teste = 1;
            }
            if (Session::get('iduser')==1) {
                // MUDAR PARA PEGAR O ADM PELO PERFIL
                // $mostrar=true;
                $adm=true;
            }
        } else {
            Session::forget('Nome');
            Session::forget('iduser');
            Session::forget('Debug');
            $cookie = Cookie::forget('Nome');
            $cookie = Cookie::forget('iduser');
            Auth::logout();
        }
    }
    if ($Teste==1) {
        Session::put('Teste', 1);
    } else {
        Session::forget('Teste');
    }
    $vApp = "0";
    if (isset($_GET['CEP'])) {
        $cep=$_GET['CEP'];
    }
    if ($cep=='') {
        $cep = Session::get('CEP');
        if (Session::has('LAT')) {
            $lat = Session::get('LAT');
            $long = Session::get('LONG');
        }
    }
    if ($adm==true) {
        $mensagem = Lang::get('messages.caso');
    }
    else {
        $mensagem = Lang::get('messages.informe');
    }
    if ($lat=='') {
        $req = 'required';
    }
    if (isset($_GET['PESQ'])) {
        $pesq = $_GET['PESQ'];
    }
    $Tpe=0;
    if (isset($_GET['Tpe'])) {
        $Tpe = $_GET['Tpe'];
    }
    $CatProd=0;
    if (isset($_GET['CatProd'])) {
        $CatProd = $_GET['CatProd'];
    }
    $cProd = new App\Produtos;
    $idPesq = 0;
    if (($pesq>'') || ($CatProd>0)) {
        if ((strlen($pesq)>1) || ($CatProd>0)) {
            if ($lat=='') {
                if ($cep>'')  {
                    $ultcep = '';
                    if (Session::has('ULTCEP')) {
                        $ultcep = Session::get('ULTCEP');
                    }
                    $cepSes='';
                    if (Session::has('CEP')) {
                        $cepSes = Session::get('CEP');
                    }
                    $idPais = Session::get('pais');
                    $cCep = new App\Cep;
                    $status= $cCep->GetCoordenadas($cep, $ultcep, $idPais, $cepSes, $idUser, 'prod');
                    if ($status=="OK") {
                        $lat = $cCep->getLat();
                        $long = $cCep->getLong();
                        $cep_tmp = $cCep->getcep_tmp();
                        Session::put('LAT', $lat);
                        Session::put('LONG', $long);
                        Session::forget('LOCAL');
                        Session::put('CEP', $cep);
                        Session::put('ULTCEP', $cep_tmp);
                        Session::put('CID', $cCep->getIdCidade());
                        Session::put('BAI', $cCep->getBairro());
                    }
                    /* else {
                        echo "<div class='alert alert-danger'>".$status."</div>";
                        if ($status==Lang::get('cep.demais')) {
                            echo "<div class='alert alert-info'>Tente novamente amanh&atilde; ou utilize um CEP</div>";
                        }
                    } */
                }
            }
        }
        $idForn=0;
        if (isset($_REQUEST['ped'])) {
            $idPed = $_REQUEST['ped'];
            $idForn = Session::get('Fornec');
        }

        $idPesq = $cProd->Procura($pesq, $cep, $lat, $long, $Teste, $idForn, $CatProd);
        $cid = $cProd->getCid();
        if ($cid>'') {
            $lstLojas = $cProd->getLojas();
            /* if ($lstLojas>'') {
                $procurar = 1;
            } */
        }
    }
    $UA = $_SERVER['HTTP_USER_AGENT'];
    if (strrpos($UA, "Windows")) {
        $BRO = "BRO=PC";
        $TamPesq = 40;
    } else {
        $BRO = "BRO=AN";
        $TamPesq = 30;
    }

    if ($pesq>'') {
        // if ($procurar==1) {

        $cProd->GetResultados($idPesq, $logado, $BRO, $CatProd);
        $Tpe = $cProd->GetTpe();
        $qtItens=$cProd->Qtd();
    }

    if (isset($_REQUEST['erro'])) {
        echo "<div class='alert alert-info'><h3>Senha Inválida</h3></div>";
    }
    if (isset($_REQUEST['s'])) {
        if (Session::Has('forn')) {
            DB::update("update empresa set dtON = null where idEmpresa = ".Session::get('forn'));
        }
        Session::forget('Nome');
        Session::forget('iduser');
        Session::forget('Debug');
        Session::forget('PEDIDO');
        $cookie = Cookie::forget('Nome');
        $cookie = Cookie::forget('iduser');
        Auth::logout();
    }

    ?>
    <form name="formPesq" action="https://tele-tudo.com/produtos" method="get">
        <input name='CatProd' id='CatProd' type='text' hidden='hidden' value='' />
        {{--<div class='alert alert-info'>Site em modo de teste</div>--}}
        </p>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">

            <?php
            if ($BRO == "BRO=PC") {
                echo "<tbody><tr>";
                echo "<td valign='top' width='321px'>";
            }

            ?>

            @if ($qtItens==0)
                <table align="center" border="0" width="400" cellspacing="0" cellpadding="0">
		        <thead>
                    	<tr>
                        <td>
                        <span style="opacity:0.0;">
                      
                        
                            <!--{{ $password = Request::input('password') }}-->
                        	{{ $User = isset($_POST['User'])?$_POST['User']:null }}    
                        </span>
                        	@if($User != '')
                            		<label for="">Parabéns, {{ $User }} pela compra de {{ $Descricao }} no valor de {{ $Valor }}, continue comprando com a gente!</label>
                        	@else
                            		{{ '' }}
                        	@endif
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>

                            <img border="0" src="{{asset('resources/assets/img/indexcep.png')}}" width="400" height="60"></td>
                    </tr>
                    <tr>
                        <td background="{{asset('resources/assets/img/fundo%20menu.png')}}" align="center">
                            <p>
                                <input type="text" name="CEP" id="CEP" autofocus="" enabled="false" required value="{{$cep}}" size="20"></p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <img border="0" src="{{asset('resources/assets/img/indexcep2.png')}}" width="400" height="10"></td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <table align="center" border="0" width="400" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <p align="center">
                                <img border="0" src="{{asset('resources/assets/img/indexdesc.png')}}" width="400" height="60"></p>
                        </td>
                    </tr>
                    <tr>
                        <td background="{{asset('resources/assets/img/fundo%20menu.png')}}">
                            <p align="center">&nbsp;
                                <input type="search" results="10" value="{{$pesq}}" enabled="false" required="" placeholder="Informe a mercadoria que deseja comprar" name="PESQ" id="PESQ" size="{{$TamPesq}}">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p align="center">
                                <img border="0" src="{{asset('resources/assets/img/indexpesq2.png')}}" width="400" height="5"></p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <p align="center">
                                <img border="0" src="{{asset('resources/assets/img/indexpesq.png')}}" width="400" height="55"></p>
                        </td>
                    </tr>
                    <tr>
                        <td background="{{asset('resources/assets/img/fundo%20menu.png')}}">
                            <p align="center">&nbsp;
                                <img border="0" src="{{asset('resources/assets/img/btpesq.png')}}" width="160" onclick="aciona()" style="cursor:hand" height="28"></p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p align="center">
                    <?php
                    $ConsCat = DB::table('categoriasprodutos')
                        ->select('ID','ImgCad')
                        ->orderby('Descricao')
                        ->get();
                    $ret ='';
                    foreach ($ConsCat as $Catego) {
                        if  ($Catego->ID>1) {
                            $img = asset('resources/assets/img/'.$Catego->ImgCad);
                            $ret.="<img border='0' src='".$img."' style='cursor:hand; width='100px'; height=100px;' onclick='ClicCat(".$Catego->ID.")' >";
                        }
                    }
                    echo $ret;
                    ?>
                </p>
                </td>
                <td></td>
                </tr>
                </tbody>
        </table>
    </form>
    @else
        <p align="center"><br>
            <input type="search" results="10" value="{{$pesq}}" enabled="false" required="" placeholder="Informe a mercadoria" name="PESQ" id="PESQ" size="20"></p>
        <p align="center">
            <img border="0" src="{{asset('resources/assets/img/btpesq.png')}}" width="160" onclick="aciona()" style="cursor:hand" height="28"><br> &nbsp;
        </p>
        <p align="center">
            <?php
            echo $cProd->MostraCategos();
            ?>
        </p>
        </td>
        {{$cProd->MostraResultado()}}
        </tr>
        </tbody>
        </table>
        <?php

        $idPed=0;
        if (isset($_REQUEST['ped'])) {
            $idPed = $_REQUEST['ped'];
            echo "<input name='ped' type='text' hidden='hidden' value='".$idPed."' /></p>";
            echo "<input name='f' type='text' hidden='hidden' value='".$idForn."' /></p>";
        }
        if ($idPed>0) {
        $cEnt = new App\Entrega();
        $Mais = $cEnt->getValorTotal($idPed);
        $vMais = number_format($Mais, 2, ',', '.');
        $idFornProd = Session::get('Fornec');
        ?>
        <script>
            VlrPedAnt = <?php echo $Mais; ?>;
            idPedAnt = <?php echo $idPed; ?>;
            var sForn = <?php echo $idFornProd; ?>;
            PedAnt = "&ped="+idPedAnt;
            Forn = "&f="+sForn;
            AtualizaTotal(VlrPedAnt);
        </script>
        <?php
        echo "<div class='alert alert-success'>Valor do pedido até agora: R$ ".$vMais."</div>";
        echo "<p><div class='alert alert-info'>Itens adicionais somente do mesmo fornecedor</div><p>";
        } else {
        if ($qtItens==1) {
        $VlrTotUm = $cProd->VlrTotUm();
        ?>
        <script>
            var VlrTotUm = <?php echo $VlrTotUm; ?>;
            AtualizaTotal(VlrTotUm);
            EsseFor = document.getElementById("txFor1").value;
            var ID = document.getElementById("txID1").value;
            var quant = document.getElementById("txQt1").value;
            Adiciona(1, ID, quant);
        </script>
        <?php
        }
        }
        ?>
        </form>
        </div>
        <p>&nbsp;</p>
        </div>
    @endif
    <?php
    $urlredir='';
    if ($qtItens==0) {
        if ($pesq>"") {
            if (strlen($pesq)==1) {
                echo "<div class='alert alert-danger'>Pesquisa muito pequena</div>";
            } else {
                if ($cProd->getLojasNaCidade()) {
                    echo "<div class='alert alert-danger'><font size='5'>Não há lojas abertas vendendo essa mercadoria, na sua região</font></div>";
                } else {
                    echo "<div class='alert alert-danger'><font size='5'>Não existe fornecedores cadastrados na sua cidade[1]</font></div>";
                    echo "<div class='alert alert-info'><font size='5'>Caso queira ser um fornecedor faça um cadastro e nos informe via email xeviousbr@gmail.com</font></div>";
                }
            }
        }
        $cat=0;
        /* if ($idPesq>0) {
            echo "$idPesq>0"; die;
            $cat = $cServ->ProcuraServico($pesq, $lat, $long, $idPesq);
        } */
        /* if ($cat>0) {
            $echo "cat>0"; die;
            $urlredir="http://www.tele-tudo.com/servicos?CEP=".$cep."&Cat=".$cat;
        } */
    }
    ?>
    <script>
        function Enviar() {
            var Quant = ArrIts.length;
            if (Quant==0) {
                alert('Selecione o que deseja comprar antes');
            } else {
                txtenviar='';
                var vlr = 0;
                var vlrIt=0;
                var Tpe = '';
                for (var i = 1; i < (Quant+1); i++) {
                    txtenviar+="&q"+i+"="+ArrIts[i-1].qua;
                    txtenviar+="&p"+i+"="+ArrIts[i-1].cod;
                    vlrIt = ArrIts[i-1].qua*ArrIts[i-1].vlr;
                    Tpe = ArrIts[i-1].Tpe;
                    vlr+=vlrIt;
                }
                if (Tpe<'0') {
                    Tpe = 3;
                }
                var CEP = <?php echo $cep; ?>;

                txtenviar+="&Qtd="+Quant;
                txtenviar+="&t="+Tpe;
                txtenviar+='&f='+EsseFor;
                txtenviar+=PedAnt;
                txtenviar+="&r="+idrede;
                txtenviar+="&v="+sValor;
                txtenviar+="&c="+CEP;

                if (idrede>0) {
                    document.location.assign('http://tele-tudo.com/criapedido?'+txtenviar);
                } else {
                    if (Logado>0) {
                        if (CliSemEnder>0) {
                            document.location.assign('http://tele-tudo.com/entrega/ender');
                        } else {
                            document.location.assign('http://tele-tudo.com/entrega?'+txtenviar);
                        }
                    } else {
                        document.location.assign('http://tele-tudo.com/criapedido?'+txtenviar);
                    }
                }
            }
        }
    </script>
@stop