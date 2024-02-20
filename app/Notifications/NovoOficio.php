<?php

namespace App\Notifications;

use App\Models\Oficio;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NovoOficio extends Notification
{
    use Queueable;

    private Oficio $oficio;
    private User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Oficio $oficio, User $user)
    {
        $this->oficio = $oficio;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo Ofício Cadastrado')
            ->greeting('Olá, '. $this->user->name.' existe um novo ofício cadastrado no sistema.')
            ->line('Abaixo você pode conferir um resumo do ofício.')
            ->line('Tipo de ofício: '.$this->oficio->tipo_oficio)
            ->line('Número do ofício: '.$this->oficio->numero_oficio)
            ->line('Prazo: '.$this->oficio->prazo)
            ->line('Etapa: '.$this->oficio->etapa)
            ->action('Ver ofício', route('oficio.detail', $this->oficio->id))
            ->line('Mensagem automática, favor não responder a este e-mail.')
            ->line('Atenciosamente,')
            ->salutation('Sistema de Ofícios');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
