<?php $idUser = 0; ?>
@extends('layouts.padrao')
<title>Tele Tudo - Produtos - Confirmação da Compra - Sem Login</title>
@section('content')
    <?php
    $tpEnt1 = $_REQUEST['t'];
    $forn = $_REQUEST['f'];
    Session::put('FORN', $forn);
    $cPed = new App\Pedido();
    $Teste=0;
    if (Session::get('iduser')==21) {
        $Teste = 1;
    }
    $cSessao = new App\Sessao();
    $CriarPedido=false;
    if (isset($_REQUEST['ped'])) {
        $idPedido = $_REQUEST['ped'];
    } else {
        $cPed->CriaPedido(0, $Teste, $tpEnt1);
        $idPedido = $cPed->getIdPedido($Teste);
    }
    $tpEnt2 = $cSessao->tpEntrega($idPedido);
    if ($tpEnt2==3) {
        $QtdItens = $_REQUEST['Qtd'];
        if ($QtdItens==0) {$QtdItens = 1;}
        $QtdItens++;
        $ClsItens = new App\PedidoItens();
        $idFornProd = 0;
        $MaisDeUmFor=0;
        for ($i=1;$i<$QtdItens;$i++) {
            $p=$_REQUEST['p'.$i];
            $ClsItens->setIdProd($p);

            $EsseidFornProd = $ClsItens->getidFornProd();

            if ($idFornProd>0) {
                if ($EsseidFornProd!=$EsseidFornProd) {
                    $MaisDeUmFor=1;
                    break;
                }
            }
        }
        $sValor = $_REQUEST['v'];
        Session::put('ValorPed', $sValor);
        if ($MaisDeUmFor==0) {
            $Cons = DB::table('empresa')
                ->select('site')
                ->where('idEmpresa', '=', $EsseidFornProd)
                ->first();
            $url = $Cons->site;
            if ($url=="") {
                echo "<div class='alert alert-danger'>Fornecedor sem site definido</div>";
            } else {
                echo "<div class='alert alert-sucess'>Voce será direcionado a página do vendedor[1]</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>Mais de um fornecedor selecionado</div>";
            $url = "https://www.tele-tudo.com";
        }
    } else {
        Session::put('PEDIDO', $idPedido);
        Session::put('TpEntrega', $tpEnt2);
        $QtdItens = $_REQUEST['Qtd'];
        $QtdItens++;
        for ($i=1;$i<$QtdItens;$i++) {
            if ($Teste==0) {
                $ClsItens = new App\PedidoItens();
                $q=$_REQUEST['q'.$i];
                $ClsItens->setQtd($q);
                $p=$_REQUEST['p'.$i];
                $ClsItens->setIdProd($p);
                $ClsItens->Add($idPedido);
            } else {
                $ClsItens = new App\PedidoItens();
                break;
            }
        }
        $idFornProd = $ClsItens->getidFornProd();
        Session::put('Fornec',$idFornProd);
        $url="login";
        $idRede=$_REQUEST['r'];
        if ($idRede>0) {
            $Cons = DB::table('users')
                ->select('id','Nome')
                ->where('RedeID', '=', $idRede)
                ->first();
            if ($Cons==null) {
            $url="loginrede";

            // obterDados.php
            ?>
            <form action="loginrede" method=post name="teletudopede">
                <input type="hidden" name="id" value="{{$idRede}}">
                <input type="hidden" name="idPedido" value="{{$idPedido}}">
            </form>
            <script language="javascript" type="text/javascript">
                document.teletudopede.submit();
            </script>
            <?php
            exit(0);
            } else {
                $idUser=$Cons->id;
                $Nome=$Cons->Nome;
                Session::put('idRede',$idRede);
                Auth::loginUsingId($idUser);
                $cEntrega = new App\Entrega();
                DB::update("update pedido set User = '".$idUser."' where idPed = ".$idPedido);
                $idEntrega = $cEntrega->CriaRegistro($idPedido, $idUser, $Teste, $tpEnt2);
                $cEntrega->setidEntrega($idEntrega);
                $VlrOrc = $cEntrega->PedeOrcamento($idPedido, $Teste, $tpEnt2);

                if ($VlrOrc>0) {

                $cEntrega->setVlrEntrega($VlrOrc);
                Session::put('ENTREGA', $idEntrega);

                if ($tpEnt2>0) {
                    Session::put('Kms',$cEntrega->getKms());
                    Session::put('TmpPrevisto',$cEntrega->getTmpPrevisto());
                }
                ?>
                <div class="alert alert-info">{{ 'Usuario Logado: '.$Nome }}</div>
                <script language="javascript" type="text/javascript">
                    var idPedido = <?php echo $idPedido; ?>;
                    document.location.assign("https://www.tele-tudo.com/confirma?IDPED="+idPedido);
                </script>
                <?php
                } else {
                    echo 'Não foi possível obter a informação referente a Tele-Entrega[2]';
                }
                exit(0);
            }
        } else {
            $url="login";
        }
    }
    if ($url>"") {
        ?>
        <script language="javascript" type="text/javascript">
            document.location.assign("{{$url}}");
        </script>
        <?php
    }
?>
@stop