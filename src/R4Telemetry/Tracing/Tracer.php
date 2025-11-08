<?php
declare(strict_types=1);

namespace R4Telemetry\Tracing;

use R4Telemetry\Bootstrap\TelemetryBootstrap;
use OpenTelemetry\API\Trace\TracerInterface;

final class Tracer
{
    public static function get(string $name = 'r4telemetry'): TracerInterface
    {
        return TelemetryBootstrap::tracer($name);
    }
}
