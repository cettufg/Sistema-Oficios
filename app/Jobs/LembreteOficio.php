<?php

namespace App\Jobs;

use App\Models\Oficio;
use App\Mail\OficioLembrete;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class LembreteOficio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $oficio;
    private $email;

    /**
     * Create a new job instance.
     */
    public function __construct(Oficio $oficio, $email)
    {
        $this->oficio = $oficio;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->email)->send(new OficioLembrete($this->oficio));
    }
}
