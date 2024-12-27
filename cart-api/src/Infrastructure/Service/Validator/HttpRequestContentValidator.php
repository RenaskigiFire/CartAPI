<?php

namespace App\Infrastructure\Service\Validator;

class HttpRequestContentValidator
{
    public function validate(array $request, array $keysToValidate): bool
    {
        foreach ($keysToValidate as $key) {
            if (!array_key_exists($key, $request)) {
                return false;
            }
        }

        return true;
    }
}
