<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Financeiro extends Model
{
    public function getTransferencias($idFornec) {
        $qry = DB::table('notificacao')
            ->join('pedido', 'pedido.idPed', '=', 'notificacao.idPedido')
            ->join('users as PC', 'PC.id', '=', 'pedido.User')
            ->join('vlrtransf', 'vlrtransf.ID', '=', 'notificacao.idTransf')
            ->join('contasbancarias', 'contasbancarias.id', '=', 'vlrtransf.idConta')
            ->join('bancos', 'bancos.cod', '=', 'contasbancarias.idBanco')
            ->select('notificacao.Valor', 'notificacao.Hora','notificacao.idAviso','notificacao.vizualizado','notificacao.Confirmado',
                'PC.Nome','PC.fone',
                'vlrtransf.ID as idTrans','vlrtransf.BCO', 'vlrtransf.AGE','vlrtransf.CTA',
                'pedido.idPed','pedido.status as stPedido',
                'bancos.banco','bancos.apelido')
            ->where('notificacao.idFornec', '=', $idFornec)
            /*->where('notificacao.Confirmado', '=', null)*/
            ->get();
        return $qry;
    }

    public function getTransferenciasADM() {
        $qry = DB::table('notificacao')
            ->join('pedido', 'pedido.idPed', '=', 'notificacao.idPedido')
            ->join('users as PC', 'PC.id', '=', 'pedido.User')
            ->join('empresa', 'empresa.idEmpresa', '=', 'notificacao.idFornec')
            ->leftJoin('vlrtransf', 'vlrtransf.ID', '=', 'notificacao.idTransf')
            ->leftJoin('contasbancarias', 'contasbancarias.id', '=', 'vlrtransf.ID')
            ->leftJoin('bancos', 'bancos.cod', '=', 'contasbancarias.idBanco')
            ->select('notificacao.Valor', 'notificacao.Hora','notificacao.idAviso','notificacao.vizualizado',
                'PC.Nome','PC.fone',
                'vlrtransf.ID as idTrans','vlrtransf.BCO', 'vlrtransf.AGE','vlrtransf.CTA',
                'pedido.idPed','pedido.Comentario',
                'bancos.banco','bancos.apelido',
                'empresa.Empresa')
            ->where('notificacao.Ativo', '=', 1)
            ->get();
        return $qry;
    }


    public function Visualizou($id, $idPedido) {
        DB::update("update notificacao set vizualizado = now() where idAviso = " .$id);

        // $clsEntrega = new Entrega();
        // $clsEntrega->Efetiva($idPedido);
    }

    public function Confirmou($idAviso, $idPed, $idTrans) {
        DB::update("update notificacao set Confirmado = now(), ativo = 0 where idAviso = " .$idAviso);
        DB::insert('insert into pagamento (TP, idTrans, idPed) values (?, ?, ?)',
        [
            1,
            $idTrans,
            $idPed
        ]);
    }

}