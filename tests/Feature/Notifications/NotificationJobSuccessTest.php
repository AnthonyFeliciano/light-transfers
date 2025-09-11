<?php

namespace Tests\Feature\Notifications;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

use App\Jobs\SendTransferNotificationJob;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Notification;

class NotificationJobSuccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_marca_notification_como_send_e_incrementa_attempts_quando_post_200(): void
    {
        $user     = User::factory()->create();
        $transfer = Transfer::factory()->create();

        $n = Notification::factory()->create([
            'receiver_id' => $user->id,
            'transfer_id' => $transfer->id,
            'status'      => 'pending',
            'attempts'    => 0,
        ]);

        Http::fake([
            'https://util.devi.tools/api/v1/notify' => Http::response(['ok' => true], 200),
        ]);

        (new SendTransferNotificationJob($n->id))->handle();

        $n->refresh();
        $this->assertSame('send', strtolower($n->status));
        $this->assertSame(1, $n->attempts);
        $this->assertNull($n->last_error);
    }
}
