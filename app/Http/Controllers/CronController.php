<?php

namespace App\Http\Controllers;

use App\Jobs\SendLembrete;
use App\Models\Oficio;
use App\Utils\generateDeadline;

class CronController extends Controller
{
    public function index($hash): string
    {
        if ($hash == 'a34206a74fbf37bf07e4140f036e368c6fa1e1a2') {
            $oficios = Oficio::with('interessados', 'responsaveis')->where('etapa', '!=', 'Finalizado')
                             ->where('etapa', '!=', null)->get();
            foreach ($oficios as $oficio) {
                if (count($oficio->interessados) > 0) {
                    SendLembrete::dispatch($oficio, $oficio->interessados);
                }
                if (count($oficio->responsaveis) > 0) {
                    SendLembrete::dispatch($oficio, $oficio->responsaveis);
                }
            }

            return 'ok';
        }
    }

    public function generateprazo($hash): void
    {
        if ($hash === '1adbca67fd1368bdcc4940a16e779e441c499ff5') {
            $oficios = Oficio::select('id', 'tipo_oficio', 'prazo', 'data_emissao', 'data_recebimento', 'data_prazo', 'etapa')
                             ->get();
            if (count($oficios) > 0) {
                foreach ($oficios as $oficio) {
                    $data_inicio = '';

                    if ($oficio->tipo_oficio === 'Recebido') {
                        $prazo = generateDeadline::run($oficio->data_recebimento, $oficio->data_prazo);
                    } else {
                        $prazo = generateDeadline::run($oficio->data_emissao, $oficio->data_prazo);
                    }


                    if ($prazo < 0 && $oficio->etapa !== 'Finalizado') {
                        $oficio->etapa = 'Atrasado';
                    }

                    if ($oficio->etapa !== 'Finalizado') {
                        $oficio->prazo = $prazo;

                        $oficio->save();
                    }
                }
            }
        }
    }
}
