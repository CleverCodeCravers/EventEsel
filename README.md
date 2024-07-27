# EventEsel
Eine einfache Web-Anwendung zur Findung eines gemeinsamen Termins

## Anforderungen für die Zeit-Planungs-Anwendung

### Hosting-Optionen

- xyz.de
  - PHP, MySQL
  - Cronjob

- Strato-Server (vielleicht als was anderes)
  - 7€/monat
  - "sollte mal was hosten"
  - Subdomäne (Platz für weitere Anwendungen lassen)

## Allgemeine Anforderungen
- Einfache Webanwendung für eine vertrauensvolle Gruppe von Freunden.
- Minimalistisches, benutzerfreundliches Design.
- Kleine, MySQL Datenbank zur Datenspeicherung.
- Backend-Bereich läuft vollautomatisch ohne manuelle Eingriffe.
- Integration eines separaten Benachrichtigungssystems über eine API.

## Umfragenerstellung und -verwaltung
- Automatische Erstellung von Terminumfragen alle 2 Monate für die nächsten 2 Monate (Montag, Mittwoch, Freitag).
- Generierung eines einzigartigen Abstimmungslinks mit Code für jeden Treffenszeitraum.
- Einfache Abstimmungsfunktion für Termine ohne individuelle Anmeldung, nur über den Abstimmungslink.
- Übersichtliche Anzeige der Umfrageergebnisse und geplanter Treffen für alle Freunde mit Zugang zum Link.
- Kommentarfunktion unter jeder Umfrage zur Absprache.

## Terminauswahl
- Automatische Auswahl eines Termins, wenn mindestens 4 Freunde an einem Tag Zeit haben.
- Bei Gleichstand wird der Termin zufällig aus den Tagen mit mindestens 4 Zusagen ausgewählt.

## Benachrichtigungsfunktionen
- Erstellung und Versand von Benachrichtigungen für neue Umfragen mit dem Abstimmungslink.
- Implementierung einer optionalen Erinnerungsfunktion für bevorstehende Treffen.
- Generierung von Benachrichtigungen für andere relevante Ereignisse.
- Vorbereitung der Benachrichtigungstexte und Empfängerlisten für die Übergabe an das separate Benachrichtigungssystem.

## Benachrichtigungssystem
- Separates System, das über eine API mit der Planungs-Anwendung kommuniziert.
- Bietet eine einfache Schnittstelle, die nur Empfänger und fertigen Nachrichtentext entgegennimmt.
- Kennt keine Details über den konkreten Anwendungsfall der Planungs-Anwendung.
- Zustellung von Benachrichtigungen über verschiedene Kanäle, aktuell WhatsApp.
- Flexibel und erweiterbar für zukünftige Benachrichtigungskanäle.
