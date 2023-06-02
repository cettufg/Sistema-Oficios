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

        $oficios = Oficio::with(['destinatario', 'responsaveis' => function ($query) use ($logged_user) {
            $query->where('user_id', $logged_user)->with('user');
        }, 'interessados' => function ($query) use ($logged_user) {
            $query->where('user_id', $logged_user)->with('user');
        }, 'anexos'])->where('user_created', $logged_user)->orderBY('created_at', 'desc')->get();

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
                'dados_expedidos.observacao' => 'required',
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
            $data_inicio = '';
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
            if (count($request['dados_recebidos']['interessados']) > 0) {
                foreach ($request['dados_recebidos']['interessados'] as $interessado) {
                    InteressadosOficio::create([
                        'user_id' => $interessado['id'],
                        'oficio_id' => $oficio->id,
                    ]);

                    //Envia email para os responsaveis
                    $usuario = User::find($interessado['id']);
                    SendMail::dispatch($oficio, $usuario);
                }
            }

            //Cadastra os responsaveis
            if (count($request['dados_recebidos']['responsaveis']) > 0) {
                foreach ($request['dados_recebidos']['responsaveis'] as $responsavel) {
                    ResponsavelOficio::create([
                        'user_id' => $responsavel['id'],
                        'oficio_id' => $oficio->id,
                    ]);
                    //Envia email para os interessados
                    $usuario = User::find($responsavel['id']);
                    SendMail::dispatch($oficio, $usuario);
                }
            }
        } else {
            //Cadastra os ofícios
            $data_inicio = '';
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
                if ($request['dados_expedidos']['anexos']) {
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
            }

            //Cadastra a diretoria do ofício
            if (count($request['dados_expedidos']['diretoria']) > 0) {
                foreach ($request['dados_expedidos']['diretoria'] as $diretoria) {
                    DiretoriaOficio::create([
                        'diretoria_id' => $diretoria['id'],
                        'oficio_id' => $oficio->id
                    ]);
                };
            };

            //Cadastra os interessados
            if (count($request['dados_expedidos']['interessados']) > 0) {
                foreach ($request['dados_expedidos']['interessados'] as $interessado) {
                    InteressadosOficio::create([
                        'user_id' => $interessado['id'],
                        'oficio_id' => $oficio->id,
                    ]);
                }
            }

            //Cadastra os responsaveis
            if (count($request['dados_expedidos']['responsaveis']) > 0) {
                foreach ($request['dados_expedidos']['responsaveis'] as $responsavel) {
                    ResponsavelOficio::create([
                        'user_id' => $responsavel['id'],
                        'oficio_id' => $oficio->id,
                    ]);
                }
            }
        }

        //Cadastra os ofícios relacionados
        if ($request->input('oficio_relacionado')) {
            if (count($request['oficio_relacionado']) > 0) {
                foreach ($request['oficio_relacionado'] as $oficiorelacionado) {
                    OficioRelacionado::create([
                        'oficio_pai' => $oficio->id,
                        'oficio_filho' => $oficiorelacionado['id']
                    ]);
                }
            }
        }


        //Cadastra os ofícios externos
        if ($request->input('oficio_externo')) {
            if (count($request['oficio_externo']) > 0) {
                foreach ($request['oficio_externo'] as $oficioexterno) {
                    OficioExterno::create([
                        'oficio_id' => $oficio->id,
                        'descricao' => $oficioexterno
                    ]);
                }
            }
        }

        return redirect()->route('oficio.index');
    }

    public function update(Request $request, $id)
    {
        $dados = $request->all();

        $request->validate(
            [
                'tipo_oficio' => 'required'
            ]
        );
        if ($request['tipo_oficio'] == 'Recebido') {
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
                    'dados_expedidos.observacao' => 'required',
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
        if ($dados['tipo_oficio'] == 'Recebido') {
            //Cadastra os ofícios
            $oficio = Oficio::find($id);
            $data_inicio = '';
            $data_inicio = new \DateTime($dados['dados_recebidos']['data_recebimento']);
            $data_final = new \DateTime($dados['dados_recebidos']['data_prazo']);
            $data_atual = new \DateTime();
            $intervaloPadrao = $data_inicio->diff($data_final);
            $intervaloAtual = $data_inicio->diff($data_atual);
            $prazoPadrao = $intervaloPadrao->d;
            $prazoAtual = $intervaloAtual->d;
            $prazo = $prazoPadrao - $prazoAtual;

            $etapa = '';

            if ($dados['dados_recebidos']['etapa'] == 'Finalizado') {
                $prazo = 0;
                $etapa = $dados['dados_recebidos']['etapa'];
            } elseif ($prazo < 0 && $dados['dados_recebidos']['etapa'] != 'Finalizado') {
                $etapa = 'Atrasado';
            } else {
                $etapa = $dados['dados_recebidos']['etapa'];
            }

            $oficio->fill([
                'destinatario_id' => $dados['dados_recebidos']['destinatario_id']['id'],
                'numero_oficio' => $dados['dados_recebidos']['numero_oficio'],
                'assunto_oficio' => $dados['dados_recebidos']['assunto_oficio'],
                'data_recebimento' => $dados['dados_recebidos']['data_recebimento'],
                'data_prazo' => $dados['dados_recebidos']['data_prazo'],
                'prazo' => $prazo,
                'dias_corridos' => $dados['dados_recebidos']['dias_corridos'] == 'Não' ? 0 : 1,
                'observacao' => $dados['dados_recebidos']['observacao'],
                'tipo_oficio' => $dados['tipo_oficio'],
                'tipo_documento' => $dados['dados_recebidos']['tipo_documento'],
                'status_inicial' => $dados['dados_recebidos']['status_inicial'],
                'status_final' => $dados['dados_recebidos']['status_final'],
                'etapa' => $etapa,
                'user_updated' => auth()->user()->id
            ])->save();

            //Cadastra os arquivos anexados
            if (array_key_exists('anexos', $dados['dados_recebidos'])) {
                if (count($dados['dados_recebidos']['anexos']) > 0) {
                    foreach ($dados['dados_recebidos']['anexos'] as $file) {
                        if ($file->isValid()) {
                            $tamanho = $file->getSize();
                            $tipo = $file->getMimeType();
                            $arqname = $file->getClientOriginalName();
                            $caminho = $file->storeAs('anexos', $arqname);

                            $cadArquivo = AnexoOficio::create([
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
            if (count($dados['dados_recebidos']['interessados']) > 0) {
                //Deleta a relação de interessados para cadastrar novamente
                $existInteressado = InteressadosOficio::where('oficio_id', $id)->get();

                if (count($existInteressado) > 0) {
                    foreach ($existInteressado as $deleteExistInteressado) {
                        $deleteExistInteressado->delete();
                    }
                }

                foreach ($dados['dados_recebidos']['interessados'] as $interessado) {
                    $cadInteressado = InteressadosOficio::create([
                        'user_id' => $interessado['id'],
                        'oficio_id' => $id,
                    ]);
                }
            }

            //Cadastra os responsaveis
            if (count($dados['dados_recebidos']['responsaveis']) > 0) {
                //Deleta a relação de responsaveis para cadastrar novamente
                $existResponsavel = ResponsavelOficio::where('oficio_id', $id)->get();

                if (count($existResponsavel) > 0) {
                    foreach ($existResponsavel as $deleteexistResponsavel) {
                        $deleteexistResponsavel->delete();
                    }
                }

                foreach ($dados['dados_recebidos']['responsaveis'] as $responsavel) {
                    $cadResponsavel = ResponsavelOficio::create([
                        'user_id' => $responsavel['id'],
                        'oficio_id' => $id,
                    ]);
                }
            }
        } else {
            $oficio = Oficio::find($id);

            $data_inicio = '';
            $data_inicio = new \DateTime($dados['dados_expedidos']['data_emissao']);
            $data_final = new \DateTime($dados['dados_expedidos']['data_prazo']);
            $data_atual = new \DateTime();
            $intervaloPadrao = $data_inicio->diff($data_final);
            $intervaloAtual = $data_inicio->diff($data_atual);
            $prazoPadrao = $intervaloPadrao->d;
            $prazoAtual = $intervaloAtual->d;
            $prazo = $prazoPadrao - $prazoAtual;

            $etapa = '';

            if ($prazo < 0 && $dados['dados_expedidos']['etapa'] != 'Finalizado') {
                $etapa = 'Atrasado';
            } else {
                $etapa = $dados['dados_expedidos']['etapa'];
            }

            $oficio = $oficio->fill([
                'destinatario_id' => $dados['dados_expedidos']['destinatario_id']['id'],
                'tipo_oficio' => $dados['tipo_oficio'],
                'numero_oficio' => $dados['dados_expedidos']['numero_oficio'],
                'assunto_oficio' => $dados['dados_expedidos']['assunto_oficio'],
                'data_emissao' => $dados['dados_expedidos']['data_emissao'],
                'data_prazo' => $dados['dados_expedidos']['data_prazo'],
                'prazo' => $prazo,
                'dias_corridos' => $dados['dados_expedidos']['dias_corridos'] == 'Não' ? 0 : 1,
                'numero_notificacao' => $dados['dados_expedidos']['numero_notificacao'],
                'observacao' => $dados['dados_expedidos']['observacao'],
                'status_inicial' => $dados['dados_expedidos']['status_inicial'],
                'status_final' => $dados['dados_expedidos']['status_final'],
                'etapa' => $etapa,
                'user_updated' => auth()->user()->id
            ])->save();

            //Cadastra a diretoria do ofício
            //Deleta a relação de diretoria para cadastrar novamente
            $existDiretoria = DiretoriaOficio::where('oficio_id', $id)->get();
            if (count($existDiretoria) > 0) {
                foreach ($existDiretoria as $deleteExistDiretoria) {
                    $deleteExistDiretoria->delete();
                }
            }
            if (count($dados['dados_expedidos']['diretoria']) > 0) {
                foreach ($dados['dados_expedidos']['diretoria'] as $diretoria) {
                    $CADdiretoria = DiretoriaOficio::create([
                        'diretoria_id' => $diretoria['id'],
                        'oficio_id' => $id
                    ]);
                };
            };

            //Cadastra os arquivos anexados
            if (array_key_exists('anexos', $dados['dados_expedidos'])) {
                if (count($dados['dados_expedidos']['anexos']) > 0) {
                    foreach ($dados['dados_expedidos']['anexos'] as $file) {
                        if ($file->isValid()) {
                            $tamanho = $file->getSize();
                            $tipo = $file->getMimeType();
                            $arqname = $file->getClientOriginalName();
                            $caminho = $file->storeAs('anexos', $arqname);

                            $cadArquivo = AnexoOficio::create([
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
            if (count($dados['dados_expedidos']['interessados']) > 0) {
                //Deleta a relação de interessados para cadastrar novamente
                $existInteressado = InteressadosOficio::where('oficio_id', $id)->get();

                if (count($existInteressado) > 0) {
                    foreach ($existInteressado as $deleteExistInteressado) {
                        $deleteExistInteressado->delete();
                    }
                }

                foreach ($dados['dados_expedidos']['interessados'] as $interessado) {
                    $cadInteressado = InteressadosOficio::create([
                        'user_id' => $interessado['id'],
                        'oficio_id' => $id,
                    ]);
                }
            }

            //Cadastra os responsaveis
            if (count($dados['dados_expedidos']['responsaveis']) > 0) {
                //Deleta a relação de responsaveis para cadastrar novamente
                $existResponsavel = ResponsavelOficio::where('oficio_id', $id)->get();

                if (count($existResponsavel) > 0) {
                    foreach ($existResponsavel as $deleteexistResponsavel) {
                        $deleteexistResponsavel->delete();
                    }
                }

                foreach ($dados['dados_expedidos']['responsaveis'] as $responsavel) {
                    $cadResponsavel = ResponsavelOficio::create([
                        'user_id' => $responsavel['id'],
                        'oficio_id' => $id,
                    ]);
                }
            }
        }

        //Cadastra os ofícios relacionados
        if (array_key_exists('oficio_relacionado', $dados)) {
            if (count($dados['oficio_relacionado']) > 0) {
                //Deleta a relação de oficios relacionados para cadastrar novamente
                $existOficioRelacionado = OficioRelacionado::where('oficio_pai', $id)->get();
                if (count($existOficioRelacionado) > 0) {
                    foreach ($existOficioRelacionado as $deleteExistOficioRelacionado) {
                        $deleteExistOficioRelacionado->delete();
                    }
                }

                foreach ($dados['oficio_relacionado'] as $oficiorelacionado) {
                    $oficios_relacionados = OficioRelacionado::create([
                        'oficio_pai' => $id,
                        'oficio_filho' => $oficiorelacionado['id']
                    ]);
                }
            }
        }


        //Cadastra os ofícios externos
        if (array_key_exists('oficio_externo', $dados)) {
            if (count($dados['oficio_externo']) > 0) {
                //Deleta a relação de oficios relacionados para cadastrar novamente
                $existOficioRelacionadoExt = OficioExterno::where('oficio_id', $id)->get();


                if (count($existOficioRelacionadoExt) > 0) {
                    foreach ($existOficioRelacionadoExt as $deleteExistOficioRelacionadoExt) {
                        $deleteExistOficioRelacionadoExt->delete();
                    }
                }

                foreach ($dados['oficio_externo'] as $oficioexterno) {
                    if (gettype($oficioexterno) == 'array') {
                        $oficios_externos = OficioExterno::create([
                            'oficio_id' => $id,
                            'descricao' => $oficioexterno['label']
                        ]);
                    } else {
                        $oficios_externos = OficioExterno::create([
                            'oficio_id' => $id,
                            'descricao' => $oficioexterno
                        ]);
                    }
                }
            }
        }

        return Redirect::route('oficio.index');
    }

    public function detail($id)
    {
        $oficio = Oficio::where('id', $id)
        ->with('destinatario')->with('responsaveis')
        ->with('interessados')->with('anexos')
        ->with('cientes')
        ->with('diretorias')->with('oficios_externos')->get();

        $diretorias = Diretoria::all('id', 'nome');
        $oficios = Oficio::select('id', 'numero_oficio', 'tipo_oficio')->where('id', '<>', $id)->get();
        $usuarios = User::all('id', 'name');
        $oficios_relacionados = OficioRelacionado::where('oficio_pai', $id)->get();
        $usuario = auth()->user();

        return Inertia::render('Oficio/Show', [
            'oficio' => $oficio,
            'oficios_relacionados' => $oficios_relacionados,
            'usuarios' => $usuarios,
            'oficios' => $oficios,
            'diretorias' => $diretorias,
            'usuario' => $usuario
        ]);
    }

    public function edit($id)
    {
        $oficios = Oficio::select('id', 'numero_oficio', 'tipo_oficio')->where('id', '<>', $id)->get();

        $oficio = Oficio::where('id', $id)
        ->with('destinatario')->with('responsaveis')
        ->with('interessados')->with('anexos')
        ->with('diretorias')->with('oficios_externos')->get();

        $usuarios = User::all('id', 'name');
        $destinatarios = Destinatario::select('id', 'nome')->where('status', 'Ativo')->get();
        $diretorias = Diretoria::all('id', 'nome');
        $oficios_relacionados = OficioRelacionado::where('oficio_pai', $id)->get();

        return Inertia::render('Oficio/Edit', [
            'oficios' => $oficios,
            'oficio' => $oficio,
            'usuarios' => $usuarios,
            'destinatarios' => $destinatarios,
            'diretorias' => $diretorias,
            'oficios_relacionados' => $oficios_relacionados
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


    public function destroyselected(Request $request)
    {
        $dados = $request->all();

        if (count($dados) > 0) {
            foreach ($dados as $oficio) {
                if ($oficio['id']) {
                    //Exclui os Anexos vinculados
                    $anexos = AnexoOficio::where('oficio_id', $oficio['id'])->get();
                    if (count($anexos) > 0) {
                        foreach ($anexos as $anexo) {
                            Storage::disk('public')->delete($anexo->caminho);
                            $anexo->delete();
                        }
                    }

                    //Exclui os Ciente vinculados
                    $cientes = CienteOficio::where('oficio_id', $oficio['id'])->get();
                    if (count($cientes) > 0) {
                        foreach ($cientes as $ciente) {
                            $ciente->delete();
                        }
                    }

                    //Exclui os Diretoria vinculados
                    $diretores = DiretoriaOficio::where('oficio_id', $oficio['id'])->get();
                    if (count($diretores) > 0) {
                        foreach ($diretores as $diretor) {
                            $diretor->delete();
                        }
                    }

                    //Exclui os Responsaveis vinculados
                    $responsaveis = ResponsavelOficio::where('oficio_id', $oficio['id'])->get();
                    if (count($responsaveis) > 0) {
                        foreach ($responsaveis as $responsavel) {
                            $responsavel->delete();
                        }
                    }

                    //Exclui os Interessados vinculados
                    $interessados = InteressadosOficio::where('oficio_id', $oficio['id'])->get();
                    if (count($interessados) > 0) {
                        foreach ($interessados as $interessado) {
                            $interessado->delete();
                        }
                    }

                    //Exclui os Oficio Externo vinculados
                    $oficio_externo = OficioExterno::where('oficio_id', $oficio['id'])->get();
                    if (count($oficio_externo) > 0) {
                        foreach ($oficio_externo as $oficio_ex) {
                            $oficio_ex->delete();
                        }
                    }

                    //Exclui os Oficio Relacionado vinculados
                    $oficio_relacionado = OficioRelacionado::where('oficio_pai', $oficio['id'])->get();
                    if (count($oficio_relacionado) > 0) {
                        foreach ($oficio_relacionado as $oficio_rel) {
                            $oficio_rel->delete();
                        }
                    }

                    $oficioDelete = Oficio::findOrFail($oficio['id']);
                    $oficioDelete->delete();
                }
            }
        }

        $oficios = Oficio::with('destinatario')->with('responsaveis')->with('anexos')->get();

        return $oficios;
    }

    public function removeanexo($id)
    {
        if ($id) {
            $anexo = AnexoOficio::find($id);
            if ($anexo) {
                Storage::disk('public')->delete($anexo->caminho);
                $anexo->delete();
            }
            $anexo = AnexoOficio::find($id);
            $anexo->delete();
        }
        return 'Anexo deletado';
    }

    public function ciencia($id)
    {
        $oficio = Oficio::find($id);
        $user_id = auth()->user()->id;

        $ciente = CienteOficio::where('user_id', $user_id)->where('oficio_id', $oficio->id)->get();
        if ($oficio) {
            if (count($ciente) == 0) {
                $ciencia = CienteOficio::create([
                    'user_id' => $user_id,
                    'oficio_id' => $oficio->id,
                ]);
            }
        }

        $oficio = Oficio::where('id', $id)
        ->with('destinatario')->with('responsaveis')
        ->with('interessados')->with('anexos')
        ->with('cientes')
        ->with('diretorias')->with('oficios_externos')->get();

        return $oficio;
    }

    public function generatepdf($id)
    {
        $oficio = Oficio::where('id', $id)
        ->with('destinatario')->with('responsaveis')
        ->with('interessados')->with('anexos')
        ->with('cientes')
        ->with('diretorias')->with('oficios_externos')->get();

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

                $oficioUpdate = Oficio::find($oficio->id);
                if ($prazo < 0 && $oficio->etapa != 'Finalizado') {
                    $oficioUpdate->prazo = $prazo;
                    $oficioUpdate->etapa = 'Atrasado';
                }
                $oficioUpdate->save();
            }
        }
    }

    public function email($id)
    {
        $oficio = Oficio::where('id', $id)
        ->with('destinatario')->with('responsaveis')
        ->with('interessados')->with('anexos')
        ->with('diretorias')->with('oficios_externos')->get();

        return view('mail.novooficio', compact('oficio'));
    }
}
