<?php
function loadTextoptionUmfrage($conn, $code) {
    $sql = "SELECT * FROM Textoptionenumfrage WHERE Code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    return $stmt->get_result();
}

function loadTextoptionen($conn, $textoptionenumfrageId) {
    $sql = "SELECT * FROM Textoption WHERE Textoptionenumfrage = ? AND IstAktiv = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $textoptionenumfrageId);
    $stmt->execute();
    return $stmt->get_result();
}

function loadTextoptionAntworten($conn, $textoptionenumfrageId) {
    $sql = "SELECT ta.Teilnehmer, ta.Textoption AS TextoptionId FROM TextoptionAntwort ta
            JOIN Textoption t ON ta.Textoption = t.TextoptionId
            WHERE t.Textoptionenumfrage = ? AND ta.IstAktiv = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $textoptionenumfrageId);
    $stmt->execute();
    return $stmt->get_result();
}