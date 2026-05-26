<?php

declare(strict_types=1);

namespace Hivehook\Exceptions;

class ConflictException extends ApiException
{
    public function __construct(
        string $message = 'conflict',
        ?int $statusCode = null,
        ?array $extensions = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $extensions, $previous);
    }
}
