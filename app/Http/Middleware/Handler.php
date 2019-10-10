<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class Handler
{
    /**
     * The exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $handler;

    /**
     * Create a new handler middleware instance.
     *
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $handler
     * @return void
     */
    public function __construct(ExceptionHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $response = $next($request);
        } catch (Exception $e) {
            $this->handler->report($e);

            $response = $this->handler->render($request, $e);
        } catch (Throwable $e) {
            $this->handler->report($e = new FatalThrowableError($e));

            $response = $this->handler->render($request, $e);
        }

        app()->make('events')->dispatch(
            new RequestHandled($request, $response)
        );

        return $response;
    }
}
