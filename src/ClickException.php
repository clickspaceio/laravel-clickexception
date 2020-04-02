<?php

namespace Clickspace\LaravelClickException;

use Exception;
use Throwable;

class ClickException extends Exception
{

    public $responseBodyType;
    public $responseBodyCode;
    public $responseBodyMessage;
    public $responseBodyAppends;
    public $responseBodyHttpCode;
    public $responseHeaders;
    public $message;

    public function __construct(string $responseBodyType, string $responseBodyCode, string $responseBodyMessage = "", array $responseBodyAppends = [], string $responseBodyHttpCode = "500", array $responseHeaders = [], string $message = "", Throwable $previous = null)
    {
        $this->responseBodyType = $responseBodyType;
        $this->responseBodyCode = $responseBodyCode;
        $this->responseBodyMessage = $responseBodyMessage;
        $this->responseBodyAppends = $responseBodyAppends;
        $this->responseBodyHttpCode = $responseBodyHttpCode;
        $this->responseHeaders = $responseHeaders;
        $this->message = $message;

        parent::__construct($message, $responseBodyHttpCode, $previous);
    }

}