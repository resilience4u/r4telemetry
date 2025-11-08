<?php
declare(strict_types=1);

namespace R4Telemetry\Utils;

final class EnvFilter
{
    public static function shouldSkipExport(): bool
    {
        $env = getenv('APP_ENV') ?: 'local';
        return in_array($env, ['local', 'testing', 'ci'], true);
    }
}
