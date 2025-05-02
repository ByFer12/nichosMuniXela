<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrato;
use Carbon\Carbon;

class UpdateContractStatus extends Command
{
    protected $signature = 'contracts:update-status';
    protected $description = 'Actualiza el estado de los contratos basado en fechas';

    public function handle()
    {
        $hoy = Carbon::today();

        // Contratos que están en período de gracia
        Contrato::where('activo', true)
            ->where('fecha_fin_original', '<', $hoy)
            ->where('fecha_fin_gracia', '>=', $hoy)
            ->each(function ($contrato) {
                // Registrar advertencia (opcional)
                \Log::info("Contrato {$contrato->id} en período de gracia hasta {$contrato->fecha_fin_gracia}");
            });

        // Contratos que han superado la gracia
        Contrato::where('activo', true)
            ->where('fecha_fin_gracia', '<', $hoy)
            ->update(['activo' => false]);

        $this->info('Estados de contratos actualizados correctamente');
    }
}