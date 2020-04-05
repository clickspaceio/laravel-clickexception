<?php

namespace Clickspace\LaravelClickException\Exceptions;

use Clickspace\LaravelClickException\ClickException;

class InvalidParameterException extends ClickException
{

    public $responseBodyType = "invalid_request";
    public $responseBodyCode = "invalid_parameter";
    public $responseBodyMessage = "The given parameter was invalid.";
    public $responseBodyHttpCode = "400";
    public $message = "The given parameter was invalid.";

    public function __construct($fields)
    {
        $this->responseBodyAppends = [
            'fields' => $fields
        ];
    }

}