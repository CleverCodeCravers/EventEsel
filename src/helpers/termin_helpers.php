<?php
function validateInput($titel, $beschreibung, $termine) {
    if (strlen($titel) > 200) {
        return "Der Titel darf maximal 200 Zeichen lang sein.";
    } elseif (strlen($beschreibung) > 16777215) {
        return "Die Beschreibung ist zu lang.";
    } elseif (empty($termine)) {
        return "Bitte geben Sie mindestens einen möglichen Termin ein.";
    }
    return "";
}

function countZusagen($termin_id, $teilnehmer_antworten) {
  $count = 0;
  foreach ($teilnehmer_antworten as $antworten) {
      if (isset($antworten[$termin_id])) {
          $count++;
      }
  }
  return $count;
}
?>