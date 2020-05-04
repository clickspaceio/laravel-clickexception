<?php


namespace Clickspace\LaravelClickException\Exceptions;


use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModelNotFoundValidationException extends ClickException
{

    public $responseBodyType;
    public $responseBodyCode;
    public $responseBodyMessage;
    public $responseBodyAppends;
    public $responseBodyHttpCode;
    public $responseHeaders;
    public $message;

    public function __construct($field_name, ModelNotFoundException $previous = null)
    {
        $this->responseBodyType = 'invalid_request';
        $this->responseBodyCode = 'validation_error';

        $this->responseBodyMessage = 'The object was not found.';
        $this->responseBodyAppends = [
            'fields' => [
                "{$field_name}" => "not_found"
            ]
        ];
        $this->responseBodyHttpCode = "422";
        $this->responseHeaders = [];
        $this->message = 'The object was not found';

//        parent::__construct($this->message, $this->responseBodyHttpCode, $previous);
    }

}