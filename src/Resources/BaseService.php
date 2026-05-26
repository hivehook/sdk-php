<?php

declare(strict_types=1);

namespace Hivehook\Resources;

use Hivehook\GraphQLTransport;

abstract class BaseService
{
    public function __construct(protected readonly GraphQLTransport $transport) {}

    protected function buildVariables(array $options, array $allowed): array
    {
        $vars = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $options)) {
                $vars[$key] = $options[$key];
            }
        }
        return $vars;
    }
}
