# SuluActionBlocksBundle

![compatibility](https://img.shields.io/badge/sulu%20compatibility-%3E=2.6-52b6ca.svg)

The **Sulu Action Blocks Bundle** lets you make application actions available as reusable blocks in Sulu. Editors can select an action in the Sulu admin, configure it when necessary, and place it anywhere in the content tree.

An action can either return HTML that is rendered at the position of the block or a redirect URL that is handled after the action has been executed.

<p style="display: flex; gap: 32px; justify-content: center;">
    <a href="docs/pics/action-block-examples.png" target="_blank">
        <img src="docs/pics/action-block-examples.png" style="border-radius: 3px;" alt="Example of action blocks embedded in page">
    </a>
</p>

## 🚀 Features

- **Custom actions**: Connect your own application, forms or business logic to Sulu blocks.
- **Optional configuration**: Give an action its own global block and receive the editor's values in your service.
- **Editor friendly**: Editors select and configure actions through the familiar Sulu interface.
- **Twig integration**: Render one action block or a complete collection of action blocks with Twig functions.

## 🛠️ Installation

### 1. Requirements

- **PHP**: 8.2 or higher
- **Sulu**: 2.6 or higher and below 3.0

### 2. Install the bundle

Run the following command in your project root:

```bash
composer require perspeqtive/sulu-action-blocks-bundle
```

If Symfony Flex does not register the bundle automatically, add it to `config/bundles.php`:

```php
return [
    // ...
    PERSPEQTIVE\SuluActionBlocksBundle\SuluActionBlocksBundle::class => ['all' => true],
];
```

## 📖 Usage

### 1. Create an action service

Create a service that implements `ServiceActionItemInterface`. The service is the only application-specific part you need to provide.

```php
<?php

declare(strict_types=1);

namespace App\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

final class FeaturedProductsAction implements ServiceActionItemInterface
{
    public function __construct(
        private readonly FeaturedProductServiceInterface $featuredProductService,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Featured products';
    }

    public function getConfigurationBlock(): ?string
    {
        return 'featured-products';
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult(
            html: $this->featuredProductService->getFeaturedProducts($options['product-ids'] ?? []),
        );
    }
}
```

The interface requires these four methods:

| Method | Purpose |
| --- | --- |
| `getIdentifier()` | Returns a stable and unique identifier for the action. `self::class` is a suitable choice. |
| `getTitle()` | Returns the name shown to editors. It is also used to generate the block name when no configuration block is defined. |
| `getConfigurationBlock()` | Returns the key of the global block containing the action's configuration fields, or `null` when the action needs no editor input. |
| `execute()` | Runs your application logic and returns an `ActionExecutionResult`. The values configured in Sulu are available in `$options`. |

Register any dependencies of the action as usual. With Symfony autoconfiguration, services implementing `ServiceActionItemInterface` are registered automatically. If autoconfiguration is disabled, add the bundle tag manually:

```yaml
# config/services.yaml
services:
    App\ActionBlocks\FeaturedProductsAction:
        tags: ['perspeqtive.sulu_action_block.action']
```

### 2. Add an optional configuration block

If an action needs values from an editor, create a regular Sulu global block in `config/templates/blocks/`. Its `<key>` must be the value returned by `getConfigurationBlock()`.

For example, the `FeaturedProductsAction` above uses the following block:

```xml
<template xmlns="http://schemas.sulu.io/template/template"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://schemas.sulu.io/template/template http://schemas.sulu.io/template/template-1.0.xsd">
    <key>featured-products</key>
    <meta>
        <title>Featured products</title>
    </meta>
    <properties>
        <property name="product-ids" type="product_selection">
            <meta>
                <title lang="en">Featured products</title>
            </meta>
        </property>
    </properties>
</template>
```

The values of the block's properties are passed to `execute()` as the `$options` array. An action without configuration can return `null` from `getConfigurationBlock()`; editors then select it by the title returned from `getTitle()`.

After adding or changing an action or its configuration block, clear or warm up the Symfony cache so that Sulu can update the available action block types:

```bash
bin/console cache:clear
```

### 3. Add action blocks to a page template

Add the `action-blocks` type to the block property of every page template where editors should be able to use actions:

```xml
<block name="blocks" default-type="text">
    <types>
        <type ref="text" />
        <!-- ... other block types ... -->
        <type ref="action-blocks" />
    </types>
</block>
```

The bundle adds each registered action to the `action-blocks` selection automatically. You do not need to add individual action types to the page template.

### 4. Render action blocks with Twig

Use `perspeqtive_render_action_blocks()` when rendering the complete value of an `action-blocks` property:

```twig
{% for module in content.blocks %}
    {{ perspeqtive_render_action_blocks(module['action-blocks']) }}
{% endfor %}
```

To render individual entries, use `perspeqtive_render_action_block()`. Pass the complete block value; it must contain the `type` field generated by Sulu:

```twig
{% for module in content.blocks %}
    {% for actionBlock in module['action-blocks'] %}
        {{ perspeqtive_render_action_block(actionBlock) }}
    {% endfor %}
{% endfor %}
```

You can add values to the options passed to the action before rendering:

```twig
{% set actionBlock = actionBlock|merge({highlighted: true}) %}
{{ perspeqtive_render_action_block(actionBlock) }}
```

### 5. Return HTML or a redirect

Return HTML when the action should render content at its position:

```php
return new ActionExecutionResult(html: '<h1>Hello World</h1>');
```

Return a redirect URL when the action should redirect the visitor after execution:

```php
return new ActionExecutionResult(redirect: 'https://example.com/success');
```

Only the HTML value is rendered by the Twig function. Redirects are handled by the bundle's request integration.

## 💡 Example implementation

The [`docs/example`](docs/example) directory contains a complete, minimal setup:

- [`FeaturedProductsAction.php`](docs/example/src/ActionBlocks/FeaturedProductsAction.php) shows an action with a configurable global block.
- [`RedirecterServiceAction.php`](docs/example/src/ActionBlocks/RedirecterServiceAction.php) shows an action without a configuration block that returns a redirect.
- [`featured-products.xml`](docs/example/config/templates/blocks/featured-products.xml) defines the configuration block used by the first action.
- [`default.xml`](docs/example/config/templates/pages/default.xml) enables action blocks in a page template.

## 👩‍🍳 Contribution

We welcome contributions! Please feel free to fork the repository, add features, and submit a pull request. To maintain high code quality, please include unit tests for all changes and update the documentation accordingly.
