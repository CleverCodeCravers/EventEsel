# EventEsel

Webanwendung zur Terminabstimmung und Textoptionsumfragen fuer Freundesgruppen.

## Tech-Stack

- **Backend:** Laravel 13.x (PHP 8.4)
- **Frontend:** Blade-Templates + Tailwind CSS (via CDN)
- **Datenbank:** MariaDB
- **Tests:** Pest (PHPUnit), PHPStan/Larastan (Level 4)
- **Dev-Umgebung:** Laravel Herd (Windows)

## Lokale Entwicklung

```bash
# Voraussetzung: Laravel Herd installiert
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Admin-Login: `admin` / `admin`

## Tests

```bash
php artisan test
```

## Projektstruktur

```
app/Models/           # Eloquent-Models (Admin, Terminumfrage, etc.)
app/Http/Controllers/ # Controller (Auth, Terminumfrage, Textoptionumfrage)
resources/views/      # Blade-Templates
database/migrations/  # Datenbank-Schema
tests/Feature/        # Feature-Tests (HTTP, Auth)
tests/Unit/           # Unit-Tests (Models)
Anforderungen/        # Projektanforderungen (R00001-R00011)
```

## Konventionen

- Deutsche Sprache fuer UI-Texte und Variablennamen im Domainkontext
- Englische Laravel-Konventionen fuer Framework-Code
- Pest-Syntax fuer Tests (kein PHPUnit-Klassenformat)
