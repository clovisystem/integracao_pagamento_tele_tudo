<?php

/*header('HTTP/1.0 404 Not Found');
echo '<p>Página não encontrada</p>';
exit(0);

echo $_SERVER['HTTP_USER_AGENT']."<Br>";
echo $_SERVER['HTTP_X_REAL_IP']."<Br>";
echo $_SERVER['REMOTE_ADDR']."<Br>";
echo $_SERVER['SERVER_ADDR']."<Br><Br>";*/

// echo var_dump($_REQUEST);

// echo var_dump($_POST);

echo "Entrou<Br>";

/*if (isset($_POST["mail"])) {
    echo "Yes, mail is set";
}else{
    echo "N0, mail is not set";
}*/

// Logar todos acessos a essa pagina

if (isset($_REQUEST['id_transacao'])) {

    $IP = $_SERVER['REMOTE_ADDR'];
    $id_transacao = $_REQUEST['id_transacao'];
//    $valor = $_REQUEST['id_transacao'];
    $status_pagamento = $_REQUEST['status_pagamento'];
    $cod_moip = $_REQUEST['status_pagamento'];
    $forma_pagamento = $_REQUEST['status_pagamento'];
    $tipo_pagamento = $_REQUEST['status_pagamento'];
    $email_consumidor = $_REQUEST['status_pagamento'];

/*    $sql = "insert into logMoIp (IP, id_transacao, valor, status_pagamento, cod_moip, forma_pagamento, tipo_pagamento, email_consumidor) ";
        $sql.="values ('";
        $sql.=$IP."',";
        $sql.=$id_transacao.",";
        $sql.=$valor.",";
        $sql.=$status_pagamento.",";
        $sql.=$cod_moip.",";
        $sql.=$forma_pagamento.",";
        $sql.=$tipo_pagamento.",";
        $sql.=$email_consumidor.")";
    DB::update($sql);*/

    DB::insert("insert into logMoIp (IP, id_transacao, status_pagamento, cod_moip, forma_pagamento, tipo_pagamento, email_consumidor)
    values (?, ?, ?, ?, ?, ?, ?)", [
        $IP,
        $id_transacao,
        $status_pagamento,
        $cod_moip,
        $forma_pagamento,
        $tipo_pagamento,
        $email_consumidor
    ]);
    $idlog = DB::table('logMoIp')->max('idLogMoIp');

    // SE FOR O MESMO IP DA MOIP
    DB::insert("insert into moiplanc (idlog, idped, cod_moip, status)
    values (? , ?, ?, ?)", [
        $idlog,
        $id_transacao,
        $cod_moip,
        $status_pagamento
    ]);

    echo "Gravou<Br>";

    // id_transacao 	Identificador da transação informado por você para controle em seu site 	Alfanumérico 	32 	abcd1234
    // $id_transacao = $_REQUEST['id_transacao'];
    // Verificar se existe este pedido
        // e se ele não esta num status que não pode ser alterado

    // echo "id_transacao = ".$id_transacao."<Br>";

    // valor 	Valor do pagamento, sem vírgulas, com casas decimais (veja exemplo para R$20,00) 	Numérico (inteiro) 	9 	2000
    // $valor = $_REQUEST['id_transacao'];
    // verificar se o valor confere

    // status_pagamento 	Codigo informando o status atual da transação (veja Anexo A) 	Numérico inteiro 	2 	3
    // $status_pagamento = $_REQUEST['status_pagamento'];

    // EFETIVAR O PAGAMENTO
        // autorizado 	1 	Pagamento já foi realizado porém ainda não foi creditado na Carteira MoIP recebedora (devido ao floating da forma de pagamento)
        // concluido 	4 	Pagamento já foi realizado e dinheiro já foi creditado na Carteira MoIP recebedora
            // Se status atual é = 1
                // então só atualiza a informação
                // senão efetiva o recebimento

    // Verificar se existe necessidade de programação no estado atual
        // cancelado 	5 	Pagamento foi cancelado pelo pagador, instituição de pagamento, MoIP ou recebedor antes de ser concluído

    // Há necessidade de prever, necessita analize
    // estornado 	7 	Pagamento foi estornado pelo pagador, recebedor, instituição de pagamento ou MoIP*/

    // Não previsto atualmente
        // iniciado 	2 	Pagamento está sendo realizado ou janela do navegador foi fechada (pagamento abandonado)
        // boleto impresso 	3 	Boleto foi impresso e ainda não foi pago
        // em análise 	6 	Pagamento foi realizado com cartão de crédito e autorizado, porém está em análise pela Equipe MoIP. Não existe garantia de que será concluído
            // Mostrar ao fornecedor que existe um pedido sendo feito
            // Seria então um segundo status porque o primeiro seria quando finaliza o pagamento no tele-tudo

    // cod_moip 	Código da transação no ambiente MoIP. Valor único gerado pelo MoIP. 	Numérico 	20 	12341234
    $cod_moip = $_REQUEST['cod_moip'];

    // forma_pagamento 	Codigo informando a forma de pagamento escolhida pelo pagador (veja Anexo B) 	Numérico inteiro 	2 	1
    $forma_pagamento = $_REQUEST['forma_pagamento'];

    // tipo_pagamento 	Tipo de pagamento utilizado, descritivo, em formato de texto (veja Anexo C) 	Alfanumérico 	32 	CartaoDeCredito
    $tipo_pagamento = $_REQUEST['tipo_pagamento'];
    /*DebitoBancario 	Débito em conta no domicilio bancário do pagador
    FinanciamentoBancario 	Financiamento obtido junto ao domicílio bancário do pagador e o montante total debitado diretamente da conta e creditado na Carteira MoIP do recebedor
    BoletoBancario 	Boleto bancário impresso pelo pagador
    CartaoDeCredito 	Cartão de crédito
    CartaoDeDebito 	Cartão de débito Visa Electron (apenas para correntistas do Bradesco)
    CarteiraMoIP 	Diretamente da Carteira MoIP do pagador
    NaoDefinida 	Ainda não definida pelo pagador*/

    // email_consumidor 	E-mail informado pelo pagador, no MoIP 	Alfanumérico 	45 	pagador@email.com.br"
    $email_consumidor = $_REQUEST['email_consumidor'];
}

/*Depois que o MoIP envia a notificação para o URL de notificação previamente cadastrado e você processa os dados do pagamento, você deve responder com um código HTTP de acordo com o resultado do seu processamento.

OK = 200

Não reconhecido
nr de pedido desconhecido
valor não confere
status fora da sequencia


    COM SUCESSO: Caso o processamento e atualização tenham ocorrido corretamente, envie o código HTTP 200 como resposta.
COM ERRO: Caso algum erro tenha ocorrido, você deve retornar um código HTML 4XX ou 5XX para o MoIP.*/

echo "Saiu<Br>";

?>