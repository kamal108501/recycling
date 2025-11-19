<?php

namespace App\Exceptions;

use Exception;

class ApiValidationException extends Exception
{
    public array $response;

    public function __construct(array $response)
    {
        $this->response = $response;

        parent::__construct($response['responseMessage'] ?? 'Validation failed');
    }
}
