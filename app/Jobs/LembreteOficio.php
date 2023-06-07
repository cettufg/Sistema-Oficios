<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OficioLembrete;

class LembreteOficio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $oficio;
    private $usuario;

    /**
     * Create a new job instance.
     */
    public function __construct($oficio, $usuario)
    {
        $this->oficio = $oficio;
        $this->usuario = $usuario;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->usuario->email)->send(new OficioLembrete($this->oficio));
    }
}
