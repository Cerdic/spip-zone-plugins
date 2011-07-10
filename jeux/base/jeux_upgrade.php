<?php

$GLOBALS['jeux_base_version'] = 0.17;

function jeux_install($install){
	switch($install) {
		case 'test':
			return isset($GLOBALS['meta']['jeux_base_version']) 
				AND ($GLOBALS['meta']['jeux_base_version']>=$GLOBALS['jeux_base_version']);
		case 'install':
			jeux_verifier_base();
			break;
		case 'uninstall':
			jeux_vider_tables();
			break;
	}
}

function jeux_vider_tables() {
	include_spip('base/abstract_sql');
	sql_drop_table('spip_jeux');
	sql_drop_table("spip_jeux_resultats");
	effacer_meta('jeux_base_version');

}

function jeux_maj_version(&$v1, $v2) {
	echo "MAJ Jeux : $v1 =&gt; $v2<br />";
	ecrire_meta('jeux_base_version', $v1=$v2, 'non');
}

function jeux_verifier_base(){
	// version de la base de donnee
	$version_base = $GLOBALS['jeux_base_version'];
	$current_version = isset($GLOBALS['meta']['jeux_base_version'])
		?$GLOBALS['meta']['jeux_base_version']
		:0.0;
	if ($current_version != $version_base){
		include_spip('base/create');
		include_spip('base/abstract_sql');

		
		include_spip('base/jeux_tables');
		if ($current_version==0.0){
			creer_base();
			jeux_maj_version($current_version, 0.10);
		}
		if ($current_version<($test_version=0.11)){
			// ajout du champ 'nom' a la table spip_jeux, si pas deja existant
			$desc = sql_showtable('spip_jeux', true);
			if (!isset($desc['field']['nom'])){
				sql_alter("TABLE spip_jeux ADD `nom` text DEFAULT '' NOT NULL AFTER `date`");
				// ajout d'un nom par defaut aux jeux existants
				$res = sql_select(array('id_jeu'),array('spip_jeux'));
				$sans = _T('jeux:sans_type');
				while ($row = sql_fetch($res))
					sql_updateq('spip_jeux', array('nom'=>'$sans'), "id_jeu=".$row['id_jeu']);
			}
			// ajout du champ 'titre' a la table spip_jeux, si pas deja existant
			if (!isset($desc['field']['titre'])){
				sql_alter("TABLE spip_jeux ADD `titre` text DEFAULT '' NOT NULL AFTER `nom`");
				// ajout d'un titre par defaut aux jeux existants
				$res = sql_select(array('id_jeu'), 'spip_jeux');
				$sans = _T('jeux:sans_titre_prive');
				while ($row = sql_fetch($res))
					sql_update('spip_jeux', array('titre'=>$sans), "id_jeu=".$row['id_jeu']);
			}
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.12)){
			// changement de noms 'titre' => 'titre_prive' et 'nom' => 'type_jeu'
			$desc = sql_showtable('spip_jeux', true);
			if (isset($desc['field']['titre']))
				sql_alter('TABLE spip_jeux CHANGE `titre` `titre_prive` TEXT');
			if (isset($desc['field']['nom']))
				sql_alter('TABLE spip_jeux CHANGE `nom` `type_jeu` TEXT');
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.13)){
			// suppression de 'titre' et 'nom'
			$desc = sql_showtable('spip_jeux', true);
			if (isset($desc['field']['titre']))
				sql_alter('TABLE spip_jeux DROP `titre`');
			if (isset($desc['field']['nom']))
				sql_alter('TABLE spip_jeux DROP `nom`');
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.14)){
			// ajout de 'total'
			$desc = sql_showtable("spip_jeux_resultats", true);
			if (!isset($desc['field']['total']))
				sql_alter('TABLE spip_jeux_resultats ADD `total` int(12) NOT NULL DEFAULT 0 AFTER `resultat_long`');
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.15)){
			// ajout de 'resultat_unique'
			$desc = sql_showtable('spip_jeux', true);
			if (!isset($desc['field']['resultat_unique']))
				sql_alter("TABLE spip_jeux ADD `resultat_unique` NOT NULL DEFAULT 'non'");
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.16)){
			// fusion de 'resultat_unique' et 'enregistrer_resultat' vers 'type_resultat'
			// types d'enregistrement disponibles : 'defaut', 'aucun', 'premier', 'dernier', 'meilleur', 'tous'
			sql_alter('TABLE spip_jeux CHANGE `resultat_unique` `type_resultat` VARCHAR(10)');
			sql_updateq('spip_jeux',array('type_resultat'=>'premier'),  "`type_resultat`='oui'");
			sql_updateq('spip_jeux',array('type_resultat'=>'dernier'), "`type_resultat`='non'");
			sql_updateq('spip_jeux',array('type_resultat'=>'aucun'), "`enregistrer_resultat`='non'");
			sql_alter('TABLE spip_jeux DROP `enregistrer_resultat`');
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.17)){
			// tenir compte du bug sur les prefixes
			$desc = sql_showtable('spip_jeux', true);
			if (isset($desc['field']['titre']))
				sql_alter('TABLE spip_jeux CHANGE `titre` `titre_prive` TEXT');
			if (isset($desc['field']['nom']))
				sql_alter('TABLE spip_jeux CHANGE `nom` `type_jeu` TEXT');
			if (!isset($desc['field']['resultat_unique']))
				sql_alter("TABLE spip_jeux ADD `resultat_unique` VARCHAR(10) NOT NULL DEFAULT 'non'");
			jeux_maj_version($current_version, $test_version);
		}
		
        
	
	}
}
	
?>
