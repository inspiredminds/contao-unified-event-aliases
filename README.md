[![](https://img.shields.io/packagist/v/inspiredminds/contao-unified-event-aliases.svg)](https://packagist.org/packages/inspiredminds/contao-unified-event-aliases)
[![](https://img.shields.io/packagist/dt/inspiredminds/contao-unified-event-aliases.svg)](https://packagist.org/packages/inspiredminds/contao-unified-event-aliases)

Contao Unified Event Aliases
===========================

This Contao extensions allows you to use the same event alias for the same event translated into different languages.

In Contao, each and every event must have a unique alias. However there are use cases where you might want to use the same alias for translated events. This extension allows you to always use the alias of the main calendar in the front end.

_Note:_ this extension does not actually allow you to _save_ duplicate aliases in the back end. The extension only affects the event URLs of event modules in the front end and provides an additional eventreader module.

## Usage

Assuming that the language settings of your calendars are already properly set up there are only two steps necessary to use the functionality of this extension:

1. Enable the unified aliases in the language settings of your main calendar.
2. Use the _Eventreader for unified aliases_ module instead of the regular eventreader module.

## Attributions

Development funded by:

* [interAKTIV.net GmbH](https://www.interaktiv.net/).
* [priint Group | WERK II](https://www.priint.com/).
