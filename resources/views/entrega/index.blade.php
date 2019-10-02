<?php
$idUser = 0;
?>
@extends('layouts.padrao')
<title>Tele-Tudo: Fornecedores</title>
@section('content')
<style type="text/css">
    .centro {
        text-align: center;
}
</style>
<?php
if (Auth::check() == false) {
    echo "<div class='alert alert-danger'><font size='5'>Esta área é apenas para usuários registrados</font></div>";
} else {
    $iduser = 0;
    $iduser = Auth::id();

    /* if ($iduser == 3) {
        // $iduser=38;   // Ricardo
        // $iduser=203;   // Peixaria Vitória
        // $iduser=31;   // Coqueiro
        // $iduser=202;   // ReiDoDog
        // $iduser=40;   // Roger
        // $iduser=11;   // Airton
        // $iduser=22;   // Maicon - NewInfo
        // $iduser=126; // Clovis
        // $iduser=200; // rancho
        // $iduser=45; // Agápio
        // $iduser=265; // TeleRefeições
        $iduser=269;    // Luciano

        // Session::put('idPedido', 403);

        Session::put('iduser', $iduser);
    } */

    $cForn = new Fornecedor();
    if ($cForn->SetIdPessoa($iduser) == false) {
        echo "<div class='alert alert-danger'><font size='5'>Esta área é apenas para fornecedores</font></div>";
        exit(0);
    } else {
    Session::put('SemChat', 1);
    $cSessao = new Sessao;
    $tpEntrega = $cForn->gettpEntrega();
    $PodeOnLine = 1;
    $Nome = $cForn->getNome();
    $Saldo = $cForn->getSaldo();
    $Modo = $cSessao->Modo();
    // echo '50';
    if ($Modo != "Produção") {
        echo "Estamos realizando ajustes no sistema";
    }
    $UA = $_SERVER['HTTP_USER_AGENT'];
    if (strrpos($UA, "Windows")) {
        $TG=5;
        $TP=3;
    } else {
        $TG=4;
        $TP=2;
    }
    echo "<p><div class='alert alert-info'><size='".$TG."'>Fornecedor: " . $Nome . "</font><p></div>";
    if ($cForn->OnLine()==0) {
        echo "<p><div class='alert alert-danger'>Seu endereço ainda não foi validado, aguarde atualização</div><p>";
    }
    if ($tpEntrega == 0) {
        echo "<p><div class='alert alert-success'><size='".$TG."'>Saldo Atual em Conta: R$: " . $Saldo . "</font></div><p>";
        $TituloGrid = "Informação de transferência de valor";
    } else {
        echo "<p><div class='alert alert-success'><size='".$TG."'>Vendas realizadas até o momento: R$: " . $Saldo . "</font></div><p>";
        $VlrRepasse = $cForn->getRepasse();
        if ($VlrRepasse > 0) {
            $DataRepasse = $cForn->getDataRepasse();
            echo "<p><div class='alert alert-warning'><size='".$TG."'>Valor a ser repassado a Tele-Tudo.com : R$ " . $VlrRepasse . "</div><p>";
        } else {
            echo "<p><div class='alert alert-warning'><size='".$TP."'Valor a ser repassado a Tele-Tudo.com : R$ " . $VlrRepasse . "</font></div><p>";
        }
    }
    ?>
    <div class="panel-body">
        <button type="button" onclick="produtos()" class="btn btn-primary">Editar Produtos</button>
        <button type="button" onclick="entregas()" class="btn btn-success">Entregas</button>
        <button type="button" onclick="contasbancarias()" class="btn btn-info">Contas Bancárias</button>
        <button type="button" onclick="rede()" class="btn btn-warning">Perfil na Rede</button>
        <button type="button" onclick="compras()" class="btn btn-primary">Comprar</button>
    </div>
    <?php
    if (isset($_GET['op'])) {
        $op = $_GET['op'];
        $id = $_GET['id'];
        $idPed = $_GET['idPed'];
        switch ($op) {
            case 1:
                // Financeiro: Visualizou
                $cFin = new Financeiro();
                $cFin->Visualizou($id, $idPed);
                break;
            case 2:
                // Financeiro: Confirmou
                // $idPed = $_GET['idPed'];
                $idTrans = $_GET['idTrans'];
                $cFin = new Financeiro();
                $cFin->Confirmou($id, $idPed, $idTrans);
                break;
            case 3:
                // Fornecedor: Visualizou
                // $idPed = $_GET['idPed'];
                $cForn->Visualizou($id);
                echo "<script>window.open('https://www.tele-tudo.com/pedido/" . $idPed . "', '_blank'); </script>";
                break;
        }
    }
    if ($cSessao->Som() == 1) {
        $som = "https://www.tele-tudo.com/mapa/Voz.mp3";
    } else {
        $som = "";
    }
    $VerTrans = false;
    if ($tpEntrega == 0) {
    ?>
    <form name="Form1"
          action="https://www.tele-tudo.com/fornecedor"
          method="get" target="_self">
        <br>
        <table class="table table-striped table-bordered">
            <tr>
                <td class="centro">Conta</td>
                <td class="centro">Comprador</td>
                <td class="centro">Valor</td>
                <td class="centro">Hora</td>
                <td class='centro'>Banco</td>
                <td class='centro'>Agência</td>
                <td class='centro'>Conta</td>
                <td class="centro">Telefone</td>
                <td class="centro">Visto</td>
                <td class="centro">Status</td>
            </tr>
            <tbody>
            <h2>Informação de recebimento de valores</h2>";
            <?php
            Session::put('SemChat', 1);
            $cFin = new Financeiro();
            $regs = $cFin->getTransferencias($cForn->getidEmpresa());
            $Agora = date("Y-m-d H:i:s");
            $HouveVenda=0;
            foreach ($regs as $reg) {
                // echo 'reg->idTrans = '.$reg->idTrans.'<Br>';
                echo '<tr>';
                $Banco = ($reg->apelido == null) ? $reg->banco : $reg->apelido;
                echo '<td style="width: 50px">' . $Banco . '</td>';
                echo '<td style="width: 50px">' . $reg->Nome . '</td>';
                echo '<td style="width: 50px; text-align: right;">R$ ' . number_format($reg->Valor, 2, ',', '.') . '</td>';
                echo '<td style="width: 60px; text-align: center;">' . substr($reg->Hora, 11, 8) . '</td>';
                echo '<td style="width: 60px; text-align: center;">' . $reg->BCO . '</td>';
                echo '<td style="width: 60px; text-align: center;">' . $reg->AGE . '</td>';
                echo '<td style="width: 50px; text-align: center;">' . $reg->CTA . '</td>';
                echo '<td style="width: 50px; text-align: center;">' . $reg->fone . '</td>';

                if ($reg->stPedido == 5) {
                    // CANCELADO
                    echo "<td style='width: 50px; text-align: center;'><input type='button' value='Visualizar' onclick='VerVenda(" . $reg->idAviso . ", " . $reg->idPed . ")'</td>";
                    echo '<td style="width: 100px; text-align: center;">CANCELADO</td>';
                } else {
                    if ($reg->vizualizado == null) {
                        // NÃO VISUALIZADO
                        echo "<td style='width: 50px; text-align: center;'><input type='button' value='Visualizei' onclick='ClicVisualizei(" . $reg->idAviso . ", " . $reg->idPed . ")'</td>";
                        echo '<td style="width: 100px; text-align: center;"></td>';
                        echo "<audio id='audio' autoplay='true'><source src='" . $som . "' type='audio/mp3'></audio>";
                        $HouveVenda++;
                    } else {

                        if ($reg->Confirmado == null) {
                            // VISUALIZADO MAS NÃO CONFIRMADO
                            echo '<td style="width: 60px; text-align: center;">' . substr($reg->vizualizado, 10, 19) . '</td>';
                            echo "<td style='width: 50px; text-align: center;'><input type='button' value='Confirmar' onclick='ClicConfirmei(" .
                                $reg->idAviso . "," .
                                $reg->idPed . "," .
                                $reg->idTrans . ")'</td>";
                        } else {
                            // CONFIRMADO
                            echo '<td style="width: 60px; text-align: center;">' . substr($reg->vizualizado, 10, 19) . '</td>';
                            echo "<td style='width: 50px; text-align: center;'><input type='button' value='Visualizar' onclick='VerVenda(" .
                                $reg->idAviso . "," .
                                $reg->idPed . ")'</td>";
                        }
                    }
                }
                echo '</tr>';
            }
            if ($HouveVenda>0) {
            ?>
            <script>
                document.title = "VENDA";
            </script>
            <?php
            }
            } else {
            ?>
            <form name="Form2"
                  action="https://www.tele-tudo.com/fornecedor"
                  method="get" target="_self">
                <br>
                <table class="table table-striped table-bordered">
                    <tr>
                        <td class="centro">Comprador</td>
                        <td class="centro">Valor</td>
                        <td class="centro">Hora</td>
                        <td class="centro">Telefone</td>
                        <td class="centro">Status</td>
                    </tr>
                    <tbody>
                    {{--<h2>Vendas sendo realizadas no momento</h2>--}}
                    <?php
                    $regs = $cForn->getNotificacoes();
                    $Agora = date("Y-m-d H:i:s");
                    foreach ($regs as $reg) {
                        echo '<tr>';
                        echo '<td style="width: 50px">' . $reg->Nome . '</td>';
                        echo '<td style="width: 50px; text-align: right;">R$ ' . number_format($reg->Valor, 2, ',', '.') . '</td>';
                        echo '<td style="width: 60px; text-align: center;">' . substr($reg->Hora, 11, 8) . '</td>';
                        echo '<td style="width: 50px; text-align: center;">' . $reg->fone . '</td>';
                        if ($reg->stPedido==5) {
                            echo "<td style='width: 50px; text-align: center;'><strong style='color: #0000FF'>Cancelado</strong></td>";
                        } else {
                            if ($tpEntrega == 0) {
                                if ($reg->vizualizado == null) {
                                    echo "<td style='width: 50px; text-align: center;'><strong style='color: #0000FF'>Aguardando confirmação do pagamento</strong></td>";
                                } else {
                                    if ($reg->Confirmado == null) {
                                        echo "<td style='width: 50px; text-align: center;'><strong style='color: #00FFFF'>Pagamento sendo conferido neste momento</strong></td>";
                                    } else {
                                        echo "<td style='width: 50px; text-align: center;'><input type='button' value='Ver venda' onclick='VerVenda(" . $reg->idAviso . "," . $reg->idPed . ")'</td>";
                                        echo "<audio id='audio' autoplay='true'><source src='" . $som . "' type='audio/mp3'></audio>";
                                    }
                                }
                            } else {
                                echo "<td style='width: 50px; text-align: center;'><input type='button' value='Ver venda' onclick='VerVenda(" . $reg->idAviso . "," . $reg->idPed . ")'</td>";
                                echo "<audio id='audio' autoplay='true'><source src='" . $som . "' type='audio/mp3'></audio>";
                            }
                        }
                        echo '</tr>';
                    }
                }
            }
        }
        ?>
        </tbody>
    </table>
