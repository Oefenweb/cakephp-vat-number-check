# VatNumberCheck plugin for CakePHP

[![Build Status](https://travis-ci.org/Oefenweb/cakephp-vat-number-check.png?branch=master)](https://travis-ci.org/Oefenweb/cakephp-vat-number-check) [![PHP 7 ready](http://php7ready.timesplinter.ch/Oefenweb/cakephp-vat-number-check/badge.svg)](https://travis-ci.org/Oefenweb/cakephp-vat-number-check) [![Coverage Status](https://coveralls.io/repos/Oefenweb/cakephp-vat-number-check/badge.png)](https://coveralls.io/r/Oefenweb/cakephp-vat-number-check) [![Packagist downloads](http://img.shields.io/packagist/dt/Oefenweb/cakephp-vat-number-check.svg)](https://packagist.org/packages/oefenweb/cakephp-vat-number-check) [![Code Climate](https://codeclimate.com/github/Oefenweb/cakephp-vat-number-check/badges/gpa.svg)](https://codeclimate.com/github/Oefenweb/cakephp-vat-number-check)

## Requirements

* CakePHP 2.4.2 or greater.
* PHP 5.4.16 or greater.

## Installation

Clone/Copy the files in this directory into `app/Plugin/VatNumberCheck`

## Configuration

Ensure the plugin is loaded in `app/Config/bootstrap.php` by calling:

```
CakePlugin::load('VatNumberCheck', array('routes' => true));
```

## Usage

### Model

Normalizes a VAT number:

```
$vatNumber = $this->VatNumberCheck->normalize($vatNumber);
```

Checks a given VAT number:

```
$vatNumberValid = $this->VatNumberCheck->check($vatNumber);
```

### Helper

Generates a VAT number check form field:

```
echo $this->VatNumberCheck->input('vat_number', array('label' => __('VAT number')));
```
