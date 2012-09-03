<?php

function liste_js($str, $sep="\r?\n") {
    $str = trim($str);
    if (strlen($str)) {
        return '["' . preg_replace("/$sep/", '", "', addcslashes($str, '"')) . '"]';
    } else {
        return '';
    }
}

?>
