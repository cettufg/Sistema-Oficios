<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Ofício - {{ $oficio[0]->numero_oficio }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            *{
                font-family: figtree;
            }
            table{
                width: 70%;
                border:1px solid #ccc;
                border-radius: 2rem;
                padding: 1rem;
            }
            span{
                font-weight: bold;
            }
            .titulo{
                font-size: 2rem;
                font-weight: bold;
                text-align: center;
            }
            .subtitle{
                font-size: 1.2rem;
                font-weight: bold;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <table>
            <tr>
                <td colspan="2" class="titulo">Ofício</td>
            </tr>
            <tr>
                <td>
                    <span class="font-bold">Tipo de Ofício</span>
                    <div>{{ $oficio[0]->tipo_oficio }}</div>
                </td>
                @if($oficio[0]->tipo_oficio == 'Recebido')
                <td>
                    <span class="font-bold">Tipo de documento</span>
                    <div>{{ $oficio[0]->tipo_documento }}</div>
                </td>
                @endif
                @if($oficio[0]->tipo_oficio == 'Expedido')
                <td>
                    <span class="font-bold">Diretoria</span>
                    @foreach($oficio[0]->diretorias as $diretoria)
                        @foreach($diretorias as $dir)
                            @if($dir->id == $diretoria['diretoria_id'])
                                <div>
                                    {{ $dir->nome }}
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </td>
                @endif
            </tr>
            <tr>
                <td>
                    <span class="font-bold">Número do ofício</span>
                    <div>{{ $oficio[0]->numero_oficio }}</div>
                </td>
                <td>
                    <span class="font-bold">Destinatário</span>
                    <div>{{ $oficio[0]->destinatario->nome }}</div>
                </td>
            </tr>
            <tr>
                @if($oficio[0]->tipo_oficio == 'Recebido')
                <td>
                    <span class="font-bold">Assunto do ofício recebido</span>
                    <div>{{ $oficio[0]->assunto_oficio }}</div>
                </td>
                @endif
                <td>
                    <span class="font-bold">Prazo</span>
                    <div>{{ $oficio[0]->prazo }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="font-bold">Dias corridos?</span>
                    <div>{{ $oficio[0]->dias_corridos == 0 ? 'Não' : 'Sim' }}</div>
                </td>
                @if($oficio[0]->tipo_oficio == 'Recebido')
                    <td>
                        <span class="font-bold">Data de recebimento</span>
                        <div>{{ date('d/m/Y', strtotime($oficio[0]->data_recebimento)) }}</div>
                    </td>
                @else
                    <td>
                        <span class="font-bold">Data de emissão</span>
                        <div>{{ date('d/m/Y', strtotime($oficio[0]->data_emissao)) }}</div>
                    </td>
                @endif
            </tr>
            <tr>
                <td>
                    <span class="font-bold">Responsáveis</span>
                    <div>
                        @foreach($oficio[0]->responsaveis as $responsavel)
                            @foreach($usuarios as $user)
                                @if($user->id == $responsavel['user_id'])
                                    <div>
                                        {{ $user->name }}
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                        
                    </div>
                </td>
                <td>
                    <span class="font-bold">Interessados</span>
                    <div>
                        @foreach($oficio[0]->interessados as $interessado)
                            @foreach($usuarios as $user)
                                @if($user->id == $interessado['user_id'])
                                    <div>
                                        {{ $user->name }}
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="font-bold">Status inicial</span>
                    <div>{{ $oficio[0]->status_inicial }}</div>
                </td>
                <td>
                    <span class="font-bold">Status final</span>
                    <div>{{ $oficio[0]->status_final }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <span>Anexos</span>
                    @foreach($oficio[0]->anexos as $anexo)
                        <div>
                            {{ $anexo['nome'] }}
                        </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>
                    <span class="font-bold">Observação</span>
                    <div>{{ $oficio[0]->observacao }}</div>
                </td>
                <td>
                    <span class="font-bold">Etapa</span>
                    <div>{{ $oficio[0]->etapa }}</div>
                </td>
            </tr>
            @if(count($oficios_relacionados) > 0 || count($oficio[0]->oficios_externos) > 0)
                <tr>
                    <td colspan="2" class="subtitle">Ofícios relacionados</td>
                </tr>
                <tr>
                    @if(count($oficios_relacionados) > 0)
                    <td>
                        <span>Ofício Interno</span>
                        @foreach($oficios_relacionados as $oficioInterno)
                            @foreach($oficios as $of)
                                @if($of->id == $oficioInterno['oficio_filho'])
                                    <div>
                                        {{ $of->tipo_oficio }} - {{ $of->numero_oficio }}
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </td>
                    @endif
                    @if(count($oficio[0]->oficios_externos) > 0)
                        <td>
                            <span>Ofício Externo</span>
                            <div>
                                @foreach($oficio[0]->oficios_externos as $oficioExterno)
                                <div>
                                    {{ $oficioExterno['descricao'] }}
                                </div>
                                @endforeach                        
                            </div>
                        </td>
                    @endif
                </tr>
            @endif
            @if($oficio[0]->tipo_oficio == 'Recebido')
                <tr>
                    <td colspan="2" class="subtitle">Cientes</td>
                </tr>
                <tr>
                    <td>
                        <span>Deram ciência</span>
                        @if(count($oficio[0]->cientes) > 0)
                            @foreach($oficio[0]->cientes as $ciente)
                                @foreach($usuarios as $usuario)
                                    @if($usuario->id == $ciente['user_id'])
                                        <div>
                                            {{ $usuario->name}}
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        @else
                        <div>Niguém deu ciência ainda</div>
                        @endif
                    </td>
                </tr>
            @endif
        </table>
    </body>
</html>