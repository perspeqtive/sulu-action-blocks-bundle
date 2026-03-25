---
apply: by file patterns
patterns: *.php
---

# Coding Conventions

ALWAYS: `declare(strict_types=1)` in every PHP file. Typed properties and return types everywhere. No `mixed`. No Traits. No commented-out code. No inline comments.

## PHP & Types
- Use the PHP version from `composer.json`. Enable the newest available language features.
- Typed properties, parameters, and return types everywhere. Avoid `mixed`.
- Use union/intersection types and `readonly` where appropriate for the declared PHP version.
- Prefer small value objects over associative arrays for stable/reused structures.
- Document array shapes with PHPDoc when arrays are unavoidable: `@var array{framework: array, monolog: array}`.

## Namespaces & Classes
- PSR-4 autoloading. Domain-driven namespaces: `Vendor\Package\Domain`, `...\Application`, `...\Infrastructure`.
- One class per file. File name must match class name. Never multiple classes in one file — including test doubles.
- Classes, interfaces, traits, enums: PascalCase. Methods, variables, parameters, properties: camelCase. Constants: UPPER_SNAKE_CASE.
- Exceptions suffix: `Exception`. Public contract interfaces suffix: `Interface`.
- Booleans as predicates: `isEnabled`, `hasItems`, `shouldPersist`.
- Consistent suffixes: `...Config`, `...Factory`, `...Provider`, `...Repository`, `...Mapper`.
- Database columns: keep storage naming as-is, adapt in mapping layers only.

## Imports
- Group `use` statements by vendor (Symfony/framework → PSR → domain), then alphabetically within each group.
- No unused imports.

## Formatting
- PSR-12: 4-space indentation, braces on next line for classes and methods.
- Max ~120 chars per line. Break fluent calls and arrays across lines.

## Exceptions
- Throw domain-specific exceptions. Never use `return false`/`null` to signal errors.
- Never catch and rethrow without adding context.

## Comments & PHPDoc
- Self-document via naming. Add PHPDoc only when type info or intent is not obvious, or to document array shapes.
- No commented-out code. No inline comments.

## Dependencies
- Avoid Traits. Use constructor injection and composition instead. Extract behavior to small services.
- Prefer intention-revealing names: `$messengerConfig` not `$cfg`.
- Keep variables as narrow in scope as possible. Prefer immutability.

## Example
```php
<?php

declare(strict_types=1);

namespace Vendor\Package\Domain\Format;

use Vendor\Package\Domain\Exception\InvalidInputException;

final class PriceFormatter implements PriceFormatterInterface
{
    public function __construct(
        private readonly CurrencyProviderInterface $currencyProvider,
    ) {}

    public function format(int $amountInCents, string $currencyCode): string
    {
        if ($amountInCents < 0) {
            throw new InvalidInputException('Amount must not be negative.');
        }

        $currency = $this->currencyProvider->getByCode($currencyCode);

        return $currency->formatAmount($amountInCents);
    }
}
```
