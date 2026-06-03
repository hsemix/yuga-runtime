<?php

namespace Yuga\Runtime\Support;

use Psr\Http\Message\ResponseInterface;

final class Psr7Emitter
{
    public function emit(ResponseInterface $response): void
    {
        if (!headers_sent()) {
            http_response_code($response->getStatusCode());

            foreach ($response->getHeaders() as $name => $values) {
                $replace = strtolower($name) !== 'set-cookie';

                foreach ($values as $value) {
                    header($name . ': ' . $value, $replace);
                }
            }
        }

        echo (string) $response->getBody();
    }
}