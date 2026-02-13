---
apply: by file patterns
patterns: *Type.php
---

#### FormTypes
- When creating an event listener for example for FormEvents::PRE_SUBMIT, implement the callback method as an internal function which is private
- Do not use the fluent interface of the FormBuilderInterface. Finish each FormBuilderInterface::add method call with a ;

#### Example call
When creating a new FormType class or editing an existing one, ensure compliance with the rules mentioned above.

```php
$builder->add('firstname', TextType::class);
$builder->add('lastname', TextType::class);
```

**EventListener Callback:**
```php
$builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);

// ...

public function onPreSubmit(FormEvent $event): void
{
    // logic goes here
}
```