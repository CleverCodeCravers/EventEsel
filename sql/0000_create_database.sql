CREATE DATABASE eventesel;

USE eventesel;

CREATE TABLE Terminumfrage (
  TerminumfrageId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Code VARCHAR(200) NOT NULL,
  Titel VARCHAR(200) NOT NULL,
  Beschreibung MEDIUMTEXT,
  IstAktiv BIT NOT NULL DEFAULT 1,
  ErstelltAmUm DATETIME NOT NULL DEFAULT NOW(),
  IstAbgeschlossen BIT NOT NULL DEFAULT 0
);

CREATE TABLE Textoptionenumfrage (
  TextoptionenumfrageId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Code VARCHAR(200) NOT NULL,
  Titel VARCHAR(200) NOT NULL,
  Beschreibung MEDIUMTEXT,
  IstAktiv BIT NOT NULL DEFAULT 1,
  ErstelltAmUm DATETIME NOT NULL DEFAULT NOW(),
  IstAbgeschlossen BIT NOT NULL DEFAULT 0
);

CREATE TABLE MoeglicherTermin (
  MoeglicherTerminId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Terminumfrage INT NOT NULL,
  Datum DATETIME,
  IstAktiv BIT NOT NULL DEFAULT 1,
  FOREIGN KEY (Terminumfrage) REFERENCES Terminumfrage (TerminumfrageId) ON DELETE CASCADE
);

CREATE TABLE TerminAntwort (
  TerminAntwortId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  ErfasstAmUm DATETIME NOT NULL DEFAULT NOW(),
  Teilnehmer VARCHAR(200) NOT NULL DEFAULT '',
  IstAktiv BIT NOT NULL DEFAULT 1
);

CREATE TABLE TerminAntwortMoeglicherTermin (
  TerminAntwortMoeglicherTerminId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  TerminAntwort INT NOT NULL,
  MoeglicherTermin INT NOT NULL,
  IstAktiv BIT NOT NULL DEFAULT 1,
  FOREIGN KEY (TerminAntwort) REFERENCES TerminAntwort (TerminAntwortId) ON DELETE CASCADE,
  FOREIGN KEY (MoeglicherTermin) REFERENCES MoeglicherTermin (MoeglicherTerminId) ON DELETE CASCADE
);

CREATE TABLE Admin (
  AdminId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Username VARCHAR(100) NOT NULL UNIQUE,
  PasswordHash VARCHAR(255) NOT NULL
);

-- New table for text options
CREATE TABLE Textoption (
  TextoptionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Textoptionenumfrage INT NOT NULL,
  Text VARCHAR(200) NOT NULL,
  IstAktiv BIT NOT NULL DEFAULT 1,
  FOREIGN KEY (Textoptionenumfrage) REFERENCES Textoptionenumfrage (TextoptionenumfrageId) ON DELETE CASCADE
);

CREATE TABLE TextoptionAntwort (
  TextoptionAntwortId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Textoption INT NOT NULL,
  Teilnehmer VARCHAR(200) NOT NULL DEFAULT '',
  ErfasstAmUm DATETIME NOT NULL DEFAULT NOW(),
  IstAktiv BIT NOT NULL DEFAULT 1,
  FOREIGN KEY (Textoption) REFERENCES Textoption (TextoptionId) ON DELETE CASCADE
);