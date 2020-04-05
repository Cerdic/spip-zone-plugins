<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Tester si un objet ce trouve dans le panier de l'auteur connecté.
 *
 * @param mixed $id_objet
 * @param mixed $objet
 * @access public
 * @return mixed
 */
function dans_panier($id_objet, $objet) {
    include_spip('action/editer_liens');
    include_spip('inc/session');
    $objet_panier = objet_trouver_liens(
        array('paniers' => session_get('id_panier')),
        array($objet => $id_objet)
    );

    return (empty($objet_panier)) ? false : true;
}
