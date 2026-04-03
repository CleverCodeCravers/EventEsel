# EventEsel

Eine Webanwendung zur Findung eines gemeinsamen Termins und zur Abstimmung ueber Textoptionen.

## Features

- **Terminumfrage erstellen** — Admin erstellt Umfrage mit Datumsoptionen
- **Terminumfrage abstimmen** — Teilnehmer stimmen ueber einen geteilten Link ab
- **Textoptionumfrage erstellen** — Admin erstellt Umfrage mit Freitextoptionen
- **Textoptionumfrage abstimmen** — Teilnehmer waehlen bevorzugte Optionen
- **Ergebnis-Anzeige** — Echtzeit-Uebersicht aller Stimmen
- **Admin-Login** — Geschuetzter Bereich fuer Umfrageerstellung

## Tech-Stack

Laravel 13 (PHP 8.4) · Blade · Tailwind CSS · SQLite/MySQL · Pest

## Setup

```bash
# Voraussetzung: Laravel Herd oder PHP 8.2+/Composer
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Oeffne http://localhost:8000 — Admin-Login: `admin` / `admin`

## Tests

```bash
php artisan test          # Alle 30 Tests ausfuehren
```

## Lizenz

MIT
