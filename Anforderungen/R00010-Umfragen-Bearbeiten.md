---
id: R00010
titel: "Umfragen nachbearbeiten (Titel, Beschreibung, Termine/Optionen)"
typ: Feature
status: Offen
erstellt: 2026-04-03
---

# R00010: Umfragen nachbearbeiten

## Beschreibung

Als angemeldeter Admin möchte ich bestehende Terminumfragen und Textoptionumfragen bearbeiten können. Über einen Bearbeiten-Button im Dashboard gelange ich zu einem Formular, in dem ich Titel, Beschreibung sowie die Termine bzw. Textoptionen ändern kann. Neue Termine/Optionen können hinzugefügt und bestehende entfernt werden. Beim Entfernen eines Termins/einer Option werden die zugehörigen Antworten ebenfalls entfernt (Cascade). Bestehende Antworten zu nicht-entfernten Einträgen bleiben erhalten.

## Akzeptanzkriterien

### Navigation zur Bearbeitung
- [ ] Im Dashboard gibt es einen "Bearbeiten"-Button (Stift-Icon) pro Umfrage in der Aktionen-Spalte
- [ ] Der Button führt zur Bearbeitungsseite der jeweiligen Umfrage
- [ ] Auch abgeschlossene Umfragen können bearbeitet werden (z.B. Titel/Beschreibung korrigieren)

### Terminumfrage bearbeiten
- [ ] Formular zeigt aktuellen Titel, Beschreibung und alle bestehenden Termine vorausgefüllt
- [ ] Titel und Beschreibung können geändert werden
- [ ] Bestehende Termine können entfernt werden (mit Warnung: "Zugehörige Stimmen werden gelöscht")
- [ ] Neue Termine können hinzugefügt werden (gleiche Datumsfeld-Logik wie bei Erstellung)
- [ ] Nach dem Speichern Redirect zum Dashboard mit Erfolgsmeldung
- [ ] Validierung: Titel erforderlich (max 200 Zeichen), mindestens ein Termin muss verbleiben

### Textoptionumfrage bearbeiten
- [ ] Formular zeigt aktuellen Titel, Beschreibung und alle bestehenden Optionen vorausgefüllt
- [ ] Titel und Beschreibung können geändert werden
- [ ] Bestehende Optionen können entfernt werden (mit Warnung: "Zugehörige Stimmen werden gelöscht")
- [ ] Neue Optionen können hinzugefügt werden
- [ ] Nach dem Speichern Redirect zum Dashboard mit Erfolgsmeldung
- [ ] Validierung: Titel erforderlich (max 200 Zeichen), Beschreibung max 1000 Zeichen, mindestens eine Option muss verbleiben, Optionen max 200 Zeichen

### Datenintegrität
- [ ] Antworten zu nicht-entfernten Terminen/Optionen bleiben vollständig erhalten
- [ ] Entfernte Termine/Optionen werden mit ihren Antworten gelöscht (bestehende Cascade-FKs)
- [ ] Der Umfrage-Code ändert sich nicht durch die Bearbeitung

## Status

- [ ] Offen

## Technische Details

### Neue Dateien

| Datei | Beschreibung |
|-------|--------------|
| `resources/views/terminumfrage/edit.blade.php` | Bearbeitungsformular Terminumfrage |
| `resources/views/textoptionumfrage/edit.blade.php` | Bearbeitungsformular Textoptionumfrage |

### Zu ändernde Dateien

| Datei | Änderung |
|-------|----------|
| `routes/web.php` | Neue Routes: `GET /terminumfrage/{terminumfrage}/edit`, `PUT /terminumfrage/{terminumfrage}`, analog für Textoptionumfrage |
| `app/Http/Controllers/TerminumfrageController.php` | `edit()` und `update()` Methoden hinzufügen |
| `app/Http/Controllers/TextoptionumfrageController.php` | `edit()` und `update()` Methoden hinzufügen |
| `resources/views/dashboard/index.blade.php` | Bearbeiten-Button (Stift-Icon) in Aktionen-Spalte |

### Vermutete Komponenten

| Komponente | Verantwortung |
|------------|---------------|
| `TerminumfrageController@edit` | Bearbeitungsformular mit vorausgefüllten Daten anzeigen |
| `TerminumfrageController@update` | Titel/Beschreibung aktualisieren, Termine sync (hinzufügen/entfernen) |
| `TextoptionumfrageController@edit` | Bearbeitungsformular mit vorausgefüllten Daten anzeigen |
| `TextoptionumfrageController@update` | Titel/Beschreibung aktualisieren, Optionen sync (hinzufügen/entfernen) |

### Tests

| Testdatei | Prüft |
|-----------|-------|
| `tests/Feature/TerminumfrageEditTest.php` | Edit-Seite zeigt vorausgefüllte Daten, Update ändert Titel/Beschreibung, Termine hinzufügen/entfernen, Validierung, Antworten bleiben bei nicht-entfernten Terminen |
| `tests/Feature/TextoptionumfrageEditTest.php` | Edit-Seite zeigt vorausgefüllte Daten, Update ändert Titel/Beschreibung, Optionen hinzufügen/entfernen, Validierung, Antworten bleiben bei nicht-entfernten Optionen |

## Abhängigkeiten

- Abhängig von: R00002, R00004, R00009 (Dashboard als Einstiegspunkt)
- Blockiert: —

## Notizen

- Die Update-Logik für Termine/Optionen muss differenzieren zwischen "bestehend behalten", "bestehend entfernen" und "neu hinzufügen". Ein Ansatz: bestehende Einträge per ID übergeben, neue ohne ID. Fehlende IDs = entfernt.
- Cascade-Delete über Foreign Keys sorgt automatisch dafür, dass Antworten zu entfernten Terminen/Optionen mit gelöscht werden.
