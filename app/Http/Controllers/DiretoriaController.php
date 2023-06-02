<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diretoria;
use App\Models\GroupUsers;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DiretoriaController extends Controller
{
    public function index()
    {
        $diretorias = Diretoria::all();

        return Inertia::render('Diretoria/Index', [
            'diretorias' => $diretorias,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'status' => 'required'
        ], [
            '*.required' => 'Campo obrigatório.'
        ]);

        //Cadastra os dados
        $diretoria = Diretoria::create([
            'group_id' => 1,
            'nome' => $request->input('nome'),
            'status' => $request->input('status.value'),
            'user_created' => auth()->user()->id
        ]);

        return redirect()->back()->with('response', $diretoria);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required',
            'status' => 'required'
        ], [
            '*.required' => 'Campo obrigatório.'
        ]);


        //Atualiza os dados
        $diretoria = Diretoria::findOrFail($id);
        $diretoria->nome = $request->input('nome');
        $diretoria->status = $request->input('status.value');
        $diretoria->user_updated = auth()->user()->id;
        $diretoria->save();

        return redirect()->back()->with('response', $diretoria);
    }

    public function destroy(Request $request)
    {
        $diretoria = Diretoria::findOrFail($request->id);
        $diretoria->delete();

        return redirect()->back()->with('response', $diretoria);

    }


    public function destroySelected(Request $request)
    {
        foreach($request->selected as $diretoria) {
            $diretoriaDelete = Diretoria::findOrFail($diretoria['id']);
            $diretoriaDelete->delete();
        }

        $diretorias = Diretoria::all();

        return redirect()->back()->with('respone', $diretorias);

    }
}
