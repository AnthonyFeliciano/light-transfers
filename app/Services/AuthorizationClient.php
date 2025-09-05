<?php 

namespace App\Services;

use App\Services\Contracts\AuthorizationClientContract;
use Illuminate\Support\Facades\Http;

class AuthorizationClient implements AuthorizationClientContract
{ 

    public function authorize(): bool
    {

        $response = Http::timeout(6)->retry(3, 200)->get('https://util.devi.tools/api/v2/authorize');

        if(!$response->ok()) {
            return false;
        }

        $json = $response->json();

        return isset(['data']['authorization']) ? (bool) $json['data']['authorization'] : false;

    }
    
}