# hivehook/sdk

Official PHP client for [Hivehook](https://hivehook.com), webhook infrastructure for modern teams (inbound and outbound).

## Install

```bash
composer require hivehook/sdk
```

## Quick start

```php
<?php

use Hivehook\HivehookClient;

$client = new HivehookClient(
    baseUrl: 'http://localhost:8080',
    apiKey: getenv('HIVEHOOK_API_KEY'),
);

$source = $client->sources->create([
    'name' => 'Stripe production',
    'slug' => 'stripe-prod',
    'providerType' => 'stripe',
    'verifyConfig' => ['secret' => 'whsec_...'],
]);

printf("created source %s. POST webhooks to /ingest/%s\n", $source['id'], $source['slug']);
```

## Webhook signature verification

```php
use Hivehook\Webhook;

$signature = $_SERVER['HTTP_X_HIVEHOOK_SIGNATURE'];
$timestamp = (int) $_SERVER['HTTP_X_HIVEHOOK_TIMESTAMP'];
$ok = Webhook::verify($body, 'your-signing-secret', $signature, $timestamp, 300);
```

## Requirements

- PHP 8.1+
- ext-curl, ext-json

## Documentation

See the full reference at [hivehook.com/docs](https://hivehook.com/docs).

## License

MIT. See [LICENSE](LICENSE).
