{{-- resources/views/pdf/reporte_pagos_pendientes.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pagos Pendientes</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Pagos Pendientes</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No. Boleta</th>
                <th>Monto (Q)</th>
                <th>Fecha Emisión</th>
                <th>Fecha Vencimiento</th>
                <th>Código Nicho</th>
                <th>Responsable</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
                <tr>
                    <td>{{ $pago->numero_boleta }}</td>
                    <td>Q {{ number_format($pago->monto, 2) }}</td>
                    <td>{{ $pago->fecha_emision ? $pago->fecha_emision->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $pago->fecha_vencimiento ? $pago->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $pago->contrato->nicho->codigo ?? 'N/A' }}</td>
                    <td>{{ $pago->contrato->responsable->nombreCompleto ?? 'N/A' }}</td>
                    <td>{{ $pago->contrato->responsable->telefono ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>© {{ date('Y') }} Sistema de Gestión de Cementerio</p>
    </div>
</body>
</html>

{{-- resources/views/pdf/reporte_ocupacion.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ocupación General</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ocupación General</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Código Nicho</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Ubicación</th>
                <th>Ocupante</th>
                <th>Contrato</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nichos as $nicho)
                <tr>
                    <td>{{ $nicho->codigo }}</td>
                    <td>{{ $nicho->tipoNicho->nombre ?? 'N/A' }}</td>
                    <td>{{ $nicho->estadoNicho->nombre ?? 'N/A' }}</td>
                    <td>Calle {{ $nicho->calle }}, Av. {{ $nicho->avenida }}</td>
                    <td>
                        @if($nicho->contratoActivo && $nicho->contratoActivo->ocupante)
                            {{ $nicho->contratoActivo->ocupante->nombres ?? '' }} 
                            {{ $nicho->contratoActivo->ocupante->apellidos ?? '' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($nicho->contratoActivo)
                            #{{ $nicho->contratoActivo->id }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>© {{ date('Y') }} Sistema de Gestión de Cementerio</p>
    </div>
</body>
</html>