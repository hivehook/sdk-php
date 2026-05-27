<?php

declare(strict_types=1);

namespace Hivehook\Tests;

use PHPUnit\Framework\TestCase;
use Hivehook\GraphQLTransport;
use Hivehook\Resources\SourceService;

class PaginationTest extends TestCase
{
    private function stub(array $pages): GraphQLTransport
    {
        return new class($pages) extends GraphQLTransport {
            public function __construct(private array $pages) {}

            public function execute(string $query, array $variables = []): array
            {
                return ['sources' => $this->pages[$variables['offset'] ?? 0]];
            }
        };
    }

    public function testIterateWalksEveryPage(): void
    {
        $svc = new SourceService($this->stub([
            0 => ['nodes' => [['id' => 'a'], ['id' => 'b']], 'pageInfo' => ['hasNextPage' => true]],
            2 => ['nodes' => [['id' => 'c']], 'pageInfo' => ['hasNextPage' => false]],
        ]));

        $ids = [];
        foreach ($svc->iterate() as $node) {
            $ids[] = $node['id'];
        }
        $this->assertSame(['a', 'b', 'c'], $ids);
    }

    public function testIterateSinglePage(): void
    {
        $svc = new SourceService($this->stub([
            0 => ['nodes' => [['id' => 'only']], 'pageInfo' => ['hasNextPage' => false]],
        ]));

        $ids = iterator_to_array($svc->iterate());
        $this->assertSame('only', $ids[0]['id']);
        $this->assertCount(1, $ids);
    }
}
