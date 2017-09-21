<?php

namespace Shrikeh\GuzzleMiddleware\TimerLogger\Handler;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Shrikeh\GuzzleMiddleware\TimerLogger\ResponseTimeLogger;

/**
 * Class StartHandler
 * @package Shrikeh\GuzzleMiddleware\TimerLogger
 */
class StopHandler
{
    /**
     * @var \Shrikeh\GuzzleMiddleware\TimerLogger\ResponseTimeLogger
     */
    private $responseTimeLogger;

    /**
     * StopHandler constructor.
     *
     * @param \Shrikeh\GuzzleMiddleware\TimerLogger\ResponseTimeLogger $responseTimeLogger
     */
    public function __construct(ResponseTimeLogger $responseTimeLogger)
    {
        $this->responseTimeLogger = $responseTimeLogger;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface   $request
     * @param array                                $options
     * @param \GuzzleHttp\Promise\PromiseInterface $promise
     */
    public function __invoke(
        RequestInterface $request,
        array $options,
        PromiseInterface $promise
    ) {
        $promise->then(
            $this->onSuccess($request),
            $this->onFailure($request)
        );
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Closure
     */
    private function onSuccess(RequestInterface $request)
    {
        return function(ResponseInterface $response) use ($request) {
            $this->responseTimeLogger->stop($request, $response);
        };
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Closure
     */
    private function onFailure(RequestInterface $request)
    {
        return function(ResponseInterface $response) use ($request) {
            $this->responseTimeLogger->stop($request, $response);
        };
    }
}
