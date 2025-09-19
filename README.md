# Zod for PHP

A PHP-first port of [Zod](https://github.com/colinhacks/zod) that mirrors the fluent schema builder and validation pipeline familiar to TypeScript users.

## Quickstart

```bash
composer install
vendor/bin/phpunit
```

Add the package to your project with Composer (the library follows PSR-4 under `Nyra\\Zod`).

## Basic Usage

```php
use Nyra\Zod\Z;

$schema = Z::object([
    'id' => Z::number()->int()->positive(),
    'email' => Z::string()->email(),
    'roles' => Z::array(Z::string()->nonempty())->nonempty(),
]);

$payload = $schema->parse([
    'id' => 42,
    'email' => 'dev@example.com',
    'roles' => ['admin'],
]);
```

## Defining a Schema

Schemas are composed with fluent helpers. Every primitive has refinement methods, nullable/optional wrappers, unions, tuples, and objects.

```php
use Nyra\Zod\Z;

$userSchema = Z::object([
    'name' => Z::string()->min(2),
    'age' => Z::coerce()->number()->int()->min(0)->default(18),
    'email' => Z::string()->email()->optional(),
    'address' => Z::object([
        'street' => Z::string()->nonempty(),
        'zip' => Z::string()->regex('/^[0-9]{5}$/'),
    ])->passthrough(),
]);
```

## Parsing Data

Call `parse()` to validate and retrieve the typed value. Use `safeParse()` for success/error separation without exceptions.

```php
$result = $userSchema->safeParse($input);

if ($result->success) {
    $user = $result->data; // validated payload
} else {
    // handle $result->error
}
```

Pipeline helpers support preprocessing and transformation:

```php
$trimmed = Z::string()
    ->preprocess(static fn ($value) => is_string($value) ? trim($value) : $value)
    ->nonempty();

$upper = Z::string()->transform('strtoupper');
```

## Handling Errors

`parse()` throws `Nyra\Zod\Errors\ZodError`. Each error contains one or more `ZodIssue` entries with codes, messages, and paths.

```php
use Nyra\Zod\Errors\ZodError;

try {
    $userSchema->parse($input);
} catch (ZodError $error) {
    foreach ($error->getIssues() as $issue) {
        printf("[%s] %s at %s\n", $issue->code, $issue->message, implode('.', $issue->path));
    }
}
```

## Inferring Types

The library returns validated PHP values. Combine schema definitions with PHPDoc or static analysis to keep types aligned:

```php
/** @var array{name: string, age: int, email?: string|null} $user */
$user = $userSchema->parse($input);
```

When using Psalm or PHPStan, you can declare custom provider extensions that map schema builders to precise array shapes, mirroring how Zod integrates with TypeScript's type inference.
