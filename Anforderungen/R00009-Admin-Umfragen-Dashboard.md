---
id: R00009
titel: "Admin-Dashboard: Umfragen-Übersicht mit Abschließen und Löschen"
typ: Feature
status: Offen
erstellt: 2026-04-03
---

# R00009: Admin-Dashboard mit Umfragen-Übersicht

## Beschreibung

Als angemeldeter Admin möchte ich nach dem Login eine Tabellenansicht aller meiner laufenden Umfragen (Terminumfragen und Textoptionumfragen) sehen. In dieser Übersicht möchte ich einzelne Umfragen abschließen (keine weiteren Abstimmungen möglich) oder löschen können.

## Akzeptanzkriterien

### Dashboard-Ansicht
- [ ] Nach dem Login wird das Dashboard als Startseite angezeigt
- [ ] Tabelle zeigt alle Terminumfragen und Textoptionenumfragen
- [ ] Spalten: Typ (Termin/Textoption), Titel, Erstellt am, Status (Aktiv/Abgeschlossen), Anzahl Stimmen, Aktionen
- [ ] Sortierung nach Erstellungsdatum (neueste zuerst)
- [ ] Abgeschlossene Umfragen werden visuell unterscheidbar dargestellt (z.B. ausgegraut)

### Umfrage abschließen
- [ ] Button "Abschließen" pro aktiver Umfrage in der Aktionen-Spalte
- [ ] Nach dem Abschließen wird `ist_abgeschlossen` auf `true` gesetzt
- [ ] Abgeschlossene Umfragen zeigen auf der öffentlichen Abstimmungsseite eine Meldung "Diese Umfrage ist abgeschlossen" statt des Abstimmungsformulars
- [ ] Die Ergebnisse bleiben auf der öffentlichen Seite weiterhin sichtbar
- [ ] Bestätigungsdialog vor dem Abschließen ("Möchten Sie diese Umfrage wirklich abschließen?")

### Umfrage löschen
- [ ] Button "Löschen" pro Umfrage in der Aktionen-Spalte
- [ ] Löschen entfernt die Umfrage und alle zugehörigen Antworten (Cascade)
- [ ] Bestätigungsdialog vor dem Löschen ("Möchten Sie diese Umfrage wirklich löschen? Alle Stimmen gehen verloren.")
- [ ] Nach dem Löschen Redirect zurück zum Dashboard mit Erfolgsmeldung

### Links zur Umfrage
- [ ] Titel ist ein Link zur öffentlichen Abstimmungsseite
- [ ] Teilnahme-Link ist kopierbar (z.B. als Text sichtbar oder Kopier-Button)

### Navigation
- [ ] Dashboard ist über die Navigation erreichbar ("Dashboard" oder "Meine Umfragen")
- [ ] Nach Login wird auf das Dashboard weitergeleitet (statt direkt zur Terminumfrage-Erstellung)

## Status

- [ ] Offen

## Technische Details

### Zielverzeichnisse

| Verzeichnis | Zweck |
|------------|-------|
| `app/Http/Controllers/` | Dashboard-Controller |
| `resources/views/dashboard/` | Dashboard-View |

### Neue Dateien

| Datei | Beschreibung |
|-------|--------------|
| `app/Http/Controllers/DashboardController.php` | Controller für Dashboard, Abschließen, Löschen |
| `resources/views/dashboard/index.blade.php` | Tabellenansicht aller Umfragen |

### Zu ändernde Dateien

| Datei | Änderung |
|-------|----------|
| `routes/web.php` | Neue Routes: `GET /dashboard`, `PATCH /terminumfrage/{id}/close`, `DELETE /terminumfrage/{id}`, analog für Textoptionumfrage |
| `resources/views/layouts/app.blade.php` | "Dashboard"-Link in Navigation ergänzen |
| `resources/views/terminumfrage/show.blade.php` | Abstimmungsformular ausblenden wenn `ist_abgeschlossen == true` |
| `resources/views/textoptionumfrage/show.blade.php` | Abstimmungsformular ausblenden wenn `ist_abgeschlossen == true` |
| `app/Http/Controllers/Auth/LoginController.php` | Redirect nach Login auf Dashboard ändern |
| `app/Http/Controllers/TerminumfrageController.php` | Vote-Aktion: Prüfung ob Umfrage abgeschlossen |
| `app/Http/Controllers/TextoptionumfrageController.php` | Vote-Aktion: Prüfung ob Umfrage abgeschlossen |

### Vermutete Komponenten

| Komponente | Verantwortung |
|------------|---------------|
| `DashboardController@index` | Alle Umfragen laden und als kombinierte Liste darstellen |
| `DashboardController@closeTerminumfrage` | Terminumfrage abschließen (`ist_abgeschlossen = true`) |
| `DashboardController@closeTextoptionumfrage` | Textoptionumfrage abschließen |
| `DashboardController@destroyTerminumfrage` | Terminumfrage löschen (Cascade durch FK) |
| `DashboardController@destroyTextoptionumfrage` | Textoptionumfrage löschen |

### Tests

| Testdatei | Prüft |
|-----------|-------|
| `tests/Feature/DashboardTest.php` | Dashboard zeigt alle Umfragen, nur für Auth-User |
| `tests/Feature/DashboardTest.php` | Abschließen setzt `ist_abgeschlossen`, öffentliche Seite blockiert Abstimmung |
| `tests/Feature/DashboardTest.php` | Löschen entfernt Umfrage + Antworten |
| `tests/Feature/TerminumfrageTest.php` | Abstimmung auf abgeschlossene Umfrage wird abgelehnt |
| `tests/Feature/TextoptionumfrageTest.php` | Abstimmung auf abgeschlossene Umfrage wird abgelehnt |

## Abhängigkeiten

- Abhängig von: R00002, R00004, R00006 (bestehende Umfrage- und Auth-Features)
- Blockiert: —

## Notizen

- Die Felder `ist_abgeschlossen` und `ist_aktiv` existieren bereits in beiden Umfrage-Models und Migrations
- Cascade-Delete ist bereits über Foreign Keys in den Migrations konfiguriert
- `ist_aktiv` könnte perspektivisch für Soft-Delete genutzt werden, ist aber für diese Anforderung nicht relevant
