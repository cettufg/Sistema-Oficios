<?php

namespace App\Http\Controllers;

use App\Models\AnexoOficio;
use App\Models\CienteOficio;
use App\Models\Destinatario;
use App\Models\Diretoria;
use App\Models\DiretoriaOficio;
use App\Models\InteressadosOficio;
use App\Models\Oficio;
use App\Models\OficioExterno;
use App\Models\OficioRelacionado;
use App\Models\ResponsavelOficio;
use App\Models\User;
use App\Notifications\NovoOficio;
use App\Utils\generateDeadline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Pdf;

class OficioController extends Controller
{
    public function index()
    {
        $oficiosQuery = Oficio::with('destinatario', 'responsaveis.user', 'interessados.user', 'anexos')->latest();

        if (auth()->user()->is_admin !== 1) {
            $logged_user = auth()->id();
            $oficiosQuery->where(function ($query) use ($logged_user) {
                $query->whereHas('responsaveis', function ($query) use ($logged_user) {
                    $query->where('user_id', $logged_user);
                })->orWhereHas('interessados', function ($query) use ($logged_user) {
                    $query->where('user_id', $logged_user);
                })->orWhere('user_created', $logged_user);
            });
        }

        $oficios = $oficiosQuery->get();

        return Inertia::render('Oficio/Index', [
            'oficios' => $oficios,
        ]);
    }

    public function edit(int $id)
    {
        $oficiosBD = Oficio::with('destinatario', 'responsaveis.user:id,name', 'interessados.user:id,name', 'anexos', 'diretorias', 'oficios_externos', 'oficios_relacionados.oficio_filho:id,tipo_oficio,numero_oficio')
                           ->get();


        $oficios = $oficiosBD->where('id', '<>', $id)->toArray();
        $oficio = $oficiosBD->where('id', $id)->first();

        $usuarios = User::active()->get();
        $destinatarios = Destinatario::active()->get();
        $diretorias = Diretoria::active()->get();

        return Inertia::render('Oficio/Crud', [
            'oficios' => $oficios,
            'oficio' => $oficio,
            'usuarios' => $usuarios,
            'destinatarios' => $destinatarios,
            'diretorias' => $diretorias
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_oficio' => 'required',
            'responsaveis' => 'required',
            'interessados' => 'required',
            'destinatario_id' => 'required',
            'numero_oficio' => 'required',
            'prazo' => 'nullable',
            'dias_corridos' => 'required',
            'data_prazo' => 'required|date',
            'observacao' => 'nullable',
            'status_inicial' => 'nullable',
            'status_final' => 'nullable',
            'etapa' => 'required',
            'anexos' => 'nullable',
            'assunto_oficio' => 'nullable',
            'tipo_documento' => 'required',
            'data_recebimento' => 'required_if:tipo_oficio,Recebido|date',
            'numero_notificacao' => 'required_if:tipo_oficio,Expedido',
            'diretoria' => 'required_if:tipo_oficio,Expedido',
            'data_emissao' => 'required_if:tipo_oficio,Expedido',
            'anexos.*' => 'nullable|file|max:10240',
        ]);

        //Valida a lógica de cadastros
        if ($request->input('tipo_oficio') === 'Recebido') {
            $prazo = generateDeadline::run($request->input('data_recebimento'), $request->input('data_prazo'));
        } else {
            $prazo = generateDeadline::run($request->input('data_emissao'), $request->input('data_prazo'));
        }
        $etapa = '';
        if ($prazo < 0 && $request->input('etapa') !== 'Finalizado') {
            $etapa = 'Atrasado';
        } else {
            $etapa = $request->input('etapa');
        }

        $oficio = Oficio::create([
            'destinatario_id' => $request->input('destinatario_id.id'),
            'numero_oficio' => $request->input('numero_oficio'),
            'assunto_oficio' => $request->input('assunto_oficio'),
            'data_recebimento' => $request->input('data_recebimento'),
            'data_emissao' => $request->input('data_emissao'),
            'data_prazo' => $request->input('data_prazo'),
            'prazo' => $prazo,
            'dias_corridos' => $request->input('dias_corridos') == 'Não' ? 0 : 1,
            'numero_notificacao' => $request->input('numero_notificacao'),
            'observacao' => $request->input('observacao'),
            'tipo_oficio' => $request->input('tipo_oficio'),
            'tipo_documento' => $request->input('tipo_documento'),
            'status_inicial' => $request->input('status_inicial'),
            'status_final' => $request->input('status_final'),
            'etapa' => $etapa,
            'user_created' => auth()->id()
        ]);

        //Cadastra os arquivos anexados
        if (array_key_exists('anexos', $request->all())) {
            foreach ($request['anexos'] as $file) {
                if ($file->isValid()) {
                    $tamanho = $file->getSize();
                    $tipo = $file->getMimeType();

                    // Remove caracteres inválidos, substituindo por um underscore
                    $cleaned_file_name = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $file->getClientOriginalName());
                    // Remove múltiplos underscores consecutivos e espaços
                    $cleaned_file_name = preg_replace('/[\-_\. ]+/', '_', $cleaned_file_name);

                    $caminho = $file->storeAs('anexos', $cleaned_file_name);

                    AnexoOficio::create([
                        'nome' => $file->getClientOriginalName(),
                        'tipo' => $tipo,
                        'tamanho' => $tamanho,
                        'caminho' => $caminho,
                        'oficio_id' => $oficio->id
                    ]);
                }
            }
        }

