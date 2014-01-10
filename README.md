# VatNumberCheck plugin for CakePHP

[![Build Status](https://travis-ci.org/Oefenweb/cakephp-vat-number-check.png?branch=master)](https://travis-ci.org/Oefenweb/cakephp-vat-number-check) [![Coverage Status](https://coveralls.io/repos/Oefenweb/cakephp-vat-number-check/badge.png)](https://coveralls.io/r/Oefenweb/cakephp-vat-number-check)

## Requirements

* CakePHP 2.0 or greater.
* PHP 5.3.10 or greater.

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