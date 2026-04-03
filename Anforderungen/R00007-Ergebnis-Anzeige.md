---
id: R00007
titel: "Ergebnis-Anzeige"
typ: Feature
status: Implementiert
erstellt: 2026-04-03
---

# Ergebnis-Anzeige

## Beschreibung

Als Teilnehmer moechte ich auf der Abstimmungsseite die bisherigen Ergebnisse sehen koennen, um meine Entscheidung informiert zu treffen.

## Akzeptanzkriterien

- [ ] Terminumfrage: Tabelle zeigt pro Termin: Datum, Checkbox, Anzahl bisheriger Stimmen, Namen der Zustimmenden
- [ ] Textoptionumfrage: Tabelle zeigt pro Option: Text, Checkbox, Anzahl Stimmen, Teilnehmernamen
- [ ] Ergebnisse werden bei jedem Seitenaufruf aktuell aus der Datenbank geladen
- [ ] Teilnehmernamen werden HTML-escaped dargestellt

## Implementierung

- `src/render/render_results.php` — Terminumfrage-Ergebnistabelle
- `src/render/render_textoption_results.php` — Textoptionumfrage-Ergebnistabelle
- `src/helpers/termin_helpers.php` — countZusagen() Hilfsfunktion
