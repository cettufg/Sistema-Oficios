<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use Illuminate\Http\Request;
use App\Models\Diretoria;
use Inertia\Inertia;

class DiretoriaController extends Controller
{
    public function index()
    {
        $diretorias = Diretoria::active()->get();

        return Inertia::render('Diretoria/Index', [
            'diretorias' => $diretorias,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'status' => 'required'
        ]);

        Diretoria::create([
            'group_id' => 1,
            'nome' => $request->input('nome'),
            'status' => $request->input('status.value'),
            'user_created' => auth()->user()->id
        ]);

        return redirect()->back();
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nome' => 'required',
            'status' => 'required'
        ]);

        $diretoria = Diretoria::findOrFail($id);
        $diretoria->nome = $request->input('nome');
        $diretoria->status = $request->input('status.value');
        $diretoria->user_updated = auth()->user()->id;
        $diretoria->save();

        return redirect()->back();
    }

    public function destroy(int $id)
    {
        $diretoria = Diretoria::findOrFail($id);
        $diretoria->status = Status::TRASH;
        $diretoria->user_updated = auth()->user()->id;
        $diretoria->save();

        return redirect()->back();

    }


    public function destroySelected(Request $request)
    {
        foreach($request->selected as $diretoria) {
            $diretoriaDelete = Diretoria::findOrFail($diretoria['id']);
            $diretoriaDelete->status = Status::TRASH;
            $diretoriaDelete->user_updated = auth()->user()->id;
            $diretoriaDelete->save();
        }

        return redirect()->back();

    }
}
