<?php

namespace spec\Shrikeh\GuzzleMiddleware\TimerLogger\Timer;

use DateTimeImmutable;
use Litipk\BigNumbers\Decimal;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;

class StopwatchSpec extends ObjectBehavior
{
    public function getMatchers()
    {
        return [
            'beAValidDuration' => function($number) {
                return is_float($number) && $number > 0;
            }
        ];
    }

    function let(RequestInterface $request)
    {
        $this->beConstructedWith($request);
    }

    function it_has_a_start_time()
    {
        $this->start()->shouldBeAnInstanceOf(DateTimeImmutable::class);
    }

    function it_has_an_end_time()
    {
        $this->stop()->shouldBeAnInstanceOf(DateTimeImmutable::class);
    }


    function it_returns_the_duration()
    {
        $this->start();
        usleep(1200);
        $this->duration()->shouldBeAValidDuration();
    }
}