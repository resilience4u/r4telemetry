<?php

require __DIR__ . '/../vendor/autoload.php';

use R4Telemetry\Bootstrap\TelemetryBootstrap;
use R4Telemetry\Integration\LoggerProvider;

putenv('R4_LOGGER_DRIVER=faceless');

TelemetryBootstrap::init([
    'service_name' => 'r4telemetry-demo',
]);

$logger = LoggerProvider::get();

$logger->debug('Debug message from R4Telemetry');
$logger->info('Info message with {context}', ['context' => 'sample']);
$logger->warning('Something might be wrong');
$logger->error('Something went wrong!', ['error_code' => 500]);

TelemetryBootstrap::shutdown();
