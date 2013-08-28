<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2013 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Définitions des tables et insertion dans les champs extras
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function inscription3_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['geo_pays']='geo_pays';

	return $interface;
}

function inscription3_declarer_tables_principales($tables_principales){
	/**
	 * A partir de Inscription 2 (0.70)
	 * on utilise le plugin geographie pour gerer les pays.
	 * on ne le rend pas obligatoire en creant la table spip_geo_pays
	 * si le plugin n'est pas installe
	 */
	
	/* penser a modifier si le plugin geographie modifie cette table */
	$spip_geo_pays = array(
			"id_pays"	=> "smallint NOT NULL",
			"code_iso"	=> "varchar(2) NOT NULL default ''",
			"nom"	=> "text DEFAULT '' NOT NULL"
	);
	$spip_geo_pays_key = array(
			"PRIMARY KEY"		=> "id_pays",
			"UNIQUE KEY code_iso"	=> "code_iso"
	);
	$tables_principales['spip_geo_pays'] = array(
		'field' => &$spip_geo_pays,
		'key' => &$spip_geo_pays_key);

	return $tables_principales;
}

function inscription3_declarer_champs_extras($champs = array()){
	include_spip('inc/config');

	$exceptions_des_champs_auteurs_elargis = pipeline('i3_exceptions_des_champs_auteurs_elargis',array());

	$definitions_champs = pipeline('i3_definition_champs',array());

	$config = lire_config('inscription3');
	if(!is_array($config))
		return $champs;

	$champ_presents = array();

	foreach($config as $clef => $val) {
		if(!preg_match(",(_nocreation).*$,", $clef)){
			if(!in_array($clef,$champ_presents) && ($val == 'on')){
				$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $clef);
				if(!in_array($cle,$exceptions_des_champs_auteurs_elargis) and ($val == 'on')){
					$champ_presents[] = $cle;
					if(array_key_exists($cle,$definitions_champs)){
						$array_defaut = array(
							'saisie' => 'input', // type de saisie
							'options' => array(
								'nom' => $cle,
								'label' => _T('inscription3:label_'.$cle), // chaine de langue 'prefix:cle'
								'sql' => "text NOT NULL DEFAULT ''", // declaration sql
								'obligatoire' => (isset($config[$cle.'_obligatoire']) && $config[$cle.'_obligatoire'] == 'on') ? true : false
							),
							'verifier' => array()
						);
						$array_defaut['options'] = array_merge($array_defaut['options'],(!test_espace_prive() && (isset($GLOBALS['visiteur_session']['statut']) && $GLOBALS['visiteur_session']['statut'] != '0minirezo') && ($config[$cle.'_fiche_mod'] != 'on')) ? array('readonly'=>'oui','size'=>'30') : array('size'=>'30'));
						$array['saisie'] = isset($definitions_champs[$cle]['saisie']) ? $definitions_champs[$cle]['saisie'] : $array_defaut['saisie'];
						$array['options'] = array_merge($array_defaut['options'],is_array($definitions_champs[$cle]['options']) ? $definitions_champs[$cle]['options'] : array());
						$array['verifier'] = array_merge($array_defaut['verifier'],is_array($definitions_champs[$cle]['verifier']) ? $definitions_champs[$cle]['verifier'] : array());
						$array['source'] = 'inscription3';
						$champs['spip_auteurs'][$cle] = $array;
					}
					else
						$champs['spip_auteurs'][$cle] = array(
							'saisie' => 'input', // type de saisie
							'options' => array(
								'nom' => $cle,
								'label' => _T('inscription3:label_'.$cle), // chaine de langue 'prefix:cle'
								'sql' => "text NOT NULL DEFAULT ''", // declaration sql
								'obligatoire' => (isset($config[$cle.'_obligatoire']) && $config[$cle.'_obligatoire'] == 'on') ? true : false
							),
							'verifier' => array()
						);
						$champs['spip_auteurs'][$cle]['source'] = 'inscription3';
						$champs['spip_auteurs'][$cle]['options'] = array_merge($champs['spip_auteurs'][$cle]['options'],(!test_espace_prive() && (isset($GLOBALS['visiteur_session']['statut']) && $GLOBALS['visiteur_session']['statut'] != '0minirezo') && ($config[$cle.'_fiche_mod'] != 'on')) ? array('readonly'=>'oui','size'=>'30') : array('size'=>'30'));
				}
			}
		}
	}
	return $champs;
}

?>