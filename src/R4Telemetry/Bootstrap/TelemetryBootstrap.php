<?php
declare(strict_types=1);

namespace R4Telemetry\Bootstrap;

use OpenTelemetry\API\Common\Time\Clock;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporterFactory;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\ResourceAttributes;

final class TelemetryBootstrap
{
    private static ?TracerProvider $provider = null;
    private static ?TracerInterface $tracer = null;

    public static function init(array $opts = []): void
    {
        $serviceName    = $opts['service_name']    ?? getenv('OTEL_SERVICE_NAME') ?? 'r4telemetry-app';
        $endpoint = $opts['endpoint']
            ?? getenv('OTEL_EXPORTER_OTLP_TRACES_ENDPOINT')
            ?? getenv('OTEL_EXPORTER_OTLP_ENDPOINT')
            ?: null;

        if (!$endpoint || $endpoint === false) {
            $endpoint = file_exists('/.dockerenv')
                ? 'http://otel-collector:4318/v1/traces'
                : 'http://localhost:4318/v1/traces';
        }

        $serviceVersion = $opts['service_version'] ?? '1.0.0';
        $environment    = $opts['environment']     ?? (getenv('APP_ENV') ?: 'local');
        $autoFlush      = $opts['auto_flush']      ?? true;

        $attributes = new Attributes([
            ResourceAttributes::SERVICE_NAME => $serviceName,
            ResourceAttributes::SERVICE_VERSION => $serviceVersion,
            ResourceAttributes::DEPLOYMENT_ENVIRONMENT_NAME => $environment,
        ], 0);

        $resource = ResourceInfo::create($attributes);

        $transport = (new OtlpHttpTransportFactory())->create(
            endpoint: $endpoint,
            contentType: 'application/x-protobuf'
        );

        $exporter = (new SpanExporterFactory())->create($transport);

        $clock = Clock::getDefault();

        $processor = new BatchSpanProcessor(
            exporter: $exporter,
            clock: $clock,
            maxQueueSize: BatchSpanProcessor::DEFAULT_MAX_QUEUE_SIZE,
            scheduledDelayMillis: BatchSpanProcessor::DEFAULT_SCHEDULE_DELAY,
            exportTimeoutMillis: BatchSpanProcessor::DEFAULT_EXPORT_TIMEOUT,
            maxExportBatchSize: BatchSpanProcessor::DEFAULT_MAX_EXPORT_BATCH_SIZE,
            autoFlush: $autoFlush
        );

        self::$provider = new TracerProvider(
            spanProcessors: [$processor],
            resource: $resource
        );

        self::$tracer = self::$provider->getTracer($serviceName);
    }

    public static function provider(): TracerProvider
    {
        if (!self::$provider) {
            throw new \RuntimeException('TelemetryBootstrap::init() ainda não foi chamado.');
        }
        return self::$provider;
    }

    public static function tracer(?string $name = null): TracerInterface
    {
        if (!self::$tracer) {
            throw new \RuntimeException('TelemetryBootstrap::init() ainda não foi chamado.');
        }
        return $name ? self::$provider->getTracer($name) : self::$tracer;
    }

    public static function shutdown(): void
    {
        if (self::$provider) {
            self::$provider->forceFlush();
            self::$provider->shutdown();
        }
        self::$provider = null;
        self::$tracer = null;
    }
}
