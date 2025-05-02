<?php

namespace App\Exports;

use App\Models\Nicho;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OcupacionExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Cargar nichos con relaciones clave
        // Necesitas una forma eficiente de saber el ocupante actual si está ocupado
        // Esto puede requerir una relación específica en el modelo Nicho
        // Ejemplo: hasOne(Contrato::class)->where('activo', true)->latestOfMany()
         return Nicho::with([
                    'tipoNicho',
                    'estadoNicho',
                    // Asume que tienes una relación 'contratoActivo' o similar
                     'contratoActivo' => function ($q) {
                          $q->with('ocupante:id,nombres,apellidos,dpi');
                     }
                ])->orderBy('codigo')->get();
    }

    public function headings(): array
    {
        return [
            'Código Nicho',
            'Tipo',
            'Estado',
            'Calle',
            'Avenida',
            'Histórico',
            'ID Contrato Activo',
            'Ocupante Actual Nombre',
            'Ocupante Actual DPI',
        ];
    }

    public function map($nicho): array
    {
        $contrato = $nicho->contratoActivo; // Usar la relación cargada
        $ocupante = $contrato ? $contrato->ocupante : null;

        return [
            $nicho->codigo,
            $nicho->tipoNicho->nombre ?? 'N/A',
            $nicho->estadoNicho->nombre ?? 'N/A',
            $nicho->calle,
            $nicho->avenida,
            $nicho->es_historico ? 'Sí' : 'No',
            $contrato->id ?? '-',
            $ocupante ? ($ocupante->nombres . ' ' . $ocupante->apellidos) : '-',
            $ocupante->dpi ?? '-',
        ];
    }

     public function styles(Worksheet $sheet)
    {
        return [ 1 => ['font' => ['bold' => true]] ];
    }
}