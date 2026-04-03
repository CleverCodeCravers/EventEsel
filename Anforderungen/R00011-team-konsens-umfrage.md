# R00011: Team-Konsens-Umfrage

## Beschreibung

Neuer Umfrage-Typ "Team-Konsens" fuer Entscheidungsfindung in festen Teams. Im Gegensatz zu den bestehenden Umfrage-Typen (wo jeder mit Link und beliebigem Namen abstimmen kann) werden hier die Teilnehmer vorab definiert. Jeder Teilnehmer erhaelt einen eigenen, eindeutigen Link und darf genau 1 Stimme fuer genau 1 Option abgeben. Der Admin verwaltet die Teilnehmerliste und sieht, wer bereits abgestimmt hat.

## Akzeptanzkriterien

### Erstellen (Admin)
- [ ] Admin kann im Dashboard eine neue "Team-Konsens-Umfrage" erstellen
- [ ] Formular mit Titel, Beschreibung (optional) und mindestens 2 Optionen
- [ ] Admin gibt Teilnehmer-Namen ein (mindestens 2, dynamisch erweiterbar wie bei Optionen)
- [ ] Nach Erstellung wird pro Teilnehmer ein eindeutiger Link generiert
- [ ] Admin sieht eine Uebersicht aller Teilnehmer-Links zum Kopieren/Verteilen

### Abstimmen (Teilnehmer)
- [ ] Teilnehmer oeffnet seinen persoenlichen Link (z.B. `/konsens/{code}/t/{teilnehmer_code}`)
- [ ] Teilnehmer sieht Titel, Beschreibung und alle Optionen
- [ ] Teilnehmer kann genau 1 Option waehlen (Radio-Buttons, nicht Checkboxen)
- [ ] Nach Abgabe sieht der Teilnehmer eine Bestaetigung und die bisherigen Ergebnisse
- [ ] Ein Teilnehmer der bereits abgestimmt hat sieht seine Wahl und die Ergebnisse
- [ ] Ein bereits abgestimmter Teilnehmer kann seine Stimme nicht mehr aendern
- [ ] Ungueltige oder bereits verwendete Links zeigen eine Fehlermeldung

### Ergebnis-Anzeige
- [ ] Ergebnis zeigt pro Option die Anzahl der Stimmen und den Prozentsatz
- [ ] Visueller Fortschrittsbalken pro Option
- [ ] Anzeige wie viele Teilnehmer bereits abgestimmt haben (z.B. "3 von 5 haben abgestimmt")
- [ ] Teilnehmer-Namen werden bei der Ergebnis-Anzeige NICHT den Optionen zugeordnet (geheime Wahl)

### Administration
- [ ] Admin kann Teilnehmer nachtraeglich hinzufuegen (Edit-Seite)
- [ ] Admin kann Teilnehmer entfernen (nur wenn noch nicht abgestimmt)
- [ ] Admin sieht im Dashboard welche Teilnehmer bereits abgestimmt haben (nicht was)
- [ ] Admin kann die Konsens-Umfrage abschliessen (keine weiteren Stimmen)
- [ ] Admin kann die Konsens-Umfrage loeschen
- [ ] Konsens-Umfragen erscheinen im Dashboard neben den anderen Umfrage-Typen

### Validierung
- [ ] Titel ist Pflichtfeld (max 200 Zeichen)
- [ ] Mindestens 2 Optionen erforderlich
- [ ] Mindestens 2 Teilnehmer erforderlich
- [ ] Optionen und Teilnehmer-Namen duerfen nicht leer sein
- [ ] Teilnehmer-Namen muessen innerhalb einer Umfrage eindeutig sein

## Status

- [ ] Offen

## Technische Details

### Zielverzeichnisse

| Verzeichnis | Zweck |
|---|---|
| `app/Models/` | Neue Eloquent-Models |
| `app/Http/Controllers/` | Neuer Controller |
| `database/migrations/` | Neue Tabellen |
| `resources/views/konsensumfrage/` | Blade-Templates |
| `routes/` | Neue Routes |
| `tests/Feature/` | Feature-Tests |

### Neue Dateien

| Datei | Beschreibung |
|---|---|
| `app/Models/Konsensumfrage.php` | Model mit code, titel, beschreibung, ist_aktiv, ist_abgeschlossen |
| `app/Models/Konsensoption.php` | Model mit konsensumfrage_id, text |
| `app/Models/Konsensteilnehmer.php` | Model mit konsensumfrage_id, name, code (eindeutiger Teilnehmer-Link), hat_abgestimmt, konsensoption_id (nullable, FK zur gewaehlten Option) |
| `app/Http/Controllers/KonsensumfrageController.php` | Controller fuer Erstellen, Bearbeiten, Anzeigen, Abstimmen |
| `database/migrations/XXXX_create_konsensumfragen_table.php` | Tabelle konsensumfragen |
| `database/migrations/XXXX_create_konsensoptionen_table.php` | Tabelle konsensoptionen |
| `database/migrations/XXXX_create_konsensteilnehmer_table.php` | Tabelle konsensteilnehmer |
| `resources/views/konsensumfrage/create.blade.php` | Formular: Titel, Beschreibung, Optionen, Teilnehmer |
| `resources/views/konsensumfrage/edit.blade.php` | Bearbeiten: Teilnehmer verwalten, Optionen anpassen |
| `resources/views/konsensumfrage/show.blade.php` | Abstimmungsseite fuer Teilnehmer |
| `resources/views/konsensumfrage/links.blade.php` | Uebersicht aller Teilnehmer-Links nach Erstellung |
| `tests/Feature/KonsensumfrageTest.php` | Feature-Tests fuer den gesamten Flow |

