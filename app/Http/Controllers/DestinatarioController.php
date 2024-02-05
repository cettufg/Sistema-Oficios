<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use Illuminate\Http\Request;
use App\Models\Destinatario;
use Inertia\Inertia;

class DestinatarioController extends Controller
{
    public function index()
    {
        $destinatarios = Destinatario::active()->get();

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

        Destinatario::create([
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

        //Cadastra os dados
        $destinatario = Destinatario::findOrFail($id);
        $destinatario->nome = $request->input('nome');
        $destinatario->status = $request->input('status.value');
        $destinatario->user_updated = auth()->user()->id;
        $destinatario->save();

        return redirect()->back();
    }

    public function destroy(int $id)
    {
        $destinatario = Destinatario::findOrFail($id);
        $destinatario->status = Status::TRASH;
        $destinatario->user_updated = auth()->user()->id;
        $destinatario->save();

        return redirect()->back();

    }

    public function destroySelected(Request $request)
    {
        foreach($request->selected as $destinatario){
            $destinatarioDelete = Destinatario::findOrFail($destinatario['id']);
            $destinatarioDelete->status = Status::TRASH;
            $destinatarioDelete->user_updated = auth()->user()->id;
            $destinatarioDelete->save();
        }

        return redirect()->back();
    }
}
