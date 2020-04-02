# Laravel ClickException




## Instalação

1. O pacote deve ser instalado pelo [Composer](https://getcomposer.org/). Para instalar, basta executar o comando abaixo.

    ```sh
    composer require clickspaceio/laravel-clickexception
    ```
    
2. Extender a classe `App\Exceptions\Handler` a `ClickExceptionHandler`, do pacote que acabou de ser instalado.

    ```
    use Clickspace\LaravelClickException\ClickExceptionHandlerkExceptionHandler as ExceptionHandler;
    
    class Handler extends ExceptionHandler
    ```