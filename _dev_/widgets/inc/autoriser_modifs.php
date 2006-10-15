<?php

// retourne vrai si l'utilisateur courant a le droit de modifier un champ
// de l'objet du type et d'id donnes

function inc_autoriser_modifs_dist($type, $champ, $id) {
	if ($type != 'article') {
		echo "pas implemente";
		return false;
	}

	global $connect_id_auteur, $connect_statut;
	$connect_id_auteur = intval($GLOBALS['auteur_session']['id_auteur']);
	$connect_statut = $GLOBALS['auteur_session']['statut'];
	include_spip('inc/auth');
	auth_rubrique($GLOBALS['auteur_session']['id_auteur'], $GLOBALS['auteur_session']['statut']);
	return acces_article($id);
}

?>
