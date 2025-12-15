<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Activity;

class MigrateDealNotesToActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:migrate-notes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate deal notes from deals table to activities table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contactoInicialStage = DealStage::where('name', 'Contacto Inicial')->first();
        $propuestaEnviadaStage = DealStage::where('name', 'Propuesta Enviada')->first();
        $negociacionStage = DealStage::where('name', 'Negociación')->first();

        if (!$contactoInicialStage || !$propuestaEnviadaStage || !$negociacionStage) {
            $this->error("Una o más etapas de deal no se encontraron. Asegúrate de ejecutar el seeder DealStageSeeder.");
            return;
        }

        $this->info('Migrating deal notes to activities...');

        Deal::chunk(100, function ($deals) use ($contactoInicialStage, $propuestaEnviadaStage, $negociacionStage) {
            foreach ($deals as $deal) {
                if ($deal->notes_contact) {
                    Activity::create([
                        'loggable_id' => $deal->id,
                        'loggable_type' => Deal::class,
                        'deal_stage_id' => $contactoInicialStage->id,
                        'type' => 'note',
                        'description' => $deal->notes_contact,
                        'user_id' => $deal->user_id,
                    ]);
                }

                if ($deal->notes_proposal) {
                    Activity::create([
                        'loggable_id' => $deal->id,
                        'loggable_type' => Deal::class,
                        'deal_stage_id' => $propuestaEnviadaStage->id,
                        'type' => 'note',
                        'description' => $deal->notes_proposal,
                        'user_id' => $deal->user_id,
                    ]);
                }

                if ($deal->notes_negotiation) {
                    Activity::create([
                        'loggable_id' => $deal->id,
                        'loggable_type' => Deal::class,
                        'deal_stage_id' => $negociacionStage->id,
                        'type' => 'note',
                        'description' => $deal->notes_negotiation,
                        'user_id' => $deal->user_id,
                    ]);
                }
            }
        });

        $this->info('Migration of deal notes to activities completed.');
    }
}
