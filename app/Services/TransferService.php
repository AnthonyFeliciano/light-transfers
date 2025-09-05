<?php 

namespace App\Services;

use App\Models\{LedgerEntry, Notification, Transfer, User, Wallet};
use app\Services\Contracts\{AuthorizationClientContract, TransferServiceContract};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferService implements TransferServiceContract
{
    public function __construct(private AuthorizationClientContract $auth){}

    public function execute(string $payerId, string $payeeId, string $amount, string $key): Transfer
    {

        if($payerId === $payeeId){
            throw ValidationException::withMessages(['status' => 'error', 'message' => 'Não é permitido transferir para si mesmo.']);
        }

        if($existing = Transfer::where('key',$key)->first()){
            return $existing;
        }

        $payer = User::FindOrFail($payerId);   
        $payee = User::FindOrFail($payeeId);

        if($payer->role !== 'user'){
            throw ValidationException::withMessages(['status' => 'error', 'message' => 'Lojista não pode enviar transferências.']);
        }

        if(!$this->auth->authorize()){
            throw ValidationException::withMessages(['status' => 'error', 'message' => 'Operação não autorizada pelo serviço externo.']);
        }


        return DB::transaction(function () use ($payer, $payee, $amount, $key){
            $payerW = Wallet::FindOrFail('user_id', $payer->id)->lockForUpdate()->firstOrFail();
            $payeeW = Wallet::FindOrFail('user_id', $payee->id)->lockForUpdate()->findOrFail();

            if(bccomp((string)$payerW->balance, (string)$amount, 2) < 0){
                throw ValidationException::withMessages(['status' => 'error', 'message' => 'Saldo insuficiente.']);
            }

            $transfer = Transfer::create([
                'payer' => $payer->id,
                'payee' => $payee->id,
                'amount' => $amount,
                'status' => 'authorized',
                'key' => $key,
            ]);

            $payerW->balance = bcsub((string)$payerW->balance, (string)$amount, 2);
            $payeeW->balance = bcadd((string)$payeeW->balance, (string)$amount, 2);
            $payer->save();
            $payee->save();

            LedgerEntry::create([
                'transfer_id' => $transfer->id, 'wallet_id' => $payerW->id, 'type' => 'debit', 'amount' => $amount,
            ]);

            LedgerEntry::create([
                'transfer_id' => $transfer->id, 'wallet_id' => $payerW->id, 'type' => 'credit', 'amount' => $amount,
            ]);

            $transfer::update(['status' => 'completed']);


            $notification = Notification::create([
                'transfer_id' => $transfer->id,
                'receiver_id' => $payee->id,
                'status' => 'pending',
            ]);

            //dispatch(new \App\Jobs\SendTransferNotificationJob($notification->id))->afterCommit();

            return $transfer; 

        });
    }
}