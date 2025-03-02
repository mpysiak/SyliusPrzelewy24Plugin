# Installation

## Overview:
GENERAL
- [Requirements](#requirements)
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
---
ADDITIONAL
- [Known Issues](#known-issues)
---

## Requirements:
We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version  |
|---------------|----------|
| PHP           | \>=8.2   |
| sylius/sylius | ^2.0     |
| MySQL         | \>= 8.4  |
| NodeJS        | \>= 20.x |

## Composer:
```bash
composer require bitbag/przelewy24-plugin
```

## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

return [
    ...
    BitBag\SyliusPrzelewy24Plugin\BitBagSyliusPrzelewy24Plugin::class => ['all' => true],
];
```

## Known issues
### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```
