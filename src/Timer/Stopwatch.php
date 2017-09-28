<?php
/**
 * @codingStandardsIgnoreStart
 *
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 *
 * @codingStandardsIgnoreEnd
 */

namespace Shrikeh\GuzzleMiddleware\TimerLogger\Timer;

use DateTimeImmutable;
use Litipk\BigNumbers\Decimal;

/**
 * Class Stopwatch.
 */
class Stopwatch implements TimerInterface
{
    /**
     * @var float
     */
    private $start;

    /**
     * @var float
     */
    private $end;

    /**
     * @return self
     */
    public static function startStopWatch()
    {
        $t = \microtime(true);

        return new self((float) $t);
    }

    /**
     * Stopwatch constructor.
     *
     * @param float|null $start The start time
     */
    public function __construct($start = null)
    {
        $this->start = $start;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $t = \microtime(true);
        if (!$this->start) {
            $this->start = (float) $t;
        }

        return $this->dateTime($this->start);
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $t = \microtime(true);
        if (!$this->end) {
            $this->end = (float) $t;
        }

        return $this->dateTime($this->end);
    }

    /**
     * {@inheritdoc}
     */
    public function duration($precision = 0)
    {
        $this->stop();

        $start = $this->decimal($this->start);
        $end = $this->decimal($this->end);

        return Decimal::fromDecimal($end->sub($start)
            ->mul(Decimal::fromInteger(1000)), $precision)->asFloat();
    }

    /**
     * @param float $time The time to format to a DateTimeImmutable
     *
     * @return \DateTimeImmutable
     */
    private function dateTime($time)
    {
        $time = $this->decimal($time);
        $micro = sprintf('%06d', $this->mantissa($time)->asInteger());

        return new DateTimeImmutable(
            \date('Y-m-d H:i:s.'.$micro, $time->asFloat())
        );
    }

    /**
     * @param Decimal $time The time to get the mantissa from
     *
     * @return Decimal
     */
    private function mantissa(Decimal $time)
    {
        $mantissa = ($time->sub($time->floor()));

        return $mantissa->mul(Decimal::fromInteger(1000000));
    }

    /**
     * @param float $t The float to turn into a Decimal
     *
     * @return Decimal
     */
    private function decimal($t)
    {
        return Decimal::fromFloat($t);
    }
}
