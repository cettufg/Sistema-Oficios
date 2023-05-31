<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ofício</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        * {
            font-family: figtree;
            font-size: 1.1rem;
        }

        .container {
            width: 60%;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 0.8rem;
            padding: 2rem;
        }

        .destaque {
            font-weight: bold;
        }

        .logotipo {
            text-align: center;
        }

        .details {
            margin-top: 1rem;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            margin-top: 1rem;
        }

        .row div {
            text-align: left;
        }

        span {
            font-weight: bold;
        }

        .sectionbutton {
            margin-top: 3rem;
        }

        .sectionbutton a {
            background-color: #032b44;
            text-decoration: none;
            color: #fff;
            padding: .5rem .5rem;
            border: 0;
            border-radius: .5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logotipo">
            <img src="https://i.ibb.co/JRZsk2Q/logotipo.png" alt="Logotipo" width="220px">
        </div>
        <div>
            <p class="destaque">Olá, existe um ofício que precisa de sua atenção!</p>
            <p>Abaixo você pode conferir os detalhes do ofício</p>
        </div>
        <div class="details">
            <div class="row">
                <div>
                    <span>Tipo de ofício:</span>
                    <div>{{ $oficio->tipo_oficio }}</div>
                </div>
                <div>
                    <span>Número do ofício:</span>
                    <div>{{ $oficio->numero_oficio }} - {{ $oficio->destinatario->nome }}</div>
                </div>
            </div>
            <div class="row">
                <div>
                    <span>Prazo:</span>
                    <div>{{ $oficio->prazo }}</div>
                </div>
                @if ($oficio->tipo_oficio == 'Recebido')
                    <div>
                        <span>Data de recebimento</span>
                        <div>{{ date('d/m/Y', strtotime($oficio->data_recebimento)) }}</div>
                    </div>
                @else
                    <div>
                        <span>Data de emissão</span>
                        <div>{{ date('d/m/Y', strtotime($oficio->data_emissao)) }}</div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div>
                    <span>Etapa:</span>
                    <div>{{ $oficio->etapa }}</div>
                </div>
            </div>
        </div>

        <div class="sectionbutton">
            <a href="{{ route('oficio.detail', $oficio->id) }}">Ver ofício</a>
        </div>
    </div>
</body>

</html>
