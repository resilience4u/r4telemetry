<?php

require __DIR__ . '/../vendor/autoload.php';

use R4Telemetry\Facade as Telemetry;

Telemetry::bootstrap([
    'service_name' => 'r4telemetry-demo',
    'endpoint' => 'http://otel-collector:4318/v1/traces',
]);

try {
    Telemetry::measure('example', 'failingWork', function () {
        usleep(50_000);
        throw new RuntimeException('falhou de propósito');
    });
} catch (Throwable $e) {
    echo 'Erro capturado: ' . $e->getMessage() . PHP_EOL;
} finally {
    Telemetry::shutdown();
}
