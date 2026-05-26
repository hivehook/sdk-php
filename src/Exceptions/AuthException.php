<?php

declare(strict_types=1);

namespace Hivehook\Exceptions;

class AuthException extends ApiException
{
    public function __construct(
        string $message = 'unauthorized',
        ?int $statusCode = 401,
        ?array $extensions = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $extensions, $previous);
    }
}
