<?php

namespace App\Services;

use App\Models\{LedgerEntry, Notification, Transfer, User, Wallet};
use App\Services\Contracts\{AuthorizationClientContract, TransferServiceContract};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class TransferService implements TransferServiceContract
{
    public function __construct(private AuthorizationClientContract $auth) {}

    private function validationException(string $msg= 'error', string $status = 'error',): never
    {
        throw ValidationException::withMessages(['status' => $status, 'message' => $msg]);
    }

    public function execute(string $payerId, string $payeeId, string $amount, string $key): Transfer
    {

        if ($payerId === $payeeId) {
            $this->validationException('Não é permitido transferir para si mesmo.');
        }

        // key: se já existir, devolve o registro existente
        if ($existing = Transfer::where('key', $key)->first()) {
            return $existing;
        }

        $payer = User::findOrFail($payerId);
        $payee = User::findOrFail($payeeId);

        if ($payer->role !== 'user') {
            $this->validationException('Lojista não pode enviar transferências.');
        }

        // Autorização externa (seu mock já retorna true/false)
        if (!$this->auth->authorize()) {
            $this->validationException('Operação não autorizada pelo serviço externo.');
        }

        return DB::transaction(function () use ($payer, $payee, $amount, $key) {

            // trava as linhas de carteira do pagador e recebedor
            $payerW = Wallet::where('user_id', $payer->id)->lockForUpdate()->firstOrFail();
            $payeeW = Wallet::where('user_id', $payee->id)->lockForUpdate()->firstOrFail();

            // Saldo suficiente
            if (bccomp((string) $payerW->balance, (string) $amount, 2) < 0) {
                $this->validationException('Saldo insuficiente.');
            }

            // Cria a transferência em estado "authorized"
            $transfer = Transfer::create([
                'payer_id'  => $payer->id,
                'payee_id'  => $payee->id,
                'amount' => (string) $amount,
                'status' => 'authorized',
                'key'    => $key,
            ]);

            // Debita e Credita
            $payerW->balance = bcsub((string) $payerW->balance, (string) $amount, 2);
            $payeeW->balance = bcadd((string) $payeeW->balance, (string) $amount, 2);

            $payerW->save();
            $payeeW->save();

            // Lançamentos no razão (debit pagador / credit recebedor)
            LedgerEntry::create([
                'transfer_id' => $transfer->id,
                'wallet_id'   => $payerW->id,
                'type'        => 'debit',
                'amount'      => (string) $amount,
            ]);

            LedgerEntry::create([
                'transfer_id' => $transfer->id,
                'wallet_id'   => $payeeW->id,
                'type'        => 'credit',
                'amount'      => (string) $amount,
            ]);

            // Conclui a transferência
            $transfer->update(['status' => 'completed']);

            // Notificação para o recebedor (fila opcional)
            $notification = Notification::create([
                'transfer_id' => $transfer->id,
                'receiver_id' => $payee->id,
                'status'      => 'pending',
            ]);
            
            dispatch(new \App\Jobs\SendTransferNotificationJob($notification->id))->afterCommit();

            return $transfer;
        });
    }
}
