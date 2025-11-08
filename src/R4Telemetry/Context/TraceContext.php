<?php
declare(strict_types=1);

namespace R4Telemetry\Context;

use OpenTelemetry\API\Trace\Span;

final class TraceContext
{
    public static function getTraceId(): ?string
    {
        return Span::getCurrent()->getContext()->getTraceId();
    }

    public static function getSpanId(): ?string
    {
        return Span::getCurrent()->getContext()->getSpanId();
    }

    public static function toArray(): array
    {
        $span = Span::getCurrent()->getContext();
        return [
            'trace_id' => $span->getTraceId(),
            'span_id' => $span->getSpanId(),
        ];
    }

    public static function isActive(): bool
    {
        return Span::getCurrent()->isRecording();
    }
}
