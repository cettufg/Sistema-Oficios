<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\LembreteOficio;
use App\Models\Oficio;
use App\Models\User;

class CronController extends Controller
{
    public function index()
    {
        $oficios = Oficio::with('destinatario')->with('interessados')->with('responsaveis')->where('etapa', '!=', 'Finalizado')->where('etapa', '!=', null)->get();

        foreach ($oficios as $oficio) {
            $this->sendResponsaveis($oficio, $oficio->responsaveis);
            $this->sendInteressados($oficio, $oficio->interessados);
        }
    }

    public function sendResponsaveis($oficio, $responsaveis)
    {
        foreach($responsaveis as $responsavel) {
            $usuario = User::find($responsavel->user_id);
            LembreteOficio::dispatch($oficio, $usuario);
        }
    }

    public function sendInteressados($oficio, $interessados)
    {
        foreach($interessados as $interessado) {
            $usuario = User::find($interessado->user_id);
            LembreteOficio::dispatch($oficio, $usuario);
        }
    }
}
