<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{

    protected $table = 'users';


    protected $fillable = [
        'user',
        'Nome',
        'email',
        'fone',
        'password',
        'remember_token',
        'txCep',
        'txLogra',
        'cbLogra'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


   
}
