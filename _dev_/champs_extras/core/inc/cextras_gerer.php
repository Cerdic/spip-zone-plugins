<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');

function creer_champs_extras($champs, $nom_meta_base_version, $version_cible) {
	$current_version = 0.0;

	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		// cas d'une installation
		if ($current_version==0.0){
			include_spip('base/create');
			// on recupere juste les differentes tables a mettre a jour
			$tables = array();
			foreach ($champs as $c){ 
				if ($table = table_objet_sql($c->table)) {
					$tables[$table] = $table;
				}
			}		
			// on met a jour les tables trouvees
			foreach($tables as $table) {
				maj_tables($table);
			}
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}	
}

/**
 * Supprime les champs extras (objets ChampExtra passes dans le tableau $champs)
 */
function vider_champs_extras($champs, $nom_meta_base_version) {
	// on efface chaque champ trouve
	foreach ($champs as $c){ 
		if ($table = table_objet_sql($c->table) and $c->champ and $c->sql) {
			sql_alter("TABLE $table DROP $c->champ");
		}
	}
	effacer_meta($nom_meta_base_version);	
}



/**
 * 
 * Rechercher les champs non declares mais existants
 * dans la base de donnee en cours
 * (code d'origine : _fil_)
 * 
 */


// Liste les champs anormaux par rapport aux definitions de SPIP
// (aucune garantie que $connect autre que la connexion principale fasse quelque chose)
function extras_champs_anormaux($connect='') {
	// recuperer les tables et champs accessibles
	$tout = extras_base($connect);

	// recuperer les champs SPIP connus
	include_spip('base/auxiliaires');
	include_spip('base/serial');
	$tables_spip = array_merge($GLOBALS['tables_principales'], $GLOBALS['tables_auxiliaires']);

	// chercher ce qui est different
	$ntables = array();
	$nchamps = array();
	foreach ($tout as $table => $champs) {
		if (!isset($tables_spip[$table]['field'])) {
			$nchamps[$table] = $champs;
		} else {
			foreach($champs as $champ => $desc) {
				if (!isset($tables_spip[$table]['field'][$champ])) {
					$nchamps[$table][$champ] = $desc;
				}
			}
		}
	}

	unset($tout);
	if($nchamps) {
		$tout = $nchamps;
	}

	return $tout;
}

// etablit la liste de tous les champs de toutes les tables du connect donne
// ignore la table 'spip_test'
function extras_base($connect='') {
	$champs = array();
	foreach (extras_tables($connect) as $table) {
		if ($table != 'spip_test') {
			$champs[$table] = extras_champs($table, $connect);
		}
	}
	return $champs;
}

// liste les tables dispos ans la connexion $connect
function extras_tables($connect='') {
	$a = array();
	if ($s = sql_showbase(null, $connect)) {
		while ($t = sql_fetch($s, $connect)) {
				$a[] = array_pop($t);
		}
	}
	return $a;
}


// liste les champs dispos ans la table $table de la connexion $connect
function extras_champs($table, $connect) {
	$desc = sql_showtable($table, null, $connect);
	if (is_array($desc['field'])) {
		return $desc['field'];
	} else {
		return array();
	}
}


/** fonctions non utilisees du futur defunt plugin extras2 **

// Liste les connexions disponibles dans config/
function extras_connexions() {
	$connexions = array();
	foreach(preg_files(_DIR_CONNECT.'.*[.]php$') as $fichier) {
		if (lire_fichier($fichier, $contenu)
		AND strpos($contenu, 'spip_connect_db')
		)
			$connexions[] = basename($fichier, '.php');
	}

	return $connexions;
}


// etablit la liste de tous les champs de toutes les tables de toutes les bases dispos
function extras_tout() {
	$champs = array();
	foreach(extras_connexions() as $connect)
		foreach (extras_tables($connect) as $table)
			$champs[$connect][$table] = extras_champs($table, $connect);

	return $champs;
}
*/
?>
