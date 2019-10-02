<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Contamov extends Model
{
    protected $table = 'contamov';

    private $pedido=0;
    private $idEmpresa=0;
    private $ContaForn=0;

    public function setPedido($pedido) {
        $this->pedido=$pedido;
    }

    private function Consulta($sql) {
        $qry = DB::select( DB::raw($sql));
        $ret=0;
        foreach ($qry as $qryC) {
            $ret=$qryC->Campo;
        }
        return $ret;
    }

    public function credita_entregadora($idEntregadora, $vEntrega) {
        // Cria lançamento de movimentação de valor, para a entregadora
        DB::insert('insert into contamov (orig, dest, data, pedido, valor) values (?, ?, ?, ?, ?)',
            [0, $idEntregadora, new DateTime, $this->pedido, $vEntrega]);

        $sql="select idPessoa as Campo from entregadoras where entregadoras.ID = ".$idEntregadora;
        $idPessoaEntrega = $this->Consulta($sql);

        $sql="SELECT idConta as Campo FROM conta WHERE idPessoa = ".$idPessoaEntrega;
        $idConta = $this->Consulta($sql);

        $this->CreditaNaConta($idConta, $vEntrega);
    }

    private function CreditaNaConta($Conta, $Adic) {
        DB::update("update conta set Saldo = Saldo + ".$Adic.", updated_at = Now() where idConta = ".$Conta);
    }

    public function DescobreFornecedorPelosItens($Pedido) {
        $sql="select produtos.Empresax_ID as Campo ";
        $sql.="from pedidoItens ";
        $sql.="inner join produtos on produtos.ID = pedidoItens.idprod ";
        $sql.="where pedidoItens.idPed = ".$this->pedido;
        $idEmpresa = $this->Consulta($sql);
        $this->idEmpresa=$idEmpresa;
    }

    public function credita_fornecedor($vProdutos, $Pendente) {
        // Essa operação vai ser mais complicada um pouco
        // Um pedido pode ser de vários fornecedores diferentes

        /*        $sql="select produtos.Empresax_ID as Campo ";
                $sql.="from pedidoItens ";
                $sql.="inner join produtos on produtos.ID = pedidoItens.idprod ";
                $sql.="where pedidoItens.idPed = ".$this->pedido;
                $idEmpresa = $this->Consulta($sql);
                $this->idEmpresa=$idEmpresa;*/
        // $this->DescobreFornecedorPelosItens($this->pedido);

        // echo "vProdutos = ".$vProdutos." Pendente = ".$Pendente;

        $sql="select conta.idConta, conta.Saldo,empresa.idEmpresa, empresa.idCaptador ";
        $sql.="from empresa ";
        $sql.="inner join conta on conta.idPessoa = empresa.idPessoa ";
        $sql.="where empresa.idEmpresa = ".$this->idEmpresa;

        $qry = DB::select( DB::raw($sql));
        $captador=0;
        foreach ($qry as $qryC) {
            DB::insert('insert into contamov (orig, dest, data, pedido, valor, Pendente) values (?, ?, ?, ?, ?, ?)',
                [0, $qryC->idConta, new DateTime, $this->pedido, $vProdutos, $Pendente]);
            /*DB::insert('insert into contamov (orig, dest, data, pedido, valor, ) values (?, ?, ?, ?, ?)',
                [0, $qryC->idConta, new DateTime, $this->pedido, $vProdutos]);*/

            DB::update("update conta set Saldo = Saldo + ".$vProdutos.", Pendente = Pendente + ".$Pendente.", updated_at = Now() where idConta = ".$qryC->idConta);
            // $this->CreditaNaConta($qryC->idConta, $vProdutos);

            $captador=$qryC->idCaptador;
            break;
        }
        return $captador;
    }

    public function credita_fornecedorCEntrega($FornecRecebeu, $vSistema) {
        // Esse metodo é indicado para fornecedores que tem sua própria entregadora
        // Então não precisa prever mais de um fornecedor por pedido

        // $this->idEmpresa=$FornecRecebeu;

        $sql="select conta.idConta, conta.Saldo, conta.Pendente, empresa.idCaptador ";
        $sql.="from empresa ";
        $sql.="inner join conta on conta.idPessoa = empresa.idPessoa ";
        $sql.="where empresa.idEmpresa = ".$this->idEmpresa;

        // echo $sql; die;

        $qry = DB::select( DB::raw($sql));

        // Criar um registro em ContaMov
        // Valor = Valor da Venda + TeleEntrega
        // Pendência = Nosso valor

        $captador=0;
        foreach ($qry as $qryC) {

            $this->ContaForn=$qryC->idConta;

            DB::insert('insert into contamov (orig, dest, data, pedido, valor, Pendente) values (?, ?, ?, ?, ?, ?)',
                [0, $this->ContaForn, new DateTime, $this->pedido, $FornecRecebeu, $vSistema]);
            DB::update("update conta set Saldo = Saldo + ".$FornecRecebeu.", Pendente = Pendente + ".$vSistema.", updated_at = Now() where idConta = ".$qryC->idConta);
            $captador=$qryC->idCaptador;
            break;
        }
        return $captador;
    }

    public function credita_PendenciaSistema($vSistema, $idContaForn) {
        DB::insert('insert into contamov (orig, dest, data, pedido, Pendente) values (?, ?, ?, ?, ?)',
            [$idContaForn, 1, new DateTime, $this->pedido, $vSistema]);
        DB::update("update conta set Pendente = Pendente + ".$vSistema.", updated_at = Now() where idConta = 1");
    }

    public function credita_Pendencia_captador($captador, $valor) {
        $contaCap=$this->DescobreContaDoCaptador($captador);
        DB::insert('insert into contamov (orig, dest, data, pedido, Pendente) values (?, ?, ?, ?, ?)',
            [1, $contaCap, new DateTime, $this->pedido, $valor]);
        DB::update("update conta set Pendente = Pendente + ".$valor.", updated_at = Now() where idConta = ".$contaCap);
    }

    private function DescobreContaDoCaptador($idCaptador) {
        $sql="select idPessoa as Campo ";
        $sql.="from captador ";
        $sql.="where captador.idCaptador = ".$idCaptador;
        $idPessoaCap = $this->Consulta($sql);

        // echo "idPessoaCap = ".$idPessoaCap."<p>";

        $sql="select idConta as Campo ";
        $sql.="from conta ";
        $sql.="where idPessoa = ".$idPessoaCap;
        $contaCap = $this->Consulta($sql);

        // echo "contaCap = ".$contaCap."<p>";

        return $contaCap;
    }

    public function credita_captador($captador, $valor) {

        // Modificar
        // Colocar um campo que aceite muitas casas decimais

        $contaCap=$this->DescobreContaDoCaptador($captador);

        /*        $sql="select idPessoa as Campo ";
                $sql.="from captador ";
                $sql.="where captador.idCaptador = ".$captador;
                $idPessoaCap = $this->Consulta($sql);

                $sql="select idConta as Campo ";
                $sql.="from conta ";
                $sql.="where idPessoa = ".$idPessoaCap;
                $contaCap = $this->Consulta($sql);*/

        DB::insert('insert into contamov (orig, dest, data, pedido, valor) values (?, ?, ?, ?, ?)',
            [0, $contaCap, new DateTime, $this->pedido, $valor]);

        $this->CreditaNaConta($contaCap, $valor);
    }

    public function credita_sistema($valor) {
        DB::insert('insert into contamov (dest, data, pedido, valor) values (?, ?, ?, ?)',
            [0, new DateTime, $this->pedido, $valor]);

        $this->CreditaNaConta(1, $valor);
    }

    public function getContaForn() {
        return $this->ContaForn;
    }

}