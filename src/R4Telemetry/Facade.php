<?php
declare(strict_types=1);

namespace R4Telemetry;

use OpenTelemetry\API\Trace\TracerInterface;
use R4Telemetry\Bootstrap\TelemetryBootstrap;
use R4Telemetry\Tracing\Tracer;

final class Facade
{
    public static function bootstrap(array $opts = []): void
    {
        TelemetryBootstrap::init($opts);
    }

    public static function measure(string $component, string $spanName, callable $fn, array $attributes = []): mixed
    {
        $tracer = Tracer::get($component);
        $span   = $tracer->spanBuilder($spanName)->startSpan();

        foreach ($attributes as $k => $v) {
            $span->setAttribute((string)$k, (string)$v);
        }

        try {
            $result = $fn();
            $span->setAttribute('status', 'ok');
            return $result;
        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setAttribute('status', 'error');
            throw $e;
        } finally {
            $span->end();
        }
    }

    public static function tracer(string $name = 'r4telemetry'): TracerInterface
    {
        return Tracer::get($name);
    }

    public static function shutdown(): void
    {
        TelemetryBootstrap::shutdown();
    }
}
