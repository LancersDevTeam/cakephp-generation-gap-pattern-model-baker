# GenerationGapModelBaker plugin for CakePHP4
This is a plugin that uses the Generation gap pattern to realize bakeable Model operations.

The automatic generation tool creates only superclasses. And humans do not modify it. Humans create subclasses of that class. The auto-generator does not manipulate those subclasses.

Generation gap Pattern Reference (Japanese)

http://www.hyuki.com/dp/dpinfo.html#GenerationGap

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require --dev lancers/cakephp-generation-gap-pattern-model-baker
```

To install with `composer.json` , add the following

```json
    "require-dev": {
        "lancers/cakephp-generation-gap-pattern-model-baker": "1.*",
    }
```

## Usage

### 1. Add to bootstrap_cli.php

Add config to load custom template.

```php
Configure::write('Bake.theme', 'GenerationGapModelBaker');
```

### 2. Add to Application.php

This is not necessary in the runtime environment, so addPlugin at debug time.

```php
if (Configure::read('debug')) {
    $this->addPlugin('GenerationGapModelBaker');    // add line
    $this->addPlugin('DebugKit');
}
```

### 3. Run bake command to create Models

```
bin/cake bake extended_model table_name
```

In the case of the examples table, a Model file will be created as follows.

```
$ bin/cake bake extended_model examples 
One moment while associations are detected.

Baking table class for Examples...

Creating file /var/www/lancers_admin/src/Model/Baked/Table/ExamplesTable.php
Wrote `/var/www/lancers_admin/src/Model/Baked/Table/ExamplesTable.php`

Baking entity class for Example...

Creating file /var/www/lancers_admin/src/Model/Baked/Entity/Example.php
Wrote `/var/www/lancers_admin/src/Model/Baked/Entity/Example.php`

Baking entended table class for Examples...

Creating file /var/www/lancers_admin/src/Model/Table/ExamplesTable.php
Wrote `/var/www/lancers_admin/src/Model/Table/ExamplesTable.php`

Baking entended entity class for Example...

Creating file /var/www/lancers_admin/src/Model/Entity/Example.php
Wrote `/var/www/lancers_admin/src/Model/Entity/Example.php`

Baking test fixture for Examples...

Creating file /var/www/lancers_admin/tests/Fixture/ExamplesFixture.php
Wrote `/var/www/lancers_admin/tests/Fixture/ExamplesFixture.php`
Bake is detecting possible fixtures...

Baking test case for App\Model\Table\ExamplesTable ...

Creating file /var/www/lancers_admin/tests/TestCase/Model/Table/ExamplesTableTest.php
Wrote `/var/www/lancers_admin/tests/TestCase/Model/Table/ExamplesTableTest.php`
Done
```