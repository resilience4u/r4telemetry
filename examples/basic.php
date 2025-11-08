<?php

require __DIR__ . '/../vendor/autoload.php';

use R4Telemetry\Facade;

Facade::bootstrap([
    'service_name' => getenv('OTEL_SERVICE_NAME') ?: 'r4telemetry-demo',
]);

$result = Facade::measure('demo', 'heavyWork', fn () => usleep(100_000) ?: 42);

echo "Resultado: {$result}\n";

Facade::shutdown();
