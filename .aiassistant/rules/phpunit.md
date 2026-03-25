---
apply: by file patterns
patterns: *Test.php
---

# PHPUnit Rules

ALWAYS: Manual test doubles over PHPUnit mocks. All test classes and doubles must be `final`. Use `#[DataProvider]`, never `@dataProvider`.

## Structure
- Unit tests: `tests/Unit/` mirroring `src/` namespace. Application tests: `tests/Application/<FeatureName>/`.
- Manual doubles: `tests/Unit/Mocks/` only. For bundles: `bundles/<BundleName>/tests/`.
- One test class per production class. Always use `setUp()` to instantiate the SUT and all doubles.
- Method names as sentences in camel case: `testMethodNameToTestUpdatesMetadataForMatchingChannel`.

## Doubles and Mocking
- Always write manual doubles. `createMock()` only when: no interface exists, class has direct DB/persistence dependency (e.g. Record Pattern), third-party or Symfony-internal class (e.g. `Request`, `Response`).
- Never extend concrete classes in tests.
- Naming: `Mock` + interface name without `Interface` suffix — `FooBarInterface` → `MockFooBar`.
- Public properties for return values (set in `setUp()` or before act) and for recording arguments (nullable, default `null`, assert after act):

```php
final class MockProductRepository implements ProductRepositoryInterface
{
    public Product $productToReturn;   // return value — set before act
    public ?Product $savedProduct = null; // recorded arg — assert after act

    public function findBySku(string $sku): Product { return $this->productToReturn; }
    public function save(Product $product): void { $this->savedProduct = $product; }
}
```

## Assertions & Exceptions
- Default: `assertSame()`. Use `assertEquals()` only for object value comparison — add a comment.
- Exceptions: `$this->expectException(FooException::class)`. Only add `expectExceptionMessage()` when the message is part of the contract.

## Test Data & Isolation
- No shared fixture files. Data inline or via Builder/Factory. `#[DataProvider]` provider methods must be `public static`.
- No external services or real filesystem. Temp dirs must be cleaned up in `tearDown()`.
