<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Destinatario;
use App\Models\ResponsavelOficio;
use App\Models\AnexoOficio;
use App\Models\OficioRelacionado;
use App\Models\OficioExterno;
use App\Models\DiretoriaOficio;
use App\Models\CienteOficio;

class Oficio extends Model
{
    use HasFactory;

    protected $fillable = [
        'destinatario_id',
        'numero_oficio',
        'assunto_oficio',
        'data_emissao',
        'data_recebimento',
        'data_prazo',
        'prazo',
        'dias_corridos',
        'numero_notificacao',
        'observacao',
        'tipo_oficio',
        'tipo_documento',
        'status_inicial',
        'status_final',
        'etapa',
        'user_created',
        'user_updated'
    ];

    public function destinatario()
    {
        return $this->hasOne(Destinatario::class, 'id', 'destinatario_id');
    }

    public function responsaveis()
    {
        return $this->hasMany(ResponsavelOficio::class);
    }

    public function interessados()
    {
        return $this->hasMany(InteressadosOficio::class);
    }

    public function anexos()
    {
        return $this->hasMany(AnexoOficio::class);
    }

    public function oficios_externos()
    {
        return $this->hasMany(OficioExterno::class);
    }

    public function diretorias()
    {
        return $this->hasMany(DiretoriaOficio::class);
    }

    public function cientes()
    {
        return $this->hasMany(CienteOficio::class);
    }

    public function oficios_relacionados()
    {
        return $this->hasMany(OficioRelacionado::class, 'oficio_pai', 'id');
    }
}
