---
apply: always
---

### Engineering Guidelines (Project-wide)

These are project-wide engineering guidelines for PHP codebases in this repository. They are framework‑agnostic where possible and align with PSR standards and common Symfony practices. Use examples below only as illustrations — the rules apply across the project (libraries, bundles, and application code).


#### Coding Conventions
- PHP version and declarations
    - Use the PHP version declared in `composer.json` and the locked dependencies. Enable the newest language features available for that version.
    - Use `declare(strict_types=1);` at the top of every PHP file.
- Namespaces and classes
    - Follow PSR‑4 autoloading. Use clear, domain‑driven namespaces (e.g., `Vendor\Package\Domain`, `...\Application`, `...\Infrastructure`).
    - One class per file; file names must match the class name. Never place multiple classes in a single file — applies to production code and tests. If you need test doubles, create separate classes/files under the test namespace.
- Imports and ordering
    - Group `use` statements; order by vendor group (Symfony/framework, PSR, domain), then alphabetically within each group.
    - Avoid unused imports; keep them clean via static analysis or IDE inspections.
- Formatting
    - PSR‑12 indentation (4 spaces); braces on the next line for classes and methods.
    - Limit line length to ~120 chars; break fluent calls and arrays across lines for readability.
- Naming and casing
    - Classes, interfaces, traits, and enums: PascalCase (e.g., `UpdateRepository`, `StringFloaterInterface`).
    - Methods, local variables, parameters, and properties: camelCase (e.g., `mapRequest`, `$produktEan`).
    - Constants: UPPER_SNAKE_CASE (e.g., `DEFAULT_TIMEOUT`).
    - Exceptions end with the `Exception` suffix; interfaces with the `Interface` suffix when they are public contracts.
    - Database columns are an explicit exception: do not force camelCase; keep the storage naming as-is and adapt in mapping layers.
- Nullability and typing
    - Prefer typed properties and parameter/return types everywhere; avoid `mixed`.
    - Use union/intersection types and `readonly` properties when appropriate for the current PHP version declared in composer.
- Exceptions and errors
    - Throw meaningful, domain‑specific exceptions; do not use return `false`/`null` to indicate errors.
    - Don’t catch exceptions just to rethrow without adding context.
- Comments and PHPDoc
    - Self‑document through clear naming. Add PHPDoc when type info or intent is not obvious or when documenting array shapes.
    - Keep comments up‑to‑date; no commented‑out code.
    - Do not add inline comments yourself.
- Traits vs DI
    - Avoid Traits. Prefer Dependency Injection and composition. Extract behavior to small services and inject them.


#### Architecture and Code Organization (DDD, Ports & Adapters)
- DDD layers
    - Domain — business rules, entities/value objects, domain events, repository interfaces.
    - Application — use cases/services orchestrating domain logic; depends on domain interfaces only.
    - Infrastructure — framework/persistence/adapters implementing the domain/application ports.
- Ports & Adapters
    - Define interfaces (ports) in Domain/Application. Implement them in Infrastructure (adapters). Wire via DI.
- Every service must have an interface
    - Public services should expose an interface. Consumers depend on abstractions; implementations can evolve without breaking clients.
- Dependency management
    - Depend on abstractions (interfaces) and inject via constructor. Avoid service location and static calls.
    - Autowiring policy: Autowiring is allowed only at the application/project level. Bundles and packages must define services explicitly in YAML service configuration files.
- Symfony bundles (new structure)
    - Use the new `AbstractBundle` entry with `loadExtension`/`prependExtension`. Keep the bundle thin; move parsing/validation to `DependencyInjection/Configuration.php` and a custom `Extension` when it grows.
    - Use YAML configuration files for bundles/packages.
    - Autowiring policy: In bundles and reusable packages, do not use autowiring. Define services explicitly in YAML service configuration files (e.g., `services.yaml`). Autowiring is allowed only at the application/project level.
- Configuration
    - Use YAML for configurations across bundles/packages and at the application/project level.
    - Keep configs cohesive per subsystem. Validate required keys before merging; fail fast with clear exceptions.
- Directory layout (general)
    - `src/Domain`, `src/Application`, `src/Infrastructure` (or an equivalent grouping by bounded context/responsibility).
    - `config/` — framework/library configuration files (YAML).
    - `tests/` — mirror `src/` structure.
- Logging and messaging
    - Centralize channel names and message class names; document them here or in a `README`.
- ORM Entities
  - ORM-Entities reside in the src/Entities directory of the project or the package
  - Doctrine Mapping is always done inside the `config/doctrine` directory in packages or for the current project
  - The configuration format is xml


#### General Design Principles
- DRY — do not repeat yourself. Extract shared behavior into small, reusable services.
- KISS — favor simple, readable solutions over clever ones. Prefer small functions and explicit control flow.
- YAGNI — implement only what is needed for current requirements; keep options open via good abstractions.
- Established patterns — use well‑known patterns where appropriate: Adapter, Strategy, Factory, Repository, Specification, Builder.


#### Variables and Naming
- Naming principles
    - Prefer intention‑revealing names over abbreviations (e.g., `$messengerConfig`, `$flysystemConfig`, `$monologConfig`).
    - Use consistent suffixes/prefixes: `...Config`, `...Factory`, `...Provider`, `...Repository`, `...Mapper`.
    - Booleans read as predicates: `isEnabled`, `hasItems`, `shouldPersist`.
