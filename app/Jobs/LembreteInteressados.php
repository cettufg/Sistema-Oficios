<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Oficio;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;

class LembreteInteressados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $interessados;
    private Oficio $oficio;

    /**
     * Create a new job instance.
     */
    public function __construct(Oficio $oficio, $interessados)
    {
        $this->oficio = $oficio;
        $this->interessados = $interessados;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->interessados as $interessado) {
            $user = User::find($interessado->user_id);

            if($user->status == '1') {
                Notification::route('mail', $user->email)->notify(new LembreteOficio($this->oficio, $user));
            }
        }
    }
}
