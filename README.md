# Entity Alias module for Drupal 8

## Smart Aliasing
This module does smart aliasing for content types selected by admin under Entity Alias settings form.

## How this module works?
This module appends node id to whatever path alias node has like this `{path-alias}-{node-id}`.
This means every node's url will contain `-nid` at the and of the url.

Also every request which contains dash followed by node id will resolve to the node alias itself.

Here is an example:

* `whatever-123` will resolve to the alias of `node/123`
* `-123` will resolve to the alias of `node/123`
* If not alias, then it will resolve to `node/123`


## How to use
* Install module as a standard Drupal module.
* Enable smart aliasing for content types under `/admin/config/entity_alias/entityaliassettings`

## Dependencies
This module depends on [Pathauto](https://www.drupal.org/project/pathauto) Drupal module


## Development Tools

### Install

```bash
composer install
```

### Usage

```bash
# PHP Code standards audit (Coder, DrupalStrict, etc.)
composer run phpcs

# PHP Copy-paste detection
composer run phpcpd

# Run all code audits
composer run audit
```