<?php
/**
 * Plugin Inscription2 pour SPIP
 * Licence GPL v3
 *
 */


/**
 * Fonction de vérification des tables pour inscription2
 * Appelée à chaque validation du formulaire CFG
 */
function inc_inscription2_verifier_tables_dist(){
	include_spip('base/create');
	
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
	spip_log('INCRIPTION 2 : verification des tables','inscription2');

	include_spip('base/serial');
	global $tables_principales;
    base_serial($tables_principales);

	//definition de la table cible
	$table = "spip_auteurs_elargis";

	if(isset($tables_principales[$table]['field']) and $tables_principales[$table]['key']['PRIMARY KEY']!='id_auteur'){
		if(!isset($tables_principales[$table]['field']['id_auteur'])){
			sql_alter("TABLE ".$table." ADD id_auteur INT NOT NULL PRIMARY KEY");
		}
		sql_alter("TABLE ".$table." DROP id, DROP INDEX id_auteur, ADD PRIMARY KEY (id_auteur)");
	}else if(!$tables_principales[$table]){
		creer_base();
	}

	if (is_array(lire_config('inscription2'))){
		$clef_passee = array();
		foreach(lire_config('inscription2',array()) as $clef => $val) {
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $clef);
			if(!in_array($cle,$clef_passee)){
				if(!in_array($cle,$exceptions_des_champs_auteurs_elargis) and !preg_match("/^(categories|zone|newsletter).*$/", $cle) ){
					if($cle == 'naissance' and !isset($tables_principales[$table]['field'][$cle]) and _request($clef)!=''){
						$tables_principales[$table]['field'][$cle] = "DATE DEFAULT '0000-00-00' NOT NULL";
					}elseif(_request($clef)!='' and !isset($tables_principales[$table]['field'][$cle]) and $cle == 'validite'){
						$tables_principales[$table]['field'][$cle] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
					}elseif(_request($clef)!='' and !isset($tables_principales[$table]['field'][$cle]) and $cle == 'pays'){
						$tables_principales[$table]['field'][$cle] = " int NOT NULL";
					}elseif(!isset($tables_principales[$table]['field'][$cle]) and _request($clef)!=''){
						$tables_principales[$table]['field'][$cle] = "text NOT NULL";
					}
				}
				if(in_array($cle,$exceptions_des_champs_auteurs_elargis)){
					spip_log("INSCRIPTION 2 : le champs $cle est dans les exception de creation de champs...",'inscription2');
				}
				$clef_passee[] = $cle;
			}
		}
	}

	if($GLOBALS['meta']['spiplistes_version'] and !isset($tables_principales[$table]['field']['spip_listes_format']))
		$tables_principales[$table]['field']['spip_listes_format'] = "VARCHAR(8) DEFAULT 'non' NOT NULL";

	maj_tables($table);
	
	// vider le cache de la description sql
	$trouver_table = charger_fonction('trouver_table','base');
	$trouver_table('');
	return;
}
?>
