
# REDAXO-Properties für Templates und Module

Hier können REDAXO-Properties gesetzt werden die man zum Beispiel in Templates und Modulen verwenden kann. Die Properties sind im Backend und Frontend verfügbar. Siehe auch https://redaxo.org/doku/master/eigenschaften#rex-klasse.
Unter dem Menüpunkt **System** können die Properties verwaltet werden.

Oft werden in verschiedenen Templates/Modulen die gleichen Einstellungen wie z.B. bestimmte Artikel-Id's, Anzahl Datensätze usw. benötigt. Durch die Verwendung von Properties können diese Einstellungen (einfach und flexibel) **zentral an einer** Stelle gepflegt und dann in Templates/Modulen verwendet werden.

### Formatierung

Die Formatierung für die Properties ist angelehnt an die bekannten ini-Dateien und die language-Files von REDAXO-Addons, und bewusst einfach gehalten.
Die Properties werden in der Addon-Konfiguration gespeichert und werden bei einem REDAXO-Export auch exportiert.

**Zeilen-Format**:

`propertyName = propertyInhalt`
oder
`propertyName=propertyInhalt`

* jede Property-Einstellung in einer Zeile
* Kommentare beginnen mit '#' am Zeilenanfang
* Inline-Kommentare mit '#' möglich

> **Hinweis:**
Bereits bestehende REDAXO-Properties werden nicht überschrieben!

> **Hinweis:**
Properties können durch setzen eines frei wählbaren PREFIXes für die eigene Übersicht gruppiert werden.
Zum Beispiel: `PREFIX = my_`
Alternativ kann auch die Section-Schreibweise (ini-Dateien) verwendet werden.
Zum Beispiel: `[my_]`

Die Properties werden hier **immer ohne** den Prefix/Section notiert z.B. `HalloText = Servus Welt!` und nur der Zugriff über `rex::getProperty` muss bei gesetztem `PREFIX = my_` oder bei gesetzer Section `[my_]` **mit** dem Prefix erfolgen, also `rex::getProperty('my_HalloText');`.

### Verwendung im Template / Modul

```php
// Zugriff auf Properties ohne gesetztem PREFIX
$value = rex::getProperty('HalloText');

// Zugriff auf Properties mit gesetztem PREFIX = my_
$value = rex::getProperty('my_HalloText');
```

Alternativ zur PHP-Schreibweise kann auch folgende Schreibweise in Templates und Modulen verwendet werden.

```
REX_PROPERTY[key=HalloText]
REX_PROPERTY[key=my_HalloText]
```

## Beispiel für Property-Einstellungen

```ini
# Einstellungen für den News-Bereich
PREFIX = news_
listLimit = 10       # Anzahl Teaser-Einträge für die Startseite   rex::getProperty('news_listLimit')
detailArticle = 123  # Artikel-ID für die News-Anzeige             rex::getProperty('news_detailArticle')

# Diverse Artikel-ID's
PREFIX = id_
search = 123         # Artikel-ID für die Suche                    rex::getProperty('id_search')
basket = 456         # Artikel-ID für den Warenkorb                rex::getProperty('id_basket')
```

oder alternativ mit Sections statt PREFIX

```ini
# Einstellungen für den News-Bereich
[news_]
listLimit = 10       # Anzahl Teaser-Einträge für die Startseite   rex::getProperty('news_listLimit')
detailArticle = 123  # Artikel-ID für die News-Anzeige             rex::getProperty('news_detailArticle')

# Diverse Artikel-ID's
[id_]
search = 123         # Artikel-ID für die Suche                    rex::getProperty('id_search')
basket = 456         # Artikel-ID für den Warenkorb                rex::getProperty('id_basket')
```

## Anwendungs-Beispiel

Für ein Galerie-Modul müssen mindestens 3 Bilder ausgewählt werden.

**Property-Einstellungen**

```ini
# Backend-Properties
PREFIX = be_
minimumGalleryPics = 3

# Frontend-Properties
PREFIX = fe_
...
```

**Modul-Input**

```php
<?php
// Hinweis im Edit-Modus (am Modul-Anfang)
$_imagelist = explode(',', "REX_MEDIALIST[1]");
if (rex_request('save', 'string', '') == '1') {
    if (count($_imagelist) < rex::getProperty('be_minimumGalleryPics')) {
        echo rex_view::warning('Achtung! Keine oder zu wenige Bilder ausgewählt (mind. ' . rex::getProperty('be_minimumGalleryPics') . ')! Es erfolgt keine Ausgabe!');
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
if (count($_imagelist) < rex::getProperty('be_minimumGalleryPics')) {
    if (rex::isBackend()) {
        echo rex_view::warning('Achtung! Keine oder zu wenige Bilder ausgewählt (mind. ' . rex::getProperty('be_minimumGalleryPics') . ')! Es erfolgt keine Ausgabe!');
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
* Durch den Eintrag **load: early** in der package.yml sind die Properties auch in (fast) allen Addons verfügbar