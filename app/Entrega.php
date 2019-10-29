<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Entrega extends Model {

    protected $table = 'entrega';

    private $idEntrega=0;
    private $VlrEntrega=0;
    private $VlrCompras=0;
    private $VlrTotal=0;
    private $VlrNosso=-1;
    private $idPedido=0;
    private $Kms=0;
    private $TmpPrevisto="";
    private $vSistema=0;
    private $OrcFree=-1;
    private $debug=-1;
    private $ErroCalcTempo=0;
    private $idFornProd=0;

    private $AMB = ""; // D = Desenvolvimento / P = Produção
    private $Modo = 0;
    /*
    0 = Não inicializado
    1 = Modo teste, PlayDelivery Simulado
    2 = Modo teste, PlayDelivery Real, modo Dev
    3 = Produção
    */

    // PRIMEIRO INSTANEAMENTO

    public function Login($idEntrega) {
        $this->setidEntrega($idEntrega);

        if ($this->getModo()==1) {
            $status = "success";
            $Token = "GUqmXzHQ91ZghjZWtWSt";
        } else {
            $cPlay = new PlayDelivery();
            $Token = $cPlay->Login();
            $status = $cPlay->getstatus();
        }
        if ($status == "success") {
            echo 'Token = '.$Token.'<p>';
            return $Token;
        } else {
            echo 'sem login na PlayDelivery'; die;
            return "";
        }
    }

    public function CriaRegistro($idPedido, $idCliente, $Teste, $tpEnt) {
        $this->setPedido($idPedido);
        // if ($Teste==0) {

        // $Entregadora=1;
        if ($tpEnt==0) { // 0 = Default, Entregadora por integração
            $Entregadora=1; // Somente existe integração para PlayDelivery
        } else { // 1 = Entrega feita pelo fornecedor
            $Entregadora=0;
        }

        $sql = "insert into entrega (idEntregadora, idPedido, idCliente, HoraIN) values ( ".
            $Entregadora.",".
            $idPedido.",".
            $idCliente.",".
            "now())";
        DB::update($sql);
        $Cons = DB::table('entrega')
            ->select('id')
            ->where('idPedido', '=', $idPedido)
            ->first();
        $this->idEntrega = $Cons->id;
        return $this->idEntrega;
        /* } else {
            return 1;
        } */
    }

    private function DadosEnvolvidos() {
        // Fornecedor e Comprador
        $qry1 = DB::table('pedido')
            ->select('pedido.User','produtos.Empresax_ID',
                'empresa.idEntrega',
                'cep.cep', 'cep.latitude as lat', 'cep.longitude as lon')
            ->join('pedidoItens', 'pedidoItens.idped', '=', 'pedido.idPed')
            ->join('produtos', 'produtos.ID', '=', 'pedidoItens.idprod')
            ->join('empresa', 'empresa.idEmpresa', '=', 'produtos.Empresax_ID')
            ->join('endereco', '.endereco.ID', '=', 'empresa.idEndereco')
            ->join('cep', '.cep.cep', '=', 'endereco.CEP')
            ->where('pedido.idPed', '=', $this->idPedido)
            ->get();
        return $qry1;
    }

    private function DadosEmpresa ($Empresax_ID) {
        $ConsF = DB::table('empresa')
            ->join('endereco', 'endereco.ID', '=', 'empresa.idEndereco')
            ->join('logra', 'logra.ID', '=', 'endereco.Logradouro_ID')
            ->join('tplogradouro', 'tplogradouro.ID', '=', 'logra.TpLogradouro_ID')
            ->join('cep_bairro', 'cep_bairro.id_bairro', '=', 'endereco.idBairro')
            ->join('cep_cidade', 'cep_cidade.id_cidade', '=', 'cep_bairro.cidade_id')
            ->join('cep_estado', 'cep_estado.Sigla', '=', 'cep_cidade.estado')
            ->join('pais', 'pais.ID', '=', 'cep_estado.idPais')
            ->join('cep', 'cep.cep', '=', 'endereco.CEP')
            ->select('empresa.Empresa','empresa.Telefone','empresa.EntregaFree',
                'endereco.Numero','endereco.Complemento',
                'logra.NomeLog',
                'tplogradouro.nometplog',
                'cep_bairro.bairro as NomeBairro',
                'cep_cidade.cidade as NomeCidade',
                'cep_estado.estado as NomeEstado',
                'pais.Nome as NomePais',
                'cep.latitude as lat','cep.longitude as lon')
            ->where('empresa.idEmpresa', '=', $Empresax_ID)
            ->first();
        $this->OrcFree = $ConsF->EntregaFree;
        return $ConsF;
    }

    private function getidFornProd() {
        return $this->idFornProd;
    }

    public function setidFornProd($idFornProd) {
        $this->idFornProd = $idFornProd;
    }

    public function getOrcFree() {
        if ($this->OrcFree==-1) {
            $this->loga('this->OrcFree = '.$this->OrcFree);
            // $idFornProd = Session::get('Fornec');
            $idFornProd = $this->getidFornProd();
            $ConsF = DB::table('empresa')
                ->select('empresa.EntregaFree')
                ->where('empresa.idEmpresa', '=', $idFornProd)
                ->first();
            $this->OrcFree = $ConsF->EntregaFree;
            $this->loga('ConsF->EntregaFree = '.$ConsF->EntregaFree);
        }
        $this->loga('this->OrcFree = '.$this->OrcFree);
        return $this->OrcFree;
    }

    private function DadosCliente ($idCliente) {
        $ConsP = DB::table('users')
            ->join('endereco', 'endereco.ID', '=', 'users.Endereco_ID')
            ->join('logra', 'logra.ID', '=', 'endereco.Logradouro_ID')
            ->join('tplogradouro', 'tplogradouro.ID', '=', 'logra.TpLogradouro_ID')
            ->join('cep_bairro', 'cep_bairro.id_bairro', '=', 'endereco.idBairro')
            ->join('cep_cidade', 'cep_cidade.id_cidade', '=', 'cep_bairro.cidade_id')
            ->join('cep_estado', 'cep_estado.Sigla', '=', 'cep_cidade.estado')
            ->join('pais', 'pais.ID', '=', 'cep_estado.idPais')
            ->join('cep', 'cep.cep', '=', 'users.CEP')
            ->select('users.Nome','users.fone'
                ,'endereco.Numero','endereco.Complemento',
                'logra.NomeLog',
                'tplogradouro.nometplog',
                'cep_bairro.id_bairro as BairroID','cep_bairro.bairro as NomeBairro',
                'cep_cidade.cidade as NomeCidade',
                'cep_estado.estado as NomeEstado',
                'pais.Nome as NomePais',
                'cep.latitude as lat','cep.longitude as lon')
            ->where('users.ID', '=', $idCliente)
            ->first();
        return $ConsP;
    }

    public function PedeOrcamento($idPedido, $Teste, $TpEntrega, $VlrEntrega, $cep) {
        $this->idPedido = $idPedido;
        $qryE = $this->DadosEnvolvidos();
        $ret = 0;
        $ConsF = $this->DadosEmpresa($qryE[0]->Empresax_ID);
        $ConsC = $this->DadosCliente($qryE[0]->User);
        $Tpe=-1;
        switch ($TpEntrega) {
            case 0:
                // Entrega pela PlayDelivery
                $this->loga('PLAYDELIVERY');
                $ret = $this->PedeOrgPlay($qryE, $ConsF, $ConsC, $Teste, $VlrEntrega, $idPedido);
                if ($ret>0) {$Tpe=0;}
                break;
            case 1:
                // Entrega pelo fornecedor, por distância
                $this->loga('POR DISTÂNCIA');
                $ret = $this->OrcamentoFornD($qryE, $ConsF, $cep);
                if ($ret>0) {$Tpe=1;}
                break;
            case 2:
                // Entrega pelo fornecedor, por bairro
                $this->loga('POR BAIRRO');
                $ret = $this->OrcamentoFornB($qryE, $ConsC);
                if ($ret>0) {$Tpe=1;}
                break;
        }

        $this->loga('ret = '.$ret);
        if ($ret ==0) {
            $this->loga('qryE[0]->idEntrega = '.$qryE[0]->idEntrega);
            if ($qryE[0]->idEntrega==2) {
                $this->loga('PLAYDELIVERY');
                $ret = $this->PedeOrgPlay($qryE, $ConsF, $ConsC, $Teste, $VlrEntrega, $idPedido);
                if ($ret>0) {$Tpe=0;}
            }
        }
        $this->loga('Tpe = '.$Tpe);
        if ($Tpe>-1) {
            DB::update("update pedido set Tpe = ".$Tpe." where idPed = " .$idPedido);
        }
        return $ret;
    }

    private function PedeOrgPlay($qryE, $ConsF, $ConsP, $Teste, $VlrEntrega, $idPedido) {
        $cPlay = new PlayDelivery();
        $ret = $cPlay->OrcamentoPlay($qryE, $ConsF, $ConsP, $Teste);
        $this->setVlrEntrega($cPlay->getVlrEntrega($idPedido), "OrcamentoPlay");
        $valor = $this->getVlrEntrega($idPedido)+$this->getVlrNosso($VlrEntrega, $idPedido);
        DB::update("update entrega set Valor = ".$this->getVlrEntrega($idPedido)." where id = " .$this->getidEntrega());
        echo 'Valor relativo a Tele-Entrega = '.number_format($valor, 2, ',', '.');
        return $ret;
    }

    private function OrcamentoFornB($qryE, $ConsC) {
        $ret = 0;
        $idBairro = $ConsC->BairroID;
        $ConsBC = DB::table('TpEntregaEmpresa')
            ->select('TpEntregaEmpresa.Valor')
            ->where('idBairro','=', $idBairro)
            ->where('idEmpresa','=', $qryE[0]->Empresax_ID)
            ->first();
        if ($ConsBC==null) {
            $AuxLog='OrcamentoFornB:ConsBC=null';
            $this->loga('OrcamentoFornB:ConsBC=null');
            $Valor=0;
        } else {
            $AuxLog='ConsBC->Valor = '.$ConsBC->Valor;
            $Valor = $ConsBC->Valor;
            $ret = 1;
        }
        $this->loga($AuxLog);
        $this->setVlrEntrega($Valor, "OrcamentoFornB");
        return $ret;
    }

    private function OrcamentoFornD($qryE, $ConsF, $cep) {
        $ret=0;
        $idCliente = $qryE[0]->User;
        /* $latF=$ConsF->lat;
        $lonF=$ConsF->lon;
        $ConsC = $this->DadosCliente($idCliente);
        $latC=$ConsC->lat;
        $lonC=$ConsC->lon; */

        $ConCep = DB::table('cep')
            ->select('latitude as lat', 'longitude as lon')
            ->where('cep', '=', $cep)
            ->first();
        $latC=$ConCep->lat;
        $lonC=$ConCep->lon;

        $sql = "SELECT fn_distance (".$latC.", ".$lonC.", cep.latitude, cep.longitude) distancia ";
        $sql.="from cep ";

        $sql.="where cep =  ".$qryE[0]->cep;
        // $sql.="where cep =  ".$qryE[0]->NrCep;

        $results = DB::select( DB::raw($sql));
        $Kms = $results[0]->distancia;

        /*
        PESQUISA PELO GOOGLE EXIGE KEY, tentei colocar e não foi
        pode ser que de pra usar o OpenStreetMap
        $cGoogle = new Google();
        $cGoogle->PrevisaoGoogle($latF, $lonF, $latC, $lonC);
        $Kms = $cGoogle->getKms();
        $this->setKms($Kms);
        $TmpPrevisto = $cGoogle->getTmpPrevisto();
        $this->setTmpPrevisto($TmpPrevisto); */

        $this->loga('Kms = '.$Kms);

        $ConsD = DB::table('TpEntregaEmpresa')
            ->select('Valor')
            ->where('Distancia','<', $Kms)
            ->where('idEmpresa','=', $qryE[0]->Empresax_ID)
            ->orderBy('Distancia', 'desc')
            ->first();

        if ($ConsD==null) {
            $ConsD = DB::table('TpEntregaEmpresa')
                ->select('TpEntregaEmpresa.Valor')
                ->where('idEmpresa','=', $qryE[0]->Empresax_ID)
                ->orderBy('Distancia')
                ->first();
            if ($ConsD==null) {
                $Valor=0;
            } else {
                $Valor = $ConsD->Valor;
                $ret=1;
            }
        } else {
            $Valor = $ConsD->Valor;
            $ret=1;
        }
        $this->loga('Valor = '.$Valor);
        $this->setVlrEntrega($Valor,"OrcamentoFornD");
        if ($Valor>0) {
            $Valor+=$this->getVlrNosso();
        }
        $this->loga('Valor = '.$Valor);
        return $ret;
    }

    public function setidPedido($idPedido) {
        $this->idPedido = $idPedido;
    }

    public function getVlrNosso() {
        if ($this->VlrNosso==-1) {
            if ($this->getVlrEntrega($this->idPedido)==0) {
                if ($this->getOrcFree()==1) {
                    $this->VlrNosso = 0;
                    return $this->VlrNosso;
                }
            }
            $Cons = DB::table('TConfigVlrEntrega')
                ->select('TConfigVlrEntrega.Valor')
                ->where('Limite','<', $this->getVlrEntrega($this->idPedido))
                ->orWhere('id', '1')
                ->orderBy('Valor', 'desc')
                ->first();
            $this->VlrNosso = $Cons->Valor;
        }
        return $this->VlrNosso;
    }

    /* public function getVlrNosso($VlrEntrega, $idPedido) {
        if ($this->VlrNosso==-1) {
            if ($this->getVlrEntrega($VlrEntrega, $idPedido)==0) {
                if ($this->getOrcFree()==1) {
                    $this->VlrNosso = 0;
                    return $this->VlrNosso;
                }
            }
            $Cons = DB::table('TConfigVlrEntrega')
                ->select('TConfigVlrEntrega.Valor')
                ->where('Limite','<', $this->getVlrEntrega($VlrEntrega, $idPedido))
                ->orWhere('id', '1')
                ->orderBy('Valor', 'desc')
                ->first();
            $this->VlrNosso = $Cons->Valor;
        }
        return $this->VlrNosso;
    } */

    // SEGUNDO INSTANCIAMENTO

    public function SolicitaEntrega($idPedido, $Token) {
        $this->idPedido = $idPedido;
        $qry1 = $this->DadosEnvolvidos();
        $ConsF = $this->DadosEmpresa($qry1[0]->Empresax_ID);
        $ConsP = $this->DadosCliente($qry1->User);
        $cPlay = new PlayDelivery();
        $ret = $cPlay->SolicitaEntrega($idPedido, $Token, $qry1, $ConsF, $ConsP);
        return $ret;
    }

    public function EmEntrega($id) {
        DB::update("update pedido set status = 3 where idPed = " .$id);
    }

    /* private function CalculaCaptador($Sistema) {
        if ($Sistema<3) {
            $captador = $Sistema/2;
        } else {
            $captador = 1;
        }
        return $captador;
    } */

    public function VeQtoFornRecebeu($id) {
        $this->vSistema=$this->getVlrNosso();
        $FornecRecebeu=$this->vSistema;
        $FornecRecebeu+= $this->getCompras($id);
        $FornecRecebeu+=$this->getVlrEntrega($this->idPedido);
        return $FornecRecebeu;
    }

    public function Efetiva ($id) {
        $this->idPedido = $id;
        $qry = DB::table('pedido')
            ->select('pedido.Tpe')
            ->where('pedido.idPed', '=', $id)
            ->first();
        if ($qry->Tpe==1) {
            $this->EfetivaPedidoNoBDSemEntrega($id);
        } else {
            $this->EfetivaPedidoNoBD($id);
        }
    }

    private function EfetivaPedidoNoBD($idPedido) {
        $ContaMov = new Contamov();
        $ContaMov->setPedido($idPedido);

        // ACUMULAR VALOR DEVIDO A ENTREGADORA
        // Entregadora fixa na PlayDelivery
        $idEntregadora = 1;
        $ContaMov->credita_entregadora($idEntregadora, $this->getVlrEntrega($idPedido));

        // ACUMULAR VALOR DEVIDO AO FORNECEDOR
        $compras=$this->getCompras($idPedido);
        $ContaMov->DescobreFornecedorPelosItens($idPedido);

        $captador=$ContaMov->credita_fornecedor($compras+$this->getVlrNosso()+$this->getVlrEntrega($idPedido),
            $this->getVlrEntrega($idPedido)+$this->getVlrNosso());
        // $captador=$ContaMov->credita_fornecedor($compras);

        // ACUMULAR VALOR AO CAPTADOR
        // $vCaptador=$this->CalculaCaptador($this->getVlrNosso());

        // ACUMULAR VALOR AO SISTEMA
        $this->vSistema=$this->getVlrNosso();
        // $this->vSistema=$this->getVlrNosso()-$vCaptador;

        DB::update("update conta set Pendente = Pendente + ".$this->vSistema.", updated_at = Now() where idConta = 1");
        DB::insert('insert into contamov (dest, data, pedido, valor) values (?, ?, ?, ?)',
            [1, new DateTime, $idPedido, $this->vSistema]);
        // $ContaMov->credita_sistema($this->vSistema);

        // ACUMULAR VALOR AO CAPTADOR
        // SETA PENDÊNCIA NO CAPTADOR
        $vCaptador=$this->vSistema/2;
        $ContaMov->credita_Pendencia_captador($captador, $vCaptador);

        /*$vCaptador=$this->CalculaCaptador($this->getVlrNosso());
        DB::update("update conta set Pendente = Pendente + ".$vCaptador.", updated_at = Now() where idConta = ".$captador);

        echo "captador = ".$captador." ";
        echo "vCaptador = ".$vCaptador." ";
        $ContaMov->credita_captador($captador,$vCaptador);*/

        $Valor=$compras + $this->getVlrEntrega($idPedido) + $this->getVlrNosso();
        DB::update("update pedido set Valor = ".$Valor." where idPed = ".$idPedido);
    }

    public function EfetivaPedidoNoBDSemEntrega($id) {
        // echo "EfetivaPedidoNoBDSemEntrega : ".$origem; die;


        // PEDIDO VAI PARA STATUS 4 = FINALIZADO
        // VALOR É SETADO NO PEDIDO
        // if ($this->getModo()==3) {

        $FornecRecebeu = $this->VeQtoFornRecebeu($id);

        DB::update("update pedido set status = 4, Valor = ".$FornecRecebeu." where idPed = " .$this->idPedido);

        $ContaMov = new Contamov();
        $ContaMov->setPedido($id);

        // SETA PENDÊNCIA EM FORNECEDOR
        $ContaMov->DescobreFornecedorPelosItens($this->pedido);

        // Se fornecedor faz entrega gratuíta, então o valor de débito é R$ 1,00
        if ($this->vSistema==0) {
            $this->vSistema=1;
        }

        $captador=$ContaMov->credita_fornecedorCEntrega($FornecRecebeu, $this->vSistema);

        // SETA PENDÊNCIA NO SISTEMA
        $ContaForn = $ContaMov->getContaForn();
        $ContaMov->credita_PendenciaSistema($this->vSistema, $ContaForn);

        // SETA PENDÊNCIA NO CAPTADOR
        $vCaptador=$this->vSistema/2;
        $ContaMov->credita_Pendencia_captador($captador, $vCaptador);

        // }
    }

    // ATUALIZAÇÃO DAS INFORMAÇÕES

    public function CoordCliente($iduser) {
        $qry = DB::table('users')
            ->join('endereco', 'endereco.ID', '=', 'users.Endereco_ID')
            ->join('cep', 'cep.cep', '=', 'endereco.CEP')
            ->select('cep.latitude as lat','cep.longitude as lon')
            ->where('users.id', '=', $iduser)
            ->first();
        return $qry->lat."|".$qry->lon;
    }

    public function Coord_Fornecedor($idForn) {
        $qry = DB::table('empresa')
            ->join('endereco', 'endereco.ID', '=', 'empresa.idEndereco')
            ->join('cep', 'cep.cep', '=', 'endereco.CEP')
            ->select('cep.latidude as lat','cep.longitude as lon')
            ->where('empresa.idEmpresa', '=', $idForn)
            ->first();
        return $qry->lat."|".$qry->lon;
    }

    public function CoordFornecedor() {
        // A PREVISÃO ATUAL É PARA APENAS UM ÚNICO FORNECEDOR POR PEDIDO
        $qry = DB::table('pedidoItens')
            ->join('produtos', 'produtos.ID', '=', 'pedidoItens.idprod')
            ->join('empresa', 'empresa.idEmpresa', '=', 'produtos.Empresax_ID')
            ->join('endereco', 'endereco.ID', '=', 'empresa.idEndereco')
            ->join('cep', 'cep.cep', '=', 'endereco.CEP')
            ->select('cep.latitude as lat','cep.longitude as lon')
            ->first();
        return $qry->lat."|".$qry->lon;
    }

    public function Motorista($Token, $vez) {
        if ($this->getModo()==1) {
            $dados = ['request_id' => "0", 'status' => "matched"];
            $results = json_encode($dados);
        } else {
            $cPlay = new PlayDelivery();
            $results=$cPlay->Motorista($Token, $vez);
        }
        return $results;
    }

    public function OndeEleTa($Token, $vez, $Teste) {
        // $headers = $this->MontaHeader($Token);

        $lcModo=$this->getModo();
        if ($Teste==1) {
            $lcModo=1;
        }

        if ($lcModo==1) {
            if ($vez>14) {
                $status= "finished";
                $last_delivery_point_finished = 2;
            } else {
                $status= "delivering";
                if ($vez>9) {
                    $last_delivery_point_finished = 1;	// Pronto para entrega
                }
                else {
                    if ($vez>4) {
                        $last_delivery_point_finished = 0;  // Se deslocando para o CLIENTE
                    } else {
                        $last_delivery_point_finished = -1; // Se deslocando para o FORNECEDOR
                    }
                }
            }
            $latitude = -30.0664355-($vez/150);
            $longitude = -51.1584507; // -($vez/250);
            $deliveryboy = ['latitude' => $latitude,
                'license_plate' => "IJK-5875",
                'longitude' => $longitude,
                'name' => "Testelino da Silva",
                'phone' => "(51)97250505",
                'photo_url' => "http://devplaydelivery.s3.amazonaws.com/deliveryboys/photos/000/000/004/original/Foto3x4_Cesar.jpg?1461353945"
            ];
            $dados = ['request_id' => "0",
                'status' => $status,
                'last_delivery_point_finished' => $last_delivery_point_finished,
                'deliveryboy' => $deliveryboy
            ];
            $results = json_encode($dados);
        } else {

            $cPlay = new PlayDelivery();
            $results = $cPlay->OndeEleTa($Token, $vez);
        }
        $deco = json_decode($results);
        if ($vez==0) {
            $this->SalvaEntregador($deco);
        } else {
            if ($deco->status=='finished') {
                DB::update("update entrega set HoraFIM = Now() where idPedido = ".$this->getPedido());
                DB::update("update pedido set status=4 where idPed = ".$this->getPedido());
            }
        }
        return $results;
    }

    private function SalvaEntregador($deco) {
        $boy = $deco->{'deliveryboy'};
        $Nome = $boy->{'name'};
        $Placa = $boy->{'license_plate'};
        $Fone = $boy->{'phone'};
        $qry = DB::table('entregador')
            ->select('idEntregador')
            ->where('Nome', '=', $Nome)
            ->get();
        if ($qry==null) {
            DB::insert("insert into entregador (idEntregadora, Nome, Placa, Fone, entregas) values (?, ?, ?, ?, ?)", [1, $Nome, $Placa, $Fone, 1]);
            $idEntregador = DB::table('entregador')->max('idEntregador');
        } else {
            $idEntregador = $qry[0]->idEntregador;
            DB::update("update entregador set entregas = entregas+1 where idEntregador = ".$idEntregador);
        }
        DB::update("update entrega set idEntregador = ".$idEntregador." where idPedido = ".$this->getPedido());
    }

    public function Cancelar($Token) {
        $cPlay = new PlayDelivery();
        $results = $cPlay->Cancelar($Token);
        return $results;
    }

    // FUNCOES GERAIS

    // METODOS ACESSADOS POR OUTRAS CLASSES

    public function getValorTotal($idPedido) {
        if ($this->VlrTotal==0) {
            // Valor da compra

            $vCompras=$this->getCompras($idPedido);

            $this->idPedido = $idPedido;

            // Valor da entrega
            $vEntrega=$this->getVlrEntrega($idPedido);

            // Nosso valor
            $vNosso=$this->getVlrNosso($vEntrega, $idPedido);

            // Valor total
            $vTotal=$vCompras+$vEntrega+$vNosso;

            $this->VlrTotal=$vTotal;
        }
        return $this->VlrTotal;
    }

    public function VerificaConfirmacaoEnvio($idPedido) {
        $qry1 = DB::table('pedido')
            ->select('status')
            ->where('idPed', '=', $idPedido)
            ->get();
        if ($qry1=="1") {
            return true;
        } else {
            return false;
        }
    }

    // GETTERS E SETTERS

    public function getVlrEntrega($idPedido) {
        if ($this->VlrEntrega==0) {
            if ($this->VlrEntrega==0) {
                if ($this->idPedido==0) {
                    $this->idPedido = $idPedido;
                    if ($this->idPedido==0) {
                        echo 'IdPedido = 0 A'; die;
                    }
                }
                $qry = DB::table('entrega')
                    ->join('entregador', 'entregador.idEntregador', '=', 'entrega.idEntregador')
                    ->select('entrega.Valor')
                    ->where('entrega.idPedido', '=', $this->idPedido)
                    ->first();
                if ($qry==null) {
                    echo "VlrEntrega da Session nao tem valor[1]"; die;
                } else {
                    $this->VlrEntrega = $qry->Valor;
                    if ($this->VlrEntrega==0) {
                        if ($this->getOrcFree()<1) {
                            echo "VlrEntrega da Session nao tem valor[2]"; die;
                        }
                    }
                }
            }
        }
        return $this->VlrEntrega;
    }

    /* public function getVlrEntrega($VlrEntrega, $idPedido) {
        if ($this->VlrEntrega==0) {
            $this->VlrEntrega = $VlrEntrega;
            if ($this->VlrEntrega==0) {
                if ($this->idPedido==0) {
                    $this->idPedido = $idPedido;
                    if ($this->idPedido==0) {
                        echo 'IdPedido = 0 A'; die;
                    }
                }
                $qry = DB::table('entrega')
                    ->join('entregador', 'entregador.idEntregador', '=', 'entrega.idEntregador')
                    ->select('entrega.Valor')
                    ->where('entrega.idPedido', '=', $this->idPedido)
                    ->first();
                if ($qry==null) {
                    echo "VlrEntrega da Session nao tem valor[1]"; die;
                } else {
                    $this->loga("qry->Valor = ".$qry->Valor);
                    $this->VlrEntrega = $qry->Valor;
                    if ($this->VlrEntrega==0) {
                        if ($this->getOrcFree()<1) {
                            echo "VlrEntrega da Session nao tem valor[2]"; die;
                        }
                    }
                }
            }
        }
        return $this->VlrEntrega;
    } */

    public function getNossaCobranca($idPedido) {

        $VlrEntrega = $this->getVlrEntrega($idPedido);
        $this->loga('getNossaCobranca:VlrEntrega = '.$VlrEntrega);
        $VlrNosso = $this->getVlrNosso($VlrEntrega, $idPedido);
        return number_format(($VlrEntrega+$VlrNosso), 2, ',', '.');
    }

    public function setVlrEntrega($VlrEntrega, $lugar) {
        $VE = str_replace(',', '.', $VlrEntrega);
        $this->loga('setVlrEntrega = '.$VE.' lugar = '.$lugar);
        $this->VlrEntrega=$VE;
        if ($this->idEntrega==0) {
            echo "lugar = ".$lugar;
            echo " this->idEntrega = 0"; die;
        }
        DB::update("update entrega set Valor = ".$VE." where id = " .$this->idEntrega);
    }

    public function getCompras($idPedido) {
        // Valor original dos produtos
        if ($this->VlrCompras==0) {
            $total=0;
            $sql = "SELECT Sum(pedidoItens.quant * pedidoItens.Valor) as Vlr ";
            $sql.="From pedidoItens ";
            $sql.="Where idped = ".$idPedido;
            $results = DB::select( DB::raw($sql));
            foreach ($results as $result) {
                $total=$result->Vlr;
            }
            $this->VlrCompras=$total;
        }
        return $this->VlrCompras;
    }

    private function setPedido($idPedido) {
        $this->idPedido=$idPedido;
    }

    public function getPedido() {
        if ($this->idPedido==0) {
            if (Session::has('idPedido')) {
                $this->idPedido = Session::get('idPedido');
            }
        }

        if ($this->idPedido==0) {
            echo 'IdPedido = 0 B'; die;
        }
        return $this->idPedido;
    }

    public function getErroCalcTempo() {
        return $this->ErroCalcTempo;
    }

    public function TempoDecorrido($idPedido) {
        $sql = "SELECT HoraIN, HoraFIM, (HoraFIM - HoraIN) AS Tempo ";
        $sql.="From entrega ";
        $sql.="Where idPedido = ".$idPedido;
        $sql.=" and HoraFIM > 0 ";
        $results = DB::select( DB::raw($sql));
        if ($results==null) {
            $this->ErroCalcTempo=1;
            return null;
        } else {
            foreach ($results as $result) {
                $tempoIN=$result->HoraIN;
                $tempoFM=$result->HoraFIM;
                $tempo=$result->Tempo;
            }
            $dados = ['HoraIN' => $tempoIN, 'HoraFIM' => $tempoFM, 'tempo' => $tempo];
            $results = json_encode($dados);
            return $results;
        }
    }

    public function getEntregador($idPedido) {
        $qry = DB::table('entrega')
            ->join('entregador', 'entregador.idEntregador', '=', 'entrega.idEntregador')
            ->select('entregador.Nome','entregador.Placa')
            ->where('entrega.idPedido', '=', $idPedido)
            ->get();
        return $qry[0]->Nome."|".$qry[0]->Placa;
    }

    public function getModo() {
        if (($this->Modo==0)) {
            $qry = DB::table('config')
                ->select('Modo')
                ->where('ID', '=', 1)
                ->get();
            $this->Modo=$qry[0]->Modo;
            if ($this->Modo==3) {
                $this->AMB="P";
            } else {
                $this->AMB="D";
            }
        }
        return $this->Modo;
    }

    public function getEnderecoCliente($idUser) {
        $Cons = DB::table('users')
            ->select('users.Endereco_ID')
            ->where('users.ID', '=', $idUser)
            ->first();
        return $Cons->Endereco_ID;
    }

    public function setKms($Kms) {
        $this->Kms = $Kms;
    }

    public function getKms() {
        return $this->Kms;
    }

    public function setTmpPrevisto($TmpPrevisto) {
        $this->TmpPrevisto = $TmpPrevisto;
    }

    public function getTmpPrevisto() {
        return $this->TmpPrevisto;
    }

    public function setidEntrega($idEntrega) {
        $this->idEntrega=$idEntrega;
    }

    public function Cancela2() {

        /*
        GET API_PATH/cancel_delivery.json?
            delivery_protocol=delivery_protocol&
            requestor_delivery_cancel_reason_type_id=reason_type&
            requestor_delivery_cancel_reason_input=reason_input
        */

        // 781cWHSPHE7G9VyaJDqs
        // "protocol":"5205TTG0DT"
        // requestor_delivery_cancel_reason_type_id=reason_type&

        // $headers = ['Accept: application/json', 'Content-Type: application/json'];

        // 501711

        $dados = ['delivery_protocol' => "501711",
            'requestor_delivery_cancel_reason_type_id' => 1,
            'requestor_delivery_cancel_reason_input' => 'foi um teste'
        ];

        $Token="781cWHSPHE7G9VyaJDqs";
        $headers = $this->MontaHeader($Token);

        /*$url="https://playdelivery.herokuapp.com/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,  $url.$Pag.".json");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $Operacao);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $Dados);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $results = curl_exec($ch);
        curl_close($ch);*/


        $results= $this->Comunica("GET", json_encode($dados), "/api/requestor/cancel_delivery", $headers);

        // $results = "ok4";
        return $results;
    }

    private $Telefone;
    private $email;
    public function GetNomeFor($idPedido) {
        /*vlrtransf
            idConta

        conta
            idPessoa*/
        $qryI = DB::table('pedidoItens')
            ->select('idprod')
            ->where('idped', '=', $idPedido)
            ->first();
        $qryP = DB::table('produtos')
            ->select('Empresax_ID')
            ->where('ID', '=', $qryI->idprod)
            ->first();
        $qryE = DB::table('empresa')
            ->select('Empresa','Telefone','email')
            ->where('idEmpresa', '=', $qryP->Empresax_ID)
            ->first();
        $this->Telefone = $qryE->Telefone;
        $this->email = $qryE->email;
        return $qryE->Empresa;
    }

    public function GetFoneFor() {
        return $this->Telefone;
    }

    public function GetEmailFor() {
        return $this->email;
    }

    private function getidEntrega() {
        return $this->idEntrega;
    }

    private function loga($texto) {
        if ($this->debug==-1) {
            $ConsConfig = DB::table('config')
                ->select('Debug')
                ->where('ID','=',1)
                ->first();
            $this->debug=$ConsConfig->Debug;
        }
        if ($this->debug==1) {
            echo $texto.'<Br>';
        }
    }

}