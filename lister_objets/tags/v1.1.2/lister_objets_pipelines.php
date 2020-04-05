<?php

/**
 * Plugin Lister les objets principaux de SPIP
 * Licence GPL
 * Auteur : Teddy Payet.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function lister_objets_listermenu($flux) {
	$flux['data']['lister_objets'] = array(
		'titre' => _T('lister_objets:titre_lister_objets'),
		'icone' => 'prive/themes/spip/images/lister_objets-16.png',
	);

	return $flux;
}
