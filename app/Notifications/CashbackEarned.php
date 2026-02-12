<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CashbackEarned extends Notification
{
  use Queueable;

  public function __construct(
    public int $amount,
    public string $badgeName
  ) {}

  public function via(object $notifiable): array
  {
    return ['database'];
  }

  public function toArray(object $notifiable): array
  {
    return [
      'message' => "You earned {$this->amount} NGN for unlocking {$this->badgeName}!",
      'amount' => $this->amount,
      'badge' => $this->badgeName,
    ];
  }
}