- Scope and mutability
    - Keep variables as narrow in scope as possible; prefer immutability. Reassign only when necessary.
- Types and arrays
    - Prefer small value objects over associative arrays when structures are stable/reused.
    - When arrays are used, document shapes with PHPDoc (e.g., `@var array{framework: array, monolog: array, flysystem: array}`).


#### SOLID Principles in Practice
- Single Responsibility Principle (SRP) — the “S” is critical
    - Classes should be as small and cohesive as possible. One reason to change. Extract collaborators rather than grow classes.
- Open/Closed Principle (OCP)
    - Design for extension via configuration/new implementations rather than modifying existing classes.
- Liskov Substitution Principle (LSP)
    - Program to interfaces; ensure implementations can be substituted without breaking consumers.
- Interface Segregation Principle (ISP)
    - Prefer multiple small, focused interfaces over large ones. Consumers should depend only on what they use.
- Dependency Inversion Principle (DIP)
    - High‑level policies (domain/application) must not depend on low‑level details (framework/persistence). Define ports in domain/application; implement adapters in infrastructure.


#### Review and Quality Gates
- Static analysis: No PHPStan.
- Code style: enforce PSR‑12 using Rector and PHP‑CS‑Fixer rules from https://github.com/perspeqtive/dev-dependencies.git when that package is installed. This centralized package defines the consistent ruleset for all projects. If not installed, follow PSR‑12 and match the existing project style.
- Tests: new/changed code must be covered by unit tests; container/configuration wiring should have at least smoke tests.
- Commits: keep changes small and well‑described; follow Conventional Commits if mandated by the repository.


#### Examples (from PERSPEQTIVESyliusAdapterBundle — simple classes)
- Small, SRP‑focused value/formatting service
```php
<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SyliusAdapterBundle\Format;

use function preg_replace;
use function strlen;
use function strrpos;
use function substr;

final class StringFloater implements StringFloaterInterface
{
    public function fromString(string $number): float
    {
        $separator = $this->getSeparatorPosition($number);
        if ($separator === null) {
            return (float)preg_replace('/\D/', '', $number);
        }

        return $this->normalizeDecimalNumber($number, $separator);
    }

    private function normalizeDecimalNumber(string $number, int $separator): float
    {
        $integerPart = preg_replace('/\D/', '', substr($number, 0, $separator));
        $fractionalPart = preg_replace('/\D/', '', substr($number, $separator + 1));

        return (float) "$integerPart.$fractionalPart";
    }

    private function getSeparatorPosition(string $number): ?int
    {
        $separatorPos = $this->findLastSeparator($number);

        if ($separatorPos === null || $this->isThousandSeparator($number, $separatorPos)) {
            return null;
        }

        return $separatorPos;
    }

    private function findLastSeparator(string $number): ?int
    {
        $dotPos = strrpos($number, '.');
        $commaPos = strrpos($number, ',');

        if ($dotPos > $commaPos) {
            return $dotPos;
        }
        if ($commaPos > $dotPos) {
            return $commaPos;
        }
        return null;
    }

    private function isThousandSeparator(string $number, int $separatorPos): bool
    {
        $decimalPart = substr($number, $separatorPos + 1);
        $digitCount = strlen(preg_replace('/\D/', '', $decimalPart));

        return $digitCount > 2;
    }
}
```

- Simple PHPUnit test using a `DataProvider` (no framework boot required)
```php
<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SyliusAdapterBundle\Tests\Format;

use PERSPEQTIVE\SyliusAdapterBundle\Format\StringFloater;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StringFloaterTest extends TestCase
{
    #[DataProvider('provideStringCases')]
    public function testFromString(string $number, float $expectedResult): void
    {
        $floater = new StringFloater();

        $this->assertSame($expectedResult, $floater->fromString($number));
    }

    public static function provideStringCases(): array
    {
        return [
            ['100,25', 100.25],
            ['100', 100.00],
            ['100.25', 100.25],
            ['1,000.25', 1000.25],
            ['1,000,000.25', 1000000.25],
            ['1000.25', 1000.25],
            ['1.000,25', 1000.25],
            ['1.000.000,25', 1000000.25],
            ['100.2', 100.20],
            ['100,2', 100.20],
            ['10.000', 10000.00],
            ['10,000', 10000.00],
            ['0', 0.00],
        ];
    }
}
```

- Notes
    - Keep examples simple and domain‑agnostic. Avoid using bundle entry classes in tests.
    - Prefer self‑written test doubles where collaboration exists (e.g., `StringFloaterInterface` could be faked in consumers). If impractical, use PHPUnit mocks/stubs — do not extend concrete classes to mock them.
    - The example reinforces: the PHP version declared in `composer.json`, `declare(strict_types=1);`, PSR‑12, SRP, and deterministic tests with `DataProvider`.

#### System
- The used Filesystem is always UNIX based, so do not use Windows paths or functions
- Use Linux-style path separators like /


#### Maintenance
- Keep `config/*.yaml` in sync with supported Symfony and library versions used here.
- Prefer introducing `DependencyInjection/Configuration.php` and a custom `Extension` class when configuration grows beyond simple prepend/import logic in bundles.
- Document any new channels, storages, or handlers in a short `README.md` where appropriate.
 