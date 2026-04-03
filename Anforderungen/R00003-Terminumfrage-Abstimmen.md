---
id: R00003
titel: "Terminumfrage abstimmen"
typ: Feature
status: Implementiert
erstellt: 2026-04-03
---

# Terminumfrage abstimmen

## Beschreibung

Als Teilnehmer moechte ich ueber einen Link an einer Terminumfrage teilnehmen koennen, ohne mich anmelden zu muessen.

## Akzeptanzkriterien

- [ ] Teilnehmer oeffnet den Umfrage-Link mit Code-Parameter
- [ ] Titel und Beschreibung der Umfrage werden angezeigt
- [ ] Alle moeglichen Termine werden tabellarisch dargestellt
- [ ] Teilnehmer gibt seinen Namen ein
- [ ] Teilnehmer waehlt per Checkbox die passenden Termine aus
- [ ] Nach Absenden wird die Antwort gespeichert und die Seite neu geladen
- [ ] Erfolgsmeldung wird angezeigt
- [ ] Bei ungueltigem/leerem Code wird eine Fehlermeldung angezeigt

## Implementierung

- `src/terminumfrage.php` — Formular, Abstimmungslogik, Anzeige
- `src/operations/terminumfrage_operations.php` — DB-Abfragen (loadUmfrage, loadMoeglicheTermine, loadTeilnehmerAntworten)
- `src/render/render_results.php` — Ergebnistabelle rendern