</form>
<?php
$idUltPedDia = $cForn->getidUltPedDia();
// $idUltPedDia = 403;
if ($idUltPedDia>0) {
$RegUltCompra = $cForn->getUltCompra($idUltPedDia);
$cEnd = new Enderecos();
$Endereco = $cEnd->GetEndereco($RegUltCompra->Endereco_ID, 0);
$sValor = $RegUltCompra->Valor;
?>
<br>
<form action="https://www.tele-tudo.com/pedido/{{$idUltPedDia}}" name="Form3" target="_blank" >
    Dados da última venda:
    <br>
    <table class="table table-striped table-bordered">
        <tbody>
        <tr>
            <td class="centro">Comprador</td>
            <td class="centro">Valor</td>
            <td class="centro">Endereço</td>
            <td class="centro">Telefone</td>
            <td class="centro">Visualizar</td>
        </tr>
        <tr>
            <td class="centro">{{$RegUltCompra->Nome}}</td>
            <td class="centro">{{$sValor}}</td>
            <td class="centro">{{$Endereco}}</td>
            <td class="centro">{{$RegUltCompra->fone}}</td>
            <td class="centro"><input type="submit" value="visualizar"></td>
        </tr>
        </tbody>
    </table>
    <form name="Form4">
        Pedido Tele-Tudo de nr {{$idUltPedDia}}
        <br>
        <table class="table table-striped table-bordered">
            <tbody>
            <tr>
                <td class="centro">Produto</td>
                <td class="centro">Quantidade</td>
            </tr>
            </tbody>
            <?php
            $cPed = new Pedido();
            $RegItens = $cPed->getRgItens($idUltPedDia);
            $ret='';
            foreach ($RegItens as $Reg) {
                $ret.="<tr>";
                $ret.="<td class='centro'><strong>".$Reg->Nome."</strong></td>";
                $ret.="<td class='centro'><strong>".$Reg->quant."</strong></td>";
                $ret.="</tr>";
                echo $ret;
            }
            ?>
        </table>
    </form>
    <?php
    }
    ?>
