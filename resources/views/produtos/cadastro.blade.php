<?php
$idUser=0;
?>
@extends('layouts.padrao')
<title>Cadastro de produtos</title>
@section('content')
    <?php
    if (Auth::check()==false) {
        echo "<div class='alert alert-danger'><font size='5'>Esta área é apenas para usuários registrados</font></div>";
    } else {

    $iduser = Auth::id();
    /* if ($iduser==3) {
        // $iduser=38;   // Coqueiro
        // $iduser=31;   // Coqueiro
        // $iduser = 26; // Agronilo
        // $iduser = 45; // Agapio
        // $iduser = 22; // NewInfo
        // $iduser = 40; // Roger
        // $iduser = 202; // ReiDoDog
        $iduser = 26; // Vladimir
    } */

    $cForn = new Fornecedor();
    // echo '15'; die;
    if (isset($_GET['op'])) {
        $op = $_GET['op'];

        $cProd = new Produtos();
        $pag = 1;
        $ComplPag = '';

        switch ($op) {
            case 1:
                $cProd->Deleta($_GET['del']);
                break;
            case 2:
                $n = $_GET['n'];
                $p = $_GET['p'];
                $i = $_GET['i'];
                $m = $_GET['m'];
                $d = $_GET['d'];
                $c = $_GET['c'];
                $cProd->Atualiza($i, $p, $n, $m, $d, $c);
                if (isset($_GET['pag'])) {
                    $pag = $_GET['pag'];
                    if ($pag>1) {
                        $ComplPag = '?pag='.$pag;
                    }
                }
                break;
            case 3:
                $cProd->Desativa($_GET['id']);
                break;
            case 4:
                $cProd->Ativa($_GET['id']);
                break;
        }

        ?>
        <script>
            document.location.assign('http://www.tele-tudo.com/Cadastro{{$ComplPag}}');
        </script>
        <?php
        exit(0);
    }
    ?>
    <div class="alert alert-minimal alert-warning nomargin">
        <button class="close" data-dismiss="alert">×</button>
        <h1><i class="fa fa-info"></i>Produtos registrados: </h1>
    </div>

    <div class="table-responsive">
        <?php
        $cForn->SetIdPessoa($iduser);
        $idEmpresa = $cForn->getidEmpresa();
        if (isset($_GET['pag'])) {
            $pag=$_GET['pag'];
        } else {
            $pag=1;
        }
        $busca='';
        if (isset($_GET['Busca'])) {
            $busca=$_GET['Busca'];
        }
        $regs = $cForn->ProdForn($pag, $busca);
        $UA = $_SERVER['HTTP_USER_AGENT'];
        if (strrpos($UA, "Windows")) {
            $QtdPPag=20;
        } else {
            $QtdPPag=10;
        }
        if (($cForn->getTotLista($busca)>$QtdPPag) || ($busca>'')) {
        ?>
        <form name="formulario" action="http://www.tele-tudo.com/Cadastro" method="get">
            <label id='Label1'>Busca: </label>
            <input name="Busca" type="text" style="width: 226px" value="{{$busca}}" />
            <input name='btBusca' type='submit' value='submit' />
        </form>
        <?php
        }
        ?>
        <table class="table table-bordered table-striped"  id="tabelaContas">
            <?php
            $cForn->Paginacao($pag, $QtdPPag);
            ?>
            <thead>
            <tr>
                <th><i class="fa fa-building pull-right hidden-xs"></i> Nome do produto</th>
                <th><i class="fa fa-building pull-right hidden-xs"></i> Descrição</th>
                <th><i class="fa fa-building pull-right hidden-xs"></i> Preço</th>
                <th><i class="fa fa-building pull-right hidden-xs"></i> Imagem</th>
                <th><i class="fa fa-building pull-right hidden-xs"></i> Operações</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($regs as $reg) {
                $nr = 0;
                echo "<tr>";
                if ($reg->Disponivel) {
                    $stI="<strong>";
                    $stF="</strong>";
                } else {
                    $stI="";
                    $stF="";
                }
                echo "<td id='tdB".$nr."'>".$stI.$reg->Nome.$stF."</td>";
                echo "<td id='tdB".$nr."'>".$stI.$reg->Descricao.$stF."</td>";
                echo "<td id='tdA".$nr."' align='right' >".$stI.$reg->Valor.$stF." </td>";
                $Descr = str_replace(",", " ", $reg->Descricao);
                $dados=$reg->Nome.';'.$reg->Valor.';'.$Descr.';'.$reg->Imagem.';'.$reg->CategoriasProdutos_ID;
                if ($reg->Imagem>'') {
                    $Imagem = $reg->Imagem;
                    $posH = strpos($Imagem,'http');
                    if ($posH === false) {
                        echo "<td width='100'><img src='http://".$Imagem."' style='height: 86px; width: 111px' /></td>";
                    } else {
                        echo "<td width='100'><img src='".$Imagem."' style='height: 86px; width: 111px' /></td>";
                    }

                    /* if ($posH==null) {
                        echo "<td width='100'><img src='http://".$Imagem."' style='height: 86px; width: 111px' /></td>";
                    } else {
                        echo "<td width='100'><img src='".$Imagem."' style='height: 86px; width: 111px' /></td>";
                    } */

                    /* if ($posH==0) {
                        echo "posH = ".$posH; die;
                        echo "<td width='100'><img src='".$Imagem."' style='height: 86px; width: 111px' /></td>";
                    } else {
                        echo "<td width='100'><img src='http://".$Imagem."' style='height: 86px; width: 111px' /></td>";
                    } */
                } else {
                    echo "<td></td>";
                }
                ?>
                <td align="center">
                    <div>
                        <input name="btEditar" type="button" onclick="edita({{$reg->ID}},'{{$dados}}')" value="Editar">
                        <input name="btExcluir" type="button" onclick="deletar({{$reg->ID}})" value="excluir">
                    </div>
                    @if($reg->Disponivel)
                        <input name="btDesativar" type="button" onclick="Desativar({{$reg->ID}})" value="Inativar">
                    @else
                        <input name="btAtivar" type="button" onclick="Ativar({{$reg->ID}})" value="Ativar">
                    @endif
                </td>
                </tr>
                <?php
                $nr++;
            }
            $Categorias = $cForn->getCatProds($idEmpresa);
            ?>
            </tbody>
        </table>
    </div>

    <form action="http://www.tele-tudo.com/produtos" method="post" id="prepara">
        <input type="hidden" name="Empresa" value="{{$idEmpresa}}">
        <input type="hidden" id="txId">
        <input type="hidden" name="pag" value="{{$pag}}">
        <div id="dvAdic"><h4>Adicionar</h4></div>
        <div id="dvEditar" style="visibility:hidden; display:none"><h4>Edição</h4></div>

        <label>Nome do produto: <input type="text" id="nome" name="nome"></label>
        <label>Descrição: <input type="text" id="desc" name="desc"></label><Br>
        <label>Preço do produto:<input type="number" step=".01" id="preco" name="preco" style="width: 57px" /></label>
        <label>Categoria:
            <select id="cbCat" name="cbCat" style="width: 300px" >
                {{$Categorias}}
            </select>
        </label><Br>
        <label>Endereço da imagem: <input id="tximagem" name="tximagem" type="text" style="width: 350px" onclick="this.select()" ></label>
        <label><input type="submit" name="salvar" id='btEnviar' onclick='AcionaEdicao()'  value="salvar"/></label>
    </form>

    <script>
        function AcionaEdicao() {
            if (document.getElementById("btEnviar").value=="Atualizar") {
                var pag = <?php echo $pag; ?>;
                var op="n="+document.getElementById("nome").value;
                op+="&p="+document.getElementById("preco").value;
                op+="&i="+document.getElementById("txId").value;
                op+="&m="+document.getElementById("tximagem").value;
                op+="&d="+document.getElementById("desc").value;
                op+="&c="+document.getElementById("cbCat").value;
                document.location.assign('http://www.tele-tudo.com/Cadastro?op=2&'+op+'&pag='+pag);
            }
        }

        function edita(nr, dados) {
            var itens = dados.split(";");
            document.getElementById("nome").value=itens[0];
            document.getElementById("preco").value=itens[1];
            document.getElementById("desc").value=itens[2];

            var Imagem = itens[3];
            var PosBarra = Imagem.indexOf('//')+2;
            Imagem = Imagem.substring(PosBarra, Imagem.length);
            document.getElementById("tximagem").value=Imagem;

            document.getElementById("txId").value=nr;
            document.getElementById("btEnviar").value="Atualizar";
            document.getElementById('btEnviar').type='button';
            $('#dvAdic').css({visibility:"hidden"});
            $('#dvAdic').css({display:"none"});
            $('#dvEditar').css({visibility:"visible"});
            $('#dvEditar').css({display:"block"});
            document.getElementById('cbCat').value = itens[4];
        }

        function deletar(nr) {
            if (confirm("Tem certeza que quer excluir este produto ?")) {
                var op = 'del='+nr;
                document.location.assign('http://www.tele-tudo.com/Cadastro?op=1&'+op);
            }
        }

        function Desativar(nr) {
            var id = 'id='+nr;
            document.location.assign('http://www.tele-tudo.com/Cadastro?op=3&id='+id);
        }

        function Ativar(nr) {
            var id = 'id='+nr;
            document.location.assign('http://www.tele-tudo.com/Cadastro?op=4&id=&'+id);
        }

    </script>
    <?php
    }
    ?>
@stop