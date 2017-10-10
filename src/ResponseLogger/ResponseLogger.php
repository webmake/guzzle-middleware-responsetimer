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

namespace Shrikeh\GuzzleMiddleware\TimerLogger\ResponseLogger;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Shrikeh\GuzzleMiddleware\TimerLogger\Formatter\FormatterInterface;
use Shrikeh\GuzzleMiddleware\TimerLogger\ResponseLogger\Exception\ResponseLogStartException;
use Shrikeh\GuzzleMiddleware\TimerLogger\ResponseLogger\Exception\ResponseLogStopException;
use Shrikeh\GuzzleMiddleware\TimerLogger\Timer\TimerInterface;

/**
 * Class ResponseLogger.
 */
final class ResponseLogger implements ResponseLoggerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * ResponseLogger constructor.
     *
     * @param LoggerInterface    $logger    The PSR-3 logger
     * @param FormatterInterface $formatter A formatter
     */
    public function __construct(
        LoggerInterface $logger,
        FormatterInterface $formatter
    ) {
        $this->logger = $logger;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function logStart(TimerInterface $timer, RequestInterface $request)
    {
        try {
            $this->writeStart($timer, $request);

            return $this;
        } catch (Exception $e) {
            throw new ResponseLogStartException(
                ResponseLogStartException::START_MSG,
                ResponseLogStartException::START_CODE,
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function logStop(
        TimerInterface $timer,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        try {
            $this->writeStop($timer, $request, $response);

            return $this;
        } catch (Exception $e) {
            throw new ResponseLogStopException(
                ResponseLogStopException::STOP_MSG,
                ResponseLogStopException::STOP_CODE,
                $e
            );
        }
    }

    /**
     * @param TimerInterface                     $timer   The timer to log
     * @param \Psr\Http\Message\RequestInterface $request The Request to log
     */
    private function writeStart(TimerInterface $timer, RequestInterface $request)
    {
        $this->logger->log(
            $this->formatter->levelStart($timer, $request),
            $this->formatter->start($timer, $request)
        );
    }

    /**
     * @param TimerInterface                      $timer    The timer to log
     * @param \Psr\Http\Message\RequestInterface  $request  The Request to log against
     * @param \Psr\Http\Message\ResponseInterface $response The Response to log
     */
    private function writeStop(
        TimerInterface $timer,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->logger->log(
            $this->formatter->levelStop($timer, $request, $response),
            $this->formatter->stop($timer, $request, $response)
        );
    }
}
