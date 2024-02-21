<?php

namespace App\Http\Controllers;

use App\Models\AnexoOficio;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Index');
    }

    public function teste()
    {
        $anexos = AnexoOficio::all();
        foreach ($anexos as $anexo) {
            $path = $anexo->caminho;
            //verify if the path ends with .pdf
            $endsValidates = [
                '.pdf',
                '.mp4',
                '.png',
                '.jpg',
            ];
            if (!in_array(substr($path, -4), $endsValidates) && $anexo->tipo === 'application/pdf') {
                $anexo->caminho .= '.pdf';
                $anexo->save();
            }
        }
    }
}
