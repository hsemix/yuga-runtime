<?php

namespace Yuga\Runtime\Support;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestFactory
{
    public function fromGlobals(): ServerRequestInterface
    {
        $factory = new Psr17Factory();

        return (new ServerRequestCreator(
            $factory,
            $factory,
            $factory,
            $factory
        ))->fromGlobals();
    }
}