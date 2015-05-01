<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Définitions des tables et insertion dans les champs extras
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function inscription3_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_auteurs'][] = 'auteurs_liens';

	$interface['table_des_tables']['geo_pays']='geo_pays';
	$interface['table_des_tables']['auteurs_liens']='auteurs_liens';

	return $interface;
}

function inscription3_declarer_tables_principales($tables_principales){
	/*
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
	$exceptions_des_champs_auteurs_elargis = pipeline('i3_exceptions_des_champs_auteurs_elargis',array());
	$definitions_champs = pipeline('i3_definition_champs',array());

	if(function_exists('lire_config')){
		$config = lire_config('inscription3');
		if(($config == '') OR !is_array($config)){
			return $champs;
		}

		$champ_presents = array();

		foreach($config as $clef => $val) {
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $clef);
			if(!in_array($cle,$champ_presents) && ($val == 'on')){
				if(!in_array($cle,$exceptions_des_champs_auteurs_elargis) and !preg_match(",(categories|zone|newsletter).*$,", $cle)  and ($val == 'on')){
					$champ_presents[] = $cle;
					if(array_key_exists($cle,$definitions_champs)){
						$array_defaut = array(
							'table' => 'auteur', // sur quelle table ?
							'champ' => $cle, // nom sql
							'label' => 'inscription3:label_'.$cle, // chaine de langue 'prefix:cle'
							'saisie_externe' => true,
							'type' => 'input', // type de saisie
							'saisie_parametres' => (!test_espace_prive() && (isset($GLOBALS['visiteur_session']['statut']) && ($GLOBALS['visiteur_session']['statut'] != '0minirezo')) && ($config[$cle.'_fiche_mod'] != 'on')) ? array('readonly'=>'oui','size'=>'30') : array('size'=>'30'),
							'sql' => "text NOT NULL", // declaration sql
							'obligatoire' => ($config[$cle.'_obligatoire'] == 'on') ? true : false
						);
						$array = array_merge($array_defaut,$definitions_champs[$cle]);
						$champs[] = new ChampExtra($array);
					}
					else
						$champs[] = new ChampExtra(array(
							'table' => 'auteur', // sur quelle table ?
							'champ' => $cle, // nom sql
							'label' => 'inscription3:label_'.$cle, // chaine de langue 'prefix:cle'
							'saisie_externe' => true,
							'type' => 'input', // type de saisie
							'saisie_parametres' => (!test_espace_prive() && (isset($GLOBALS['visiteur_session']['statut']) && ($GLOBALS['visiteur_session']['statut'] != '0minirezo')) && ($config[$cle.'_fiche_mod'] != 'on')) ? array('readonly'=>'oui','size'=>'30') : array('size'=>'30'),
							'sql' => "text NOT NULL", // declaration sql
							'obligatoire' => ($config[$cle.'_obligatoire'] == 'on') ? true : false
						));
				}
			}
		}
	}
	return $champs;
}

function inscription3_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_auteurs_liens = array(
		"id_auteur" => "bigint(21) NOT NULL",
		"id_objet" 	=> "bigint(21) NOT NULL",
		"objet" 	=> "VARCHAR(25) DEFAULT '' NOT NULL",
		"type" 		=> "VARCHAR(25) DEFAULT '' NOT NULL");

	$spip_auteurs_liens_key = array(
		"PRIMARY KEY" 	=> "id_auteur,id_objet,objet",
		"KEY id_objet" => "id_auteur");

	$tables_auxiliaires['spip_auteurs_liens'] = array(
		'field' => &$spip_auteurs_liens,
		'key' => &$spip_auteurs_liens_key);

	return $tables_auxiliaires;
}

?>