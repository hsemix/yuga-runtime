<?php

declare(strict_types=1);

namespace Yuga\Runtime\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

final class RoadRunnerServer
{
    public function run(
        string $binary,
        string $configuration,
        string $workingDirectory
    ): int {
        if (!is_file($binary)) {
            throw new RuntimeException(
                "RoadRunner executable was not found: {$binary}"
            );
        }

        if (
            PHP_OS_FAMILY !== 'Windows'
            && !is_executable($binary)
        ) {
            throw new RuntimeException(
                'The RoadRunner file is not executable.'
            );
        }

        if (!is_file($configuration) || !is_readable($configuration)) {
            throw new RuntimeException(
                "RoadRunner configuration is not readable: {$configuration}"
            );
        }

        $process = new Process(
            [$binary, 'serve', '-c', $configuration],
            $workingDirectory
        );

        // A server must not receive Process's default execution timeout.
        $process->setTimeout(null);

        if (
            PHP_OS_FAMILY !== 'Windows'
            && Process::isTtySupported()
        ) {
            $process->setTty(true);
        }

        return $process->run(
            static function (string $type, string $output): void {
                fwrite(
                    $type === Process::ERR ? STDERR : STDOUT,
                    $output
                );
            }
        );
    }
}