<?php

declare(strict_types=1);

namespace R4Telemetry\Integration;

use FacelessLogger\Anonymization\AnonymizationProcessor;
use FacelessLogger\Anonymization\AutoDetect\DefaultAutoDetectionRegistry;
use FacelessLogger\FacelessLogger;
use Psr\Log\LoggerInterface;

final class LoggerProvider
{
    private static ?LoggerInterface $instance = null;

    public static function set(LoggerInterface $logger): void
    {
        self::$instance = $logger;
    }

    public static function get(): LoggerInterface
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $driver = strtolower((string)(getenv('R4_LOGGER_DRIVER') ?: 'faceless'));

        switch ($driver) {
            case 'faceless':
            default:
                if (class_exists(FacelessLogger::class)) {
                    $logger = FacelessLogger::create('r4telemetry');

                    if (class_exists(DefaultAutoDetectionRegistry::class)) {
                        $processor = new AnonymizationProcessor(
                            autoDetectionRegistry: new DefaultAutoDetectionRegistry()
                        );
                        $logger->withProcessor($processor);
                    }

                    if (method_exists($logger, 'withTelemetry')) {
                        $logger->withTelemetry();
                    }

                    self::$instance = $logger;
                } else {
                    self::$instance = new FacelessJsonLogger();
                }
                break;

            case 'stdout':
                self::$instance = new FacelessJsonLogger();
                break;
        }

        return self::$instance;
    }
}
