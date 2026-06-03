<?php

namespace Yuga\Runtime\Adapters;

use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Throwable;
use Yuga\Runtime\Kernel;
use Yuga\Runtime\RuntimeAdapter;
use Yuga\Runtime\RuntimeException;

final class RoadRunnerRuntime implements RuntimeAdapter
{
    public function run(Kernel $kernel): void
    {   
        if (!class_exists(PSR7Worker::class) || !class_exists(Worker::class)) {
            throw new RuntimeException(
                'RoadRunner runtime requires spiral/roadrunner-http and spiral/roadrunner-worker.'
            );
        }

        $worker7 = Worker::create();

        // Create common PSR-17 HTTP factory
        $factory = new Psr17Factory();

        $psr7 = new PSR7Worker(
            $worker7,
            $factory,
            $factory,
            $factory,
        );

        while ($request = $psr7->waitRequest()) {
            try {
                $response = $kernel->handle($request);

                $psr7->respond($response);

                $kernel->terminate($request, $response);
            } catch (Throwable $e) {
                $psr7->getWorker()->error((string) $e);
            } finally {
                gc_collect_cycles();
            }
        }
    }
}