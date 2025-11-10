<?php
declare(strict_types=1);

namespace R4Telemetry\Utils;

use Psr\Log\LoggerInterface;

final class EnvFilter
{
    public static function shouldSkipExport(): bool
    {
        $env = getenv('APP_ENV') ?: 'local';
        return in_array($env, ['local', 'testing', 'ci'], true);
    }

    public static function get(): LoggerInterface
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $driver = getenv('R4_LOGGER_DRIVER') ?: 'faceless';

        switch (strtolower($driver)) {
            case 'stdout':
                self::$instance = new \Resilience4u\FacelessLogger\Adapters\StdoutLogger();
                break;
            default:
                self::$instance = new FacelessLogger();
        }

        return self::$instance;
    }

}