### Zu aendernde Dateien

| Datei | Aenderung |
|---|---|
| `routes/web.php` | Neue Routes fuer Konsensumfrage (oeffentlich + Admin) |
| `app/Http/Controllers/DashboardController.php` | Konsensumfragen im Dashboard laden, close/destroy Methoden |
| `resources/views/dashboard/index.blade.php` | Konsensumfragen in der Umfrage-Liste anzeigen |

### Datenbank-Schema

**konsensumfragen**
```
id              - bigIncrements, PK
code            - string(200), unique
titel           - string(200)
beschreibung    - mediumText, nullable
ist_aktiv       - boolean, default true
ist_abgeschlossen - boolean, default false
created_at, updated_at
```

**konsensoptionen**
```
id                  - bigIncrements, PK
konsensumfrage_id   - foreignId, cascadeOnDelete
text                - string(200)
ist_aktiv           - boolean, default true
created_at, updated_at
```

**konsensteilnehmer**
```
id                  - bigIncrements, PK
konsensumfrage_id   - foreignId, cascadeOnDelete
name                - string(200)
code                - string(200), unique
konsensoption_id    - foreignId, nullable, nullOnDelete
hat_abgestimmt      - boolean, default false
ist_aktiv           - boolean, default true
created_at, updated_at
```

### Vermutete Komponenten

| Komponente | Verantwortung |
|---|---|
| `Konsensumfrage` | Umfrage-Entity mit Code-Generierung (wie Terminumfrage) |
| `Konsensoption` | Eine waehlbare Option innerhalb der Umfrage |
| `Konsensteilnehmer` | Ein benannter Teilnehmer mit eigenem Zugangs-Code und Stimme |
| `KonsensumfrageController` | CRUD + Abstimmungslogik |
| `DashboardController` | Integration ins bestehende Dashboard |

### Routes

```
# Oeffentlich (ohne Auth)
GET  /konsens/{code}/t/{teilnehmer_code}    → KonsensumfrageController@show
POST /konsens/{code}/t/{teilnehmer_code}    → KonsensumfrageController@vote

# Admin (mit Auth)
GET  /konsensumfrage-erstellen              → KonsensumfrageController@create
POST /konsensumfrage-erstellen              → KonsensumfrageController@store
GET  /konsensumfrage/{konsensumfrage}/edit   → KonsensumfrageController@edit
PUT  /konsensumfrage/{konsensumfrage}        → KonsensumfrageController@update
GET  /konsensumfrage/{konsensumfrage}/links  → KonsensumfrageController@links
PATCH /konsensumfrage/{konsensumfrage}/close → DashboardController@closeKonsensumfrage
DELETE /konsensumfrage/{konsensumfrage}/delete → DashboardController@destroyKonsensumfrage
```

### Tests

| Testdatei | Prueft |
|---|---|
| `tests/Feature/KonsensumfrageTest.php` | Erstellen mit Optionen und Teilnehmern |
| | Abstimmen ueber persoenlichen Link |
| | Doppeltes Abstimmen wird verhindert |
| | Ungueltige Links werden abgelehnt |
| | Abgeschlossene Umfrage akzeptiert keine Stimmen |
| | Teilnehmer hinzufuegen/entfernen |
| | Ergebnis-Anzeige mit korrekten Zaehler |
| | Dashboard zeigt Konsensumfragen |

## Abhaengigkeiten

- Abhaengig von: keine (nutzt bestehende Patterns aus Terminumfrage/Textoptionumfrage)
- Blockiert: keine

## Notizen

- Das Datenmodell ist bewusst einfacher als bei Terminumfrage (keine Pivot-Tabelle), weil jeder Teilnehmer genau 1 Option waehlt — die Stimme wird direkt als `konsensoption_id` im Teilnehmer gespeichert
- Teilnehmer-Codes werden wie Umfrage-Codes mit `Str::random(16)` generiert
- Die geheime Wahl (Teilnehmer sehen nicht wer was gewaehlt hat) ist wichtig fuer ehrliche Konsens-Findung
- Der Admin sieht nur ob jemand abgestimmt hat, nicht was — das schuetzt die Integritaet der Abstimmung
- Pattern folgt exakt den bestehenden Konventionen: deutsche Model-/Tabellennamen, gleiche Blade-Struktur, gleiche Dashboard-Integration
