---
apply: by file patterns
patterns: *.php
---

# Architecture & Code Organization

ALWAYS: Depend on interfaces, never concrete implementations. Constructor injection only. No service locators, no static calls. Bundles never use autowiring — explicit YAML only.

## Directory Layout
Follow Symfony standards. Flat structure under `src/`:

```
src/
  Entity/         # Doctrine ORM entities
  Repository/     # Repository implementations
  Adapter/        # External API integrations and other adapters
  Service/        # Application services
  EventListener/  # Symfony event listeners/subscribers
config/
  services.yaml   # Service definitions
tests/            # Mirrors src/ structure
```

## Ports & Adapters (DDD)
- Define interfaces (ports) in the consuming service or domain. Implement them in `Adapter/`. Wire via DI.
- Every public service must have an interface. Consumers depend on the interface, never the implementation.

## Dependency Injection
- Always inject via constructor. Never use service location or static calls.
- Autowiring: allowed only at application/project level. Bundles must define all services explicitly in YAML.

## Symfony Bundles
- Always use the new `AbstractBundle` structure: `config/`, `src/`, `tests/` layout.
- Bundle entry class extends `AbstractBundle`, uses `loadExtension()`/`prependExtension()`. Keep it thin.
- All config in YAML — services, routing, and bundle configuration. Service definitions in `config/services.yaml`.
- No autowiring in bundles — define all services explicitly.
- Only introduce `DependencyInjection/Configuration.php` and a custom `Extension` when bundle configuration becomes too large for `loadExtension()`.

## ORM & Doctrine
- Entities in `src/Entity/`. Repositories in `src/Repository/`.
- Doctrine mapping always via PHP Attributes directly on the entity class. Never use XML or YAML mapping.

## Design Principles
- SRP: one reason to change. Extract collaborators rather than grow classes.
- DIP: depend on interfaces, implement in adapters. Never let application code depend on adapter details.
- DRY/KISS/YAGNI: extract shared behavior, favor simple readable solutions, implement only what is needed now.
