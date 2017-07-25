<?php
/*
 * Plugin En Travaux
 * (c) 2006-2009 Arnaud Ventre, Cedric Morin
 * Distribue sous licence GPL
 *
 */


/**
 * Charger
 * @return array
 */
function formulaires_configurer_entravaux_charger_dist(){

	$valeurs = array(
		'accesferme' => is_entravaux()?'1':'',
		'message' => isset($GLOBALS['meta']['entravaux_message']) ? $GLOBALS['meta']['entravaux_message'] : '',
		'disallow_robots' => isset($GLOBALS['meta']['entravaux_disallow_robots']) ? $GLOBALS['meta']['entravaux_disallow_robots'] : '',
	);

	return $valeurs;
}

/**
 * Traiter
 * @return array
 */
function formulaires_configurer_entravaux_traiter_dist(){

	include_spip('entravaux_administrations');
	if (_request('accesferme')) {
		entravaux_poser_verrou('accesferme');
	} else {
		entravaux_lever_verrou('accesferme');
	}


	foreach (array('message','disallow_robots') as $k) {
		ecrire_meta('entravaux_' . $k, _request($k) ? _request($k) : '', 'non');
	}

	return array('message_ok' => _T('config_info_enregistree'));
}