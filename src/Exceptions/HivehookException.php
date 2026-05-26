<?php

declare(strict_types=1);

namespace Hivehook\Exceptions;

class HivehookException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        public readonly ?int $statusCode = null,
        public readonly ?array $extensions = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
