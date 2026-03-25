---
apply: by file patterns
patterns: *Type.php
---

# Symfony FormType Conventions

ALWAYS: No fluent interface on FormBuilderInterface. No closures as event callbacks. No Doctrine queries inside FormTypes.

## Builder
- Never chain `$builder->add()` calls. Always terminate each call with `;` on its own line.
- Always define both `buildForm()` and `configureOptions()`.

```php
$builder->add('firstname', TextType::class);
$builder->add('lastname', TextType::class);
```

## Event Listeners
- Never use anonymous functions or closures as event callbacks.
- Always implement the callback as a private method and reference it with `[$this, 'methodName']`.

```php
$builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);

private function onPreSubmit(FormEvent $event): void
{
    // logic goes here
}
```

## Data & Dependencies
- Always set `data_class` in `configureOptions()` when the form maps to an object.
- Never query Doctrine directly inside a FormType. Inject a repository or service instead.
