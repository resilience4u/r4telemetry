<?php

require __DIR__ . '/../vendor/autoload.php';

use R4Telemetry\Bootstrap\TelemetryBootstrap;
use R4Telemetry\Integration\LoggerProvider;

putenv('R4_LOGGER_DRIVER=faceless');

TelemetryBootstrap::init([
    'service_name' => 'r4telemetry-sensitive-demo',
]);

$logger = LoggerProvider::get();

$data = [
    'user_name' => 'Diego Ananias',
    'user_email' => 'diego.ananias@example.com',
    'cpf' => '123.456.789-10',
    'credit_card' => '4111 1111 1111 1111',
    'ip_address' => '192.168.1.100',
    'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9',
    'password' => 'superSecreta123',
];

$logger->info('User data received', $data);

TelemetryBootstrap::shutdown();
