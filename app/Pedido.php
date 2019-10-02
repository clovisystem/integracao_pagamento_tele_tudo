<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// use DateTime;

class Pedido extends Model
{
    protected $table = 'pedido';
    // private $total = 0;
    private $Qtd=0;
    private $IP="";
    private $User="";

    // Visualização do pedido pelo fornecedor
    private $CliFone='';
    private $CliEnder=0;
    private $FoneBoy="";
    private $latF = 0;
    private $lonF = 0;
    private $latC = 0;
    private $lonC = 0;
    // private $Valor = 0;
    private $tpEntrega = -1;
    private $Placa = "";
    private $FormaPagto = 0;
    private $Troco=0;
    private $Pedido=0;
    private $idEndForn=0;
    private $Comentario='';

    public function setUser($User)
    {
        $this->User = $User;
    }

    public function getUser()
    {
        return $this->User;
    }

    public function setIP($IP)
    {
        $this->IP = $IP;
    }

    public function getIP()
    {
        return $this->IP;
    }

    public function getIdPedido($Teste) {
        if ($Teste==0) {
            $ConsID = DB::table('pedido')
                ->select('idPed')
                ->where('IP', '=', $this->getIP())
                ->orderBy('idPed', 'desc')
                ->take(1)
                ->first();
            return $ConsID->idPed;
        } else {
            return 1;
        }
    }

    public function CriaPedido($User, $Teste, $Tpe) {
        $this->setUser($User);
        $this->setIP($_SERVER['REMOTE_ADDR']);

        if ($Teste==0) {
            DB::table('pedido')->insert(
                array('IP' => $this->getIP(),
                    'User' => $this->getUser(),
                    'Tpe' => $Tpe)
            );
        }
    }

    public function setTotal($Qtd) {
        $this->Qtd=$Qtd;
    }

    public function getTotal($idPedido) {

        $Cons = DB::table('pedido')
            ->select('Valor')
            ->where('idPed', '=', $idPedido)
            ->first();
        return $Cons->Valor;
    }

    public function getItensPedido($idPedido) {
        $qry = DB::table('pedidoItens')
            ->join('produtos', 'produtos.ID', '=', 'pedidoItens.idprod')
            ->select('pedidoItens.quant','pedidoItens.Valor','produtos.Nome', 'produtos.Descricao')
            ->where('pedidoItens.idped', '=', $idPedido)
            ->get();
        return $qry;
    }

    // Visualização do pedido pelo fornecedor

    public function getIdFornUser($idUser) {

        // $idUser = 200;

        $Cons = DB::table('empresa')
            ->select('empresa.idEmpresa','empresa.tpEntrega','empresa.idEndereco',
                'cep.lat','cep.lon')
            ->join('endereco', 'endereco.ID', '=', 'empresa.idEndereco')
            ->join('cep', 'cep.id', '=', 'endereco.idCep')
            ->where('empresa.idPessoa', '=', $idUser)
            ->first();
        $this->latF = $Cons->lat;
        $this->lonF = $Cons->lon;
        $this->tpEntrega = $Cons->tpEntrega;
        $this->idEndForn = $Cons->idEndereco;
        return $Cons->idEmpresa;
    }

    public function getIdForn($idForn) {
        $Cons = DB::table('empresa')
            ->select('empresa.idEmpresa','empresa.tpEntrega',
                'cep.lat','cep.lon')
            ->join('endereco', 'endereco.ID', '=', 'empresa.idEndereco')
            ->join('cep', 'cep.id', '=', 'endereco.idCep')
            ->where('empresa.idEmpresa', '=', $idForn)
            ->first();
        $this->latF = $Cons->lat;
        $this->lonF = $Cons->lon;
        $this->tpEntrega = $Cons->tpEntrega;
        return $Cons->idEmpresa;
    }

    public function getNrVendas($idForn) {
        $reg = DB::table('notificacao')
            ->select(DB::raw('count(*) as Quant'))
            ->where('idFornec','=',$idForn)
            ->first();
        return ($reg->Quant+1);
    }

    public function getCliente($idPed) {
        $this->Pedido = $idPed;
        $Cons = DB::table('pedido')
            ->select('pedido.Troco','pedido.Comentario',
                'users.Nome','users.Endereco_ID','users.fone',
                'cep.lat','cep.lon',
                'formaspag.Nome as formanome')
            ->join('users', 'users.id', '=', 'pedido.User')
            ->join('endereco', 'endereco.ID', '=', 'users.Endereco_ID')
            ->join('cep', 'cep.id', '=', 'endereco.idCep')
            ->leftJoin('formaspag', 'formaspag.idformaspag', '=', 'pedido.FormaPagto')
            ->where('pedido.idPed', '=', $idPed)
            ->first();
        $this->CliFone = $Cons->fone;
        $this->latC = $Cons->lat;
        $this->lonC = $Cons->lon;
        $this->CliEnder = $Cons->Endereco_ID;
        $this->FormaPagto = $Cons->formanome;
        $this->Troco = $Cons->Troco;
        $this->Comentario = $Cons->Comentario;
        return $Cons->Nome;
    }

