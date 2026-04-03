<?php
function generateUmfrageCode($passwordLength) {
    $validCharacters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $result = "";

    for ($i = 0; $i < $passwordLength; $i++) {
        $randomNumber = random_int(0, strlen($validCharacters) - 1);
        $result .= $validCharacters[$randomNumber];
    }

    return $result;
}
?>