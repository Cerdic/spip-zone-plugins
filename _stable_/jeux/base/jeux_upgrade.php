<?php

$GLOBALS['jeux_base_version'] = 0.13;

function jeux_install($install){
	$version_base = $GLOBALS['jeux_base_version'];
	switch($install) {
		case 'test':
			return isset($GLOBALS['meta']['jeux_base_version']) AND ($GLOBALS['meta']['jeux_base_version']>=$version_base);
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
	spip_query("DROP TABLE jeux");
	spip_query("DROP TABLE jeux_resultats");
	effacer_meta('jeux_base_version');
	if(!defined('_SPIP19300')) ecrire_metas();
}

// compatibilite <= SPIP 1.92
function spip_abstract_showtable_jeux($table, $table_spip = false, $serveur='') {
	return spip_abstract_showtable($table, $serveur, $table_spip);
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
		// compatibilite SPIP 1.92
		$showtable = function_exists('sql_showtable')?'sql_showtable':'spip_abstract_showtable_jeux';
		$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
		
		include_spip('base/jeux_tables');
		if ($current_version==0.0){
			creer_base();
			jeux_maj_version($current_version, 0.10);
		}
		if ($current_version<($test_version=0.11)){
			// ajout du champ 'nom' a la table spip_jeux, si pas deja existant
			$desc = $showtable("spip_jeux", true);
			if (!isset($desc['field']['nom'])){
				spip_query("ALTER TABLE spip_jeux ADD `nom` text NOT NULL AFTER `date`");
				// ajout d'un nom par defaut aux jeux existants
				$res = spip_query ("SELECT id_jeu FROM spip_jeux");
				$sans = _T('jeux:sans_type');
				while ($row = $fetch($res))
					spip_query("UPDATE spip_jeux SET nom='$sans' WHERE id_jeu=".$row['id_jeu']);
			}
			// ajout du champ 'titre' a la table spip_jeux, si pas deja existant
			if (!isset($desc['field']['titre'])){
				spip_query("ALTER TABLE spip_jeux ADD `titre` text NOT NULL AFTER `nom`");
				// ajout d'un titre par defaut aux jeux existants
				$res = spip_query ("SELECT id_jeu FROM spip_jeux");
				$sans = _T('jeux:sans_titre_prive');
				while ($row = $fetch($res))
					spip_query("UPDATE spip_jeux SET titre='$sans' WHERE id_jeu=".$row['id_jeu']);
			}
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.12)){
			// changement de noms 'titre' => 'titre_prive' et 'nom' => 'type_jeu'
			$desc = $showtable("spip_jeux", true);
			if (isset($desc['field']['titre']))
				spip_query('ALTER TABLE `spip_jeux` CHANGE `titre` `titre_prive` TEXT');
			if (isset($desc['field']['nom']))
				spip_query('ALTER TABLE `spip_jeux` CHANGE `nom` `type_jeu` TEXT');
			jeux_maj_version($current_version, $test_version);
		}
		if ($current_version<($test_version=0.13)){
			// suppression de 'titre' et 'nom'
			$desc = $showtable("spip_jeux", true);
			if (isset($desc['field']['titre']))
				spip_query('ALTER TABLE `spip_jeux` DROP `titre`');
			if (isset($desc['field']['nom']))
				spip_query('ALTER TABLE `spip_jeux` DROP `nom`');
			jeux_maj_version($current_version, $test_version);
		}

		if(!defined('_SPIP19300')) ecrire_metas();
	}
}
	
?>