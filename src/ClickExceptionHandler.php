<?php

namespace Clickspace\LaravelClickException;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ClickExceptionHandler extends ExceptionHandler
{
    
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Convert the given exception to an array.
     *
     * @param  \Throwable  $exception
     * @return array
     */
    protected function convertExceptionToArray(Throwable $exception)
    {

        if (!isset($exception->responseRenderHttpCode)) {
            $exception->responseRenderHttpCode = $this->isHttpException($exception) ? $exception->getStatusCode() : '500';
        }

        $exception->responseHeaders = array_merge(
            $exception->responseHeaders ?? [],
            $this->isHttpException($exception) ? $exception->getHeaders() : []
        );

        if (!isset($exception->responseBodyAppends)) {
            $exception->responseBodyAppends = [];
        }

        if ($exception instanceof ModelNotFoundException) {
            $exception->responseBodyType = 'not_found';
            $exception->responseBodyCode = 'resource_not_found';
            $exception->responseBodyMessage = 'The requested resource (' . str_replace('App\Models\\', '', $exception->getModel()) . ') does not exist or has been deleted.';
        } elseif ($exception instanceof NotFoundHttpException or $exception instanceof MethodNotAllowedHttpException) {
            $exception->responseBodyType = 'not_found';
            $exception->responseBodyCode = 'endpoint_not_found';
            $exception->responseBodyMessage = 'The requested endpoint does not exist, please check our documentation.';
        } elseif ($exception instanceof ValidationException) {
            $exception->responseBodyType = 'invalid_request';
            $exception->responseBodyCode = 'validation_error';
            $exception->responseBodyMessage = $exception->getMessage();
            $exception->responseBodyAppends['fields'] = $exception->errors();
        } elseif ($exception instanceof AuthorizationException) {
            $exception->responseBodyType = 'invalid_request';
            $exception->responseBodyCode = 'invalid_credentials';
            $exception->responseBodyMessage = $exception->getMessage();
        } else {
            $exception->responseBodyType = $exception->responseBodyType ?? 'server_error';
            $exception->responseBodyCode = $exception->responseBodyCode ?? 'undefined_server_error';
            $exception->responseBodyMessage = $exception->responseBodyMessage ?? 'There was an internal processing error.';
        }

        $responseRenderBody = [
            'type' => $exception->responseBodyType,
            'code' => $exception->responseBodyCode,
            'message' => $exception->responseBodyMessage
        ];

        foreach ($exception->responseBodyAppends as $appendKey => $appendValue) {
            $responseRenderBody[$appendKey] = $appendValue;
        }

        return array_merge($responseRenderBody, config('app.debug') ? [
            '_debug' => [
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all()
            ]
        ] : []);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (method_exists($exception, 'render') && $response = $exception->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($exception instanceof Responsable) {
            return $exception->toResponse($request);
        }

        $exception = $this->prepareException($exception);

        return $this->prepareJsonResponse($request, $exception);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $exception)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($exception),
            $exception->responseRenderHttpCode,
            $exception->responseHeaders,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}