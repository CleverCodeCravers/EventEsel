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
  Terminumfrage INT NOT NULL REFERENCES Terminumfrage (TerminumfrageId),
  Datum DATETIME,
  IstAktiv BIT NOT NULL DEFAULT 1
);

CREATE TABLE TerminAntwort (
  TerminAntwortId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  ErfasstAmUm DATETIME NOT NULL DEFAULT NOW(),
  Teilnehmer VARCHAR(200) NOT NULL DEFAULT '',
  IstAktiv BIT NOT NULL DEFAULT 1
);

CREATE TABLE TerminAntwortMoeglicherTermin (
  TerminAntwortMoeglicherTerminId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  TerminAntwort INT NOT NULL REFERENCES TerminAntwort (TerminAntwortId),
  MoeglicherTermin INT NOT NULL REFERENCES MoeglicherTermin (MoeglicherTerminId),
  IstAktiv BIT NOT NULL DEFAULT 1
);

CREATE TABLE Admin (
  AdminId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Username VARCHAR(100) NOT NULL UNIQUE,
  PasswordHash VARCHAR(255) NOT NULL
);

-- New table for text options
CREATE TABLE Textoption (
  TextoptionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Textoptionenumfrage INT NOT NULL REFERENCES Textoptionenumfrage (TextoptionenumfrageId),
  Text VARCHAR(200) NOT NULL,
  IstAktiv BIT NOT NULL DEFAULT 1
);

-- New table for text option answers
CREATE TABLE TextoptionAntwort (
  TextoptionAntwortId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Textoption INT NOT NULL REFERENCES Textoption (TextoptionId),
  Teilnehmer VARCHAR(200) NOT NULL DEFAULT '',
  ErfasstAmUm DATETIME NOT NULL DEFAULT NOW(),
  IstAktiv BIT NOT NULL DEFAULT 1
);