        //Cadastra os interessados
        foreach ($request['interessados'] as $interessado) {
            $user = User::find($interessado['id']);
            Notification::route('mail', $user->email)->notify(new NovoOficio($oficio, $user));
            $oficio->interessados()->create([
                'user_id' => $interessado['id']
            ]);
        }

        //Cadastra os responsaveis
        foreach ($request['responsaveis'] as $responsavel) {
            $user = User::find($interessado['id']);
            Notification::route('mail', $user->email)->notify(new NovoOficio($oficio, $user));
            $oficio->responsaveis()->create([
                'user_id' => $responsavel['id']
            ]);
        }

        if ($request['diretoria'] != null) {
            foreach ($request['diretoria'] as $diretoria) {
                $oficio->diretorias()->create([
                    'diretoria_id' => $diretoria['id']
                ]);
            }
        }

        //Cadastra os ofícios relacionados
        if (array_key_exists('oficio_relacionado', $request->all())) {
            foreach ($request['oficio_relacionado'] as $oficio_relacionado) {
                $oficio->oficios_relacionados()->create([
                    'oficio_filho' => $oficio_relacionado['id']
                ]);
            }
        }

        //Cadastra os ofícios externos
        if (array_key_exists('oficio_externo', $request->all())) {
            foreach ($request['oficio_externo'] as $oficio_externo) {
                $oficio->oficios_externos()->create([
                    'descricao' => $oficio_externo
                ]);
            }
        }

