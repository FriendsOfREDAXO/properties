
# REDAXO-Properties für Templates und Module

Hier können REDAXO-Properties gesetzt werden die man zum Beispiel in Templates, Modulen und AddOns verwenden kann. Die Properties sind im Backend und Frontend verfügbar. Siehe auch https://redaxo.org/doku/master/eigenschaften#set-property.
Unter dem Menüpunkt **System** können die Properties verwaltet werden (siehe auch `package.yml`).

Oft werden in verschiedenen Templates/Modulen die gleichen Einstellungen wie z.B. bestimmte Artikel-Id's, Anzahl Datensätze usw. benötigt. Durch die Verwendung von Properties können diese Einstellungen (einfach und flexibel) **zentral an einer Stelle** gepflegt und dann in Templates/Modulen/AddOns verwendet werden.

> **Hinweis:** Es können nur Properties angelegt werden die noch nicht existieren. So werden Kollisionen mit den Core-Properties vermieden.

## Formatierung

Die Formatierung für die Properties ist angelehnt an die bekannten ini-Dateien und die language-Files von REDAXO-AddOns, und bewusst einfach gehalten.
Die Properties werden in der AddOn-Konfiguration gespeichert und werden bei einem REDAXO-Export auch exportiert.

**Zeilen-Format**:

`propertyName = propertyInhalt`
oder
`propertyName=propertyInhalt`

* jede Property-Einstellung in einer Zeile
* Kommentare beginnen mit '#' am Zeilenanfang
* Inline-Kommentare mit '#' möglich (ein Leerzeichen vor und nach dem #!)
* automatisches Type-Casting
* JSON-Properties möglich

> **Hinweis:** Bereits bestehende REDAXO-Properties oder Properties aus anderen Addons werden nicht überschrieben!

> **Hinweis:** Properties können durch setzen eines frei wählbaren PREFIXes für die eigene Übersicht gruppiert werden.
Zum Beispiel: `PREFIX = my.`
Alternativ kann auch die Section-Schreibweise (ini-Dateien) verwendet werden.
Zum Beispiel: `[my.]`

## Type-Casting

Die definierten Properties werden automatisch in den entsprechenden Variablen-Typ gecasted.

**Beispiele**

`property = Text` -> (string) Text
`property = 1` -> (integer) 1
`property = 1.0` -> (float) 1
`property = 1,234` -> (float) 1.234
`property = .123` -> (float) 0.123
`property = "asdf"` -> (string) asdf
`property = '"asdf"'` -> (string) "asdf"
`property = true` -> (boolean) true
`propery = FALSE` -> (boolean) false
`property = {"erstens": 1, "zweitens": 2}` -> array(2) { ["erstens"]=> int(1) ["zweitens"]=> int(2) }

> **Hinweis:** JSON-Properties müssen im gültigen JSON-Format notiert werden!

## Beispiele für Property-Einstellungen

Mit gesetztem PREFIX ...

```ini
# Einstellungen für den News-Bereich                                  Zugriff im Modul/Template
PREFIX = news.
listLimit = 10       # Anzahl Teaser-Einträge für die Startseite      rex::getProperty('news.listLimit')
detailArticle = 123  # Artikel-ID für die News-Anzeige                rex::getProperty('news.detailArticle')

# Diverse Artikel-ID's
PREFIX = id.
search = 123         # Artikel-ID für die Suche                       rex::getProperty('id.search')
basket = 456         # Artikel-ID für den Warenkorb                   rex::getProperty('id.basket')
```

oder alternativ mit Sections statt Prefix ...

```ini
# Einstellungen für den News-Bereich                                  Zugriff im Modul/Template
[news.]
listLimit = 10       # Anzahl Teaser-Einträge für die Startseite      rex::getProperty('news.listLimit')
detailArticle = 123  # Artikel-ID für die News-Anzeige                rex::getProperty('news.detailArticle')

# Diverse Artikel-ID's
[id.]
search = 123         # Artikel-ID für die Suche                       rex::getProperty('id.search')
basket = 456         # Artikel-ID für den Warenkorb                   rex::getProperty('id.basket')
```

## Verwendung im Template / Modul

Verwendung der oben definierten Properties im Template oder Modul

```php
// Zugriff auf Properties mit gesetztem PREFIX = news. / Section [news.]
$limit = rex::getProperty('news.listLimit');
```

Alternativ zur PHP-Schreibweise kann auch folgende Schreibweise in Templates verwendet werden.

> **Hinweis:** Funktioniert nicht bei Properties im JSON-Format!

```
REX_PROPERTY[news.listLimit]
REX_PROPERTY[key=news.listLimit]
```

## Anwendungs-Beispiel

Für ein Galerie-Modul müssen mindestens 3 Bilder ausgewählt werden.

**Property-Einstellungen**

```ini
# Backend-Properties
PREFIX = be.
minimumGalleryPics = 3

# Frontend-Properties
PREFIX = fe.
...
```

**Modul-Input**

```php
<?php
// Hinweis im Edit-Modus (am Modul-Anfang)
$_imagelist = explode(',', "REX_MEDIALIST[1]");
if (rex_request('save', 'string', '') == '1') {
    if (count($_imagelist) < rex::getProperty('be.minimumGalleryPics')) {
        echo rex_view::warning('Achtung! Keine oder zu wenige Bilder ausgewählt (mind. ' . rex::getProperty('be.minimumGalleryPics') . ')! Es erfolgt keine Ausgabe!');
    }
}
?>
...
```

**Modul-Output**

```php
<?php
// Hinweis nur im Backend (am Modul-Anfang), im Frontend keine Ausgabe
$_imagelist = explode(',', "REX_MEDIALIST[1]");
if (count($_imagelist) < rex::getProperty('be.minimumGalleryPics')) {
    if (rex::isBackend()) {
        echo rex_view::warning('Achtung! Keine oder zu wenige Bilder ausgewählt (mind. ' . rex::getProperty('be.minimumGalleryPics') . ')! Es erfolgt keine Ausgabe!');
    }
    return;
}
?>

...
```

## Empfehlungen / Sonstiges

* PREFIX= oder [Section] setzen!
* Properties im CamelCase notieren -> https://en.wikipedia.org/wiki/Camel_case
* Es sind auch mehrere PREFIXe/Sections zur Gruppierung von Properties möglich
* Durch den Eintrag **load: early** in der package.yml sind die Properties auch in (fast) allen AddOns verfügbar