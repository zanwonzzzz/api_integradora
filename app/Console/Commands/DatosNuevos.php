<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobController;

class DatosNuevos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Datos:Nuevos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta los datos nuevos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ada = new CronJobController();
        $ada->CronJobParaDatosNuevos();
    }
}
