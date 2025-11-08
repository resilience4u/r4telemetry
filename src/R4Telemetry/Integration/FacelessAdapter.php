<?php

namespace R4Telemetry\Integration;

use R4Telemetry\Context\TraceContext;

final class FacelessAdapter
{
    public static function attachIfAvailable(object $logger): void
    {
        if (!class_exists('FacelessLogger\FacelessLogger')) {
            return;
        }

        $context = TraceContext::toArray();
        if (method_exists($logger, 'addGlobalContext')) {
            $logger->addGlobalContext($context);
        }
    }
}
