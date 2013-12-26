<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/dump');

/**
 * Charger
 * @return array
 */
function formulaires_saveauto_charger_dist(){

	// ici on liste tout, les tables exclue sont simplement non cochees
	$exclude = lister_tables_noexport();

	$valeurs = array(
		'tout_saveauto' => 'oui',
		'_toutes_tables' => base_lister_toutes_tables('', array(), array(), true),
		'_tables_export' => base_lister_toutes_tables('', array(), $exclude, true),
		'_noexport' => implode(', ', $exclude),
		'_tables_non_spip' => saveauto_lister_tables_ext('')
	);

	return $valeurs;
}

/**
 * Verifier
 * @return array
 */
function formulaires_saveauto_verifier_dist() {
	$erreurs = array();

	return $erreurs;
}

/**
 * Traiter
 * @return array
 */
function formulaires_saveauto_traiter_dist() {

	$options = array();
	$options['manuel'] = true;
	if ($o = _request('structure'))
		$options['structure'] = $o;
	if ($o = _request('donnees'))
		$options['donnees'] = $o;

	if (_request('tout_saveauto')) {
		// ici on prend toutes les tables sauf celles exclues par la configuration noexport
		// On laisse par contre ce traitement dans la fonction de sauvegarde afin de minimiser
		// les includes.
		$tables = array();
	}
	else {
		// On sauvegarde la liste demandées
		$tables = _request('tables_saveauto');
	}

	// On lance la sauvegarde et on traite les erreurs eventuelles
	$sauver = charger_fonction('saveauto','inc');
	$erreur = $sauver($tables, $options);

	if ($erreur) {
		$retour['message_erreur'] =
			_T('saveauto:message_sauvegarde_nok') .
			" ($erreur)";
	}
	else {
		$retour['message_ok'] = _T('saveauto:message_sauvegarde_ok');
	}

	return $retour;
}

/**
 * Lister les tables non-SPIP de la base
 * @return array
 **/ 
 function saveauto_lister_tables_ext($serveur='') {
	spip_connect($serveur);
	$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];

	$p = '/^' . $prefixe . '/';
	$res = array();
	foreach(sql_alltable('%',$serveur) as $t) {
		if (!preg_match($p, $t)) 
			$res[]= $t;
	}
	sort($res);
	return $res;
}

?>
