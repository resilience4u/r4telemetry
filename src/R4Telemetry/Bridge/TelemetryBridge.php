<?php
declare(strict_types=1);

namespace R4Telemetry\Bridge;

use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\StatusCode;
use R4Telemetry\Bootstrap\TelemetryBootstrap;
use R4Telemetry\Utils\EnvFilter;
use Resilience4u\R4Contracts\Contracts\Telemetry;

final class TelemetryBridge implements Telemetry
{
    public static function measure(string $namespace, string $operation, callable $callback): mixed
    {
        if (EnvFilter::shouldSkipExport()) {
            return $callback();
        }

        $tracer = TelemetryBootstrap::tracer($namespace);
        $span = $tracer
            ->spanBuilder($operation)
            ->setAttribute('namespace', $namespace)
            ->setAttribute('component', 'r4telemetry')
            ->startSpan();

        $scope = $span->activate();

        try {
            $result = $callback();

            $span->addEvent('success', ['result_type' => gettype($result)]);
            $span->setStatus(StatusCode::STATUS_OK);
            return $result;
        } catch (\Throwable $e) {
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            $span->addEvent('exception', [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        } finally {
            $span->end();
            $scope->detach();
        }
    }

    public static function recordEvent(string $name, array $attributes = []): void
    {
        if (EnvFilter::shouldSkipExport()) {
            return;
        }

        $span = Span::getCurrent();
        if ($span->isRecording()) {
            $span->addEvent($name, $attributes);
        }
    }

    public function startSpan(string $name, array $attributes = []): mixed
    {
        // TODO: Implement startSpan() method.
    }

    public function endSpan(mixed $span, array $attributes = []): void
    {
        // TODO: Implement endSpan() method.
    }

    public function addMetric(string $name, float|int $value, array $labels = []): void
    {
        // TODO: Implement addMetric() method.
    }

    public function addEvent(string $eventName, array $attributes = []): void
    {
        // TODO: Implement addEvent() method.
    }
}
