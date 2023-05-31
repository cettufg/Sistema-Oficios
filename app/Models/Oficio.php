<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function destinatario(): HasOne
    {
        return $this->hasOne(Destinatario::class, 'id', 'destinatario_id');
    }

    public function responsaveis(): HasMany
    {
        return $this->hasMany(ResponsavelOficio::class, 'oficio_id', 'id');
    }

    public function interessados(): HasMany
    {
        return $this->hasMany(InteressadosOficio::class, 'oficio_id', 'id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(AnexoOficio::class, 'oficio_id', 'id');
    }

    public function oficios_externos(): HasMany
    {
        return $this->hasMany(OficioExterno::class, 'oficio_id', 'id');
    }

    public function diretorias(): HasMany
    {
        return $this->hasMany(DiretoriaOficio::class, 'oficio_id', 'id');
    }
    
    public function cientes(): HasMany
    {
        return $this->hasMany(CienteOficio::class, 'oficio_id', 'id');
    }
}
