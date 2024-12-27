<?php

namespace App\Tests\Infrastructure\Service\Validator;

use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use PHPUnit\Framework\TestCase;

class HttpRequestContentValidatorTest extends TestCase
{

    private HttpRequestContentValidator $classUnderTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classUnderTest = new HttpRequestContentValidator();
    }

    public function testValidateReturnsTrueWhenAllKeysExist(): void
    {
        $request = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $keysToValidate = ['key1', 'key2'];

        $result = $this->classUnderTest->validate($request, $keysToValidate);

        $this->assertTrue($result, 'Expected validate to return true when all keys exist in the request.');
    }

    public function testValidateReturnsFalseWhenKeysAreMissing(): void
    {
        $request = [
            'key1' => 'value1',
            'key3' => 'value3',
        ];

        $keysToValidate = ['key1', 'key2'];

        $result = $this->classUnderTest->validate($request, $keysToValidate);

        $this->assertFalse($result, 'Expected validate to return false when some keys are missing in the request.');
    }

    public function testValidateReturnsTrueWithEmptyKeysToValidate(): void
    {
        $request = [
            'key1' => 'value1',
        ];

        $keysToValidate = [];

        $result = $this->classUnderTest->validate($request, $keysToValidate);

        $this->assertTrue($result, 'Expected validate to return true when keysToValidate is empty.');
    }

    public function testValidateReturnsFalseWithEmptyRequestAndNonEmptyKeysToValidate(): void
    {
        $request = [];
        $keysToValidate = ['key1'];

        $result = $this->classUnderTest->validate($request, $keysToValidate);

        $this->assertFalse($result, 'Expected validate to return false when request is empty and keysToValidate is not empty.');
    }
}
