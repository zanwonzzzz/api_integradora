<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobController;

class ConsumirAdafruit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Consumir:Adafruit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Guardar datos de Adafruit en la base de datos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $ada = new CronJobController();
            $ada->CronJobParaGuardarDatos();
            $this->info("Datos guardados correctamente.");
        } catch (\Exception $e) {
            $this->error("Error al ejecutar CronJob: " . $e->getMessage());
        }
    }
}
