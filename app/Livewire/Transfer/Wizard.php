<?php

namespace App\Livewire\Transfer;

use App\Models\User;
use App\Services\Contracts\AuthorizationClientContract;
use App\Services\Contracts\TransferServiceContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Wizard extends Component
{
    public int $step = 1;

    public string $identifier = '';
    public ?string $payeeId = null;

    public string $amount = '';
    public string $available = '0.00';

    public string $authMsg = '';
    public string $key = '';

    /** Fallback para testes/telemetria de UI */
    public ?array $lastToast = null;

    protected array $messages = [
        'identifier.required' => 'Informe o :attribute.',
        'identifier.string'   => 'O :attribute deve ser um texto.',
        'identifier.min'      => 'Digite pelo menos :min caracteres para localizar o destinatário.',
        'amount.required'     => 'Informe o :attribute.',
        'amount.numeric'      => 'Digite um :attribute válido (pode usar vírgula ou ponto).',
        'amount.min'          => 'O :attribute mínimo é :min.',
    ];

    protected array $validationAttributes = [
        'identifier' => 'destinatário (e-mail, CPF ou CNPJ)',
        'amount'     => 'valor',
    ];

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    private function fmtMoney(string|float $v): string
    {
        $n = is_numeric($v)
            ? (float) $v
            : (float) str_replace(',', '.', preg_replace('/[^\d,\.]/', '', (string) $v));

        return 'R$ ' . number_format($n, 2, ',', '.');
    }

    private function toast(string $type, string $msg): void
    {
        $this->lastToast = ['type' => $type, 'msg' => $msg];
        $this->dispatch('toast', type: $type, msg: $msg); // browser event (quando houver listener no front)
    }

    public function mount(): void
    {
        $bal = (float) (Auth::user()->wallet?->balance ?? 0);
        $this->available = number_format($bal, 2, '.', '');
        $this->key = (string) Str::uuid();
    }

    public function getPayeeProperty(): ?User
    {
        return $this->payeeId ? User::find($this->payeeId) : null;
    }

    public function findPayee(): void
    {
        $this->validate(['identifier' => ['required','string','min:3']]);

        $q = User::query()->where('id','!=', Auth::id());
        $clean = preg_replace('/\D/', '', $this->identifier ?? '');

        if (strlen($clean) >= 11) {
            $q->where('document', $clean);
        } else {
            $q->where('email', $this->identifier);
        }

        $payee = $q->first();

        if (!$payee) {
            $typed = trim($this->identifier);
            $hint  = strlen($clean) >= 11
                ? 'Confira o CPF/CNPJ informado.'
                : 'Confira se o e-mail está correto.';
            $this->fail('identifier', "Não encontrei nenhum usuário para “{$typed}”. {$hint}");
        }

        $this->payeeId = $payee->id;
        $this->step = 2;
    }

    public function validateAmount(): void
    {
        $this->validate(['amount' => ['required','numeric','min:0.01']]);

        if (bccomp((string)$this->available, (string)$this->amount, 2) < 0) {
            $this->fail(
                'amount',
                'Saldo insuficiente. Você tem ' . $this->fmtMoney($this->available) .
                '. Tente um valor igual ou menor que esse limite.'
            );
        }

        $this->step = 3;
    }

    public function confirm(TransferServiceContract $service, AuthorizationClientContract $auth): void
    {
        if (!$this->payeeId) {
            $this->fail('identifier', 'Selecione um destinatário antes de confirmar.');
        }

        $this->authMsg = '';
        $authorized = $auth->authorize();

        if (!$authorized) {
            $this->authMsg = 'Não conseguimos autorização para esta operação agora. Tente novamente em instantes.';
            $this->toast('error', $this->authMsg);
            return;
        }

        try {
            $transfer = $service->execute(
                payerId: (string) Auth::id(),
                payeeId: (string) $this->payeeId,
                amount: (string) $this->amount,
                key: $this->key
            );

            $to  = $this->payee?->name ?: 'destinatário';
            $val = $this->fmtMoney($this->amount);
            $this->toast('success', "Transferência de {$val} para {$to} concluída!");

            $this->reset('identifier','payeeId','amount','authMsg');

            $bal = (float) (User::find(Auth::id())?->wallet?->balance ?? 0);
            $this->available = number_format($bal, 2, '.', '');

            $this->key  = (string) Str::uuid();
            $this->step = 1;

            $this->dispatch('wallet-updated');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $msg = (string) (collect($e->errors())->flatten()->first() ?: 'Não foi possível concluir a transferência.');
            $this->toast('error', $msg);
            throw $e;

        } catch (\Throwable $e) {
            report($e);
            $this->toast('error', 'Ocorreu um erro inesperado. Tente novamente em alguns segundos.');
        }
    }

    public function render()
    {
        return view('livewire.transfer.wizard');
    }
}
