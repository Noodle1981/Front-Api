<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deal;
use App\Models\DealStage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuditDealsCommand extends Command
{
    protected $signature = 'crm:audit-deals';
    protected $description = 'Finds deals that have been stuck in a specific stage for more than a defined period.';

    public function handle()
    {
        $stuckStageName = 'Propuesta Enviada';
        $daysThreshold = 60;

        $this->info("Iniciando auditoría de deals estancados en la etapa '{$stuckStageName}' por más de {$daysThreshold} días...");
        Log::info('================ INICIO DE AUDITORÍA DE DEALS ESTANCADOS ===============');

        $stuckStage = DealStage::where('name', $stuckStageName)->first();

        if (!$stuckStage) {
            $this->error("La etapa '{$stuckStageName}' no fue encontrada. Abortando auditoría.");
            Log::error("No se pudo encontrar la deal_stage con nombre: {$stuckStageName}");
            return 1;
        }

        $thresholdDate = Carbon::now()->subDays($daysThreshold);

        $stuckDeals = Deal::where('deal_stage_id', $stuckStage->id)
                           ->where('updated_at', '<=', $thresholdDate)
                           ->get();

        if ($stuckDeals->isEmpty()) {
            $this->info("✓ No se encontraron deals estancados en la etapa '{$stuckStageName}' por más de {$daysThreshold} días.");
            Log::info("Criterio de Adecuación Cumplido: No hay deals estancados.");
            $this->info('[✓] Criterio de Adecuación Cumplido.');
        } else {
            $this->error(sprintf(
                '[✗] Criterio de Adecuación No Cumplido: Se encontraron %d deal(s) estancado(s) en \'%s\':',
                $stuckDeals->count(),
                $stuckStageName
            ));
            Log::warning(sprintf("Se encontraron %d deals estancados.", $stuckDeals->count()));

            $tableHeaders = ['ID del Deal', 'Nombre', 'Fecha de Última Actualización', 'Días Estancado'];
            $tableRows = [];

            foreach ($stuckDeals as $deal) {
                $stagnantDays = $deal->updated_at->diffInDays(Carbon::now());
                $tableRows[] = [
                    $deal->id,
                    $deal->name,
                    $deal->updated_at->format('Y-m-d H:i:s'),
                    $stagnantDays,
                ];
                Log::warning("Deal ID: {$deal->id} ('{$deal->name}') está estancado por {$stagnantDays} días.");
            }

            $this->table($tableHeaders, $tableRows);
        }

        Log::info('================ FIN DE AUDITORÍA DE DEALS ESTANCADOS =================');
        $this->info('Auditoría finalizada.');

        return 0;
    }
}
