<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Adm extends Model
{
    protected $table = 'users';

    public function ValorPlay()
    {
        $cons = DB::table('conta')
            ->select('Saldo')
            ->where('idConta', '=', 2)
            ->first();
        return $cons->Saldo;
    }

    public function getLojasAbertas() {
        $sql="SELECT Count(*) as Quant ";
        $sql.="FROM empresa ";
        $sql.="WHERE SUBTIME( Now( ) , '00:02:00' ) < dtON ";
        // $sql.=" and idEmpresa > 1 ";
        $ConsLojas = DB::select( DB::raw($sql));
        $QtdLojas = "";
        foreach ($ConsLojas as $Lojas) {
            $QtdLojas = $Lojas->Quant;
        }
        return $QtdLojas;
    }

    public function VeSf() {
        $SisFin = DB::table('pessoaperfil')
            ->select(DB::raw('idPessoa'))
            ->where('idPerfil', '=', 3) // FINANCEIRO
            ->first();
        $sql = "SELECT Count(*) as Quant ";
        $sql.="FROM empresa ";
        $sql.="Where idEmpresa = 1 ";
        $sql.="and SUBTIME( Now( ) , '00:02:00' ) < dtON ";
        $Cons = DB::select( DB::raw($sql));
        return $Cons[0]->Quant;
    }

    public function VeModo() {
        $qry = DB::table('config')
            ->select('Modo', 'Debug','Som')
            ->where('ID', '=', 1)
            ->get();
        $modo=$qry[0]->Modo;
        $this->Debug = $qry[0]->Debug;
        $this->Som = $qry[0]->Som;
        $cor_ativ = array('','');
        $modos = array($cor_ativ,$cor_ativ,$cor_ativ, $cor_ativ);
        $modos[$modo][0]="style='background-color: #FFF838'";
        $modos[$modo][1]="active";
        return $modos;
    }

    public function Debug() {
        $modos = array('','');
        $modos[$this->Debug]=' selected ';
        return $modos;
    }

    public function OpSom() {
        $modos = array('','');
        $modos[$this->Som]=' selected ';
        return $modos;
    }

    public function SetaModo($modo) {
        DB::update('update config set Modo = '.$modo.' where id = 1');
    }

    // SISTEMA FINANCEIRO

    public function getTransferencias() {
        $qry = DB::table('notificacao')
            ->join('pedido', 'pedido.idPed', '=', 'notificacao.idPedido')
            ->join('users', 'users.id', '=', 'users.User')
            ->join('vlrtransf', 'vlrtransf.id', '=', 'notificacao.idTransf')
            ->select('notificacao.Valor', 'notificacao.Hora','notificacao.idAviso','notificacao.vizualizado',
                'users.Nome','users.fone',
                'vlrtransf.id as idTrans','vlrtransf.BCO', 'vlrtransf.AGE','vlrtransf.CTA',
                'pedido.idPed')
            ->where('notificacao.Confirmado', '=', null)
            ->get();
        return $qry;
    }

    public function Visualizou($id) {
        DB::update("update notificacao set vizualizado = now() where idAviso = " .$id);
    }

    public function EnderNovo() {
        /* $qry = DB::table('logradouro')
            ->select('users.Nome','tplogradouro.nometplog','logradouro.NomeLog','endereco.Numero')
            ->join('endereco', 'endereco.Logradouro_ID', '=', 'logradouro.ID')
            ->join('users', 'users.Endereco_ID', '=', 'endereco.ID')
            ->join('tplogradouro', 'tplogradouro.ID', '=', 'logradouro.TpLogradouro_ID')
            ->where('logradouro.adic', '=', 1)
            ->get(); */

        $qry = DB::table('logra')
            ->select('users.Nome','users.Cep','users.email',
                'tplogradouro.nometplog',
                'logra.NomeLog','logra.ID',
                'endereco.Numero',
                'bairro.NomeBairro',
                'cidade.NomeCidade')
            ->join('endereco', 'endereco.Logradouro_ID', '=', 'logra.ID')
            ->join('users', 'users.Endereco_ID', '=', 'endereco.ID')
            ->join('tplogradouro', 'tplogradouro.ID', '=', 'logra.TpLogradouro_ID')
            ->join('bairro', 'bairro.id', '=', 'endereco.idBairro')
            ->join('cidade', 'cidade.ID', '=', 'bairro.idcidade')
            ->where('logra.adic', '=', 1)
            ->get();
        if ($qry==null) {
            $ret= "Sem Novos Endereços<Br>";
        } else {
            $ret = '<h1>';
            $i=0;
            foreach ($qry as $Nvos) {

                $ret.= $Nvos->Nome.': '.

                    $Nvos->Cep.' '.
                    $Nvos->NomeCidade.' '.
                    $Nvos->NomeBairro.' '.

                    $Nvos->nometplog.' '.$Nvos->NomeLog.' '.$Nvos->Numero.' '.
                    $Nvos->email.' '.

                    "<input name='btHE".$i."' type='button' value='habilitar' onclick='HabEnder(".$Nvos->ID.")'></Br>";
            }
            $ret.='</h1>';
        }
        return $ret;
    }

    public function SetaON() {
        DB::update('update config set dtON = now() where ID = 1');
        // DB::update('update config set dtON = now() where ID = 11');
    }

    public function ListaLojasAbertas() {
        $sql="SELECT Empresa, TpAcesso ";
        $sql.="FROM empresa ";
        $sql.="WHERE SUBTIME( Now( ) , '00:02:00' ) < dtON ";
        $sql.="Order by Empresa ";
        $Cons = DB::select( DB::raw($sql));
        return $Cons;
    }

    public function BuscaGeral($busca) {
        $sql="SELECT produtos.Nome, produtos.Descricao, produtos.Valor, ";
        $sql.="empresa.Empresa, empresa.dtON, SUBTIME( Now( ) , '00:02:00' ) DtX ";
        $sql.="FROM produtos ";
        $sql.="Inner Join empresa on empresa.idEmpresa = produtos.Empresax_ID ";
        $sql.="Where (produtos.Nome like '%".$busca."%' ";
        $sql.=" or produtos.Descricao like '%".$busca."%' )";
        $Cons = DB::select( DB::raw($sql));
        $ret='';
        foreach ($Cons as $regs) {
            $ret.= "<p><b style='color: ";
            if ($regs->dtON>$regs->DtX) {
                $ret.="#0000FF'> ";
            } else {
                $ret.="#FF0000'> ";
            }
            $ret.=$regs->Empresa."</b> ".$regs->Nome." - ".$regs->Descricao." R$ ".number_format($regs->Valor, 2, ',', '.')."</p>";
        }
        return $ret;
    }

    public function ultBusca() {
        $sql = "select texto, cep, encontrado ";
        $sql.=",DATE_SUB(data,INTERVAL 3 HOUR) as HR ";
        $sql.="from procuras ";
        $sql.="Where data > DATE_SUB(now(),INTERVAL 1 DAY) ";
        $sql.="order by id desc ";
        // $sql.="LIMIT 1";
        $qry = DB::select( DB::raw($sql));
        $ret='';
        $n=0;
        foreach ($qry as $reg) {
            $n++;
            $ret.="<h4 style='color: ";

            switch ($reg->encontrado) {
                case 0: // Não encontrado
                    {
                        $ret.="#FF0000";
                        break;

                    }
                case 1: // Encontrado
                    {
                        $ret.="#0000FF";
                        break;
                    }
                case 2: // Comprado
                    {
                        $ret.="#056f2b";
                        break;
                    }
                case 3: // Serviço Encontrado
                    {
                        $ret.="#CC0066";
                        break;
                    }
            }
            /*if ($reg->encontrado>0) {
                   $ret.="#0000FF";
                } else {
                   $ret.="#FF0000";
                }*/
            $ret.= "'>".$n.") ".$reg->texto." ".$reg->cep." ".$reg->HR."</h4>";
        }
        return $ret;
    }

    public function MontaCbCoringas() {
        $qryC = DB::table('categoriasprodutos')
            ->select('ID','Descricao')
            ->orderBy('Descricao')
            ->get();
        echo "<Br><input name='id' type='text'>";
        echo "<select name='Cat' >";
        foreach ($qryC as $regC) {
            echo "<option value='".$regC->ID."'>".$regC->Descricao."</option>";
        }
        echo "</select>";
        echo "<input value='Adicionar' type='submit' >";
    }

    public function Coringas() {
        $ret="<Br>";
        $qry = DB::table('palavras')
            ->join('categoriasprodutos', 'categoriasprodutos.ID', '=', 'palavras.categoria')
            ->select('palavras.palavra','categoriasprodutos.Descricao')
            ->orderBy('palavras.palavra')
            ->get();
        foreach ($qry as $reg) {
            $ret.=$reg->palavra." - ".$reg->Descricao."<Br>";
        }
        return $ret;
    }

    public function vPlay() {
        $qry = DB::table('conta')
            ->select('Pendente')
            ->where('idConta', '=', 2)
            ->first();
        return "R$ ".number_format($qry->Pendente, 2, ',', '.');
    }

    public function qUser() {
        $sql = "SELECT Count(0) as Quant FROM users Where updated_at > DATE_SUB(now(),INTERVAL 1 DAY) ";
        $qry = DB::select( DB::raw($sql));
        return $qry[0]->Quant;
    }

    public function OrcHjr() {
        $sql = "SELECT Count(0) as Quant FROM pedido Where Data > DATE_SUB(now(),INTERVAL 1 DAY) ";
        $qry = DB::select( DB::raw($sql));
        return $qry[0]->Quant;
    }

    public function PedHj() {
        $sql = "SELECT Count(0) as Quant FROM pedido Where Data > DATE_SUB(now(),INTERVAL 1 DAY) ";
        $sql.=" and status = 4 ";
        $qry = DB::select( DB::raw($sql));
        return $qry[0]->Quant;
    }

}