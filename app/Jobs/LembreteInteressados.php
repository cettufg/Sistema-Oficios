<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Oficio;
use App\Jobs\LembreteOficio;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class LembreteInteressados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $interessados;
    private $oficio;

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
                LembreteOficio::dispatch($this->oficio, $user->email);
            }
        }
    }
}
