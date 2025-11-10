<?php

declare(strict_types=1);

namespace R4Telemetry\Integration;

use Psr\Log\AbstractLogger;
use R4Telemetry\Context\TraceContext;

final class FacelessJsonLogger extends AbstractLogger
{
    public function log($level, $message, array $context = []): void
    {
        $payload = [
            'timestamp' => date('c'),
            'level'     => strtoupper((string)$level),
            'message'   => $this->interpolate((string)$message, $context),
            'trace'     => TraceContext::toArray(),
            'context'   => $context,
        ];

        fwrite(STDOUT, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }

    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $k => $v) {
            if (is_scalar($v)) {
                $replace['{' . $k . '}'] = (string)$v;
            }
        }
        return strtr($message, $replace);
    }
}
