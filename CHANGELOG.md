# properties - Changelog

## Version 1.3.1 - 10.02.2023

* PHP-Code mit aktuellen REDAXO-Coding-Standards überarbeitet (https://friendsofredaxo.github.io/tricks/development/editor-vscode)
  * `ForProperties::castToType()` überarbeitet
* removed .php-cs-fixer.dist.php

## Version 1.3.0 - 02.02.2023

### Features

* PHP-Code überarbeitet
  * Namespace `FriendsOfRedaxo\ForProperties`
  * added .php-cs-fixer.dist.php, Code überarbeitet mit aktuellen REDAXO Coding Standards
  * Code-Quality (rexstan) Level 9, Extensions: REDAXO SuperGlobals, Bleeding-Edge, Strict-Mode, Deprecation Warnings, PHPUnit, phpstan-dba, report-mixed, dead code
* neue Klasse `ForProperties`
  * Funktionen aus der boot.php in die Klasse übernommen
  * `ForProperties::setProperties()` setzen der Properties
  * `ForProperties::castToType()` Cast property types
  * `ForProperties::getAllProperties()` liefert alle Properties als Array
* REDAXO Mindestversion auf 5.8 gesetzt
* PHP Mindestversion auf 7.3 gesetzt
* README überarbeitet
* CHANGELOG hinzugefügt

### Bigfixes

* Type-Casting float, "81543 München" wurde fälschlicherweise als float gecasted

## Version 1.2.1 - 27.01.2020

* REDAXO Coding-Standards hinzugefügt
* Code optimiert
* Readme überarbeitet

## Version 1.2.0 - 13.08.2018

* Setzen der Properties überarbeitet
  * Probleme bei Properties mit '=' z.b. HTML
* Prüfung und Fehlermeldungen im Backend angepasst

## Version 1.1.0 - 07.08.2018

* REDAXO Mindestversion auf 5.5 gesetzt
* Inline-Kommentare müssen jeweils ein Leerzeichen vor und hinter dem # haben
* Die Properties werden jetzt in den entsprechenden Typ gecasted. JSon für Properties möglich


## Version 1.0.0 - 10.07.2018

* Erste Version
