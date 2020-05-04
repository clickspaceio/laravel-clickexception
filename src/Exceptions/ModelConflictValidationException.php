<?php


namespace Clickspace\LaravelClickException\Exceptions;


use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModelConflictValidationException extends ClickException
{

    public $responseBodyType;
    public $responseBodyCode;
    public $responseBodyMessage;
    public $responseBodyAppends;
    public $responseBodyHttpCode;
    public $responseHeaders;
    public $message;

    public function __construct($obj, ModelNotFoundException $previous = null)
    {
        $this->responseBodyType = 'invalid_request';
        $this->responseBodyCode = 'conflict';

        $this->responseBodyMessage = 'The object informed is already registered.';
        $this->responseBodyAppends = [
            'object' => $obj
        ];
        $this->responseBodyHttpCode = "409";
        $this->responseHeaders = [];
        $this->message = 'The object informed is already registered.';

//        parent::__construct($this->message, $this->responseBodyHttpCode, $previous);
    }

}