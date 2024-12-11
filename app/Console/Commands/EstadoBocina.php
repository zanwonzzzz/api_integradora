<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobController;

class EstadoBocina extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Estado:Bocina';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se consulta el estado de la bocina';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ada = new CronJobController();
        $ada->CronJobParaEstadoBocina();
    }
}
