---
id: R00005
titel: "Textoptionumfrage abstimmen"
typ: Feature
status: Implementiert
erstellt: 2026-04-03
---

# Textoptionumfrage abstimmen

## Beschreibung

Als Teilnehmer moechte ich ueber einen Link an einer Textoptionumfrage teilnehmen koennen, ohne mich anmelden zu muessen.

## Akzeptanzkriterien

- [ ] Teilnehmer oeffnet den Umfrage-Link mit Code-Parameter
- [ ] Titel und Beschreibung der Umfrage werden angezeigt
- [ ] Alle Textoptionen werden tabellarisch dargestellt
- [ ] Teilnehmer gibt seinen Namen ein
- [ ] Teilnehmer waehlt per Checkbox die gewuenschten Optionen
- [ ] Nach Absenden werden die Antworten gespeichert und die Seite neu geladen
- [ ] Erfolgsmeldung wird angezeigt
- [ ] Bei ungueltigem/leerem Code wird eine Fehlermeldung angezeigt

## Implementierung

- `src/textoptionumfrage.php` — Formular, Abstimmungslogik, Anzeige
- `src/operations/textoptionumfrage_operations.php` — DB-Abfragen
- `src/render/render_textoption_results.php` — Ergebnistabelle rendern
