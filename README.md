# VatNumberCheck plugin for CakePHP

[![Build Status](https://travis-ci.org/Oefenweb/cakephp-vat-number-check.png?branch=master)](https://travis-ci.org/Oefenweb/cakephp-vat-number-check) [![PHP 7 ready](http://php7ready.timesplinter.ch/Oefenweb/cakephp-vat-number-check/badge.svg)](https://travis-ci.org/Oefenweb/cakephp-vat-number-check) [![Coverage Status](https://codecov.io/gh/Oefenweb/cakephp-vat-number-check/branch/master/graph/badge.svg)](https://codecov.io/gh/Oefenweb/cakephp-vat-number-check) [![Packagist downloads](http://img.shields.io/packagist/dt/Oefenweb/cakephp-vat-number-check.svg)](https://packagist.org/packages/oefenweb/cakephp-vat-number-check) [![Code Climate](https://codeclimate.com/github/Oefenweb/cakephp-vat-number-check/badges/gpa.svg)](https://codeclimate.com/github/Oefenweb/cakephp-vat-number-check)

The VatNumberCheck plugin provides the tools to generate social media links (Helper) and handle them (Controller).

## Requirements

* CakePHP 3.5.* or greater.
* PHP 7.1.0 or greater.

## Installation

Clone/Copy the files in this directory into `plugin/VatNumberCheck`

```sh
git@github.com:Oefenweb/cakephp-vat-number-check.git plugin/VatNumberCheck;
```

Or even better, use `composer`.

## Configuration

Ensure the plugin is loaded in `config/bootstrap.php` by calling:

```php
<?php
Plugin::load('VatNumberCheck');
```

## Usage

### Model

Normalizes a VAT number:

```php
<?php
$vatNumber = $this->VatNumberCheck->normalize($vatNumber);
```

Checks a given VAT number:

```php
<?php
$vatNumberValid = $this->VatNumberCheck->check($vatNumber);
```

### Helper

Generates a VAT number check form field:

```php
<?php
echo $this->VatNumberCheck->input('vat_number', ['label' => __('VAT number')]);
```
