<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Oficio extends Model
{
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
        return $this->hasMany(AnexoOficio::class);
    }

    public function oficios_externos(): HasMany
    {
        return $this->hasMany(OficioExterno::class);
    }

    public function diretorias(): HasMany
    {
        return $this->hasMany(DiretoriaOficio::class);
    }

    public function cientes(): HasMany
    {
        return $this->hasMany(CienteOficio::class);
    }

    public function oficios_relacionados(): HasMany
    {
        return $this->hasMany(OficioRelacionado::class, 'oficio_pai', 'id');
    }
}
