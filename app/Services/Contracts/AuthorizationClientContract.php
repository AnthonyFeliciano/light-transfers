<?php

namespace app\Services\Contracts;

interface AuthorizationClientContract 
{
    public function authorize(): bool;
}