AOTranslationBundle Overview
============================

This bundle provides doctrine as translations storage and a nice web gui accessible from the profiler for easy translation.

[![Build Status](https://secure.travis-ci.org/adrianolek/AOTranslationBundle.png)](http://travis-ci.org/adrianolek/AOTranslationBundle)

Features
========

* All translation messages are automatically saved in database (no extraction necessary)
* Translation panel available in the Symfony web debug toolbar

Installation
============

Require vendor libraries
------------------------

Require `ao/translation-bundle` & `stof/doctrine-extensions-bundle` in `composer.json`:
```json
"require": {
  "symfony/symfony": "2.1.*",
  "_comment": "other packages",
  "stof/doctrine-extensions-bundle": "1.1.*@dev",
  "ao/translation-bundle": "1.0.*@dev",
}
```

Then install or update composer bundles with:

    php composer.phar install
    
or

    php composer.phar update

Add bundles to your application kernel
--------------------------------------

In `app/AppKernel.php` add:
```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        //...
        new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        new AO\TranslationBundle\AOTranslationBundle()
        //...
    );
}
```

Configure translator
--------------------

```yml
# app/config/config.yml
# enable translation component
framework:
    translator: ~

# use AOTranslationBundle as translator
parameters:
    translator.class: AO\TranslationBundle\Translation\Translator

# configure locales avaliable for translation 
ao_translation:
    locales:
        en: ~
        de: ~
        fr: ~
```

Update your db schema
---------------------

If you use migrations:
```
app/console doctrine:migrations:diff
app/console doctrine:migrations:migrate
```

Otherwise:
```
app/console doctrine:schema:update
```

Usage
=====

You can access translations panel by clicking on Translations in the web debug toolbar.

![Translations web debug toolbar](https://raw.github.com/adrianolek/AOTranslationBundle/master/Resources/doc/img/profiler.png)

Now you can edit all your translation messages.
Message parameters can be inserted directly into translation by clicking on the link in Parameters column.
After you are done click `Save Translations`.

![Translations panel](https://raw.github.com/adrianolek/AOTranslationBundle/master/Resources/doc/img/panel.png)

