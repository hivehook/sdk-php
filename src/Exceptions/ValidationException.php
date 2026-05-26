<?php

declare(strict_types=1);

namespace Hivehook\Exceptions;

class ValidationException extends ApiException
{
    public function __construct(
        string $message = 'validation error',
        ?int $statusCode = null,
        ?array $extensions = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $extensions, $previous);
    }
}
