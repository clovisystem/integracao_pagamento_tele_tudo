<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clientes extends Model
{
    protected $table = 'clientes';
    
    public function EnderOK ($idUser) {
        $qry = DB::table('users')
            ->select('logradouro.adic')
            ->join('endereco', 'endereco.ID', '=', 'users.Endereco_ID')
            ->join('logradouro', 'logradouro.ID', '=', 'endereco.Logradouro_ID')
            ->where('users.id', '=', $idUser)
            ->first();
        if ($qry->adic==1) {
            return 0;
        } else {
            return 1;
        }
    }

    public function GravarCli(){
        $qry = DB::table('users')
            ->insert('EnderDesc','user', 'password', 'Nome', 'email', 'Cep', 'fone')
            ->values('?','?','?','?','?','?','?');
            
    }

    public function store(Request $request, Clientes $clientes)
    {
        $clientes->name = $request->name;
        $clientes->save();
    }
    
}
