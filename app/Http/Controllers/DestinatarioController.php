<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destinatario;
use Inertia\Inertia;

class DestinatarioController extends Controller
{
    public function index()
    {
        $destinatarios = Destinatario::all();

        return Inertia::render('Destinatario/Index', [
            'destinatarios' => $destinatarios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'status' => 'required'
        ]);

        //Cadastra os dados
        $destinatario = Destinatario::create([
            'nome' => $request->input('nome'),
            'status' => $request->input('status.value'),
            'user_created' => auth()->user()->id
        ]);

        return redirect()->back()->with('respone', $destinatario);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required',
            'status' => 'required'
        ]);

        //Cadastra os dados
        $destinatario = Destinatario::find($id);
        $destinatario->nome = $request->input('nome');
        $destinatario->status = $request->input('status.value');
        $destinatario->user_updated = auth()->user()->id;
        $destinatario->save();

        return redirect()->back()->with('respone', $destinatario);
    }

    public function destroy($id)
    {
        $destinatario = Destinatario::findOrFail($id);
        $destinatario->delete();

        return redirect()->back()->with('respone', $destinatario);

    }


    public function destroySelected(Request $request)
    {
        foreach($request->selected as $destinatario){
            $destinatarioDelete = Destinatario::findOrFail($destinatario['id']);
            $destinatarioDelete->delete();
        }

        $destinatarios = Destinatario::all();

        return redirect()->back()->with('respone', $destinatarios);
    }
}
