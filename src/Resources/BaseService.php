<?php

declare(strict_types=1);

namespace Hivehook\Resources;

use Hivehook\GraphQLTransport;

abstract class BaseService
{
    public function __construct(protected readonly GraphQLTransport $transport) {}

    public function iterate(array $options = []): \Generator
    {
        $opts = $options;
        $offset = $opts['offset'] ?? 0;
        while (true) {
            $opts['offset'] = $offset;
            $conn = $this->list($opts);
            $nodes = $conn['nodes'] ?? [];
            foreach ($nodes as $node) {
                yield $node;
            }
            $pageInfo = $conn['pageInfo'] ?? [];
            if (empty($pageInfo['hasNextPage']) || count($nodes) === 0) {
                break;
            }
            $offset += count($nodes);
        }
    }

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
