<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Boleta de Pago #{{ $pago->numero_boleta }}</title>
    {{-- Estilos CSS para el PDF (puedes usar <style> o enlazar un CSS) --}}
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 40px; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 10px; }
        .content { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        .content h2 { text-align: center; margin-top: 0; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px;}
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .details-table th, .details-table td { border: 1px solid #eee; padding: 8px; text-align: left; }
        .details-table th { background-color: #f8f8f8; font-weight: bold; width: 30%; }
        .total { text-align: right; margin-top: 20px; font-size: 14px; font-weight: bold; }
        .barcode { text-align: center; margin-top: 30px; } /* Para código de barras si lo añades */
        .small-text { font-size: 9px; color: #555; }
    </style>
</head>
<body>

    <div class="header">
        {{-- Puedes poner aquí el logo de la municipalidad si tienes la imagen --}}
        {{-- <img src="{{ public_path('images/logo_muni.png') }}" alt="Logo" width="100"/> --}}
        <h1>Municipalidad de Quetzaltenango</h1>
        <p>Cementerio General - Administración</p>
        <h2>BOLETA DE PAGO (RENOVACIÓN)</h2>
    </div>

    <div class="content">
        <table class="details-table">
             <tr><th>No. Boleta:</th><td>{{ $pago->numero_boleta }}</td></tr>
             <tr><th>Fecha Emisión:</th><td>{{ \Carbon\Carbon::parse($pago->fecha_emision)->format('d/m/Y') }}</td></tr>
             <tr><th>Fecha Vencimiento:</th><td>{{ $pago->fecha_vencimiento ? \Carbon\Carbon::parse($pago->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}</td></tr>
             <tr><th>Contrato ID:</th><td>{{ $contrato->id }}</td></tr>
        </table>

         <table class="details-table">
            <tr><th colspan="2" style="text-align:center; background-color:#e9ecef;">Información del Responsable</th></tr>
            <tr><th>Nombre:</th><td>{{ $responsable->nombreCompleto ?? 'N/A' }}</td></tr>
            <tr><th>DPI:</th><td>{{ $responsable->dpi ?? 'N/A' }}</td></tr>
            {{-- <tr><th>Dirección:</th><td>{{ $responsable->direccionCompleta ?? 'N/A' }}</td></tr> --}}
             <tr><th>Teléfono:</th><td>{{ $responsable->telefono ?? 'N/A' }}</td></tr>
        </table>

        <table class="details-table">
            <tr><th colspan="2" style="text-align:center; background-color:#e9ecef;">Información del Nicho y Ocupante</th></tr>
             <tr><th>Nicho Código:</th><td>{{ $nicho->codigo ?? 'N/A' }}</td></tr>
             <tr><th>Ubicación:</th><td>{{ $nicho->calle ?? 'S/C' }} y {{ $nicho->avenida ?? 'S/A' }}</td></tr>
             <tr><th>Ocupante:</th><td>{{ $ocupante->nombreCompleto ?? 'N/A' }}</td></tr>
             <tr><th>Fecha Fallecimiento:</th><td>{{ $ocupante->fecha_fallecimiento ? \Carbon\Carbon::parse($ocupante->fecha_fallecimiento)->format('d/m/Y') : 'N/A' }}</td></tr>
        </table>

         <table class="details-table">
            <tr><th colspan="2" style="text-align:center; background-color:#e9ecef;">Detalle del Cargo</th></tr>
             <tr><th>Concepto:</th><td>Renovación de Contrato de Nicho (Período {{ \Carbon\Carbon::parse($contrato->fecha_fin_original)->addDay()->format('Y') }} - {{ \Carbon\Carbon::parse($contrato->fecha_fin_original)->addYears(6)->format('Y') }})</td></tr> {{-- Ajustar cálculo si es diferente --}}
             <tr><th>Monto:</th><td>Q {{ number_format($pago->monto, 2) }}</td></tr>
         </table>

         <div class="total">
             TOTAL A PAGAR: Q {{ number_format($pago->monto, 2) }}
         </div>

         {{-- Puedes añadir información de pago, códigos de barras, etc. --}}
         <div class="barcode small-text" style="margin-top: 30px;">
             <p>Presentar esta boleta en la ventanilla municipal o banco autorizado.</p>
             {{-- Generar código de barras si es necesario con otra librería --}}
             {{-- <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($pago->numero_boleta, 'C128') }}" alt="barcode" /> --}}
         </div>
    </div>

    <div class="footer small-text">
        <p>Documento generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este documento es válido únicamente para el pago del concepto descrito.</p>
    </div>

</body>
</html>