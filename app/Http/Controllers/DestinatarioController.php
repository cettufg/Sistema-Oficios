<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destinatario;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DestinatarioController extends Controller
{
    public function index()
    {
        $destinatarios = Destinatario::all('id', 'nome', 'status');
        
        return Inertia::render('Destinatario/Index', [
            'destinatarios' => $destinatarios
        ]);
    }

    public function create()
    {
        return Inertia::render('Destinatario/Create');
    }

    public function store(Request $request)
    {
        $dados = $request->all();

        $request->validate(
        [
            'nome' => ['string', 'required'],
            'status' => ['string', 'required']
        ]);
        
        //Cadastra os dados
        $destinatario = Destinatario::create([
            'nome' => $dados['nome'],
            'status' => $dados['status'],
            'user_created' => auth()->user()->id
        ]);

        return Redirect::route('destinatario.index');
    }

    public function update(Request $request, $id)
    {
        $dados = $request->all();
        
        $request->validate(
        [
            'nome' => ['string', 'required'],
            'status' => ['string', 'required']
        ]);

        
        //Cadastra os dados
        $destinatario = Destinatario::find($id)->fill([
            'nome' => $dados['nome'],
            'status' => $dados['status'],
            'user_updated' => auth()->user()->id
        ])->save();

        return Redirect::route('destinatario.index');
    }

    public function edit($id)
    {
        $destinatario = Destinatario::find($id);
        
        return Inertia::render('Destinatario/Edit', [
            'destinatario' => $destinatario,
        ]);
    }

    public function destroy(Request $request)
    {    
        $dados = $request->all();
        
        if($dados['id']){
            $destinatario = Destinatario::findOrFail($dados['id']);
            $destinatario->delete();
        }

        $destinatarios = Destinatario::all('id', 'nome', 'status');

        return Redirect::back()->with([
            'response' => $destinatarios
        ]);
        
    }


    public function destroyselected(Request $request)
    {    
        $dados = $request->all();

        if(count($dados) > 0){
            foreach($dados as $destinatario){
                if($destinatario['id']){
                    $destinatarioDelete = Destinatario::findOrFail($destinatario['id']);
                    $destinatarioDelete->delete();
                }
            }
        }
        
        $destinatarios = Destinatario::all('id', 'nome', 'status');

        return $destinatarios;
        
    }
}
