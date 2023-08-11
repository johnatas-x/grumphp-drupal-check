> :warning: **The version 2 of this project is made for GrumPHP 2.0. Check release 1.1.0 for GrumPHP 1.15** :warning:

# Description

This repository adds a task for GrumPHP that launchs [drupal-check](https://github.com/mglaman/drupal-check).
During a commit check Drupal code for deprecations and discover bugs via static analysis. If a deprecated code is detected, it won't pass.


# Installation

Install it using composer:

```composer require --dev johnatas-x/grumphp-drupal-check```


# Usage

1) Add the extension in your grumphp.yml file:
```yaml
extensions:
  - GrumphpDrupalCheck\ExtensionLoader
```

2) Add drupal check to the tasks:
```
tasks:
  drupal_check:
    drupal_root: ~
    memory_limit: ~
    deprecations: true
    analysis: true
    php8: true
    exclude_dir: []
```
Optionally, you can define multiple DrupalCheck arguments:

- **drupal_root** (string): Configure the path to the Drupal root. This fallback option can be used if drupal-check could not identify Drupal root from the provided path(s). This is useful when testing a module as opposed to a Drupal installation.
- **memory_limit** (string): Configure memory limit for the process.
- **deprecations** (boolean): Check code for deprecations. By default it is true.
- **analysis** (boolean): Check code analysis.
- **php8** (boolean): Set PHPStan phpVersion for 8.1 (Drupal 10 requirement).
- **exclude_dir** (array): Exclude directories. Wildcards work.