<?php declare(strict_types=1);

namespace R4Telemetry\Bridge;

use Resilience4u\R4Contracts\Contracts\Telemetry;

final class R4ContractsTelemetryAdapter implements Telemetry
{
    public function startSpan(string $name, array $attributes = []): mixed
    {
        return [
            'name' => $name,
            'attributes' => $attributes,
            'start' => microtime(true),
        ];
    }

    public function endSpan(mixed $span, array $attributes = []): void
    {
        $elapsed = (microtime(true) - ($span['start'] ?? microtime(true))) * 1000;
        TelemetryBridge::recordEvent('span.end', [
            'span' => $span['name'] ?? 'unknown',
            'duration_ms' => $elapsed,
            ...$attributes,
        ]);
    }

    public function addMetric(string $name, float|int $value, array $labels = []): void
    {
        TelemetryBridge::recordEvent("metric:{$name}", [
            'value' => $value,
            'labels' => $labels,
        ]);
    }

    public function addEvent(string $eventName, array $attributes = []): void
    {
        TelemetryBridge::recordEvent($eventName, $attributes);
    }
}
