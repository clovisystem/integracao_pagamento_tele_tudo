<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class FormasController extends Controller
{
	public function Aciona() {
		 return view('formas.index');
	}
}
