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

class LembreteResponsaveis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Oficio $oficio;
    private array $reponsaveis;

    /**
     * Create a new job instance.
     */
    public function __construct(Oficio $oficio, array $reponsaveis)
    {
        $this->oficio = $oficio;
        $this->reponsaveis = $reponsaveis;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->reponsaveis as $responsavel) {
            $user = User::find($responsavel->user_id);

            if($user->status == '1') {
                Notification::route('mail', $user->email)->notify(new LembreteOficio($this->oficio, $user));
            }
        }
    }
}
