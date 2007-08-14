<?	
$GLOBALS['jeux_base_version'] = 0.12;

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
	ecrire_metas();
}

function jeux_verifier_base(){
	// compatibilite SPIP 1.92
	$showtable = function_exists('sql_showtable')?'sql_showtable':'spip_abstract_showtable';
	// version de la base de donnee
	$version_base = $GLOBALS['jeux_base_version'];
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['jeux_base_version']) )
			|| (($current_version = $GLOBALS['meta']['jeux_base_version']) != $version_base)){
		include_spip('base/jeux_tables');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// ajout du champ 'nom' a la table spip_jeux, si pas deja existant
			$desc = $showtable("spip_jeux", '', true);
			if (!isset($desc['field']['nom'])){
				spip_query("ALTER TABLE spip_jeux ADD `nom` text NOT NULL AFTER `date`");
				// ajout d'un nom par defaut aux jeux existants
				$res = spip_query ("SELECT id_jeu FROM spip_jeux");
				$sans = _T('jeux:sans_type');
				while ($row = spip_fetch_array($res))
					spip_query("UPDATE spip_jeux SET nom='$sans' WHERE id_jeu=".$row['id_jeu']);
			}
			ecrire_meta('jeux_base_version', $current_version=0.10, 'non');
		}
		if ($current_version<($test_version=0.11)){
			// ajout du champ 'titre' a la table spip_jeux, si pas deja existant
			$desc = $showtable("spip_jeux", '', true);
			if (!isset($desc['field']['titre'])){
				spip_query("ALTER TABLE spip_jeux ADD `titre` text NOT NULL AFTER `nom`");
				// ajout d'un titre par defaut aux jeux existants
				$res = spip_query ("SELECT id_jeu FROM spip_jeux");
				$sans = _T('jeux:sans_titre_prive');
				while ($row = spip_fetch_array($res))
					spip_query("UPDATE spip_jeux SET titre='$sans' WHERE id_jeu=".$row['id_jeu']);
			}
			ecrire_meta('agenda_base_version', $current_version=$test_version, 'non');
		}
		if ($current_version<($test_version=0.12)){
			// changement de noms 'titre' => 'titre_prive' et 'nom' => 'type_jeu'
			$desc = $showtable("spip_jeux", '', true);
			if (isset($desc['field']['titre']))
				spip_query('ALTER TABLE `spip_jeux` CHANGE `titre` `titre_prive` TEXT');
			if (isset($desc['field']['nom']))
				spip_query('ALTER TABLE `spip_jeux` CHANGE `nom` `type_jeu` TEXT');
			ecrire_meta('agenda_base_version', $current_version=$test_version, 'non');
		}

		ecrire_metas();
	}
}
	
?>
