<?php

namespace App\Services;

use App\Services\Contracts\AuthorizationClientContract;
use Illuminate\Support\Facades\Http;

class AuthorizationClient implements AuthorizationClientContract
{
    public function authorize(): bool
    {
        try {
            $resp = Http::acceptJson()
                ->timeout(6)
                ->retry(2, 200, throw: false)
                ->get('https://util.devi.tools/api/v2/authorize');

            $status = $resp->status();

            if ($status === 200) {
                $ok = (bool) data_get($resp->json(), 'data.authorization', false);
                return $ok;
            }

            if ($status === 403) {
                return false;
            }
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
