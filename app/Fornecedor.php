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
    private $idPessoa = 0;
    private $Nome = '';
    private $user = '';
    private $DiaAcerto=0;
    private $idEntrega=0;
    private $site = "";
    private $catEmpr = 0;
    private $QtdPPag=20;

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

    public function SetIdPessoa($id) {
        $qry = DB::table('users')
            ->join('empresa', 'empresa.idPessoa', '=', 'users.id')
            ->select('empresa.idEmpresa', 'empresa.Empresa', 'empresa.tpEntrega','empresa.DiaAcerto','empresa.site',
                'users.user','categoriasempresas_ID','idEntrega')
            ->where('users.id', '=', $id)
            ->get();
        if ($qry!=null) {
            $this->idEmpresa = $qry[0]->idEmpresa;
            $this->Nome = $qry[0]->Empresa;
            $this->user = $qry[0]->user;
            $this->idPessoa=$id;
            $this->tpEntrega = $qry[0]->tpEntrega;
            $this->DiaAcerto=$qry[0]->DiaAcerto;
            $this->site =$qry[0]->site;
            $this->catEmpr =$qry[0]->categoriasempresas_ID;
            $this->idEntrega =$qry[0]->idEntrega;
            return true;
        } else {
            return false;
        }
    }

    public function getidEmpresa() {
        return $this->idEmpresa;
    }

    public function ProdForn($pag, $busca) {
        $in=($pag-1)*20;
        if ($busca>'') {
            $sql = "select ID, Nome, Descricao, Valor, CategoriasProdutos_ID, Imagem, ImgNorm, Peso, Disponivel ";
            $sql.="from produtos ";
            $sql.="where Empresax_ID = ".$this->idEmpresa;
            // $sql.=" and Disponivel = 1 ";
            $sql.=" and (Nome like '%".$busca."%' ";
            $sql.=" or Descricao like '%".$busca."%' ) ";
            $sql.="limit ".$this->QtdPPag;
            $sql.=" offset ".$in;
            $qry = DB::select(DB::raw($sql));
        } else {
            $qry = DB::table('produtos')
                ->select('ID','Nome', 'Descricao', 'Valor', 'CategoriasProdutos_ID', 'Imagem', 'ImgNorm', 'Peso', 'Disponivel')
                ->where('Empresax_ID', '=', $this->idEmpresa)
                // ->where('Disponivel', '=', 1)
                ->skip($in)->take(20)
                ->get();
        }
        return $qry;
    }

    public function getTotLista($busca) {
        if ($busca>'') {
            $sql = "select count(*) as Quant ";
            $sql.="from produtos ";
            $sql.="where Empresax_ID = ".$this->idEmpresa;
            $sql.=" and (Nome like '%".$busca."%' ";
            $sql.=" or Descricao like '%".$busca."%' )";
            $qry = DB::select( DB::raw($sql));
            $this->QtdRegs =$qry[0]->Quant;
        } else {
            $this->QtdRegs = DB::table('produtos')
                ->where('Empresax_ID', '=', $this->idEmpresa)
                ->count();
        }
        return $this->QtdRegs;
    }

    public function Paginacao($pag, $QtdPaginacoes) {
        $essapag = "https://tele-tudo.com/Cadastro";

        // echo "QtdRegs = ".$this->QtdRegs." QtdPPag = ".$this->QtdPPag; die;

        $qtsPags = intval($this->QtdRegs/$this->QtdPPag);

        /*echo 'QtdRegs:'.$this->QtdRegs.'<Br>';
        echo 'QtdPPag:'.$this->QtdPPag.'<Br>';
        echo 'qtsPags:'.$qtsPags.'<Br>';*/

        $sim = false;
        if (($this->QtdRegs>$this->QtdPPag) or ($qtsPags>1)) {
            $sim = true;
        }

        if ($sim) {
            // if ($qtsPags>1)

            $Qtd=0;
            $Aux=$QtdPaginacoes/2;
            echo "<ul class='pagination'>";
            if ($pag>$Aux) {
                $Qtd=$this->LinhaPag($essapag, '1','«', $Qtd);
            }
            $min = $pag-($QtdPaginacoes/2);
            if ($min<0) {$min = 1;}

            $max = ($qtsPags+2);

            /*echo 'min:'.$min.'<Br>';
            echo 'max:'.$max.'<Br>';*/

            for ($i=$min; $i<$max; $i++) {
                // for ($i=$min; $i<($qtsPags+1); $i++) {
                //

                // echo 'i:'.$i.'<Br>';

                if ($i>0) {
                    if ($Qtd==($QtdPaginacoes-1)) {
                        $Qtd= $this->LinhaPag($essapag, $qtsPags, '»', $Qtd);
                        break;
                    } else {
                        if ($i!=$pag) {
                            $Qtd = $this->LinhaPag($essapag, $i, $i, $Qtd);
                        } else {
                            $Qtd = $this->LinhaPag(null, $pag, $pag, $Qtd);
                        }
                    }
                }
            }
        }
    }

    public function getCatProds($idEmpresa) {
        $qryE = DB::table('empresa')
            ->select('categoriasempresas_ID')
            ->where('idEmpresa', '=', $idEmpresa)
            ->first();
        $qry = DB::table('categoriasprodutos')
            ->select('Descricao','TipoCategoria_ID')
            ->get();
        $sel = false;
        $i = 1;
        foreach ($qry as $Cats) {
            $sele='';
            if ($sel==false) {
                if ($qryE->categoriasempresas_ID == $Cats->TipoCategoria_ID) {
                    $sele=' selected ';
                    $sel = true;
                }
            }
            echo "<option value='".$i."' ".$sele." >".$Cats->Descricao."</option>";
            $i++;
        }
    }

    public function ConfgEntregas($idUser) {
        $this->idPessoa=$idUser;
        $qryE = DB::table('empresa')
            ->select('idEmpresa')
            ->where('idPessoa', '=', $idUser)
            ->first();
        if ($qryE==null) {
            return 0;
        } else {
            $this->idEmpresa=$qryE->idEmpresa;
            $qryEn = DB::table('TpEntregaEmpresa')
                ->where('idEmpresa', '=', $this->idEmpresa)
                ->count();
            $this->QtdConfgEntregas = $qryEn;
            return $this->QtdConfgEntregas;
        }
    }

    public function getOpLocRest() {
        $arrai = array('','');
        $qry = DB::table('empresa')
            ->select('tpEntrega','idEntrega')
            ->where('idEmpresa', '=', $this->idEmpresa)
            ->first();
        $this->idEntrega = $qry->idEntrega;
        if ($this->idEntrega==0) {
            $op=1;
        } else {
            $op=0;
        }
        $arrai[$op]=" checked='checked' ";
        return $arrai;
    }

    public function getResumoConfEntr() {
        $ret="";
        switch ($this->idEntrega) {
            case 0:
                // Sómente entrega própria
                if ($this->QtdConfgEntregas==0) {
                    $ret = "Inválida é necessário ajustar";
                } else {
                    $ret = "Entrega feita pelo fornecedor";
                }
                break;
            case 1:
                // Sómente entrega do site
                if ($this->QtdConfgEntregas==0) {
                    $ret = "Entrega feita pelo site";
                } else {
                    $ret = "Entrega feita pelo fornecedor e pelo site";
                }
                break;
            case 2:
                // As duas formas de entregas
                if ($this->QtdConfgEntregas==0) {
                    $ret = "Entrega feita pelo site";
                } else {
                    $ret = "Entrega feita pelo fornecedor e pelo site";
                }
                break;
        }
        return $ret;
    }

}