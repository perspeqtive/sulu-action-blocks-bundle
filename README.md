# SuluActionBlockBundle

![compatibility](https://img.shields.io/badge/sulu%20compatibility-%3E=2.6%20&%20%3C3.0-52b6ca.svg)

The **Sulu Action Block Bundle** allows you to create and manage reusable action blocks for your Sulu CMS project.  
It provides an easy way to define actions, manage them via a dedicated admin interface, and display them on your pages.

## 🚀 Features

- **Centralized Action Management**: Create and manage action blocks in a dedicated admin area.
- **Flexible Content**: Add different types of content (images, links, editors) to your action blocks.
- **Easy Integration**: Use the built-in Twig function to render action blocks anywhere.
- **Page Integration**: Add action blocks to your pages using a custom block type.

## 🛠 Installation

### 1. Requirements

- Sulu 2.6 or higher
- PHP 8.2 or higher

### 2. Install the Bundle

Run the following command in your project root:

```bash
composer require perspeqtive/sulu-action-block-bundle
```

### 3. Register the Bundle (if not using Symfony Flex)

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    PERSPEQTIVE\SuluActionBlockBundle\SuluActionBlockBundle::class => ['all' => true],
];
```

### 4. Update Database

Run the following command to update your database schema:

```bash
bin/console doctrine:schema:update --force
```
*Note: In production, it's recommended to use migrations.*

## 📖 Usage

### 1. Create Action Blocks

1. Log in to the Sulu Admin Interface.
2. Navigate to the **Action Blocks** section in the sidebar.
3. Click on **Add** to create a new action block.
4. Fill in the details (Title, Action and configure your action block).

### 2. Add to Page Templates

To use action blocks on your pages, add the `action-block` global block configuration to your page's XML configuration (e.g., `config/templates/pages/default.xml`):

```xml
<block name="blocks" default-type="text">
    <types>
        <!-- ... other block types ... -->
        <type ref="action-block" />
    </types>
</block>
```

### 3. Render in Twig

In your page template (e.g., `templates/pages/default.html.twig`), render the selected action block with the new method `perspeqtive_render_action_block(actionname, options)` like this:

```twig
{% for block in content.blocks %}
    {% if block.type == 'action_block' %}
        {{ perspeqtive_render_action_block(block.action) }}
    {% endif %}
{% endfor %}
```

You can also pass additional options to the render function:

```twig
{{ perspeqtive_render_action_block(block.action, { 'custom_option': 'value' }) }}
```

### 4. Add your own Action Service

You can extend the bundle by adding custom services ("actions") that render HTML or trigger redirects.

- Implement `PERSPEQTIVE\SuluActionBlockBundle\Registry\ServiceActionItemInterface`.
- Optionally use dependency injection (repositories, Twig, services) inside your action class.
- The action will automatically appear in the admin select, identified by `getIdentifier()` and labeled by `getTitle()`.

#### Example implementation

There are two examples in the [docs/example/src/ActionBlock](docs/example/src/ActionBlock) directory:
- [FeaturedProductsAction.php](docs/example/src/ActionBlock/FeaturedProductsAction.php) - renders a list of featured products as HTML
- [RedirectAction.php](docs/example/src/ActionBlock/RedirectAction.php) - redirects to a given URL after executong a service

#### Service registration

If your app uses Symfony defaults (`autowire: true`, `autoconfigure: true`), no extra config is required. The bundle auto-configures any service implementing `ServiceActionItemInterface` with the tag `perspeqtive.sulu_action_block.action`.

```yaml
# config/services.yaml
services:
    App\ActionBlock\:
        resource: '%kernel.project_dir%/src/ActionBlock/*'
        autowire: true
        autoconfigure: true
```

If you don't use autoconfiguration, tag the service manually:

```yaml
services:
    App\\ActionBlock\FeaturedProductsAction:
      class: App\Sulu\ActionBlock\FeaturedProductsAction
      tags: ['perspeqtive.sulu_action_block.action']
```

#### Using your action

- In the admin, your action will show up in the select list with the given title.
- Render it in Twig and optionally pass options:

```twig
{{ perspeqtive_render_action_block(block.action, { limit: 8 }) }}
```

## 👩‍🍳 Contribution

Please feel free to fork and extend existing or add new features and send a pull request with your changes! To establish a consistent code quality, please provide unit tests for all your changes and adapt the documentation.

