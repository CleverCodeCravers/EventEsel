# EventEsel
Eine einfache Web-Anwendung zur Findung eines gemeinsamen Termins

## Anforderungen für die Zeit-Planungs-Anwendung

## Allgemeine Anforderungen
- Einfache Webanwendung für eine vertrauensvolle Gruppe von Freunden.
- Minimalistisches, benutzerfreundliches Design.
- Kleine, MySQL Datenbank zur Datenspeicherung.
- Integration eines separaten Benachrichtigungssystems über eine API.

## Umfragenerstellung und -verwaltung
- Automatische Erstellung von Terminumfragen alle 2 Monate für die nächsten 2 Monate (Montag, Mittwoch, Freitag).
- Generierung eines einzigartigen Abstimmungslinks mit Code für jeden Treffenszeitraum.
- Einfache Abstimmungsfunktion für Termine ohne individuelle Anmeldung, nur über den Abstimmungslink.
- Übersichtliche Anzeige der Umfrageergebnisse und geplanter Treffen für alle Freunde mit Zugang zum Link.
- Kommentarfunktion unter jeder Umfrage zur Absprache.

- [ ] Schau nochmal, was bei Benutzerfreundlichkeit gemacht werden kann:
  - [ ] Möglichkeiten zur Eingabekorrektur... 
  - [ ] Wenn man mit POST an eine Webseite etwas schickt, antwortet die Seite normalerweise mit einem redirect auf ein GET (So verhindert man POST beim Neu-Laden der Seite)
  - [ ] Design (Datumsfelder viel zu breit, ...)
- [ ] Gibt es eine Möglicheit einen minimalen Admin-Zugang zum terminumfrage-edit zu machen?!
