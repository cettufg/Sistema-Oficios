<?php

namespace App\Utils;

use Carbon\Carbon;

class generateDeadline
{

    public static function run($date_initial, $date_final): int
    {
        $data_inicio = Carbon::parse($date_initial);
        $data_final = Carbon::parse($date_final);
        $data_atual = Carbon::now();
        $intervaloPadrao = $data_inicio->diff($data_final);
        $intervaloAtual = $data_inicio->diff($data_atual);
        $prazoPadrao = $intervaloPadrao->days;
        $prazoAtual = $intervaloAtual->days;

        $prazo = $prazoPadrao - $prazoAtual;

        return $prazo;
    }
}