        return redirect()->route('oficio.index');
    }

    public function create()
    {
        $usuarios = User::active()->get();
        $destinatarios = Destinatario::active()->get();
        $diretorias = Diretoria::active()->get();
        $oficios = Oficio::all('id', 'numero_oficio', 'tipo_oficio')->toArray();

        return Inertia::render('Oficio/Crud', [
            'usuarios' => $usuarios,
            'destinatarios' => $destinatarios,
            'diretorias' => $diretorias,
            'oficios' => $oficios
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipo_oficio' => 'required',
            'responsaveis' => 'required',
            'interessados' => 'required',
            'destinatario_id' => 'required',
            'numero_oficio' => 'required',
            'prazo' => 'nullable',
            'dias_corridos' => 'required',
            'data_prazo' => 'required|date',
            'observacao' => 'nullable',
            'status_inicial' => 'nullable',
            'status_final' => 'nullable',
            'etapa' => 'required',
            'anexos' => 'nullable',
            'assunto_oficio' => 'nullable',
            'tipo_documento' => 'required',
            'data_recebimento' => 'required_if:tipo_oficio,Recebido|date',
            'numero_notificacao' => 'required_if:tipo_oficio,Expedido',
            'diretoria' => 'required_if:tipo_oficio,Expedido',
            'data_emissao' => 'required_if:tipo_oficio,Expedido',
            'anexos.*' => 'nullable|file|max:10240',
        ]);

        $oficio = Oficio::findOrFail($id);

        //Valida a lógica de cadastros
        if ($request->input('tipo_oficio') === 'Recebido') {
            $prazo = generateDeadline::run($request->input('data_recebimento'), $request->input('data_prazo'));
        } else {
            $prazo = generateDeadline::run($request->input('data_emissao'), $request->input('data_prazo'));
        }
        $etapa = '';

        if ($prazo < 0 && $request->input('etapa') != 'Finalizado') {
            $etapa = 'Atrasado';
        } else {
            $etapa = $request->input('etapa');
        }

        $oficio->fill([
            'destinatario_id' => $request->input('destinatario_id.id'),
            'numero_oficio' => $request->input('numero_oficio'),
            'assunto_oficio' => $request->input('assunto_oficio'),
            'data_recebimento' => $request->input('data_recebimento'),
            'data_emissao' => $request->input('data_emissao'),
            'data_prazo' => $request->input('data_prazo'),
            'prazo' => $prazo,
            'dias_corridos' => $request->input('dias_corridos') === 'Não' ? 0 : 1,
            'numero_notificacao' => $request->input('numero_notificacao'),
            'observacao' => $request->input('observacao'),
            'tipo_oficio' => $request->input('tipo_oficio'),
            'tipo_documento' => $request->input('tipo_documento'),
            'status_inicial' => $request->input('status_inicial'),
            'status_final' => $request->input('status_final'),
            'etapa' => $etapa,
            'user_updated' => auth()->id()
        ])->save();

        //Cadastra os arquivos anexados
        if (array_key_exists('anexos', $request->all())) {
            if (count($request['anexos']) > 0) {
                foreach ($request['anexos'] as $file) {
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
        foreach ($request['interessados'] as $interessado) {
            $oficio->interessados()->create([
                'user_id' => $interessado['id']
            ]);
        }

        //Cadastra os responsaveis
        $oficio->responsaveis()->delete();
        foreach ($request['responsaveis'] as $responsavel) {
            $oficio->responsaveis()->create([
                'user_id' => $responsavel['id']
            ]);
        }

        //Cadastra os ofícios relacionados
        $oficio->oficios_relacionados()->delete();
        if (array_key_exists('oficio_relacionado', $request->all())) {
            foreach ($request['oficio_relacionado'] as $oficio_relacionado) {
                $oficio->oficios_relacionados()->create([
                    'oficio_filho' => $oficio_relacionado['id']
                ]);
            }
        }

        //Cadastra os ofícios externos
        $oficio->oficios_externos()->delete();
        if (array_key_exists('oficio_externo', $request->all())) {
            foreach ($request['oficio_externo'] as $oficio_externo) {
                if (gettype($oficio_externo) == 'string') {
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

    public function detail(int $id)
    {
        $oficio = Oficio::where('id', $id)
                        ->with('destinatario', 'responsaveis.user:id,name', 'interessados.user:id,name', 'anexos', 'diretorias.diretoria:id,nome', 'oficios_externos', 'oficios_relacionados.oficio_filho:id,tipo_oficio,numero_oficio', 'cientes.user:id,name')
                        ->get();

        return Inertia::render('Oficio/Show', [
            'oficio' => $oficio
        ]);
    }

    public function destroy(int $id)
    {
        if ($id) {
            //Exclui os Anexos vinculados
            $anexos = AnexoOficio::where('oficio_id', $id)->get();
            if (count($anexos) > 0) {
                foreach ($anexos as $anexo) {
                    Storage::disk('public')->delete($anexo->caminho);
                    $anexo->delete();
                }
            }

            //Exclui os Ciente vinculados
            $cientes = CienteOficio::where('oficio_id', $id)->get();
            if (count($cientes) > 0) {
                foreach ($cientes as $ciente) {
                    $ciente->delete();
                }
            }

            //Exclui os Diretoria vinculados
            $diretores = DiretoriaOficio::where('oficio_id', $id)->get();
            if (count($diretores) > 0) {
                foreach ($diretores as $diretor) {
                    $diretor->delete();
                }
            }

            //Exclui os Responsaveis vinculados
            $responsaveis = ResponsavelOficio::where('oficio_id', $id)->get();
            if (count($responsaveis) > 0) {
                foreach ($responsaveis as $responsavel) {
                    $responsavel->delete();
                }
            }

            //Exclui os Interessados vinculados
            $interessados = InteressadosOficio::where('oficio_id', $id)->get();
            if (count($interessados) > 0) {
                foreach ($interessados as $interessado) {
                    $interessado->delete();
                }
            }

            //Exclui os Oficio Externo vinculados
            $oficio_externo = OficioExterno::where('oficio_id', $id)->get();
            if (count($oficio_externo) > 0) {
                foreach ($oficio_externo as $oficio_ex) {
                    $oficio_ex->delete();
                }
            }

            //Exclui os Oficio Relacionado vinculados
            $oficio_relacionado = OficioRelacionado::where('oficio_pai', $id)->get();
            if (count($oficio_relacionado) > 0) {
                foreach ($oficio_relacionado as $oficio_rel) {
                    $oficio_rel->delete();
                }
            }


            $oficio = Oficio::findOrFail($id);
            $oficio->delete();
        }

        return redirect()->back();
    }

    public function destroySelected(Request $request)
    {
        $request->validate([
            'selecteds' => 'required',
        ]);

        $dados = $request['selecteds'];

        if (count($dados) > 0) {
            foreach ($dados as $oficio) {
                $oficioBD = Oficio::where('id', $oficio['id'])
                                  ->with('anexos', 'cientes', 'diretorias', 'interessados', 'responsaveis', 'oficios_externos', 'oficios_relacionados')
                                  ->get();
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

        $logged_user = auth()->id();

        return redirect()->back();
    }

    public function destroyAnexo($id)
    {
        $anexo = AnexoOficio::find($id);
        if ($anexo) {
            Storage::disk('public')->delete($anexo->caminho);
            $anexo->delete();
        }

        return redirect()->back();
    }

    public function ciencia($id)
    {
        $oficio = Oficio::find($id);
        $user_id = auth()->id();

        //validate if user is already ciente
        if ($oficio->cientes()->where('user_id', $user_id)->count() > 0) {
            return redirect()->back();
        }

        $oficio->cientes()->create([
            'user_id' => $user_id
        ]);

        return redirect()->back();
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

        $nomepdf = 'Ofício_'.Str::of($oficio[0]->numero_oficio)->slug('-').'.pdf';


        return Pdf::loadView('pdf.oficio', [
            'oficio' => $oficio,
            'diretorias' => $diretorias,
            'oficios' => $oficios,
            'usuarios' => $usuarios,
            'oficios_relacionados' => $oficios_relacionados,
            'auth' => $auth
        ])->stream($nomepdf);
    }
}
