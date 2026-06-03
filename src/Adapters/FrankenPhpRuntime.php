<?php

namespace Yuga\Runtime\Adapters;

use Throwable;
use Yuga\Runtime\Kernel;
use Yuga\Runtime\RuntimeAdapter;
use Yuga\Runtime\Support\Psr7Emitter;
use Yuga\Runtime\Support\ServerRequestFactory;

final class FrankenPhpRuntime implements RuntimeAdapter
{
    public function __construct(
        private readonly ServerRequestFactory $requestFactory = new ServerRequestFactory(),
        private readonly Psr7Emitter $emitter = new Psr7Emitter()
    ) {
    }

    public function run(Kernel $kernel): void
    {
        if (!function_exists('frankenphp_handle_request')) {
            (new FpmRuntime($this->requestFactory, $this->emitter))->run($kernel);
            return;
        }

        while (frankenphp_handle_request(function () use ($kernel) {
            $request = $this->requestFactory->fromGlobals();

            try {
                $response = $kernel->handle($request);

                $this->emitter->emit($response);

                $kernel->terminate($request, $response);
            } catch (Throwable $e) {
                throw $e;
            }
        })) {
            gc_collect_cycles();
        }
    }
}