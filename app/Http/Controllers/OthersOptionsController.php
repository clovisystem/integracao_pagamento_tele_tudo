<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use api_moip\lib\moip; 
use resources\views\produtos\index;  

include_once "autoload.inc.php";

class OthersOptionsController extends Controller
{
    //

    public function Aciona(){

        $moip = new Moip();
        $moip->setEnvironment('test');
        $moip->setCredential(array('key' => 'ABABABABABABABABABABABABABABABABABABABAB', 'token' => '01010101010101010101010101010101'));
        $moip->setUniqueID(false);
        $moip->setValue($valor);
        $moip->setReason('Teste do Moip-PHP');
    
        $moip->setPayer(array('name' => 'Nome Sobrenome',
            'email' => 'email@cliente.com.br',
            'payerId' => 'id_usuario',
            'billingAddress' => array('address' => 'Rua do Zézinho Coração',
                'number' => '45',
                'complement' => 'z',
                'city' => 'São Paulo',
                'neighborhood' => 'Palhaço Jão',
                'state' => 'SP',
                'country' => 'BRA',
                'zipCode' => $CepDoCli, //ESTÁ EM VIEWS-PRODUTOS-INDEX.BLADE.PHP
                'phone' => '(11)8888-8888')));
        $moip->validate('Identification');
    
        $moip->setReceiver($Id_carteira);
    
        $moip->addParcel('2', '4');
        $moip->addParcel('5', '7', '1.00');
        $moip->addParcel('8', '12', null, true);
    
        $moip->addComission('Razão do Split', 'recebedor_secundario', '5.00');
        $moip->addComission('Razão do Split', 'recebedor_secundario', '2.00', true);
        $moip->addComission('Razão do Split', 'recebedor_secundario_2', '12.00', true, 'recebedor_secundario_3');
    
        $moip->addPaymentWay('creditCard');
        /*$moip->addPaymentWay('billet');
        $moip->addPaymentWay('financing');
        $moip->addPaymentWay('debit');
        $moip->addPaymentWay('debitCard');
        $moip->setBilletConf("2011-04-06", true, array("Primeira linha", "Segunda linha", "Terceira linha"), "http://seusite.com.br/logo.gif");*/
    
        print_r($moip->getXML());
    }
    }
}
