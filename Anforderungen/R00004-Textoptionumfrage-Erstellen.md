---
id: R00004
titel: "Textoptionumfrage erstellen"
typ: Feature
status: Implementiert
erstellt: 2026-04-03
---

# Textoptionumfrage erstellen

## Beschreibung

Als Admin moechte ich eine Umfrage mit frei definierbaren Textoptionen erstellen koennen, um ueber beliebige Themen abstimmen zu lassen.

## Akzeptanzkriterien

- [ ] Admin kann nach Login eine neue Textoptionumfrage anlegen
- [ ] Titel (max. 200 Zeichen) und Beschreibung koennen eingegeben werden
- [ ] Mindestens eine Textoption muss angegeben werden
- [ ] Weitere Optionen koennen dynamisch hinzugefuegt und entfernt werden
- [ ] Jede Option darf max. 200 Zeichen lang sein
- [ ] Nach Erstellung wird ein eindeutiger Teilnahmelink mit Code angezeigt
- [ ] Umfrage wird in der Datenbank gespeichert (Textoptionenumfrage + Textoption)

## Implementierung

- `src/textoptionumfrage-edit.php` — Formular und Verarbeitung
- `src/helpers/umfrage_helpers.php` — Code-Generierung
- `src/helpers/textoption_helpers.php` — Eingabe-Validierung (aktuell auskommentiert!)
- `sql/0000_create_database.sql` — Tabellen Textoptionenumfrage, Textoption
