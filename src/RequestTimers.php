<?php

namespace Shrikeh\GuzzleMiddleware\TimerLogger;

use Ds\Map;
use Psr\Http\Message\RequestInterface;
use SplObjectStorage;

/**
 * Class TimerHandler
 */
class RequestTimers
{
    /**
     * @var \SplObjectStorage
     */
    private $requestTimers;

    /**
     * TimerHandler constructor.
     */
    public function __construct()
    {
        $this->requestTimers = new SplObjectStorage();
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function start(RequestInterface $request)
    {
        if (!$this->requestTimers->contains($request)) {
            $this->requestTimers->attach($request, new Timer($request));
        }
        $timer = $this->timerFor($request);
        $timer->start();

        return $timer;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function stop(RequestInterface $request)
    {
        $timer = $this->timerFor($request);
        $timer->stop();

        return $timer;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return float
     */
    public function duration(RequestInterface $request)
    {
        return $this->timerFor($request)->duration();
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Shrikeh\GuzzleMiddleware\TimerLogger\Timer
     */
    public function timerFor(RequestInterface $request)
    {
        return $this->requestTimers->offsetGet($request);
    }
}
