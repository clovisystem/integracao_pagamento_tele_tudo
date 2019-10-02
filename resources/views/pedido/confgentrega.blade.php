@extends('layouts.padrao')
<link href="http://voky.com.ua/showcase/sky-forms/examples/css/sky-forms.css" rel="stylesheet" type="text/css" />
<title>Configurações de Entrega</title>
<script>
</script>
@section('content')
<?php
$idUser = 0;
if (Session::has('iduser')) {
    $idUser = Session::get('iduser');
}
if ($idUser==0) {
    echo 'Àrea apenas para usuário registrado'; die;
}
$cCli = new Clientes();
$lst = $cCli->getBairrosCidadeCliente($idUser);
$cForn = new Fornecedor();
$temDados = $cForn->ConfgEntregas($idUser);
?>
<h2>Cadastro complementar relativo a entrega</h2>
{{--method="POST" action="http://www.tele-tudo.com/fornecedor" accept-charset="UTF-8" --}}
<form class="sky-form boxed" method="post" name="formulario" ENCTYPE="multipart/form-data" style="width: 375px; border-width: medium; border-color: #FF0000;" >

    <Br>

    <div>
        <div class='Margem'><label>Definição de tipo de entrega própria</label></div>
            <div class='Margem'>
            <table style="width: 100%">
                <tr>
                    <td><input name="rdDefEntr" id="rdDefEntrD" onclick="ClicouTpEntrega(0)" type="radio"> Por Distância</td>
                    <td><input name="rdDefEntr" id="rdDefEntrB" onclick="ClicouTpEntrega(1)" type="radio"> Por Bairro</td>
                </tr>
            </table>
        </div>
    </div>
    <Br>
<?php
$OpLecRest = $cForn->getOpLocRest();
$Resumo = $cForn->getResumoConfEntr();
?>
    <div id="fora">
        <div id='dvDis0' style="visibility: hidden; display: none">
            <br>Distância: <input id='txDist0' name='txDist0' class='txDis' type='text'> Km, Valor&nbsp;
            <input id='txVlrDist0' name='txVlrDist0' class='Dis' type="text">&nbsp;
            <input id='Bt0' name='Bt0' class='Bt' type="button" onclick="AcionaDist(0)" value="Adicionar"><br>
        </div>
        <div id="dentro">
        </div>
        <div id="dvBai0" style="visibility: hidden; display: none" >
            <Br>Bairro:
            <select id="cbBai" name="Tpe">
                {{$lst}}
            </select>
            Valor <input id="txVlrBai" name="txVlrBai" class='Dis' type="text">&nbsp;
            <input id='Bt0' name='Bt0' class='Bt' type='button' onclick="AcionaBai(0)" value='Adicionar'><br>
        </div>
    </div>
    <div class='Margem'>Utilizar Entrega do site para localidades restantes?<br>
        <div class='Margem'>
            <table style="width: 100%">
                <tr>
                    <td><input name="ckEntrIntegr" id="ckEntrIntegrS" type="radio" onclick="VeHab()" {{$OpLecRest[0]}} >Sim</td>
                    <td><input name="ckEntrIntegr" id="ckEntrIntegrN" type="radio" onclick="VeHab()" {{$OpLecRest[1]}} >Não</td>
                </tr>
            </table>
            </div>
    </div>
    {{--<input name="btSalvar" id="btSalvar" class='Bt' type="button" disabled onclick="Salvar()" value="Salvar cadastro"><br>--}}
    <button type="button" id="btSalvar" onclick="Salvar()" disabled class="btn btn btn-primary btn-block">Salvar cadastro</button>
    <div id="dvMens">
        <div id="dvMens0">Resumo da Configuração: {{$Resumo}}</div>
    </div>
