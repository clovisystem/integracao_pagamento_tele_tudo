<?php
Session::put('site', 1);
?>
@extends('layouts.padrao')

@section('content')
    <?php
    $UA = $_SERVER['HTTP_USER_AGENT'];
    if (strrpos($UA, "Windows")) {
        $tamBanner = "width='1136' height='387'";
        $tamOnOf = "style='height:86px; width:250px'; ";
        $divBan = "class='jumbotron text-center'";
    } else {
        $tamBanner = "width='350' height='150'";
        $tamOnOf = "style='height:50px; width:150px'; ";
        $divBan = "align='center'";
    }
    ?>
    <div {{$divBan}}>
        <?php
        Session::put('forn', $forn->idEmpresa);    
        $qry = DB::table('empresa')
            ->leftJoin('pagina', 'pagina.idEmpresa', '=', 'empresa.idEmpresa')
            ->select('pagina.banner', 'pagina.CorLetra', 'pagina.whats', 'pagina.face', 'pagina.celular', 'empresa.Empresa', 'empresa.Telefone', 'empresa.idEndereco', 'empresa.email', 'empresa.idEntrega', 'empresa.dtON')
            ->where('empresa.idEmpresa', '=', $forn->idEmpresa)
            ->first();
        $Empresa = $qry->Empresa;
        if ($qry->banner > '') {
            echo "<img src='" . $qry->banner . "' " . $tamBanner . ">";
        } else {
            echo "<img src='http://www.cidadaofm88.com.br/site/wp-content/uploads/2012/11/seu_banner_aqui.jpg' " . $tamBanner . ">";
        }
        ?>
    </div>
    <h1 style="color: {{$qry->CorLetra}}">{{ $Empresa }}</h1>
    <?php
    if ($qry->idEntrega < 4) {
        $date1 = date('Y-m-d H:i:s', strtotime("-3 hours", strtotime(date('Y-m-d H:i:s'))));
        $Tempo = time_diff($date1, $qry->dtON);
        if ($Tempo < 97) {
            $img = "resources/assets/img/onLine.png";
        } else {
            $img = "resources/assets/img/OffLine.png";
        }
        echo "<img src=" . URL::to($img) . " " . $tamOnOf . ">";
    }
    ?>
    <div style="color: {{$qry->CorLetra}}">
        <h2>Contatos</h2>
        <h3>
            <?php
            if ($qry->whats > '') {
                // +55 51 9513-9696
                $Whats = str_replace("+", "", $qry->whats);
                $Whats = str_replace(" ", "", $Whats);
                $Whats = str_replace("-", "", $Whats);
                // "5551995139696";
                echo "WhatApp: " . $qry->whats;
                $p = strpos($UA, "Chrome/");
                if ($p > - 1) {
                    $NrV = substr($UA, $p + 7, 2);
                    if ($NrV > "64") {
                        // echo "&nbsp<input name='btWhats' type='button' onclick='EnvWhats(" . $Whats . ")' value='Enviar Mensagem'><br>";
                    }
                }
                /* Funcionam
                Chrome
                Vivaldi

                Não funcionam
                FireFox
                Opera
                Explorer
                */
            }
            if ($qry->face > '') {
                echo "FaceBook: <a href='https://www.facebook.com/" . $qry->face . "'<label>https://www.facebook.com/" . $qry->face . "</label></a><br>";
            }
            if ($qry->Telefone > '') {
                echo "Telefone: " . $qry->Telefone . "<br>";
            }
            if ($qry->celular > '') {
                echo "Celular: " . $qry->celular . "<br>";
            }
            $ClsEnderecos = new App\Enderecos;
            echo 'Endereço: ' . $ClsEnderecos->GetEndereco($qry->idEndereco, 0) . '<br>';
            if ($qry->email > '') {
                echo "Email: " . $qry->email . "<br>";
            }
            ?>
            <h3>
    </div>
    <script>
        function EnvWhats(Nr) {
            window.open("https://api.whatsapp.com/send?phone="+Nr+"", "_blank");
// 5551995139696
        }
    </script>

    <?php
    function time_diff($dt1, $dt2) {
        $y1 = substr($dt1, 0, 4);
        $m1 = substr($dt1, 5, 2);
        $d1 = substr($dt1, 8, 2);
        $h1 = substr($dt1, 11, 2);
        $i1 = substr($dt1, 14, 2);
        $s1 = substr($dt1, 17, 2);
        $y2 = substr($dt2, 0, 4);
        $m2 = substr($dt2, 5, 2);
        $d2 = substr($dt2, 8, 2);
        $h2 = substr($dt2, 11, 2);
        $i2 = substr($dt2, 14, 2);
        $s2 = substr($dt2, 17, 2);
        $r1 = date('U', mktime($h1, $i1, $s1, $m1, $d1, $y1));
        $r2 = date('U', mktime($h2, $i2, $s2, $m2, $d2, $y2));
        return ($r1 - $r2);
    }
    ?>
@stop
