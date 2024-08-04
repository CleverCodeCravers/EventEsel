<?php
function loadUmfrage($conn, $code) {
    $sql = "SELECT * FROM Terminumfrage WHERE Code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    return $stmt->get_result();
}

function loadMoeglicheTermine($conn, $umfrageId) {
    $sql_termine = "SELECT * FROM MoeglicherTermin WHERE Terminumfrage = ? ORDER BY Datum";
    $stmt_termine = $conn->prepare($sql_termine);
    $stmt_termine->bind_param("i", $umfrageId);
    $stmt_termine->execute();
    return $stmt_termine->get_result();
}

function loadTeilnehmerAntworten($conn, $umfrageId) {
    $sql_antworten = "SELECT ta.Teilnehmer, tatmt.MoeglicherTermin 
                      FROM TerminAntwort ta
                      JOIN TerminAntwortMoeglicherTermin tatmt ON ta.TerminAntwortId = tatmt.TerminAntwort
                      JOIN MoeglicherTermin mt ON tatmt.MoeglicherTermin = mt.MoeglicherTerminId
                      WHERE mt.Terminumfrage = ?
                      ORDER BY ta.Teilnehmer, mt.Datum";
    $stmt_antworten = $conn->prepare($sql_antworten);
    $stmt_antworten->bind_param("i", $umfrageId);
    $stmt_antworten->execute();
    return $stmt_antworten->get_result();
}
?>