</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script>

    var QtdMens = 0;
    function ColocaMensagem(STR) {
        $('#dvMens'+QtdMens).css({display:"none"});
        $('#dvMens'+QtdMens).css({visibility:"hidden"});
        QtdMens++;
        $("#dvMens").append("<div id='dvMens"+QtdMens+"'>"+STR+"</div>");
    }

    function ClicouTpEntrega(opCheck) {
        /* var Bai = false;
        var Dis = false; */
        if (opCheck==1) {
            $('#dvDis0').css({display:"none"});
            $('#dvDis0').css({visibility: "hidden"});
            $('#dvBai0').css({display:"block"});
            $('#dvBai0').css({visibility: "visible"});
        } else {
            $('#dvDis0').css({display:"block"});
            $('#dvDis0').css({visibility: "visible"});
            $('#dvBai0').css({display:"none"});
            $('#dvBai0').css({visibility: "hidden"});
            ColocaMensagem("Resumo da Configuração: Entrega pelo fornecedor");
        }
    }

    var QtdBai = 0;
    function AcionaBai(Nr) {
        var txBt = document.getElementById("Bt"+Nr).value;
        var nt="txDist"+Nr;
        if (txBt == "Excluir") {
            DeletaBai(Nr, nt);
        } else {
            AdicionaBairro();
        }
    }

    function AdicionaBairro() {
        var nt="cbBai";
        var nrBai = document.getElementById(nt).value;
        var select = document.querySelector('select');
        var Tam = document.getElementById(nt).length;
        Tam = Tam * 2.3425;
        var option = select.children[select.selectedIndex];
        var txBai = option.textContent;
        var Vlr = document.getElementById("txVlrBai").value;
        if (nrBai>0 && Vlr>'') {
            QtdBai++;
            var STR = "<div id='dvBai"+
                 QtdBai + "' ><Br>Bairro:&nbsp;<input id='txBai" + QtdBai + "' name='txBai" + QtdBai +
                 "' class='Dis' type='text' style='border-style: solid; width: " +
                 Tam + "px' readonly='readonly' value='" +
                 txBai + "' >Valor <input id='txVlrBai" +QtdBai+"' name='txVlrBai" +QtdBai+"' class='Dis' type='text' value='" +
                 Vlr + "' >&nbsp;<input id='Bt"+QtdBai + "' class='Bt' type='button' onclick='AcionaBai(" +
                 QtdBai + ")' value='Excluir'><br></div>";
            $("#dentro").append(STR);
            select.selectedIndex = 0;
            document.getElementById("txVlrBai").value="";
            document.getElementById('btSalvar').disabled=false;
        } else {
            alert('Há um campo vazio');
        }
    }

    var QtdDist = 0;
    function AcionaDist(Nr) {
        var txBt = document.getElementById("Bt"+Nr).value;
        var nt="txDist"+Nr;
        if (txBt == "Excluir") {
            DeletaDist(Nr, nt);
        } else {
            Adiciona(nt);
        }
    }

    function Adiciona (nt) {
        var Dist = document.getElementById(nt).value;
        var Vlr = document.getElementById("txVlrDist0").value;
        if (Dist>'' && Vlr>'') {
            var obBt = "Bt"+(QtdDist);
            document.getElementById(obBt).value="Excluir";
            QtdDist++;
            var STR = "<div id='dvDis"+
                QtdDist+"'><br>Distância: <input id='txDist"+QtdDist +"' name='txDist"+QtdDist +
                "' class='txDis' type='text'>&nbsp;Km, Valor <input id='txVlrDist" +QtdDist +"' name='txVlrDist" +QtdDist +
                "' class='Dis' type='text'>&nbsp;<input id='Bt" +QtdDist +"' class='Bt' type='button' onclick='AcionaDist(" +
                QtdDist+")' value='Adicionar'><br></div>";
            $("#fora").append(STR);
            document.getElementById('btSalvar').disabled=false;
        } else {
            alert('Há um campo vazio');
        }
    }

    function DeletaBai(Nr) {
        var nd="dvBai"+Nr;
        document.getElementById(nd).style.visibility = "hidden";
        document.getElementById(nd).style.display = "none";
    }

    function DeletaDist(Nr, nt) {
        var nd="dvDis"+Nr;
        document.getElementById(nt).value="0";
        document.getElementById(nd).style.visibility = "hidden";
        document.getElementById(nd).style.display = "none";
    }

    function VeHab() {
        var NaoOK=false;
        var STR = "Resumo da Configuração: "
        if (document.getElementById("ckEntrIntegrN").checked==false) {
            STR+="Toda entrega é realizada pelo site";
        } else {
            if (QtdBai==0) {
                STR+="Inválida - desativada";
                NaoOK=true;
            }
        }
        ColocaMensagem(STR);
        document.getElementById('btSalvar').disabled=NaoOK;
    }

    function Salvar() {
        var OK=true;
        if (document.getElementById("ckEntrIntegrN").checked==false) {
            // Só a distância inicial pode zer zero
            var VlrAnt=0;
            for (var i = 0; i <QtdDist; i++) {
                var sNmObVlr = "txVlrDist" + i.toString();
                var sVlr = document.getElementById(sNmObVlr).value;
                var Valor = sVlr .valueOf();
                if (i>0) {
                    if (Valor==0) {
                        OK=false;
                        var STR = "Somente a primeira distância pode ter valor zero";
                        alert(STR);
                        ColocaMensagem(STR);
                        break;
                    }
                }
                if (Valor<VlrAnt) {
                    OK=false;

                    // O IDEAL É UMA FUNÇÃO SEPARADA PARA ESTA CRÍTICA
                    // E NESTA OUTRA FUNÇÃO ORDENAR OS VALORES PELA DISTÂNCIA

                    var STR = "Distância maior não pode ter valor menor";
                    alert(STR);
                    ColocaMensagem(STR);
                    break;
                }
                VlrAnt = Valor;
            }
        }
        if (OK==true) {
            document.formulario.submit();
        }
    }

</script>
@stop
