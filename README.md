AOTranslationBundle Overview
============================

This bundle provides doctrine as translations storage and a nice web gui accessible from the profiler for easy translation.

[![Build Status](https://secure.travis-ci.org/adrianolek/AOTranslationBundle.png)](http://travis-ci.org/adrianolek/AOTranslationBundle)

Installation
============

Update composer.json with (in require section):
"ao/translation-bundle": "dev-master"

Update composer bundles with:
php composer.phar update

Update app/AppKernel.php with (in registerBundles method):
new AO\TranslationBundle\AOTranslationBundle()

Configuration
=============

framework:
    translator: ~

parameters:
    translator.class: AO\TranslationBundle\Translation\Translator
    
ao_translation:
    locales:
        en: ~
        de: { fallback: [en] }
        fr: ~
        pl: ~

Usage
=====
