<?php
/* Merci : https://openclassrooms.com/courses/protegez-vous-efficacement-contre-les-failles-web/le-captcha */

function fbantispam_recaptcha_verif($response, $secret, $remoteip)
{
    // On récupère l'IP de l'utilisateur
    $remoteip = $_SERVER['REMOTE_ADDR'];

    $api_url = "https://www.google.com/recaptcha/api/siteverify?secret=" 
        . $secret
        . "&response=" . $response
        . "&remoteip=" . $remoteip ;

    $decode = json_decode(file_get_contents($api_url), true);
    if ($decode['success'] == true) {
        return true;
    }
    else {
        return false;
    }
   
}