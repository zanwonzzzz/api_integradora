<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Auditoria;
use App\Models\User;

class AuditoriaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $metodo;
    protected $ruta;
    protected $user;
    protected $info;
    protected $infopasada;
    protected $fecha;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($metodo,$ruta,User $user,$info,$infopasada = null,$fecha)
    {
        $this->metodo=$metodo;
        $this->ruta=$ruta;
        $this->user=$user;
        $this->info=$info;
        $this->infopasada=$infopasada;
        $this->fecha=$fecha;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $get = new Auditoria();
        $get->metodo = $this->metodo;
        $get->ruta = $this->ruta;
        $get->user = $this->user;
        $get->info = $this->info;
        $get->infopasada = $this->infopasada;
        $get->fecha = $this->fecha;
        $get->save();
    }
}
