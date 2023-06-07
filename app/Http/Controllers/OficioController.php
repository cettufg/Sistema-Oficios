<?php

namespace App\Http\Controllers;

use App\Models\Destinatario;
use Illuminate\Http\Request;
use App\Models\Oficio;
use App\Models\DiretoriaOficio;
use App\Models\OficioExterno;
use App\Models\OficioRelacionado;
use App\Models\InteressadosOficio;
use App\Models\ResponsavelOficio;
use App\Models\CienteOficio;
use App\Models\AnexoOficio;
use App\Models\Diretoria;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendMail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OficioController extends Controller
{
    public function index()
    {
        $logged_user = auth()->user()->id;

        $oficios = Oficio::with('destinatario', 'responsaveis.user', 'interessados.user', 'anexos')
        //filter for user logged
        ->whereHas('responsaveis', function ($query) use ($logged_user) {
            $query->where('user_id', $logged_user);
        })
        ->orWhereHas('interessados', function ($query) use ($logged_user) {
            $query->where('user_id', $logged_user);
        })
        ->orWhere('user_created', $logged_user)
        ->latest()
        ->get();


        return Inertia::render('Oficio/Index', [
            'oficios' => $oficios,
        ]);
    }

    public function create()
    {
        $usuarios = User::all('id', 'name');
        $destinatarios = Destinatario::select('id', 'nome')->where('status', 1)->get();
        $diretorias = Diretoria::select('id', 'nome')->where('status', 1)->get();
        $oficios = Oficio::all('id', 'numero_oficio', 'tipo_oficio');

        return Inertia::render('Oficio/Create', [
            'usuarios' => $usuarios,
            'destinatarios' => $destinatarios,
            'diretorias' => $diretorias,
            'oficios' => $oficios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_oficio' => 'required'
        ]);

        if ($request['tipo_oficio'] == 'Recebido') {
            $request->validate([
                'dados_recebidos.responsaveis' => 'required',
                'dados_recebidos.interessados' => 'required',
                'dados_recebidos.tipo_documento' => 'required',
                'dados_recebidos.destinatario_id' => 'required',
                'dados_recebidos.numero_oficio' => 'required',
                'dados_recebidos.prazo' => 'nullable',
                'dados_recebidos.dias_corridos' => 'required',
                'dados_recebidos.observacao' => 'nullable',
                'dados_recebidos.tipo_documento' => 'nullable',
                'dados_recebidos.status_inicial' => 'nullable',
                'dados_recebidos.status_final' => 'nullable',
                'dados_recebidos.etapa' => 'required',
                'dados_recebidos.assunto_oficio_recebido' => 'nullable',
                'dados_recebidos.data_recebimento' => 'required|date',
                'dados_recebidos.data_prazo' => 'required|date',
                'dados_recebidos.anexos' => 'nullable',
            ], [
                'dados_recebidos.*.required' => 'Campo obrigatório',
                'dados_recebidos.*.string' => 'Você deve informar dados válidos',
                'dados_recebidos.*.date' => 'Você deve informar uma data válida',
            ]);
        } else {
            $request->validate([
                'dados_expedidos.diretoria' => 'required',
                'dados_expedidos.responsaveis' => 'required',
                'dados_expedidos.interessados' => 'required',
                'dados_expedidos.destinatario_id' => 'required',
                'dados_expedidos.numero_oficio' => 'required',
                'dados_expedidos.prazo' => 'nullable',
                'dados_expedidos.dias_corridos' => 'required',
                'dados_expedidos.observacao' => 'nullable',
                'dados_expedidos.status_inicial' => 'required',
                'dados_expedidos.status_final' => 'required',
                'dados_expedidos.etapa' => 'required',
                'dados_expedidos.data_emissao' => 'required|date',
                'dados_expedidos.data_prazo' => 'required|date',
                'dados_expedidos.assunto_oficio' => 'nullable',
                'dados_expedidos.numero_notificacao' => 'required',
                'dados_expedidos.anexos' => 'nullable',
            ], [
                'dados_expedidos.*.required' => 'Campo obrigatório',
                'dados_expedidos.*.string' => 'Você deve informar dados válidos',
                'dados_expedidos.*.date' => 'Você deve informar uma data válida',
            ]);
        }

        //Valida a lógica de cadastros
        if ($request->input('tipo_oficio') == 'Recebido') {
            //Cadastra os ofícios

            $data_inicio = new \DateTime($request->input('dados_recebidos.data_recebimento'));
            $data_final = new \DateTime($request->input('dados_recebidos.data_prazo'));
            $data_atual = new \DateTime();
            $intervaloPadrao = $data_inicio->diff($data_final);
            $intervaloAtual = $data_inicio->diff($data_atual);
            $prazoPadrao = $intervaloPadrao->d;
            $prazoAtual = $intervaloAtual->d;
            $prazo = $prazoPadrao - $prazoAtual;

            $etapa = '';

            if ($prazo < 0 && $request->input('dados_recebidos.etapa') != 'Finalizado') {
                $etapa = 'Atrasado';
            } else {
                $etapa = $request->input('dados_recebidos.etapa');
            }

            $oficio = Oficio::create([
                'destinatario_id' => $request->input('dados_recebidos.destinatario_id.id'),
                'numero_oficio' => $request->input('dados_recebidos.numero_oficio'),
                'assunto_oficio' => $request->input('dados_recebidos.assunto_oficio'),
                'data_recebimento' => $request->input('dados_recebidos.data_recebimento'),
                'data_prazo' => $request->input('dados_recebidos.data_prazo'),
                'prazo' => $prazo,
                'dias_corridos' => $request->input('dados_recebidos.dias_corridos') == 'Não' ? 0 : 1,
                'observacao' => $request->input('dados_recebidos.observacao'),
                'tipo_oficio' => $request->input('tipo_oficio'),
                'tipo_documento' => $request->input('dados_recebidos.tipo_documento'),
                'status_inicial' => $request->input('dados_recebidos.status_inicial'),
                'status_final' => $request->input('dados_recebidos.status_final'),
                'etapa' => $etapa,
                'user_created' => auth()->user()->id
            ]);

            //Cadastra os arquivos anexados
            if (array_key_exists('anexos', $request['dados_recebidos'])) {
                if ($request->input('dados_recebidos.anexos')) {
                    foreach ($request->input('dados_recebidos.anexos') as $file) {
                        if ($file->isValid()) {
                            $tamanho = $file->getSize();
                            $tipo = $file->getMimeType();
                            $arqname = $file->getClientOriginalName();
                            $caminho = $file->storeAs('anexos', $arqname);

                            AnexoOficio::create([
                                'nome' => $arqname,
                                'tipo' => $tipo,
                                'tamanho' => $tamanho,
                                'caminho' => $caminho,
                                'oficio_id' => $oficio->id
                            ]);
                        }
                    }
                }
            }

            //Cadastra os interessados
            foreach ($request['dados_recebidos']['interessados'] as $interessado) {
                $oficio->interessados()->create([
                    'user_id' => $interessado['id']
                ]);
            }

            //Cadastra os responsaveis
            foreach ($request['dados_recebidos']['responsaveis'] as $responsavel) {
                $oficio->responsaveis()->create([
                    'user_id' => $responsavel['id']
                ]);
            }
        } else {
            //Cadastra os ofícios
            $data_inicio = new \DateTime($request->input('dados_expedidos.data_emissao'));
            $data_final = new \DateTime($request->input('dados_expedidos.data_prazo'));
            $data_atual = new \DateTime();
            $intervaloPadrao = $data_inicio->diff($data_final);
            $intervaloAtual = $data_inicio->diff($data_atual);
            $prazoPadrao = $intervaloPadrao->d;
            $prazoAtual = $intervaloAtual->d;
            $prazo = $prazoPadrao - $prazoAtual;

            $etapa = '';

            if ($prazo < 0 && $request->input('dados_expedidos.etapa') != 'Finalizado') {
                $etapa = 'Atrasado';
            } else {
                $etapa = $request->input('dados_expedidos.etapa');
            }

            $oficio = Oficio::create([
                'destinatario_id' => $request->input('dados_expedidos.destinatario_id.id'),
                'tipo_oficio' => $request->input('tipo_oficio'),
                'numero_oficio' => $request->input('dados_expedidos.numero_oficio'),
                'assunto_oficio' => $request->input('dados_expedidos.assunto_oficio'),
                'data_emissao' => $request->input('dados_expedidos.data_emissao'),
                'data_prazo' => $request->input('dados_expedidos.data_prazo'),
                'prazo' => $prazo,
                'dias_corridos' => $request->input('dados_expedidos.dias_corridos') == 'Não' ? 0 : 1,
                'numero_notificacao' => $request->input('dados_expedidos.numero_notificacao'),
                'observacao' => $request->input('dados_expedidos.observacao'),
                'status_inicial' => $request->input('dados_expedidos.status_inicial'),
                'status_final' => $request->input('dados_expedidos.status_final'),
                'etapa' => $etapa,
                'user_created' => auth()->user()->id
            ]);

            if (array_key_exists('anexos', $request['dados_expedidos'])) {
                foreach ($request['dados_expedidos']['anexos'] as $file) {
                    if ($file->isValid()) {
                        $tamanho = $file->getSize();
                        $tipo = $file->getMimeType();
                        $arqname = $file->getClientOriginalName();
                        $caminho = $file->storeAs('anexos', $arqname);

                        AnexoOficio::create([
                            'nome' => $arqname,
                            'tipo' => $tipo,
                            'tamanho' => $tamanho,
                            'caminho' => $caminho,
                            'oficio_id' => $oficio->id
                        ]);
                    }
                }
            }

            foreach ($request['dados_expedidos']['diretoria'] as $diretoria) {
                $oficio->diretorias()->create([
                    'diretoria_id' => $diretoria['id']
                ]);
            }

            //Cadastra os interessados
            foreach ($request['dados_expedidos']['interessados'] as $interessado) {
                $oficio->interessados()->create([
                    'user_id' => $interessado['id']
                ]);
            }

            //Cadastra os responsaveis
            foreach ($request['dados_expedidos']['responsaveis'] as $responsavel) {
                $oficio->responsaveis()->create([
                    'user_id' => $interessado['id']
                ]);
            }
        }

        //Cadastra os ofícios relacionados
        if(array_key_exists('oficio_relacionado', $request->all())) {
            foreach ($request['oficio_relacionado'] as $oficio_relacionado) {
                $oficio->oficios_relacionados()->create([
                    'oficio_filho' => $oficio_relacionado['id']
                ]);
            }
        }

        //Cadastra os ofícios externos
        if(array_key_exists('oficio_externo', $request->all())) {
            foreach ($request['oficio_externo'] as $oficio_externo) {
                $oficio->oficios_externos()->create([
                    'descricao' => $oficio_externo
                ]);
            }
        }

        return redirect()->route('oficio.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipo_oficio' => 'required'
        ], [
            'tipo_oficio.required' => 'Campo obrigatório'
        ]);

        if ($request->input('tipo_oficio') == 'Recebido') {
            $request->validate(
                [
                'dados_recebidos.responsaveis' => 'required',
                'dados_recebidos.interessados' => 'required',
                'dados_recebidos.tipo_documento' => 'required',
                'dados_recebidos.destinatario_id' => 'required',
                'dados_recebidos.numero_oficio' => 'required',
                'dados_recebidos.prazo' => 'nullable',
                'dados_recebidos.dias_corridos' => 'required',
                'dados_recebidos.observacao' => 'nullable',
                'dados_recebidos.tipo_documento' => 'nullable',
                'dados_recebidos.status_inicial' => 'nullable',
                'dados_recebidos.status_final' => 'nullable',
                'dados_recebidos.etapa' => 'required',
                'dados_recebidos.assunto_oficio_recebido' => 'nullable',
                'dados_recebidos.data_recebimento' => 'required|date',
                'dados_recebidos.data_prazo' => 'required|date',
                'dados_recebidos.anexos' => 'nullable',
            ],
                [
                'dados_recebidos.*.required' => 'Campo obrigatório',
                'dados_recebidos.*.string' => 'Você deve informar dados válidos',
                'dados_recebidos.*.date' => 'Você deve informar uma data válida',
            ]
            );
        } else {
            $request->validate(
                [
                'dados_expedidos.diretoria' => 'required',
                'dados_expedidos.responsaveis' => 'required',
                'dados_expedidos.interessados' => 'required',
                'dados_expedidos.destinatario_id' => 'required',
                'dados_expedidos.numero_oficio' => 'required',
                'dados_expedidos.prazo' => 'nullable',
                'dados_expedidos.dias_corridos' => 'required',
                'dados_expedidos.observacao' => 'nullable',
                'dados_expedidos.status_inicial' => 'required',
                'dados_expedidos.status_final' => 'required',
                'dados_expedidos.etapa' => 'required',
                'dados_expedidos.data_emissao' => 'required|date',
                'dados_expedidos.data_prazo' => 'required|date',
                'dados_expedidos.assunto_oficio' => 'nullable',
                'dados_expedidos.numero_notificacao' => 'required',
                'dados_expedidos.anexos' => 'nullable',
            ],
                [
                'dados_expedidos.*.required' => 'Campo obrigatório',
                'dados_expedidos.*.string' => 'Você deve informar dados válidos',
                'dados_expedidos.*.date' => 'Você deve informar uma data válida',
            ]
            );
        }

        //Valida a lógica de atualizações
        if ($request->input('tipo_oficio') == 'Recebido') {
            //Cadastra os ofícios
            $oficio = Oficio::find($id);
            $data_inicio = new \DateTime($request->input('dados_recebidos.data_recebimento'));
            $data_final = new \DateTime($request->input('dados_recebidos.data_prazo'));
            $data_atual = new \DateTime();
            $intervaloPadrao = $data_inicio->diff($data_final);
            $intervaloAtual = $data_inicio->diff($data_atual);
            $prazoPadrao = $intervaloPadrao->d;
            $prazoAtual = $intervaloAtual->d;
            $prazo = $prazoPadrao - $prazoAtual;

            $etapa = '';

            if ($request->input('dados_recebidos.etapa') == 'Finalizado') {
                $etapa = $request->input('dados_recebidos.etapa');
            } elseif ($prazo < 0 && $request->input('dados_recebidos.etapa') != 'Finalizado') {
                $etapa = 'Atrasado';
            } else {
                $etapa = $request->input('dados_recebidos.etapa');
            }

            $oficio->fill([
                'destinatario_id' => $request->input('dados_recebidos.destinatario_id.id'),
                'numero_oficio' => $request->input('dados_recebidos.numero_oficio'),
                'assunto_oficio' => $request->input('dados_recebidos.assunto_oficio'),
                'data_recebimento' => $request->input('dados_recebidos.data_recebimento'),
                'data_prazo' => $request->input('dados_recebidos.data_prazo'),
                'prazo' => $prazo,
                'dias_corridos' => $request->input('dados_recebidos.dias_corridos') == 'Não' ? 0 : 1,
                'observacao' => $request->input('dados_recebidos.observacao'),
                'tipo_oficio' => $request->input('tipo_oficio'),
                'tipo_documento' => $request->input('dados_recebidos.tipo_documento'),
                'status_inicial' => $request->input('dados_recebidos.status_inicial'),
                'status_final' => $request->input('dados_recebidos.status_final'),
                'etapa' => $etapa,
                'user_updated' => auth()->user()->id
            ])->save();

            //Cadastra os arquivos anexados
            if (array_key_exists('anexos', $request['dados_recebidos'])) {
                if (count($request['dados_recebidos']['anexos']) > 0) {
                    foreach ($request['dados_recebidos']['anexos'] as $file) {
                        if ($file->isValid()) {
                            $tamanho = $file->getSize();
                            $tipo = $file->getMimeType();
                            $arqname = $file->getClientOriginalName();
                            $caminho = $file->storeAs('anexos', $arqname);

                            AnexoOficio::create([
                                'nome' => $arqname,
                                'tipo' => $tipo,
                                'tamanho' => $tamanho,
                                'caminho' => $caminho,
                                'oficio_id' => $oficio->id
                            ]);
                        }
                    }
                }
            }

            //Cadastra os interessados
            $oficio->interessados()->delete();
            foreach ($request['dados_recebidos']['interessados'] as $interessado) {
                $oficio->interessados()->create([
                    'user_id' => $interessado['id']
                ]);
            }

            //Cadastra os responsaveis
            $oficio->responsaveis()->delete();
            foreach ($request['dados_recebidos']['responsaveis'] as $responsavel) {
                $oficio->responsaveis()->create([
                    'user_id' => $responsavel['id']
                ]);
            }

        } else {
            $oficio = Oficio::find($id);

            $data_inicio = new \DateTime($request->input('dados_expedidos.data_emissao'));
            $data_final = new \DateTime($request->input('dados_expedidos.data_prazo'));
            $data_atual = new \DateTime();
            $intervaloPadrao = $data_inicio->diff($data_final);
            $intervaloAtual = $data_inicio->diff($data_atual);
            $prazoPadrao = $intervaloPadrao->d;
            $prazoAtual = $intervaloAtual->d;
            $prazo = $prazoPadrao - $prazoAtual;

            $etapa = '';

            if ($prazo < 0 && $request->input('dados_expedidos.etapa') != 'Finalizado') {
                $etapa = 'Atrasado';
            } else {
                $etapa = $request->input('dados_expedidos.etapa');
            }

            $oficio = $oficio->fill([
                'destinatario_id' => $request->input('dados_expedidos.destinatario_id.id'),
                'tipo_oficio' => $request->input('tipo_oficio'),
                'numero_oficio' => $request->input('dados_expedidos.numero_oficio'),
                'tipo_documento' => $request->input('dados_expedidos.tipo_documento'),
                'assunto_oficio' => $request->input('dados_expedidos.assunto_oficio'),
                'data_emissao' => $request->input('dados_expedidos.data_emissao'),
                'data_prazo' => $request->input('dados_expedidos.data_prazo'),
                'prazo' => $prazo,
                'dias_corridos' => $request->input('dados_expedidos.dias_corridos') == 'Não' ? 0 : 1,
                'numero_notificacao' => $request->input('dados_expedidos.numero_notificacao'),
                'observacao' => $request->input('dados_expedidos.observacao'),
                'status_inicial' => $request->input('dados_expedidos.status_inicial'),
                'status_final' => $request->input('dados_expedidos.status_final'),
                'etapa' => $etapa,
                'user_updated' => auth()->user()->id
            ])->save();

            //Cadastra a diretoria do ofício
            $oficio->diretorias()->delete();
            foreach ($request['dados_expedidos']['diretoria'] as $diretoria) {
                $oficio->diretorias()->create([
                    'diretoria_id' => $diretoria['id']
                ]);
            }

            //Cadastra os arquivos anexados
            if (array_key_exists('anexos', $request['dados_expedidos'])) {
                foreach ($request['dados_expedidos']['anexos'] as $file) {
                    if ($file->isValid()) {
                        $tamanho = $file->getSize();
                        $tipo = $file->getMimeType();
                        $arqname = $file->getClientOriginalName();
                        $caminho = $file->storeAs('anexos', $arqname);

                        AnexoOficio::create([
                            'nome' => $arqname,
                            'tipo' => $tipo,
                            'tamanho' => $tamanho,
                            'caminho' => $caminho,
                            'oficio_id' => $oficio->id
                        ]);
                    }
                }
            }

            //Cadastra os interessados
            $oficio->interessados()->delete();
            foreach ($request['dados_expedidos']['interessados'] as $interessado) {
                $oficio->interessados()->create([
                    'user_id' => $interessado['id']
                ]);
            }

            //Cadastra os responsaveis
            $oficio->responsaveis()->delete();
            foreach ($request['dados_expedidos']['responsaveis'] as $responsavel) {
                $oficio->responsaveis()->create([
                    'user_id' => $responsavel['id']
                ]);
            }
        }

        //Cadastra os ofícios relacionados
        $oficio->oficios_relacionados()->delete();
        if(array_key_exists('oficio_relacionado', $request->all())) {
            foreach ($request['oficio_relacionado'] as $oficio_relacionado) {
                $oficio->oficios_relacionados()->create([
                    'oficio_filho' => $oficio_relacionado['id']
                ]);
            }
        }

        //Cadastra os ofícios externos
        $oficio->oficios_externos()->delete();
        if(array_key_exists('oficio_externo', $request->all())) {
            foreach ($request['oficio_externo'] as $oficio_externo) {
                if(gettype($oficio_externo) == 'string') {
                    $oficio->oficios_externos()->create([
                        'descricao' => $oficio_externo
                    ]);
                } else {
                    $oficio->oficios_externos()->create([
                        'descricao' => $oficio_externo['label']
                    ]);
                }
            }
        }

        return redirect()->route('oficio.index');
    }

    public function detail($id)
    {
        $oficio = Oficio::where('id', $id)
        ->with('destinatario', 'responsaveis.user:id,name', 'interessados.user:id,name', 'anexos', 'diretorias.diretoria:id,nome', 'oficios_externos', 'oficios_relacionados.oficio_filho:id,tipo_oficio,numero_oficio', 'cientes.user:id,name')
        ->get();

        return Inertia::render('Oficio/Show', [
            'oficio' => $oficio
        ]);
    }

    public function edit($id)
    {
        $oficios = Oficio::select('id', 'numero_oficio', 'tipo_oficio')->where('id', '<>', $id)->get();

        $oficio = Oficio::where('id', $id)
        ->with('destinatario', 'responsaveis.user:id,name', 'interessados.user:id,name', 'anexos', 'diretorias', 'oficios_externos', 'oficios_relacionados.oficio_filho:id,tipo_oficio,numero_oficio')
        ->get();

        $usuarios = User::all('id', 'name');
        $destinatarios = Destinatario::select('id', 'nome')->where('status', 1)->get();
        $diretorias = Diretoria::all('id', 'nome');

        return Inertia::render('Oficio/Edit', [
            'oficios' => $oficios,
            'oficio' => $oficio,
            'usuarios' => $usuarios,
            'destinatarios' => $destinatarios,
            'diretorias' => $diretorias
        ]);
    }

    public function destroy(Request $request)
    {
        $dados = $request->all();

        if ($dados['id']) {
            //Exclui os Anexos vinculados
            $anexos = AnexoOficio::where('oficio_id', $dados['id'])->get();
            if (count($anexos) > 0) {
                foreach ($anexos as $anexo) {
                    Storage::disk('public')->delete($anexo->caminho);
                    $anexo->delete();
                }
            }

            //Exclui os Ciente vinculados
            $cientes = CienteOficio::where('oficio_id', $dados['id'])->get();
            if (count($cientes) > 0) {
                foreach ($cientes as $ciente) {
                    $ciente->delete();
                }
            }

            //Exclui os Diretoria vinculados
            $diretores = DiretoriaOficio::where('oficio_id', $dados['id'])->get();
            if (count($diretores) > 0) {
                foreach ($diretores as $diretor) {
                    $diretor->delete();
                }
            }

            //Exclui os Responsaveis vinculados
            $responsaveis = ResponsavelOficio::where('oficio_id', $dados['id'])->get();
            if (count($responsaveis) > 0) {
                foreach ($responsaveis as $responsavel) {
                    $responsavel->delete();
                }
            }

            //Exclui os Interessados vinculados
            $interessados = InteressadosOficio::where('oficio_id', $dados['id'])->get();
            if (count($interessados) > 0) {
                foreach ($interessados as $interessado) {
                    $interessado->delete();
                }
            }

            //Exclui os Oficio Externo vinculados
            $oficio_externo = OficioExterno::where('oficio_id', $dados['id'])->get();
            if (count($oficio_externo) > 0) {
                foreach ($oficio_externo as $oficio_ex) {
                    $oficio_ex->delete();
                }
            }

            //Exclui os Oficio Relacionado vinculados
            $oficio_relacionado = OficioRelacionado::where('oficio_pai', $dados['id'])->get();
            if (count($oficio_relacionado) > 0) {
                foreach ($oficio_relacionado as $oficio_rel) {
                    $oficio_rel->delete();
                }
            }


            $oficio = Oficio::findOrFail($dados['id']);
            $oficio->delete();
        }

        $oficios = Oficio::with('destinatario')->with('responsaveis')->with('anexos')->get();

        return Redirect::back()->with([
            'response' => $oficios
        ]);
    }


    public function destroySelected(Request $request)
    {
        $request->validate([
            'selecteds' => 'required',
        ]);

        $dados = $request['selecteds'];

        if (count($dados) > 0) {
            foreach ($dados as $oficio) {
                $oficioBD = Oficio::where('id', $oficio['id'])->with('anexos', 'cientes', 'diretorias', 'interessados', 'responsaveis', 'oficios_externos', 'oficios_relacionados')->get();
                if (count($oficioBD) > 0) {
                    //Exclui os Anexos vinculados
                    $oficioBD = $oficioBD[0];

                    foreach ($oficioBD->anexos as $anexo) {
                        Storage::disk('public')->delete($anexo->caminho);
                        $anexo->delete();
                    }

                    $oficioBD->cientes()->delete();
                    $oficioBD->diretorias()->delete();
                    $oficioBD->responsaveis()->delete();
                    $oficioBD->interessados()->delete();
                    $oficioBD->oficios_externos()->delete();
                    $oficioBD->oficios_relacionados()->delete();

                    $oficioBD->delete();
                }
            }
        }

        $logged_user = auth()->user()->id;

        $oficios = Oficio::with('destinatario', 'responsaveis.user', 'interessados.user', 'anexos')
        //filter for user logged
        ->whereHas('responsaveis', function ($query) use ($logged_user) {
            $query->where('user_id', $logged_user);
        })
        ->orWhereHas('interessados', function ($query) use ($logged_user) {
            $query->where('user_id', $logged_user);
        })
        ->orWhere('user_created', $logged_user)
        ->latest()
        ->get();

        return redirect()->back()->with('response', $oficios);
    }

    public function destroyAnexo($id)
    {
        $anexo = AnexoOficio::find($id);
        if ($anexo) {
            Storage::disk('public')->delete($anexo->caminho);
            $anexo->delete();
        }

        return redirect()->back()->with('response', $anexo);
    }

    public function ciencia($id)
    {
        $oficio = Oficio::find($id);
        $user_id = auth()->user()->id;

        $ciente = CienteOficio::where('user_id', $user_id)->where('oficio_id', $oficio->id)->count();
        if ($ciente == 0) {
            CienteOficio::create([
                'user_id' => $user_id,
                'oficio_id' => $oficio->id,
            ]);
        }

        return redirect()->back()->with('response', $oficio);
    }

    public function generatepdf($id)
    {
        $oficio = Oficio::where('id', $id)
        ->with('destinatario', 'responsaveis', 'interessados', 'anexos', 'cientes', 'diretorias', 'oficios_externos')
        ->get();

        $diretorias = Diretoria::all('id', 'nome');
        $oficios = Oficio::select('id', 'numero_oficio', 'tipo_oficio')->where('id', '<>', $id)->get();
        $usuarios = User::all('id', 'name');
        $oficios_relacionados = OficioRelacionado::where('oficio_pai', $id)->get();
        $auth = auth()->user();

        $nomepdf = 'Ofício_' . Str::of($oficio[0]->numero_oficio)->slug('-') . '.pdf';

        $pdf = \PDF::loadView('pdf.oficio', compact('oficio', 'diretorias', 'oficios', 'usuarios', 'oficios_relacionados', 'auth'))
                ->setPaper('a4', 'landscape')
                ->stream($nomepdf);

        return $pdf;
    }

    public function generateprazo()
    {
        $oficios = Oficio::select('id', 'tipo_oficio', 'prazo', 'data_emissao', 'data_recebimento', 'data_prazo', 'etapa')->get();
        if (count($oficios) > 0) {
            foreach ($oficios as $oficio) {
                $data_inicio = '';
                if ($oficio->tipo_oficio == 'Recebido') {
                    $data_inicio = new \DateTime($oficio->data_recebimento);
                } else {
                    $data_inicio = new \DateTime($oficio->data_emissao);
                }
                $data_final = new \DateTime($oficio->data_prazo);
                $data_atual = new \DateTime();
                $intervaloPadrao = $data_inicio->diff($data_final);
                $intervaloAtual = $data_inicio->diff($data_atual);
                $prazoPadrao = $intervaloPadrao->d;
                $prazoAtual = $intervaloAtual->d;
                $prazo = $prazoPadrao - $prazoAtual;

                if ($oficio->etapa != 'Finalizado') {
                    $oficio->prazo = $prazo;
                    $oficio->etapa = 'Atrasado';

                    $oficio->save();
                }
            }
        }
    }

    public function email($id)
    {
        $oficio = Oficio::where('id', $id)
        ->with('destinatario', 'responsaveis', 'interessados', 'anexos', 'diretorias', 'oficios_externos')
        ->get();

        return view('mail.novooficio', compact('oficio'));
    }
}