    public function getTroco() {
        return number_format($this->Troco,2,',','.');
    }

    public function getFormaPagto() {
        return $this->FormaPagto;
    }

    public function getFoneCli() {
        if ($this->CliFone>'') {
            return $this->CliFone;
        } else {
            $Cons = DB::table('pedido')
                ->select('users.fone','pedido.Comentario')
                ->join('users', 'users.id', '=', 'pedido.User')
                ->where('pedido.idPed', '=', $this->Pedido)
                ->first();
            $this->Comentario=$Cons->Comentario;
            return $Cons->fone;
        }
    }

    public function getNmMotoBoy($idPed) {

        $Cons = DB::table('entrega')
            ->select('entregador.Nome','entregador.Placa','entregador.Fone')
            ->join('entregador', 'entregador.idEntregador', '=', 'entrega.idEntregador')
            ->where('entrega.idPedido', '=', $idPed)
            ->first();
        $this->FoneBoy = $Cons->Fone;
        $this->Placa = $Cons->Placa;
        return $Cons->Nome;
    }

    public function FoneBoy() {
        return $this->FoneBoy;
    }

    public function getPlaca() {
        return $this->Placa;
    }

    public function getPrevisao() {
        $cGoogle = new Google();
        $cGoogle->PrevisaoGoogle($this->latF, $this->lonF, $this->latC, $this->lonC);
        $tempo = intval($cGoogle->getTmpPrevisto()/60)+1;
        $tempo.=" minutos";
        return $tempo;
    }

    public function getQtdItens($idPed) {
        $Cons = DB::table('pedidoItens')
            ->select(DB::raw('count(*) as Quant'))
            ->where('idped','=',$idPed)
            ->first();
        $this->Qtd = $Cons->Quant;
        return $this->Qtd;
    }

    public function getRgItens($idPed) {
        $qry = DB::table('pedidoItens')
            ->select('produtos.Nome','pedidoItens.quant')
            ->join('produtos', 'produtos.ID', '=', 'pedidoItens.idprod')
            ->where('pedidoItens.idPed', '=', $idPed)
            ->get();
        return $qry;
    }

    public function getItens($idPed) {
        $qry = $this->getRgItens($idPed);
        $ret="";
        for($i=0;$i<$this->Qtd;$i++) {
            $ret.="<tr>";
            $ret.="<td class='centro'><strong>".$qry[$i]->Nome."</strong></td>";
            $ret.="<td class='centro'><strong>".$qry[$i]->quant."</strong></td>";
            $ret.="</tr>";
        }
        return $ret;
    }

    /*    public function tpEntrega($idPedido) {
            $Cons = DB::table('entrega')
                ->select('entrega.idEntregadora')
                ->where('idPedido','=',$idPedido)
                ->first();
            return $Cons->idEntregadora;
        }*/

    public function tpEnt($idForn) {

        if ($this->tpEntrega==-1) {
            // MataBug
            $this->getIdForn($idForn);
            if ($this->tpEntrega==-1) {
                echo 'this->tpEntrega = -1'; die;
            }
        }

        return $this->tpEntrega;
    }

    public function getEnderPedido() {
        // DEPOIS QUE PODER TER ENDEREÇO ALTERNATIVO DE ENTREGA
        // ESTA FUNÇÃO TERÁ QUE SER ADAPTADA

        $ClsEnderecos = new Enderecos;

        // QUANDO TIVER POSSIBILIDADE DE SER DE CIDADES DIFERENTES, O FORNECEDOR E O PEDIDO, ENTÃO DEVO MUDAR
        // PARA CASO NÃO SER DA MESMA CIDADE, MOSTRA A CIDADE, PARAMETRO 0
        $ender = $ClsEnderecos->GetEndereco($this->CliEnder, 1);
        return $ender;
    }

    public function getlatC() {
        return $this->latC;
    }

    public function getlonC() {
        return $this->lonC;
    }

    public function getidEndForn() {
        return $this->idEndForn;
    }

    public function getComentario() {
        return $this->Comentario;
    }

    public function CancInfTransBanc($idPedido) {
        DB::update("update pedido set status = 5 where idPed = ".$idPedido);
        DB::update("update vlrtransf set Status = 3, DtAtualiz = now() where IdPagto = ".$idPedido);
    }

}