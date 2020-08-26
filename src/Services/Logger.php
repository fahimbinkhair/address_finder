<?php
declare(strict_types=1);
/**
 * Description:
 * handles application logs
 * not using monolog because it has got memory leaking issue in the batch processing
 *
 * @package App\Services
 */

namespace App\Services;

/**
 * Class Logger
 *
 * @package App\Services
 */
class Logger
{
    /**
     * @param int $priority
     * @param string $message
     */
    public static function log(int $priority, string $message): void
    {
        openlog('address_finder', LOG_CONS | LOG_NDELAY | LOG_PERROR, LOG_LOCAL0);
        syslog($priority, $message);
        closelog();
    }
}
