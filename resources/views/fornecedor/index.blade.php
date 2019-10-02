<?php
$idUser = 0;
?>
<style type="text/css">
    .centro {
        text-align: center;
    }
</style>
@extends('layouts.padrao')
<title>Tele-Tudo: Fornecedores</title>
@section('content')
    <?php
    if (Auth::check() == false) {
        echo "<div class='alert alert-danger'><font size='5'>Esta área é apenas para usuários registrados</font></div>";
    } else {
        $iduser = 0;
        $iduser = Auth::id();
        /* if ($iduser == 3) {
            $iduser = 28;
            Session::put('iduser', $iduser);
        } */
        $qry = DB::table('users')->join('empresa', 'empresa.idPessoa', '=', 'users.id')->select('empresa.idEmpresa', 'empresa.Empresa', 'empresa.tpEntrega', 'empresa.DiaAcerto', 'empresa.site', 'users.user', 'categoriasempresas_ID', 'idEntrega')->where('users.id', '=', $iduser)->get();
        if ($qry != null) {
            $idEmpresa = $qry[0]->idEmpresa;
            $Nome = $qry[0]->Empresa;
            $user = $qry[0]->user;
            $DiaAcerto = $qry[0]->DiaAcerto;
            $site = $qry[0]->site;
            $catEmpr = $qry[0]->categoriasempresas_ID;
            $idEntrega = $qry[0]->idEntrega;
            $tpEntrega = $qry[0]->tpEntrega;
        } else {
            echo "<div class='alert alert-danger'><font size='5'>Esta área é apenas para fornecedores</font></div>";
            exit(0);
        }
        Session::put('SemChat', 1);
        $tpEntrega = $tpEntrega;
        $PodeOnLine = 1;
        $Nome = $Nome;
        $qry = DB::table('conta')->select('Saldo', 'Pendente')->where('idPessoa', '=', $iduser)->get();
        if ($qry == null) {
            DB::insert("insert into conta (idPessoa, Saldo) values (?, ?)", [$iduser, 0]);
            $Saldo = 0;
            $Pendente = 0;
        } else {
            $Saldo = $qry[0]->Saldo;
            $Pendente = $qry[0]->Pendente;
        }
        $this->vRepasse = $Pendente;
        $Saldo = number_format($Saldo, 2, ',', '.');
        $cons = DB::table('config')->select('Modo', 'NrIdFace')->where('ID', '=', 1)->first();
        $sModo = "";
        $this->NrIdFace = $cons->NrIdFace;
        switch ($cons->Modo) {
            case 1:
                $sModo = "Teste - Simulado";
                break;
            case 2:
                $sModo = "Teste";
                break;
            case 3:
                $sModo = "Produção";
                break;
        }
        $Modo = $sModo;
        if ($Modo != "Produção") {
            echo "Estamos realizando ajustes no sistema";
        }
        $UA = $_SERVER['HTTP_USER_AGENT'];
        if (strrpos($UA, "Windows")) {
            $TG = 5;
            $TP = 3;
        } else {
            $TG = 4;
            $TP = 2;
        }
        echo "<p><div class='alert alert-info'><size='" . $TG . "'>Fornecedor: " . $Nome . "</font><p></div>";
        $qry = DB::table('empresa')->select('logradouro.adic')->join('endereco', 'endereco.ID', '=', 'empresa.idEndereco')->join('logradouro', 'logradouro.ID', '=', 'endereco.Logradouro_ID')->where('empresa.idEmpresa', '=', $idEmpresa)->first();
        if ($qry->adic == 0) {
            DB::update("update empresa set dtON = now(), TpAcesso = 0 where idEmpresa = " . $idEmpresa);
        } else {
            echo "<p><div class='alert alert-danger'>Seu endereço ainda não foi validado, aguarde atualização</div><p>";
        }
        if ($tpEntrega == 0) {
            echo "<p><div class='alert alert-success'><size='" . $TG . "'>Saldo Atual em Conta: R$: " . $Saldo . "</font></div><p>";
            $TituloGrid = "Informação de transferência de valor";
        } else {
            echo "<p><div class='alert alert-success'><size='" . $TG . "'>Vendas realizadas até o momento: R$: " . $Saldo . "</font></div><p>";
            $cForn = new App\Fornecedor();
            $VlrRepasse = $cForn->getRepasse();
            if ($VlrRepasse > 0) {
                $DataRepasse = $cForn->getDataRepasse();
                echo "<p><div class='alert alert-warning'><size='" . $TG . "'>Valor a ser repassado a Tele-Tudo.com : R$ " . $VlrRepasse . "</div><p>";
            } else {
                echo "<p><div class='alert alert-warning'><size='" . $TP . "'Valor a ser repassado a Tele-Tudo.com : R$ " . $VlrRepasse . "</font></div><p>";
            }
        }
    }
    ?>
    <div class="panel-body">
        <button type="button" onclick="produtos()" class="btn btn-primary">Editar Produtos</button>
        <button type="button" onclick="entregas()" class="btn btn-success">Entregas</button>
        <button type="button" onclick="contasbancarias()" class="btn btn-info">Contas Bancárias</button>
        <button type="button" onclick="rede()" class="btn btn-secondary">Perfil na Rede</button>
        &nbsp;
        <button type="button" onclick="minhapag()" class="btn btn-danger">Minha Loja</button>
        <button type="button" onclick="minhaLoja()" class="btn btn-primary">Configurações da Loja</button>
        <button type="button" onclick="edtminhapag()" class="btn btn-warning">Editar Página</button>
        &nbsp;
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
                $cFin = new App\Financeiro();
                $cFin->Visualizou($id, $idPed);
                break;
            case 2:
                // Financeiro: Confirmou
                $idTrans = 0;
                if (isset($_GET['idTrans'])) {
                    $idTrans = $_GET['idTrans'];
                }
                $cFin = new App\Financeiro();
                $cFin->Confirmou($id, $idPed, $idTrans);
                break;
            case 3:
                // Fornecedor: Visualizou
                // $cForn1 = new App\Fornecedor();
                $cForn->Visualizou($id, $idPed);
                echo "<script>window.open('https://www.tele-tudo.com/public/pedido/" . $idPed . "', '_blank'); </script>";
                break;
        }
    }
    $sql = "SELECT dtON, SUBTIME( Now( ) , '00:02:30' ) DtX, Som ";
    $sql.= "FROM config ";
    $sql.= "Where ID = 1 ";
    $ConsA = DB::select(DB::raw($sql));
    if ($ConsA[0]->Som == 1) {
        $som = "https://www.tele-tudo.com/mapa/Voz.mp3";
    } else {
        $som = "";
    }
    $VerTrans = false;
    if ($tpEntrega < 2) {
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
            <h2>Informação de recebimento de valores</h2>
            <form>
                <?php
                Session::put('SemChat', 1);
                Session::put('forn', $idEmpresa);
                $regs = DB::table('notificacao')
                    ->join('pedido', 'pedido.idPed', '=', 'notificacao.idPedido')
                    ->join('users as PC', 'PC.id', '=', 'pedido.User')
                    ->leftJoin('vlrtransf', 'vlrtransf.ID', '=', 'notificacao.idTransf')
                    ->leftJoin('contasbancarias', 'contasbancarias.id', '=', 'vlrtransf.idConta')
                    ->leftJoin('bancos', 'bancos.cod', '=', 'contasbancarias.idBanco')
                    ->select('notificacao.Valor', 'notificacao.Hora', 'notificacao.idAviso', 'notificacao.vizualizado', 'notificacao.Confirmado',
                        'PC.Nome', 'PC.fone',
                        'vlrtransf.ID as idTrans', 'vlrtransf.BCO', 'vlrtransf.AGE', 'vlrtransf.CTA',
                        'pedido.idPed', 'pedido.status as stPedido',
                        'bancos.banco', 'bancos.apelido')
                    ->where('notificacao.idFornec', '=', $idEmpresa)
                    ->get();
                $Agora = date("Y-m-d H:i:s");
                $HouveVenda = 0;
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
                                echo "<td style='width: 50px; text-align: center;'><input type='button' value='Confirmar' onclick='ClicConfirmei(" . $reg->idAviso . "," . $reg->idPed . "," . $reg->idTrans . ")'</td>";
                            } else {
                                // CONFIRMADO
                                echo '<td style="width: 60px; text-align: center;">' . substr($reg->vizualizado, 10, 19) . '</td>';
                                echo "<td style='width: 50px; text-align: center;'><input type='button' value='Visualizar' onclick='VerVenda(" . $reg->idAviso . "," . $reg->idPed . ")'</td>";
                            }
                        }
                    }
                    echo '</tr>';
                }
                if ($HouveVenda > 0) {
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
                        $cForn = new App\Fornecedor();
                        $regs = $cForn->getNotificacoes();
                        $Agora = date("Y-m-d H:i:s");
                        foreach ($regs as $reg) {
                            echo '<tr>';
                            echo '<td style="width: 50px">' . $reg->Nome . '</td>';
                            echo '<td style="width: 50px; text-align: right;">R$ ' . number_format($reg->Valor, 2, ',', '.') . '</td>';
                            echo '<td style="width: 60px; text-align: center;">' . substr($reg->Hora, 11, 8) . '</td>';
                            echo '<td style="width: 50px; text-align: center;">' . $reg->fone . '</td>';
                            if ($reg->stPedido == 5) {
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
                        ?>
                        </tbody>
                    </table>
                </form>
                <?php
                $sql = "SELECT notificacao.idPedido, notificacao.Hora ";
                $sql.= "FROM notificacao ";
                $sql.= "Inner Join pedido on pedido.idPed = notificacao.idPedido and pedido.status < 5 ";
                $sql.= "Where notificacao.idFornec = " . $idEmpresa;
                $sql.= " and SUBTIME( Now( ) , '23:59:59' ) < notificacao.Hora ";
                $sql.= " order by notificacao.Hora desc ";
                $Cons = DB::select(DB::raw($sql));
                if ($Cons == null) {
                    $idUltPedDia = 0;
                } else {
                    $idUltPedDia = $Cons[0]->idPedido;
                }
                if ($idUltPedDia > 0) {
                $RegUltCompra = $cForn->getUltCompra($idUltPedDia);
                $cEnd = new App\Enderecos();
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
                            $cPed = new App\Pedido();
                            $RegItens = $cPed->getRgItens($idUltPedDia);
                            $ret = '';
                            foreach ($RegItens as $Reg) {
                                $ret.= "<tr>";
                                $ret.= "<td class='centro'><strong>" . $Reg->Nome . "</strong></td>";
                                $ret.= "<td class='centro'><strong>" . $Reg->quant . "</strong></td>";
                                $ret.= "</tr>";
                                echo $ret;
                            }
                            ?>
                        </table>
                    </form>
                    <?php
                    }
                    // $site = $cForn->getEnderSite();

                    ?>
                    <script>

                        function ClicVisualizei(id, idPed) {
                            // alert('aqui');
                            document.location.assign("https://www.tele-tudo.com/public/fornecedor?op=1&id=" + id + "&idPed=" + idPed);
                        }

                        function ClicConfirmei(id, idPed, idTrans) {
                            document.location.assign("https://www.tele-tudo.com/public/fornecedor?op=2&id=" + id + "&idPed=" + idPed + "&idTrans=" + idTrans);
                        }

                        function VerVenda(id, idPed) {
                            // alert('x');
                            document.location.assign("https://www.tele-tudo.com/public/fornecedor?op=3&id=" + id + "&idPed=" + idPed);
                        }

                        function recarrega() {
                            document.location.assign("https://www.tele-tudo.com/public/fornecedor");
                        }

                        function produtos() {
                            window.open('https://www.tele-tudo.com/public/Cadastro', '_blank');
                        }

                        function compras() {
                            window.open('https://www.tele-tudo.com', '_blank');
                        }

                        function contasbancarias() {
                            window.open('https://www.tele-tudo.com/public/contasbancarias', '_blank');
                        }

                        function rede() {
                            document.formulario.submit();
                        }

                        function entregas() {
                            window.open('https://www.tele-tudo.com/confgentrega', '_blank');
                        }

                        function minhapag() {
                            if ("{{$site}}"=="") {
                                alert('É necessário configurar o site');
                                window.open('fornecedor/create', '_blank');
                            } else {
                                window.open('{{$site}}', '_blank');
                            }
                        }

                        function edtminhapag() {
                            window.open('https://www.tele-tudo.com/editapagina', '_blank');
                        }

                        function minhaLoja() {
                            window.open('https://www.tele-tudo.com/fornecedor/create', '_blank');
                        }

                    </script>
                    <?php
                    $nmUser = $user;
                    ?>
                    <form action="https://intonses.com.br/rede/loginTeleTudo.php" method="post" name="formulario" target="_blank" ENCTYPE="multipart/form-data">
                        <input name="User" type="hidden" value="{{$nmUser}}">
                        <input name="Tipo" type="hidden" value="fornecedor">
                    </form>
                    <script type="text/javascript">setTimeout(recarrega, 90000);</script>
@stop