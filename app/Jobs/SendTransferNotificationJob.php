<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTransferNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $notificationId)
    {
    }

    public function handle(): void
    {
        $notification = Notification::find($this->notificationId);

        if(!$notification || $notification->status !== 'pending'){
            return;
        }

        try {
            $response = Http::timeout(5)->post('https://util.devi.tools/api/v1/notify', [
                'user_id' => $notification->receiver_id,
                'transfer_id' => $notification->transfer_id,
                'mensagem' => 'Você recebeu uma transferência!',    

            ]);

            if($response->ok()){
                $notification->update([
                    'status' => 'send',
                    'attempts' => $notification->attempts + 1,
                ]);

                Log::info('[Notify] envio OK', $response->json());
            } else {
                throw new \Exception('Ocorreu um erro');
            }

        } catch(\Throwable $e) {
            $notification->update([
                'status' => 'failed',
                'attempts' => $notification->attempts + 1,
                'last_error' => $e->getMessage(),
            ]);

            Log::info('[Notify]  Falha envio', ['error' => $e->getMessage()]);
            $this->release(60);
        }


    }
}
