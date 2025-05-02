<?php

namespace App\Exports;

use App\Models\Pago;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PagosPendientesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Obtener datos con relaciones necesarias
        return Pago::with(['contrato.nicho', 'contrato.responsable'])
                   ->where('estado_pago_id', 1) // Asume ID 1 = Pendiente
                   ->orderBy('fecha_vencimiento', 'asc')
                   ->get();
    }

    public function headings(): array
    {
        // Encabezados de columna en Excel
        return [
            'ID Pago',
            'No. Boleta',
            'Monto (Q)',
            'Fecha Emisión',
            'Fecha Vencimiento',
            'ID Contrato',
            'Código Nicho',
            'Responsable Nombre',
            'Responsable DPI',
            'Responsable Teléfono',
        ];
    }

    public function map($pago): array
    {
        // Mapear cada objeto Pago a una fila de datos
        return [
            $pago->id,
            $pago->numero_boleta,
            $pago->monto,
            $pago->fecha_emision ? $pago->fecha_emision->format('Y-m-d') : '',
            $pago->fecha_vencimiento ? $pago->fecha_vencimiento->format('Y-m-d') : '',
            $pago->contrato->id ?? 'N/A',
            $pago->contrato->nicho->codigo ?? 'N/A',
            $pago->contrato->responsable->nombreCompleto ?? 'N/A',
            $pago->contrato->responsable->dpi ?? 'N/A',
            $pago->contrato->responsable->telefono ?? 'N/A',
        ];
    }

     public function styles(Worksheet $sheet)
    {
        // Estilo para la fila de encabezados
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}