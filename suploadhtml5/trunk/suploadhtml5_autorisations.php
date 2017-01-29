<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function suploadhtml5_autoriser() {
}

/**
 * Autorisation de supprimer un document temporaire uploader avec la saisie upload
 *
 * @param  string $faire Action demandée
 * @param  string $quoi  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_supprimer_tmp_dist($faire, $quoi, $id, $qui, $opt) {
    // on vérifie que le statut est bien tmp
    $statut = sql_getfetsel('statut', 'spip_documents', 'id_document='.intval($id));

    if ($statut != 'tmp') {
        return false;
    }

    // On vérifie aussi que l'objet est dans la session de la personne.
    include_spip('inc/session');
    if (!in_array($id, session_get('upload'))) {
        return false;
    }

    return true;
}
