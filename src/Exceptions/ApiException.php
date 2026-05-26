<?php

declare(strict_types=1);

namespace Hivehook\Exceptions;

class ApiException extends HivehookException
{
    public function __construct(
        string $message,
        ?int $statusCode = null,
        ?array $extensions = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $extensions, $previous);
    }
}
