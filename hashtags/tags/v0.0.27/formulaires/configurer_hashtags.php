<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_hashtags_charger(){

	$valeurs = array();
	$objets = array();

	$tables = lister_tables_objets_sql();

	# Recuperer les champs editables suceptibles de recevoir du texte
	$pattern = '#^(text|mediumtext|longtext)#';

	foreach($tables as $key => $value)
		if ( $fieldtxt = array_keys(preg_grep($pattern, $value['field'])) )
			$objets[$value['table_objet']] = array('champs'=>$fieldtxt, 'titre' => _T($value['texte_objets']));

	# Recuperer la config des groupes de mot clefs
	if ( $req = sql_allfetsel('id_groupe, titre, tables_liees', 'spip_groupes_mots', "unseul='non'") )
		foreach($req as $k => $v)
			foreach(explode(',', $v['tables_liees']) as $tablesliees)
				$objets[$tablesliees]['groupes'][$v['id_groupe']] = $v['titre'];

	# Tous renvoyer au formulaire
	$valeurs['objets'] = $objets;
	$valeurs['cfg_hashtags'] = lire_config('cfg_hashtags');

	return $valeurs;
}

function formulaires_configurer_hashtags_verifier(){

	$erreurs = array();

	# Si on active les hashtags sur un objet éditorial on doit sélectioner le groupe et au moins un champs
	foreach(_request('cfg_hashtags') as $k => $v)
		if ( $v['active'] AND $v['active'] === 'oui' )
			foreach ( array('champs','groupes') as $obligatoire )
				if ( !count($v[$obligatoire]) )
					$erreurs[$k][$obligatoire] = _T('hashtags:err_champs_et_groupe_obligatoire');

	return $erreurs;
}

function formulaires_configurer_hashtags_traiter(){

	include_spip('inc/meta');
	if ( $cfg_hashtags = _request('cfg_hashtags') ){
		ecrire_config('cfg_hashtags',$cfg_hashtags);
		$desc['message_ok'] = _T('config_info_enregistree');
	}
	else
		$desc['message_erreur'] = _L('erreur');

	return $desc;
}