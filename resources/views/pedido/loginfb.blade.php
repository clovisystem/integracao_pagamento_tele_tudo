<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
</head>

<body>
<?php
$app_secret = "9559a449eece386b90344842e4514f39";
$app_id = "395697367529746";
$redirect_uri = urlencode("https://www.tele-tudo.com/loginfb");

$code = str_replace("#_=_", "", $_GET['code']);
$api = "https://graph.facebook.com/v2.8/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_uri&client_secret=$app_secret&code=$code";
$get_content = file_get_contents($api);
$json = json_decode($get_content, true);
$access_token = $json['access_token'];
$get_info = "https://graph.facebook.com/me/?fields=email,name,first_name,last_name,id&access_token=$access_token";
$content_info = file_get_contents($get_info);
$info_json = json_decode($content_info, true);
$idFace = $info_json['id'];

echo

$Nome = $info_json['first_name'];

$cPes = new Pessoa();

$cPes->IDpeloFace($idFace);
$id = $cPes->IDpeloFace($idFace);

if ($id==0) {
    echo "Usuário não reconhecido<Br>";
    echo "Proceder o registro<Br>";
    echo "Redirecionar para a gravação do endereço no perfil";
} else {
    echo "Bem vindo ".$Nome." 2<Br>";
    Auth::loginUsingId($ultPessoa);
    if (Session::has('PEDIDO')) {
        Session::put('Nome',$Nome);
        $str = "posface?User=".$id.
            '&Ped='.Session::get('PEDIDO').
            '&Tpe='.Session::get('TpEntrega').
            '&Tes='.Session::get('Teste');
    } else {
        $str = "/";
    }
    ?>
    <script>
        document.location.assign("{{$str}}");
    </script>
    <?php
}
?>
</body>

</html>
