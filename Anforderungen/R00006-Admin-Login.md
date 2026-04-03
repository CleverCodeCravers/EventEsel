---
id: R00006
titel: "Admin-Login"
typ: Feature
status: Implementiert
erstellt: 2026-04-03
---

# Admin-Login

## Beschreibung

Als Admin moechte ich mich mit Benutzername und Passwort anmelden koennen, um Zugang zur Umfrage-Erstellung zu erhalten.

## Akzeptanzkriterien

- [ ] Login-Seite mit Benutzername und Passwort
- [ ] Passwort wird gegen bcrypt-Hash in der Datenbank geprueft (password_verify)
- [ ] Bei erfolgreichem Login wird Session gesetzt und auf terminumfrage-edit.php weitergeleitet
- [ ] Bei fehlgeschlagenem Login wird Fehlermeldung angezeigt (ohne Hinweis ob User oder Passwort falsch)
- [ ] Edit-Seiten pruefen Session und leiten bei fehlendem Login auf login.php um

## Implementierung

- `src/login.php` — Login-Formular und Authentifizierung
- `src/terminumfrage-edit.php` — Session-Check (Zeile 13-16)
- `src/textoptionumfrage-edit.php` — Session-Check (Zeile 8-11)
- `sql/0000_create_database.sql` — Admin-Tabelle
