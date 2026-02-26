<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class SendNotification implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    protected ?int $notificationId = null;
    protected ?int $subscriberId = null;
    protected ?string $channel = null;
    protected ?string $subject = null;
    protected mixed $body = null;

    /**
     * Create a new job instance.
     *
     * Supports either a Notification (or its ID) or a Subscriber (legacy digest flow).
     */
    public function __construct(
        Notification|Subscriber|int $notificationOrSubscriber,
        ?string $channel = null,
        ?string $subject = null,
        mixed $body = null
    ) {
        if ($notificationOrSubscriber instanceof Notification) {
            $this->notificationId = $notificationOrSubscriber->id;
        } elseif ($notificationOrSubscriber instanceof Subscriber) {
            $this->subscriberId = $notificationOrSubscriber->id;
        } elseif (is_int($notificationOrSubscriber)) {
            $this->notificationId = $notificationOrSubscriber;
        } else {
            throw new InvalidArgumentException('Invalid notification payload');
        }

        $this->channel = $channel;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        if ($this->notificationId !== null) {
            return 'notification:' . $this->notificationId;
        }

        return 'subscriber:' . $this->subscriberId . ':' . sha1((string) $this->subject . json_encode($this->body));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->notificationId !== null) {
            $notification = Notification::find($this->notificationId);

            if (!$notification) {
                return;
            }

            // Placeholder delivery: mark as sent.
            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return;
        }

        Log::info('SendNotification: digest delivery placeholder', [
            'subscriber_id' => $this->subscriberId,
            'channel' => $this->channel,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendNotification: Job failed', [
            'notification_id' => $this->notificationId,
            'subscriber_id' => $this->subscriberId,
            'error' => $exception->getMessage(),
        ]);
    }
}
