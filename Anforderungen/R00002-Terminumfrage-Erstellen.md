---
id: R00002
titel: "Terminumfrage erstellen"
typ: Feature
status: Implementiert
erstellt: 2026-04-03
---

# Terminumfrage erstellen

## Beschreibung

Als Admin moechte ich eine Terminumfrage erstellen koennen, damit meine Freundesgruppe ueber moegliche Termine abstimmen kann.

## Akzeptanzkriterien

- [ ] Admin kann nach Login eine neue Terminumfrage anlegen
- [ ] Titel (max. 200 Zeichen) und Beschreibung koennen eingegeben werden
- [ ] Mindestens ein Termin (Datum) muss angegeben werden
- [ ] Weitere Termine koennen dynamisch hinzugefuegt und entfernt werden
- [ ] Terminmuster-Generator: Wochentage + Zeitraum waehlen, Termine automatisch generieren
- [ ] Nach Erstellung wird ein eindeutiger Teilnahmelink mit Code angezeigt
- [ ] Umfrage wird in der Datenbank gespeichert (Terminumfrage + MoeglicherTermin)

## Implementierung

- `src/terminumfrage-edit.php` — Formular und Verarbeitung
- `src/helpers/umfrage_helpers.php` — Code-Generierung, Validierung
- `src/helpers/termin_helpers.php` — Eingabe-Validierung
- `sql/0000_create_database.sql` — Tabellen Terminumfrage, MoeglicherTermin
