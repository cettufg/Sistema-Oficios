<?php

namespace App\Jobs;

use App\Models\Oficio;
use App\Models\User;
use App\Notifications\LembreteOficio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendLembrete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Oficio $oficio;
    private $usuarios;

    /**
     * Create a new job instance.
     */
    public function __construct(Oficio $oficio, $usuarios)
    {
        $this->oficio = $oficio;
        $this->usuarios = $usuarios;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->usuarios as $usuario) {
            $userBD = User::find($usuario->user_id);

            if ($userBD->status == '1') {
                Notification::route('mail', $userBD->email)->notify(new LembreteOficio($this->oficio, $userBD));
            }
        }
    }
}
