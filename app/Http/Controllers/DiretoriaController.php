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
        $diretores = Diretoria::all('id','group_id', 'nome', 'status');
        $grupos = GroupUsers::select('id', 'nome', 'descricao')->where('status', 'Ativo')->get();
        
        return Inertia::render('Diretoria/Index', [
            'diretores' => $diretores,
            'grupos' => $grupos
        ]);
    }

    public function create()
    {
        $grupos = GroupUsers::select('id', 'nome', 'descricao')->where('status', 'Ativo')->get();
        return Inertia::render('Diretoria/Create', [
            'grupos' => $grupos,
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->all();

        $request->validate(
        [
            'group_id' => ['required'],
            'nome' => ['string', 'required'],
            'status' => ['string', 'required']
        ],[
            '*.required' => 'Campo obrigatório.'
        ]);
        
        //Cadastra os dados
        $diretoria = Diretoria::create([
            'group_id' => $dados['group_id']['id'],
            'nome' => $dados['nome'],
            'status' => $dados['status'],
            'user_created' => auth()->user()->id
        ]);

        return Redirect::route('diretoria.index');
    }

    public function update(Request $request, $id)
    {
        $dados = $request->all();
        
        $request->validate(
        [
            'group_id' => ['required'],
            'nome' => ['string', 'required'],
            'status' => ['string', 'required']
        ],[
            '*.required' => 'Campo obrigatório.'
        ]);

        
        //Cadastra os dados
        $diretoria = Diretoria::find($id)->fill([
            'group_id' => $dados['group_id']['id'],
            'nome' => $dados['nome'],
            'status' => $dados['status'],
            'user_updated' => auth()->user()->id
        ])->save();

        return Redirect::route('diretoria.index');
    }

    public function edit($id)
    {
        $diretoria = Diretoria::find($id);
        $grupos = GroupUsers::select('id', 'nome', 'descricao')->where('status', 'Ativo')->get();
        
        return Inertia::render('Diretoria/Edit', [
            'diretoria' => $diretoria,
            'grupos' => $grupos,
        ]);
    }

    public function destroy(Request $request)
    {    
        $dados = $request->all();
        
        if($dados['id']){
            $diretoria = Diretoria::findOrFail($dados['id']);
            $diretoria->delete();
        }

        $diretorias = Diretoria::all('id', 'group_id', 'nome', 'status');

        return Redirect::back()->with([
            'response' => $diretorias
        ]);
        
    }


    public function destroyselected(Request $request)
    {    
        $dados = $request->all();

        if(count($dados) > 0){
            foreach($dados as $diretoria){
                if($diretoria['id']){
                    $diretoriaDelete = Diretoria::findOrFail($diretoria['id']);
                    $diretoriaDelete->delete();
                }
            }
        }
        
        $diretorias = Diretoria::all('id', 'group_id', 'nome', 'status');

        return $diretorias;
        
    }
}
