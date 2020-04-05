<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Fonction listant les webmestres
 * Inspirée du plugin autorité.
 *
 * @return string Liste des webmestres avec un lien vers leur fiche auteur dans l'espace privé.
 */
function lister_webmestres() {
	include_spip('abstract_sql', 'base');
	$webmestres = array();

	$auteurs = sql_allfetsel('id_auteur,nom', 'spip_auteurs', "webmestre='oui'");

	foreach ($auteurs as $auteur) {
		$webmestres[] = '<a href="' . generer_url_ecrire('auteur', 'id_auteur=' . $auteur['id_auteur']) . '" title="' . $auteur['nom'] . '">' . $auteur['nom'] . '</a>';
	}
	$webmestres = implode(', ', $webmestres);

	return $webmestres;
}
