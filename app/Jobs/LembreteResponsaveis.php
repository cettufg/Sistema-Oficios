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

class LembreteResponsaveis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $oficio;
    private $reponsaveis;

    /**
     * Create a new job instance.
     */
    public function __construct(Oficio $oficio, $reponsaveis)
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
                LembreteOficio::dispatch($this->oficio, $user->email);
            }
        }
    }
}
