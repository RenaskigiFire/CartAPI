<?php

namespace App\Application\UseCase;

abstract class AbstractUseCaseResponse
{
    const INVALID_RESPONSE_CODE = 500;
    const VALID_RESPONSE_CODE   = 200;

    protected $responseCode;
    protected $responseMessage;

    public function __construct(int $responseCode, string $responseMessage = '')
    {
        $this->responseCode    = $responseCode;
        $this->responseMessage = $responseMessage;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    abstract public function isValid(): bool;
}
