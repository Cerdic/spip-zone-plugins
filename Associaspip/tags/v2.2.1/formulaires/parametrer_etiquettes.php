<?php

function formulaires_parametrer_etiquettes_charger_dist() {

	include_spip('base/abstract_sql');
	$valeurs = array();

	$valeurs['nb_colonne']				= $GLOBALS['association_metas']['etiquette_nb_colonne'];
	$valeurs['nb_ligne']				= $GLOBALS['association_metas']['etiquette_nb_ligne'];
	$valeurs['largeur_page']			= $GLOBALS['association_metas']['etiquette_largeur_page'];
	$valeurs['hauteur_page']			= $GLOBALS['association_metas']['etiquette_hauteur_page'];
	$valeurs['marge_haut_page']			= $GLOBALS['association_metas']['etiquette_marge_haut_page'];
	$valeurs['marge_bas_page']			= $GLOBALS['association_metas']['etiquette_marge_bas_page'];
	$valeurs['marge_droite_page']		= $GLOBALS['association_metas']['etiquette_marge_droite_page'];
	$valeurs['marge_gauche_page']		= $GLOBALS['association_metas']['etiquette_marge_gauche_page'];
	$valeurs['marge_haut_etiquette']	= $GLOBALS['association_metas']['etiquette_marge_haut_etiquette'];
	$valeurs['marge_gauche_etiquette']	= $GLOBALS['association_metas']['etiquette_marge_gauche_etiquette'];
	$valeurs['marge_droite_etiquette']	= $GLOBALS['association_metas']['etiquette_marge_droite_etiquette'];
	$valeurs['espace_etiquettesh']		= $GLOBALS['association_metas']['etiquette_espace_etiquettesh'];
	$valeurs['espace_etiquettesl']		= $GLOBALS['association_metas']['etiquette_espace_etiquettesl'];
	$valeurs['type_sortie']				= $GLOBALS['association_metas']['etiquette_type_sortie'];
	$valeurs['avec_civilite']			= $GLOBALS['association_metas']['etiquette_avec_civilite'];
	return $valeurs;
}

function formulaires_parametrer_etiquettes_verifier_dist() {
	$erreurs = array();

	foreach(array('nb_colonne', 'nb_ligne', 'largeur_page', 'hauteur_page', 'marge_haut_page', 'marge_bas_page', 'marge_gauche_page', 'marge_droite_page', 'marge_gauche_etiquette', 'marge_droite_etiquette', 'espace_etiquettesh', 'espace_etiquettesl') as $value) { // on verifie que les valeurs sont des nombres (idealement des entiers) positifs
		if ($erreur = association_verifier_montant($value) )
			$erreurs[$value] = $erreur;
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
    }
   return $erreurs;
}

function formulaires_parametrer_etiquettes_traiter_dist() {
	include_spip('base/abstract_sql');
	include_spip('inc/acces');
	$table_meta = 'association_metas';
	ecrire_meta('etiquette_avec_civite', _request('avec_civilite'), NULL, $table_meta);
	ecrire_meta('etiquette_nb_colonne', _request('nb_colonne'), NULL, $table_meta);
	ecrire_meta('etiquette_nb_ligne', _request('nb_ligne'), NULL, $table_meta);
	ecrire_meta('etiquette_largeur_page', _request('largeur_page'), NULL, $table_meta);
	ecrire_meta('etiquette_hauteur_page', _request('hauteur_page'), NULL, $table_meta);
	ecrire_meta('etiquette_marge_haut_etiquette', _request('marge_haut_etiquette'), NULL, $table_meta);
	ecrire_meta('etiquette_marge_haut_page', _request('marge_haut_page'), NULL, $table_meta);
	ecrire_meta('etiquette_marge_bas_page', _request('marge_bas_page'), NULL, $table_meta);
	ecrire_meta('etiquette_marge_gauche_page', _request('marge_gauche_page'), NULL, $table_meta);
	ecrire_meta('etiquette_marge_droite_page', _request('marge_droite_page'), NULL, $table_meta);
	ecrire_meta('etiquette_marge_gauche_etiquette', _request('marge_gauche_etiquette'), NULL, $table_meta);
	ecrire_meta('etiquette_marge_droite_etiquette', _request('marge_droite_etiquette'), NULL, $table_meta);
	ecrire_meta('etiquette_espace_etiquettesh', _request('espace_etiquettesh'), NULL, $table_meta);
	ecrire_meta('etiquette_espace_etiquettesl', _request('espace_etiquettesl'), NULL, $table_meta);

	return array('editable' => FALSE, 'message_ok'=> _T('asso:config_enregistree') );
}

?>