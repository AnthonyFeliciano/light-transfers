<?php

namespace app\Services\Contracts;

use App\Models\Transfer;

interface TransferServiceContract
{
    public function execute(string $payerId, string $payeeId, string $amount, string $key): Transfer;
}