<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Oficio;
use App\Jobs\LembreteOficio;
use App\Jobs\LembreteInteressados;
use App\Jobs\LembreteResponsaveis;

class CronController extends Controller
{
    public function index($hash)
    {
        if($hash == 'a34206a74fbf37bf07e4140f036e368c6fa1e1a2') {
            $oficios = Oficio::with('destinatario', 'interessados', 'responsaveis')->where('etapa', '!=', 'Finalizado')->where('etapa', '!=', null)->get();
            foreach ($oficios as $oficio) {
                if(count($oficio->interessados) > 0) {
                    LembreteInteressados::dispatch($oficio, $oficio->interessados);
                }
                if(count($oficio->responsaveis) > 0) {
                    LembreteResponsaveis::dispatch($oficio, $oficio->responsaveis);
                }
            }

            return 'ok';
        }
    }
}
