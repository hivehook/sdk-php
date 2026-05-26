<?php

declare(strict_types=1);

namespace Hivehook\Exceptions;

class NotFoundException extends ApiException
{
    public function __construct(
        string $message = 'not found',
        ?int $statusCode = null,
        ?array $extensions = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $extensions, $previous);
    }
}
