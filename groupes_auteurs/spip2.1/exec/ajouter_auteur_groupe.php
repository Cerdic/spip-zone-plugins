<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_ajouter_auteur_groupe_dist() {
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'nom')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip('formulaires/auteur_ajouter');
	ajouter_auteur_groupe_func(_request('id_groupe'), _request('id_auteur'));
	if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
		ajouter_auteur_zone_func(_request('id_groupe'), _request('id_auteur'));
	}
}
?>