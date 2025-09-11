<?php

namespace Tests\Feature\Notifications;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

use App\Jobs\SendTransferNotificationJob;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Notification;

class NotificationJobFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_marca_failed_incrementa_attempts_e_registra_last_error_quando_post_falha(): void
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
            'https://util.devi.tools/api/v1/notify' => Http::response(['ok' => false], 500),
        ]);

        rescue(fn () => (new SendTransferNotificationJob($n->id))->handle(), report: false);

        // Assert
        $n->refresh();
        $this->assertSame('failed', strtolower($n->status));
        $this->assertSame(1, $n->attempts);
        $this->assertNotNull($n->last_error);
    }
}
