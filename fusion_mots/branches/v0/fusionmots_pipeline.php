<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function fusionmots_autoriser(){};

function autoriser_fusionmots_bouton_dist($faire, $type, $id, $qui, $opt) {
    return autoriser('fusionner','mots');
}

?>