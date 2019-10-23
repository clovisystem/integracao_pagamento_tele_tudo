<?php

Route::get('/', function () {
    return View::make('produtos/index');
});

Route::auth();

Route::post('entrar', "HomeController@postEntrar"); 

Route::get('entrar', 'HomeController@entrar');

Route::get('servicos', 'ServicosController@index');

Route::get('fornecedor', 'FornecedorController@index');

Route::get('adm', 'AdmController@index');

Route::resource('produtos', 'ProdutosController');

Route::get('sair', 'HomeController@getSair');

Route::get('criapedido', 'PedidoController@Criapedido');

Route::get('entrega', 'EntregaController@create');

//Route::get('confirma', 'PedidoController@Aciona');

Route::post('confirma', 'PedidoController@Aciona');

Route::get('formas/{ped?}/{id?}', ['as' => 'formas', 'uses' => 'FormasController@Aciona']);

Route::get('vlrtransf', 'VlrtransfController@Aciona');

Route::get('pagtodireto', 'PedidoController@Pagtodireto');

Route::resource('pedido', 'PedidoController');

Route::get('portaldaluz.com', 'PedidoController@portaldaluz');

Route::get('processo', array('uses' => 'ProcessoController@Aciona'));

Route::get('resumo', array('uses' => 'PedidoController@Resumo'));

Route::get('CriaUser', 'PessoaController@CriaUser');

Route::resource('pessoa', 'PessoaController');

Route::get('perfil', array('uses' => 'HomeController@perfil'));
Route::post('perfil', array('uses' => 'HomeController@perfil'));

Route::post('salvarcadastro', "PessoaController@store");

Route::resource('pessoa/create', 'PessoaController@index');

Route::get('pessoa/create', 'PessoaController@index');

Route::get('layout', 'PessoaController@layout');

Route::get('pessoas/create', 'PessoaController@CriaUser');

//Route::post('credito', 'OthersOptionsController@Aciona'); FOI DESATIVADO PARA INCLUSAO DA PAGINA DE CARTAO PAGAMENTO/CREDITO

Route::post('pessoa', 'PessoaController@insert');

Route::post('produtos/index', 'ProdutosController@Redireciona');

Route::any('pagamentos/credito', 'OthersOptionsController@Aciona');

Route::any('pagamento/index', [ 'as' => 'cartao.index', 'uses' => 'OthersOptionsController@Aciona' ]); 



Route::post('checkout', 'OthersOptionsController@Checkout');

//Route::get('checkout', 'OthersOptionsController@Checkout');

Route::post('store', 'OthersOptionsController@Store');

Route::get('/{site}', 'PaginaController@aciona');//DEVE FICAR COMO ĹTIMA OPÇÃO SENÃO VAI PEGAR TODAS AS ROTAS QUE ESCREVER


