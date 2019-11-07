<script language="javascript" src="js/jquery/jquery-1.6.2.min.js"></script>
<script language="javascript" src="js/jquery/cycle/jquery.cycle.all.js"></script>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
<script type="text/javascript" src="{{ URL::asset('js/jquery/jquery-1.6.2.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js') }}"></script>

<?php $idUser=0; ?>
@extends('layouts.padrao')


@section('content')



<!--
$IDPED = $_GET['ped']='1'; // VEM DA VIEW PEDIDOS/INDEX - RETIRAR O VALOR AO LADO EM TEMPO DE PRODUÇÃO

//$Valor = Session::get('VLRTOTAL'); //O CODIGO AO LADO SÓ DEVE SER USADO EM PRODUÇÃO, EM DESENVOLVIMENTO COMENTE!
$Descricao = 'DVD Independence Day';
$Valor = '20.00';
$tpEnt = '1';
//$VlrEntrega = '20.00';





//$Valor = $Valor * 100;
$Id_carteira = "xeviousbr@gmail.com";
$Nome = DB::table('users')->where('user',$user)->first()->Nome;

    "Pagamento do Tele-Tudo.com, por compra realizada";
//$user = 'teste';
//$email = 'teste@teste.com';
//$fone = '3072-5968';
//$cep = '91780118';
//$logradouro = 'endereço 20';
//$lograNumber = '15';
//$lograCompl = 'apto8';
//$cidade = 'Santo Andre';
//$bairro = 'marajoara';
//$estado = 'SP';





