<?php

namespace Yuga\Runtime;

final class RuntimeManager
{
    private array $adapters = [];

    public function register(string $name, RuntimeAdapter $adapter): self
    {
        $this->adapters[$name] = $adapter;

        return $this;
    }

    public function get(string $name): RuntimeAdapter
    {
        return $this->adapters[$name]
            ?? throw new RuntimeException("Runtime adapter [{$name}] is not registered.");
    }

    public function run(string $name, Kernel $kernel): void
    {
        $this->get($name)->run($kernel);
    }
}
