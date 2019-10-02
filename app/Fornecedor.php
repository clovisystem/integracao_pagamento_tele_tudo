<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fornecedor extends Model
{
    protected $table = 'empresa';
    private $vRepasse = 0;
    private $idEmpresa = 0;
    private $tpEntrega = 0;

    public function getRepasse() {
        return number_format($this->vRepasse, 2, ',', '.');
    }

    public function gettpEntrega() {
        return $this->tpEntrega;
    }

    public function getNotificacoes() {
        if ($this->gettpEntrega()==0) {
            // PELA PLAY-DELIVERY
            $qry = DB::table('notificacao')
                ->join('pedido', 'pedido.idPed', '=', 'notificacao.idPedido')
                ->join('users', 'users.id', '=', 'pedido.User')
                ->join('vlrtransf', 'vlrtransf.id', '=', 'notificacao.idTransf')
                ->select('notificacao.Valor', 'notificacao.Hora','notificacao.idAviso','notificacao.vizualizado','notificacao.Confirmado',
                    'users.Nome','users.fone',
                    'vlrtransf.id as idTrans','vlrtransf.BCO', 'vlrtransf.AGE','vlrtransf.CTA',
                    'pedido.idPed','pedido.status as stPedido')
                ->where('notificacao.idFornec', '=', $this->idEmpresa)
                ->where('notificacao.Ativo', '=', 1)
                ->get();
        } else {
            $qry = DB::table('notificacao')
                ->join('pedido', 'pedido.idPed', '=', 'notificacao.idPedido')
                ->join('users', 'users.id', '=', 'pedido.User')
                ->select('notificacao.Valor', 'notificacao.Hora','notificacao.idAviso','notificacao.vizualizado',
                    'users.Nome','users.fone',
                    'pedido.idPed','pedido.status as stPedido')
                ->where('notificacao.idFornec', '=', $this->idEmpresa)
                ->where('notificacao.Confirmado', '=', null)

                // Tava desabilitado e habilitei para funcionar para o fornecedor, Rancho
                ->where('notificacao.Ativo', '=', 1)

                ->get();
        }
        return $qry;
    }

    public function getUltCompra($idPed) {
        $qry = DB::table('notificacao')
            ->join('pedido', 'pedido.idPed', '=', 'notificacao.idPedido')
            ->join('users', 'users.id', '=', 'pedido.User')
            ->select('notificacao.Valor',
                'users.Nome','users.fone', 'users.Endereco_ID')
            ->where('idPedido', '=', $idPed)
            ->first();
        return $qry;
    }

    public function Visualizou($id, $idPedido) {
        DB::update("update notificacao set vizualizado = now() where idAviso = " .$id);
    }

}