//, compact(Id_carteira, Valor, Ped, Nome -> ESTAVE NO ROUTE DA ACTION DO FORM
-->
<form name="teste" method="post" action="/formas" class="form-group">
    {{ csrf_field() }}
    <label>usuário</label>
    <input type="text" name="user" id="user" value="" class="form-control"/>
    <br>
    <label id="labelUser"></label>
    <br>
    <label>vendedor</label>
    <input type="text" name="id_carteira" value="xeviousbr@gmail.com" readonly="true" class="form-control"/>
    <br>
    <br>
    <label>carrinho</label>
    <p><input type="checkbox" name="Descricao" class="Descricao" id="Descricao1" value="teclado" />Teclado</p>
    <p><input type="checkbox" name="Descricao" class="Descricao" id="Descricao2" value="notebook" />Notebook</p>
    <p><input type="checkbox" name="Descricao"  class="Descricao" id="Descricao3" value="caixa de som" />Caixa de Som</p>
    <p><input type="checkbox" name="Descricao"  class="Descricao" id="Descricao4" value="mouse" />Mouse</p>
    <br>
    <label>valor</label>
    <input type="text" name="valor" id="valor" value="" class="form-control"/>
    <label id="atencao" >Nenhum produto no carrinho</label>
    <br>
    <label>produtos</label>
    <input type="text" name="produtos" id="produtos" value="" class="form-control"/>
    <br>
    <label>Número do pedido</label>
    <input type="text" name="nrPed" value="{{ uniqid() }}" class="form-control"/>
    <br>
    
    <label>Entrega por</label>
    <select  id ="tpEnt" name="tpEnt"  class="form-control">
        <option value="0" selected>Escolha uma opção</option>
        <option value="1">Moto</option>
        <option value="2">Correios</option>
    </select>

    <label>Valor de Entrega</label>
    <input type="text" name="vlrEntr" id="vlrEntr" value="" class="form-control"/>
    <br>

    <script>
        window.onload=(function(){
            
            $("#tpEnt").change(function(){
                //var selecao = $(this).val();
                //alert(selecao);
                //alert($("#dropDownMenuKategorie").prop('selectedIndex'));
               
            


                if($("#tpEnt").prop("selectedIndex") == 1){
                    $("#vlrEntr").val("20.00");
                    var usuario = $("#user").length;
                    if(usuario >= 1){
                        teste.submit();
                    } 
                    else{
                        $("#labelUser").css("visibility", "visibility");
                        $("#labelUser").val("Informe seu nome");
                    }
                    
                }
                else if($("#tpEnt").prop("selectedIndex") == 2){
                    $("#vlrEntr").val("25.00");
                    var usuario = $("#user").length;
                    if(usuario >= 1){
                        teste.submit();
                    } 
                    else{
                        $("#labelUser").css("visibility", "visibility");
                        $("#labelUser").val("Informe seu nome");
                    }
                    
                }
                else{
                    $("#vlrEntr").val(""); 
                }
                
   
                
            });


            

            $("#atencao").css("visibility","hidden");
             
            /*if(descricao = "teclado"){
                $("#valor").val("25.00"); 
            }*/
            
            $(".Descricao").change(function(){
   
                    var descricao1 = $("#Descricao1").is(':checked');
                    var descricao2 = $("#Descricao2").is(':checked');
                    var descricao3 = $("#Descricao3").is(':checked');
                    var descricao4 = $("#Descricao4").is(':checked');

                    var descricao = [descricao1, descricao2, descricao3, descricao4];

                    $itens = $("#valor").val(descricao);


                    /*switch(descricao){
                        case "true,false,false,false" : $("#valor").val("20.00"); break;
                        case "true,true,false,false" : $("#valor").val("1020.00"); break;
                        case "true,true,true,false" : $("#valor").val("1040.00"); break;
                        case "true,true,true,true" : $("#valor").val("1050.00"); break;
                    };*/
                    if(descricao=="true,false,false,false"){
                        $("#valor").val("20.00");
                        var produtos = ["teclado"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="true,true,false,false"){
                        $("#valor").val("1020.00");
                        var produtos = ["teclado","notebook"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="true,true,true,false"){
                        $("#valor").val("1040.00");
                        var produtos = ["teclado","notebook","caixa de som"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="true,true,true,true"){
                        $("#valor").val("1050.00");
                        var produtos = ["teclado","notebook","caixa de som","mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,true,true,true"){
                        $("#valor").val("1030.00");
                        var produtos = ["notebook","caixa de som","mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,false,true,true"){
                        $("#valor").val("30.00");
                        var produtos = ["caixa de som","mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,false,false,true"){
                        $("#valor").val("10.00");
                        var produtos = ["mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,false,true,false"){
                        $("#valor").val("20.00");
                        var produtos = ["caixa de som"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,true,false,false"){
                        $("#valor").val("1000.00");
                        var produtos = ["notebook"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,true,true,false"){
                        $("#valor").val("1020.00");
                        var produtos = ["notebook","caixa de som"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="true,true,false,true"){
                        $("#valor").val("1030.00");
                        var produtos = ["teclado","notebook","mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="true,false,false,true"){
                        $("#valor").val("30.00");
                        var produtos = ["teclado","mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="true,false,true,true"){
                        $("#valor").val("50.00");
                        var produtos = ["teclado","caixa de som","mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="true,false,true,false"){
                        $("#valor").val("40.00");
                        var produtos = ["teclado","caixa de som"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,true,false,true"){
                        $("#valor").val("1010.00");
                        var produtos = ["notebook","mouse"];
                        $("#produtos").val(produtos);
                        $("#atencao").css("visibility","hidden");
                    }
                    if(descricao=="false,false,false,false"){
                        $("#valor").val("0.00");
                        $("#atencao").css("color","yellow");
                        $("#atencao").css("visibility","visible");
                        var produtos = ["nada"];
                        $("#produtos").val(produtos);
                        
                    }

                    
               

                
            });

            /*$("#valor").change(function(){ //EVENTO CHANGE DA SELECT/OPTION DA PÁGINA
        var selecao = $(this).val(); // pega o valor INTEIRO DA COMPRA
        var indice = $(this).prop('selectedIndex'); //pega o índice do select/option(html)*/
        });
       
    
    </script>

    @stop
