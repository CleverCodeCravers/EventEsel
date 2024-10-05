<?php

/**
 * Validates the input for a text option survey
 *
 * @param string $titel The title of the survey
 * @param string $beschreibung The description of the survey
 * @param array $optionen The array of text options
 * @return string Error message if validation fails, empty string otherwise
 */
function validateTextoptionInput($titel, $beschreibung, $optionen) {
    $error_message = "";

    // Validate title
    if (empty($titel)) {
        $error_message .= "Der Titel darf nicht leer sein. ";
    } elseif (strlen($titel) > 200) {
        $error_message .= "Der Titel darf nicht länger als 200 Zeichen sein. ";
    }

    // Validate description (optional, can be empty)
    if (strlen($beschreibung) > 1000) {
        $error_message .= "Die Beschreibung darf nicht länger als 1000 Zeichen sein. ";
    }

    // Validate options
    if (empty($optionen)) {
        $error_message .= "Mindestens eine Textoption muss angegeben werden. ";
    } else {
        foreach ($optionen as $option) {
            if (empty($option)) {
                $error_message .= "Textoptionen dürfen nicht leer sein. ";
                break;
            } elseif (strlen($option) > 200) {
                $error_message .= "Textoptionen dürfen nicht länger als 200 Zeichen sein. ";
                break;
            }
        }
    }

    return $error_message;
}