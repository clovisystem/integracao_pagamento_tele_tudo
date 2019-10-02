@extends('layouts.padrao')

@section('content')

    <?php
    if (Session::has('message')) {
        echo "<div class='alert alert-info'><h3>".Session::get('message')."</h3></div>";
        Session::forget('message');
    }
    ?>

    <div class="jumbotron text-center">
        <?php

        // $iduser = 42; Eduardo Rarts
        $iduser = Auth::id();

        $qry = DB::table('empresa')
            ->select('Empresa', 'idEmpresa')
            ->where('idPessoa', '=', $iduser)
            ->first();
        $Empresa = $qry->Empresa;

        $qryP = DB::table('pagina')
            ->select('fundo','banner','CorLetra','celular','face','whats')
            ->where('idempresa', '=', $qry->idEmpresa)
            ->first();
        $banner='';
        $CorLetra='';
        $fundo='';
        $celular='';
        $face='';
        $whats='';
        if ($qryP!=null) {
            $banner=$qryP->banner;
            $CorLetra=$qryP->CorLetra;
            $fundo=$qryP->fundo;
            $celular=$qryP->celular;
            $face=$qryP->face;
            $whats=$qryP->whats;
        }
        ?>
            {{ Form::model($qry, array('action' => array('PaginaController@salvapagina', $qry->idEmpresa), 'method' => 'GET')) }}
        <?php
            if ($banner>'') {
                echo "<img src='".$banner."' width='1136' height='387'>";
            }
        ?>
    </div>
    <h1 style="color: {{$CorLetra}}">{{ $Empresa }}</h1>
    <p>
        <strong style='color: {{$CorLetra}}'>Estado: ON-LINE</strong><br>
    </p>
    <table style="width: 100%">
        <tr>
            <td style="width: 118px; color: {{$CorLetra}}">Banner</td>
            <td><input name="txBanner" type="text" style="width: 477px" value="{{$banner}}" ></td>
        </tr>
        <tr>
            <td style="width: 118px; color: {{$CorLetra}}">Imagem de fundo</td>
            <td><input name="txImgFundo" type="text" style="width: 477px" value="{{$fundo}}" ></td>
        </tr>

        <tr>
            <td style="width: 118px; color: {{$CorLetra}}">Celular</td>
            <td><input name="txcelular" type="text" style="width: 477px" value="{{$celular}}" ></td>
        </tr>
        <tr>
            <td style="width: 118px; color: {{$CorLetra}}">FaceBook</td>
            <td>https://www.facebook.com/<input name="txface" id="txface" type="text" style="width: 477px" value="{{$face}}" ></td>
        </tr>
        <tr>
            <td style="width: 118px; color: {{$CorLetra}}">Whats (caso não seja o mesmo do celular)</td>
            <td><input name="txwhats" type="text" style="width: 477px" value="{{$whats}}" ></td>
        </tr>

        <tr>
            <td></td>
            <td>
                <dl style="clear: left;">
                    <dt><label style="color: {{$CorLetra}}" id="lbCor">Cor do texto:</label></dt>
                    <dd id="color_palette_placeholder" class="color_palette_placeholder" data-orientation="h" data-height="12" data-width="15" data-bbcode="true">
                        <table class="not-responsive colour-palette horizontal-palette" style="width: auto;">
                            <tbody>
                            <tr>
                                <td style="background-color: #000000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #000040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #000080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #0000BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #0000FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #004000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #004040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #004080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #0040BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #0040FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #008000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #008040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #008080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #0080BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #0080FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00BF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00BF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00BF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00BFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00BFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00FF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00FF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00FF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00FFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #00FFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                            </tr>
                            <tr>
                                <td style="background-color: #400000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #400040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #400080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #4000BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #4000FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #404000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #404040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #404080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #4040BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #4040FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #408000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #408040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #408080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #4080BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #4080FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40BF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40BF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40BF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40BFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40BFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40FF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40FF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40FF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40FFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #40FFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                            </tr>
                            <tr>
                                <td style="background-color: #800000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #800040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #800080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #8000BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #8000FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #804000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #804040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #804080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #8040BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #8040FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #808000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #808040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #808080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #8080BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #8080FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80BF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80BF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80BF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80BFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80BFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80FF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80FF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80FF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80FFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #80FFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                            </tr>
                            <tr>
                                <td style="background-color: #BF0000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF0040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF0080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF00BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF00FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF4000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF4040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF4080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF40BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF40FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF8000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF8040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF8080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF80BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BF80FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFBF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFBF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFBF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFBFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFBFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFFF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFFF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFFF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFFFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #BFFFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                            </tr>
                            <tr>
                                <td style="background-color: #FF0000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF0040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF0080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF00BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF00FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF4000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF4040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF4080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF40BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF40FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF8000; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF8040; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF8080; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF80BF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FF80FF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFBF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFBF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFBF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFBFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFBFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFFF00; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFFF40; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFFF80; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFFFBF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                <td style="background-color: #FFFFFF; width: 15px; height: 12px;" onclick="clkcor(this.style.backgroundColor)"></td>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </dd>
                </dl>
            </td>
        </tr>
    </table>
    <input name='txIdEmpresa' type='text' hidden='hidden' value='{{$qry->idEmpresa}}' /></p>
    <input name='txCorLetra' id='txCorLetra' type='hidden' value='{{$CorLetra}}' /></p>
    <input class="btn btn-primary Salvar" type="submit" value="Salvar">
    </form>

    <script>
        function clkcor (cor) {
            document.getElementById('lbCor').style.color=cor;
            var CorH = RGBToHex(cor);
            document.getElementById('txCorLetra').value=CorH;
        }

        function RGBToHex(rgb) {
            var sep = rgb.indexOf(",") > -1 ? "," : " ";
            rgb = rgb.substr(4).split(")")[0].split(sep);
            var r = (+rgb[0]).toString(16),
                g = (+rgb[1]).toString(16),
                b = (+rgb[2]).toString(16);
            if (r.length == 1) r = "0" + r;
            if (g.length == 1) g = "0" + g;
            if (b.length == 1) b = "0" + b;
            return "#" + r + g + b;
        }

    </script>

@stop
