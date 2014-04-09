AOTranslationBundle Overview
============================

This bundle provides doctrine as translations storage and a nice web gui accessible from the profiler for easy translation.

[![Build Status](https://secure.travis-ci.org/adrianolek/AOTranslationBundle.png)](http://travis-ci.org/adrianolek/AOTranslationBundle)

Features
========

* All translation messages are automatically saved in the database (no extraction necessary)
* Translation panel available in the Symfony web debug toolbar
* Only messages used in current action are loaded from the database

Installation
============

Require vendor libraries
------------------------

Require `ao/translation-bundle` & `stof/doctrine-extensions-bundle` in `composer.json`:

    "require": {
      "symfony/symfony": "2.1.*",
      "_comment": "other packages",
      "stof/doctrine-extensions-bundle": "1.1.*@dev",
      "ao/translation-bundle": "1.0.*@dev"
    }

Then install or update composer bundles with:

    php composer.phar install
    
or

    php composer.phar update

Add bundles to your application kernel
--------------------------------------

In `app/AppKernel.php` add:

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

Configure translator
--------------------

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
            
Configure doctrine extensions bundle
------------------------------------

Timestampable behavior has to be enabled.

    # app/config/config.yml
    stof_doctrine_extensions:
        orm:
            default:
                timestampable: true

Update your db schema
---------------------

If you use migrations:

    app/console doctrine:migrations:diff
    app/console doctrine:migrations:migrate


Otherwise:

    app/console doctrine:schema:update
    
Add routing information
-----------------------
    
    # app/config/routing.yml
    ao_translation:
        resource: "@AOTranslationBundle/Controller/"
        type:     annotation
        prefix:   / 


Usage
=====

Use translation methods like described in [Symfony Translations](http://symfony.com/doc/current/book/translation.html) documentation.

You can access translations panel by clicking on Translations in the web debug toolbar.

![Translations web debug toolbar](https://raw.github.com/adrianolek/AOTranslationBundle/master/Resources/doc/img/profiler.png)

Now you can edit all your translation messages.
Message parameters can be inserted directly into translation by clicking on the link in `Parameters` (2) column.
After you are done click the `Save Translations` button (1).

As the translator needs to know which messages are used in each action, it stores this relation in a cache table.
Therefore, when a message is not used anymore it will still be visible in the translations panel.
To clear the cached messages use the `Reset action cache` (3) button, which will clear the cache for current action.
Alternatively use the `Reset cache` (4) button, which will clear cache for all actions.
The cache will be rebuilt with the next execution of an action.

![Translations panel](https://raw.github.com/adrianolek/AOTranslationBundle/master/Resources/doc/img/panel.png)

