<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato - {{ $contract->teamMember->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            margin: 0;
            padding: 40px;
            background-color: #fff;
        }

        .contract-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #eee;
            padding: 60px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #f97316;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .document-title {
            font-size: 18px;
            font-weight: 600;
            margin-top: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.2em;
        }

        .clause {
            margin-bottom: 30px;
            text-align: justify;
        }

        .clause-title {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-size: 14px;
            color: #111;
        }

        .highlight {
            font-weight: 600;
            border-bottom: 1px solid #ddd;
        }

        .signatures {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #111;
            margin-bottom: 10px;
        }

        .signature-name {
            font-weight: 700;
            font-size: 12px;
        }

        .signature-id {
            font-size: 11px;
            color: #666;
        }

        @media print {
            body { padding: 0; }
            .contract-container { 
                border: none; 
                box-shadow: none; 
                padding: 0;
            }
            .no-print { display: none; }
        }

        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            background: #f97316;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(249,115,22,0.3);
        }

        .btn-secondary {
            background: #333;
        }
    </style>
</head>
<body>

<div class="controls no-print">
    <button onclick="window.print()" class="btn">Imprimir Contrato</button>
    <a href="{{ route('team.index') }}" class="btn btn-secondary">Volver</a>
</div>

<div class="contract-container">
    <div class="header">
        <div class="logo">LIMBANI</div>
        <div class="document-title">Contrato Individual de Trabajo</div>
    </div>

    <div class="clause">
        <p>
            Entre los suscritos, a saber, <span class="highlight">INJOE AGENCIA DIGITAL</span>, representada legalmente por su representante autorizado, quien en adelante se denominará EL EMPLEADOR, y por la otra parte <span class="highlight">{{ $contract->teamMember->name }}</span>, mayor de edad, identificado(a) con la cédula de ciudadanía No. <span class="highlight">{{ $contract->teamMember->cedula }}</span>, quien en adelante se denominará EL TRABAJADOR, se ha convenido celebrar el presente contrato bajo las siguientes cláusulas:
        </p>
    </div>

    <div class="clause">
        <div class="clause-title">PRIMERA. OBJETO Y CARGO:</div>
        <p>
            EL EMPLEADOR contrata los servicios personales de EL TRABAJADOR para desempeñar el cargo de <span class="highlight">{{ $contract->position }}</span>, realizando las funciones inherentes al mismo y las que se le asignen de acuerdo con la naturaleza del cargo.
        </p>
    </div>

    <div class="clause">
        <div class="clause-title">SEGUNDA. TIPO DE CONTRATO Y DURACIÓN:</div>
        <p>
            El presente contrato es de tipo <span class="highlight">{{ $contract->type }}</span>, con una fecha de inicio del <span class="highlight">{{ $contract->start_date->isoFormat('LL') }}</span>
            @if($contract->end_date)
                y una fecha de finalización pactada para el <span class="highlight">{{ $contract->end_date->isoFormat('LL') }}</span>.
            @else
                y de duración indefinida.
            @endif
        </p>
    </div>

    <div class="clause">
        <div class="clause-title">TERCERA. REMUNERACIÓN:</div>
        <p>
            EL EMPLEADOR pagará a EL TRABAJADOR por la prestación de sus servicios una remuneración mensual de <span class="highlight">${{ number_format($contract->salary, 0, ',', '.') }} COP</span>, los cuales serán cancelados mediante los periodos de pago establecidos por la agencia.
        </p>
    </div>

    <div class="clause">
        <div class="clause-title">CUARTA. JORNADA Y LUGAR DE TRABAJO:</div>
        <p>
            EL TRABAJADOR cumplirá su labor en las instalaciones de la agencia o de forma remota según se acuerde, cumpliendo con los objetivos y plazos establecidos para el cargo de <span class="highlight">{{ $contract->position }}</span>.
        </p>
    </div>

    <div class="clause">
        <div class="clause-title">QUINTA. CONFIDENCIALIDAD:</div>
        <p>
            EL TRABAJADOR se compromete a mantener absoluta reserva sobre toda la información estratégica, bases de datos, clientes y procesos internos de LIMBANI a los que tenga acceso durante el ejercicio de sus funciones.
        </p>
    </div>

    <div class="clause">
        <p>
            Para constancia se firma en dos ejemplares del mismo tenor, el día <span class="highlight">{{ \Carbon\Carbon::now()->isoFormat('LL') }}</span>.
        </p>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">INJOE AGENCIA DIGITAL</div>
            <div class="signature-id">EL EMPLEADOR</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-name">{{ strtoupper($contract->teamMember->name) }}</div>
            <div class="signature-id">C.C. {{ $contract->teamMember->cedula }}</div>
            <div class="signature-id">EL TRABAJADOR</div>
        </div>
    </div>
</div>

</body>
</html>
