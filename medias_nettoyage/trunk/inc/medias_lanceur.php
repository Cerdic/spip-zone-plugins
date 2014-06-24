<?php


if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

include_spip('medias_nettoyage_fonctions');

function medias_lanceur ($fonction, $debut = 0, $fin = 600)
{
    $timer = date_format(date_create(), 'Hi');

    // On vérifie bien que nous sommes bien dans la bonne tranche horaire
    if ($timer >= $debut and $timer < $fin) {

        $charger_fonction = charger_fonction($fonction, 'inc');

        $charger_fonction($debut, $fin);
    }

    return;
}

?>