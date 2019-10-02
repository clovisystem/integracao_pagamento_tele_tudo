<?php
$op = $_REQUEST['op'];

// $Token = Session::get('Token');
switch ($op) {
    case 1: // Confirmação
    {
        Confirmacao();
        break;
    }
    case 2: // Motorista
    {
        Motorista();
        break;
    }
    case 3: // Localização Motorista
    {
        Localizacao();
        break;
    }
    case 4: // Confirma Pagamento via Transferência Bancária
    {
        ConfirmaPag();
        break;
    }
    case 5: // Cancelar
    {
        Cancelar();
        break;
    }
    case 6: // Efetiva no BD
    {
        Efetiva();
        break;
    }
    case 7: // CEP
    {
        BuscaEnderPeloCep();
        break;
    }
    case 8: // Verifica se o FORNECEDEDOR VISUALIZOU
    {
        VeSeOFornViu();
        break;
    }
    case 9: // Verifica se o FORNECEDEDOR VISUALIZOU
    {
        Cancela();
        break;
    }
    case 10: // CEP
    {
        BuscaEnderPeloCep1();
        break;
    }

    case 11: // Cancela informação de transferência bancária
    {
        CancInfTransBanc();
        break;
    }

}

function Confirmacao() {
    if (Session::has('Teste')) {
        echo "<div class='efetuada'><h1>Solicitação Efetuada</h1></div>";
    } else {
        $clsEntrega = new App\Entrega();
        $Token = $_REQUEST['Token'];
        $idPedido = $_REQUEST['idPedido'];
        $Solicitou=$clsEntrega->SolicitaEntrega($idPedido, $Token);
        if ($Solicitou) {
            $clsEntrega->
            $clsEntrega->Efetiva($idPedido);
            // $clsEntrega->EfetivaPedidoNoBD($idPedido);
            echo "<div class='efetuada'><h1>Solicitação Efetuada</h1></div>";
        } else {
            echo 'houve erro'; die;
        }
    }
}

function Motorista() {
	$vez = $_REQUEST['vez'];
    $Token = $_REQUEST['Token'];
	$clsEntrega = new App\Entrega();
	$result = $clsEntrega->Motorista($Token, $vez);
	echo $result;    
        // if (strpos($result, 'Desculpe')==0)  {
}

function Localizacao() {
	$vez = $_REQUEST['vez'];
    $Token = $_REQUEST['Token'];
	$clsEntrega = new App\Entrega();
	$Teste=0;
	if (Session::has('Teste')) {
	   $Teste=1;
	}
	$result = $clsEntrega->OndeEleTa($Token, $vez, $Teste);
	echo $result;    
}

function ConfirmaPag() {
    if (Session::has('Teste')) {
        $result = 1;
    } else {
        $clsPagamento = new App\Pagamento();

        $idPedido = $_REQUEST['idPedido'];
        // $idPedido = Session::get('IDPED');

        $result = $clsPagamento->VePagamento($idPedido);
    }
    echo $result;
}

function Cancelar() {
    $clsEntrega = new App\Entrega();
    $Token = $_REQUEST['Token'];
    $result = $clsEntrega->Cancelar($Token);
    echo $result;
}

function Efetiva() {
    $clsEntrega = new App\Entrega();
    $idPedido = $_REQUEST['idPedido'];
    $clsEntrega->EmEntrega($idPedido);
    $clsEntrega->Efetiva($idPedido);
    echo 'ok';
}

function BuscaEnderPeloCep() {
    // echo "BuscaEnderPeloCep"; die;
    $cCep = new App\Cep();
    $Cep = $_REQUEST['cep'];
    $M = $_REQUEST['M'];
    echo $cCep->BuscaEnderPeloCep($Cep,$M);
}

function BuscaEnderPeloCep1() {
    $cCep = new App\Cep();
    $Cep = $_REQUEST['cep'];
    echo $cCep->BuscaEnderPeloCep1($Cep);
    // echo "1";
}

function VeSeOFornViu() {
    $idAviso = $_REQUEST['idAviso'];
    $cNotificacao = new App\Notificacao();
    $ret = $cNotificacao->VeSeOFornViu($idAviso);
    echo $ret;
}

function Cancela() {
    $clsEntrega = new App\Entrega();
    $clsEntrega->Cancelar('#F4AUNV10L7');
    echo 'feito';
}

function CancInfTransBanc() {
    $idPedido = $_REQUEST['idPedido'];
    $cPed = new App\Pedido();
    $cPed->CancInfTransBanc($idPedido);
    echo 'ok';
}
?>