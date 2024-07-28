CREATE DATABASE eventesel;

USE eventesel;

CREATE TABLE Terminumfrage (
  TerminumfrageId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Titel VARCHAR(200) NOT NULL,
  Beschreibung MEDIUMTEXT,
  IstAktiv BIT NOT NULL DEFAULT 1,
  ErstelltAmUm DATETIME NOT NULL DEFAULT GETDATE(),
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
  ErfasstAmUm DATETIME NOT NULL DEFAULT GETDATE(),
  Teilnehmer VARCHAR(200) NOT NULL DEFAULT '',
  IstAktiv BIT NOT NULL DEFAULT 1
);

CREATE TABLE TerminAntwortMoeglicherTermin (
  TerminAntwortMoeglicherTerminId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  TerminAntwort INT NOT NULL REFERENCES TerminAntwort (TerminAntwortId),
  MoeglicherTermin INT NOT NULL REFERENCES MoeglicherTermin (MoeglicherTerminId),
  IstAktiv BIT NOT NULL DEFAULT 1
);


