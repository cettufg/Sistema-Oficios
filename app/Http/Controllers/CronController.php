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

    public function generateprazo($hash)
    {
        if($hash == '1adbca67fd1368bdcc4940a16e779e441c499ff5') {
            $oficios = Oficio::select('id', 'tipo_oficio', 'prazo', 'data_emissao', 'data_recebimento', 'data_prazo', 'etapa')->get();
            if (count($oficios) > 0) {
                foreach ($oficios as $oficio) {
                    $data_inicio = '';
                    if ($oficio->tipo_oficio == 'Recebido') {
                        $data_inicio = new \DateTime($oficio->data_recebimento);
                    } else {
                        $data_inicio = new \DateTime($oficio->data_emissao);
                    }
                    $data_atual = new \DateTime();
                    $data_final = new \DateTime($oficio->data_prazo);
                    $intervaloPadrao = $data_inicio->diff($data_final);
                    $intervaloAtual = $data_inicio->diff($data_atual);
                    $prazoPadrao = $intervaloPadrao->d;

                    if($intervaloAtual->invert == 1 && $intervaloAtual->d > 0) {
                        $prazo = $intervaloPadrao->d;
                    } else {
                        $prazoAtual = $intervaloAtual->d;
                        $prazo = $prazoPadrao - $prazoAtual;
                    }

                    if ($oficio->etapa != 'Finalizado') {
                        $oficio->prazo = $prazo;
                        $oficio->etapa = 'Atrasado';

                        $oficio->save();
                    }
                }
            }
        }
    }
}
