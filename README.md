# SuluActionBlocksBundle

![compatibility](https://img.shields.io/badge/sulu%20compatibility-%3E=2.6%20&%20%3C3.0-52b6ca.svg)

The **Sulu Action Blocks Bundle** allows you to create and manage reusable action blocks for your Sulu CMS project. It provides a straightforward way to define "controller actions" with your custom business logic, manage them via a dedicated admin interface, and give editors the flexibility to place them throughout the content tree.

This keeps your content management flexible and transparent, ensuring a consistent user experience even as your business logic evolves.

## 🚀 Features

- **Flexible Configuration**: Define business logic actions with custom parameters like target pages, text, or media the same way, you are used to.
- **Easy Integration**: Use a simple Twig function to render action blocks anywhere in your templates.
- **Editor Friendly**: Editors can easily select and configure actions using the familiar Sulu interface.

## 🛠️ Installation

### 1. Requirements

- **PHP**: 8.2 or higher
- **Sulu**: 2.6 or higher

### 2. Install the Bundle

Run the following command in your project root:

```bash
composer require perspeqtive/sulu-action-blocks-bundle
```

### 3. Register the Bundle (if not using Symfony Flex)

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    PERSPEQTIVE\SuluActionBlocksBundle\SuluActionBlocksBundle::class => ['all' => true],
];
```

## 📖 Usage

### 1. Define your Business Logic

### 1.1 Implement a class with `ServiceActionItemInterface`
Create a service that implements the `ServiceActionItemInterface`. This interface acts as a bridge between your custom logic and the bundle.

There are several methods, which need to be implemented:

| Method | Description                                                                                                                                                                                     |
|---|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `getTitle()` | Returns the title of the action. If you do not define a configuration block, the title will appear in the action block selection for the editor, otherwise the title of the block is presented. |
| `getConfigurationBlock()` | Returns the key of the global block that defines the configuration fields for the action. Return null if the action does not need any configuration.                                            |
| `execute()` | Executes the action logic. This is the part, where you place your business logic. This method needs to return an ActionExecutionResult with either an result HTML string or a redirect URL      |
| `getIdentifier()` | Returns the identifier of the action. This is used to identify the action internally. It should return a consistent and unique string. The FQDN is a good choice.                               |

#### 1.2 Returning you result

When returning an instance of `ActionExecutionResult` from your execute method, you either return a result HTML or a redirect URL.

The following example will return a result HTML string, which will be rendered on the website at the position, where the block is placed:
```php
return new ActionExecutionResult(html: '<h1>Hello World</h1>');
```

The following example will return a redirect URL, where the user will be redirected to after finishing the request. 
```php
return new ActionExecutionResult(redirect: 'https://google.com?q=PERSPEQTIVE');
```

See the [Example Implementation](#example-implementation) below for details.

#### 1.2 Configuration Blocks

Each action can declare the fields it needs via an ordinary [global block](https://docs.sulu.io/en/2.6/book/templates.html). Place them beside your other global blocks e.g. in `config/templates/blocks/` of your project.

The bundle collects the configuration blocks of all registered actions during cache warmup and makes them available for your editors via the global `action-blocks` block (See below).+
So if you add or remove a new action block class, you might need to run `bin/console cache:warmup` to make the new action block available in the admin area.

### 2. Add to Page Templates

To allow editors to use action blocks, add the `action-blocks` type to your page's XML configuration (e.g., `config/templates/pages/default.xml`):

```xml
<block name="blocks" default-type="text">
    <types>
        <type ref="text" />
        <!-- ... other block types ... -->
        <type ref="action-blocks" />
    </types>
</block>
```

### 4. Rendering via Twig

The are two ways to render action blocks in your page template:

* `perspeqtive_render_action_blocks(modules['action-blocks'])`  
This function handles multiple action blocks in a loop and renders the concatenated output.

* `perspeqtive_render_action_block(block.action, { 'custom_option': 'value' })`  
This function handles a single action block and renders the output.

#### 4.1 Example
In your page template (e.g., `templates/pages/default.html.twig`), use the `perspeqtive_render_action_blocks` function:

```twig
{% for modules in content.blocks %}
    {{ perspeqtive_render_action_blocks(modules['action-blocks']) }}
{% endfor %}
```

You can also iterate over the blocks yourself and pass additional options to the render function if needed:

```twig
{% for modules in content.blocks %}
    {% for block in modules['action-blocks] %}
        {% set block = block|merge({'highlighted': true}) %}
        {{ perspeqtive_render_action_block(block) }}
    {% endfor %}
{% endfor %}
```

## 💡 <a id="example-implementation"></a>Example Implementation

You can find reference implementations in the `docs/example/src/ActionBlock` directory:
- [FeaturedProductsAction.php](docs/example/src/ActionBlocks/FeaturedProductsAction.php) - Renders a list of featured products.
- [RedirectAction.php](docs/example/src/ActionBlocks/RedirecterServiceAction.php) - Executes logic and then performs a redirect.

## ⚙️ Service Registration

If your application uses standard Symfony autoconfiguration, no additional setup is required. The bundle automatically tags any service implementing `ServiceActionItemInterface` with `perspeqtive.sulu_action_block.action`.

```yaml
# config/services.yaml
services:
    App\ActionBlock\:
        resource: '%kernel.project_dir%/src/ActionBlock/*'
        autowire: true
        autoconfigure: true
```

If you prefer manual configuration, apply the tag yourself:

```yaml
services:
    App\ActionBlock\FeaturedProductsAction:
        class: App\ActionBlock\FeaturedProductsAction
        tags: ['perspeqtive.sulu_action_block.action']
```

## 👩‍🍳 Contribution

We welcome contributions! Please feel free to fork the repository, add features, and submit a pull request. To maintain high code quality, please include unit tests for all changes and update the documentation accordingly.

