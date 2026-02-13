---
apply: by file patterns
patterns: *Test.php
---

#### PHPUnit Tests and Mocking (self‑written doubles first)
- Test framework & structure
    - Use PHPUnit for all PHP tests. Mirror the production namespace under a `tests` tree (e.g., `tests/Domain/...`, `tests/Application/...`). For bundles, use `bundles/<BundleName>/tests` mirroring `src`.
- What to test
    - Unit‑test services, mappers/DTOs, and domain/application logic. Use integration tests for framework/container wiring where needed.
- Mocking strategy
    - Prefer self‑written test doubles (stubs, spies, fakes, manual mocks) whenever feasible — especially when collaborating via interfaces.
    - If a self‑written double is impractical, use PHPUnit mocks/stubs. Do not extend concrete classes just to mock them — this can trigger side effects (e.g., DB connections) and tightly couples tests to implementation details.
    - Implement small, focused doubles that:
        - Implement only the methods under test.
        - Record calls and allow assertions on inputs/outputs.
- Isolation vs. realism
    - For pure units, isolate dependencies via interfaces or small hand‑written fakes.
    - For integration with Symfony/Pimcore/etc., boot a minimal kernel/container that loads only the necessary config and assert wiring/behavior.
- Fixtures and data
    - Prefer builders/factories over large fixtures. Keep data close to tests.
- Structure & naming
    - One test class per production class; descriptive method names (e.g., `test_it_updates_metadata_for_matching_channel`).
    - Keep tests deterministic. Avoid external services and filesystem unless using temp directories that are cleaned up.
- Examples use simple classes — do not use bundle entry classes as test examples.
- DataProviders
  - When using data providers from PHPUnit, prefer Attributes over annotation

- Don't run the tests yourself