<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $table = 'notificacao';

    public function InformaQueComprou($idPedido, $idFornProd, $FornecRecebeu, $FormaPagto, $Troco, $idPesq, $Com)
    {
        $sql = "Insert into notificacao (idPedido, idFornec, Valor, Hora, Ativo) Values ( ";
        $sql = $sql . $idPedido . ', ';        // idPedido
        $sql = $sql . $idFornProd . ', ';       // Fornecedor que vai receber a notificação
        $sql = $sql . $FornecRecebeu . ', ';    // Valor
        $sql = $sql . 'now(),';
        $sql = $sql . '1)';               // Hora
        DB::update($sql);
        $idAviso = DB::table('notificacao')->max('idAviso');
        if ($Com == '') {
            $Com = 'null';
        }
        DB::update("update pedido 
          set Status = 4, 
            FormaPagto = " . $FormaPagto . ",
            Valor = " . $FornecRecebeu . ",
            Troco = " . $Troco . ",
            Comentario = " . $Com . " 
             where idPed = " . $idPedido);
        if ($idPesq > 0) {
            DB::update("update procuras set encontrado = 2 where id = " . $idPesq);
        }

        $clsEntrega = new Entrega();
        $clsEntrega->Efetiva($idPedido);
//         $clsEntrega->EfetivaPedidoNoBDSemEntrega($idPedido);

        return $idAviso;
    }

    public function VeSeOFornViu($idAviso) {
        $Cons = DB::table('notificacao')
            ->select('Ativo')
            ->where('idAviso', '=', $idAviso)
            ->first();
        return $Cons->Ativo;
    }

}