<script>

    function ClicVisualizei(id, idPed) {
        // alert('aqui');
        document.location.assign("https://www.tele-tudo.com/fornecedor?op=1&id=" + id + "&idPed=" + idPed);
    }

    function ClicConfirmei(id, idPed, idTrans) {
        document.location.assign("https://www.tele-tudo.com/fornecedor?op=2&id=" + id + "&idPed=" + idPed + "&idTrans=" + idTrans);
    }

    function VerVenda(id, idPed) {
        // alert('x');
        document.location.assign("https://www.tele-tudo.com/fornecedor?op=3&id=" + id + "&idPed=" + idPed);
    }

    function recarrega() {
        document.location.assign("https://www.tele-tudo.com/fornecedor");
    }

    function produtos() {
        window.open('https://www.tele-tudo.com/Cadastro', '_blank');
    }

    function compras() {
        window.open('https://www.tele-tudo.com/produtos', '_blank');
    }

    function contasbancarias() {
        window.open('https://www.tele-tudo.com/contasbancarias', '_blank');
    }

    function rede() {
        document.formulario.submit();
    }

    function entregas() {
        // window.open('http://www.tele-tudo.com/tpentregaempresa', '_blank');
        window.open('https://www.tele-tudo.com/confgentrega', '_blank');
    }

</script>
<?php
$nmUser = $cForn->getUser();
// $nmUser = "Xevious";
?>
<script type="text/javascript">setTimeout(recarrega, 90000);</script>
<form action="http://intonses.com.br/rede/loginTeleTudo.php" method="post" name="formulario" target="_blank" ENCTYPE="multipart/form-data">
    <input name="User" type="hidden" value="{{$nmUser}}">
    <input name="Tipo" type="hidden" value="fornecedor">
</form>
@stop