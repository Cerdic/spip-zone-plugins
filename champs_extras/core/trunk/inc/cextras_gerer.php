<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');

// retourne la liste des objets valides utilisables par le plugin
// (dont on peut afficher les champs dans les formulaires)
function cextras_objets_valides(){
	
	$objets = array();
	$tables = lister_tables_objets_sql();
	ksort($tables);
	
	foreach($tables as $table => $desc) {
		if ($desc['principale'] == 'oui') {
			$objets[$table] = $desc;
		}
	}

	return $objets;
}


// formater pour les boucles pour 'type'=>'nom'
function cextras_objets_valides_boucle_pour(){
	$objets = array();
	foreach(lister_tables_objets_sql() as $table => $desc) {
		$objets[ $table ] = _T($desc['texte_objets']);
	}
	return $objets;
}


// retourne la liste des types de formulaires de saisie
// utilisables par les champs extras
// (crayons appelle cela des 'controleurs')
function cextras_types_formulaires(){
	$types = array();
	include_spip('inc/saisies');
	foreach(saisies_lister_disponibles() as $saisie => $desc) {
		$types[$saisie] = $desc['titre'];
	}

	return $types;
}


/**
 * Installe des champs extras et
 * gere en meme temps la mise a jour de la meta
 * du plugin concernant la base de donnee
 */
function installer_champs_extras($champs, $nom_meta_base_version, $version_cible, $creer_meta=true) {
	$current_version = 0.0;
	$ok = true;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		// cas d'une installation
		$ok = creer_champs_extras($champs);
		if ($ok and $creer_meta) {
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
	return $ok;
}

/**
 * Créer les champs extras 
 * definies par le lot de saisies donné 
 *
 * @param 
 * @return 
**/
function champs_extras_creer($table, $saisies) {
	if (!$table) {
		return false;
	}
	if (!is_array($saisies) OR !count($saisies)) {
		return false;
	}
	
	$desc = lister_tables_objets_sql($table);

	// parcours des saisies et ajout des champs extras nouveaux dans
	// la description de la table
	foreach ($saisies as $saisie) {
		$nom = $saisie['options']['nom'];
		// champ deja la, on passe
		if (isset($desc['field'][$nom])) {
			continue;
		}
		// la saisie possede une description SQL (sinon, ce n'est pas un champ extra !
		if ($sql = $saisie['options']['sql']) {
			$desc['field'][$nom] = $sql;
		}
	}
	
	// executer la mise a jour
	include_spip('base/create');
	creer_ou_upgrader_table($table, $desc, true, true);
	
}


/**
 * Supprimer les champs extras 
 * definies par le lot de saisies donné 
 *
 * @param 
 * @return 
**/
function champs_extras_supprimer($table, $saisies) {
	if (!$table) {
		return false;
	}
	if (!is_array($saisies) OR !count($saisies)) {
		return false;
	}
	
	$desc = lister_tables_objets_sql($table);
	
	$ok = true;
	foreach ($saisies as $saisie) {
		$nom = $saisie['options']['nom'];
		if (isset($desc['field'][$nom])) {
			$ok &= sql_alter("TABLE $table DROP COLUMN $nom");
		}
	}
	return $ok;
}


/**
 * Modifier les champs extras 
 * definies par le lot de saisies donné   
 *
 * @param 
 * @return 
**/
function champs_extras_modifier($table, $saisies_nouvelles, $saisies_anciennes) {
	
}


/**
 * /!\ À supprimer (API EXTRAS2)
 * Cree en base les champs extras demandes
 * 
 * @param $champs : objet ChampExtra ou tableau d'objets ChampExtra
 */
function creer_champs_extras($champs) {
	if (!$champs) {
		return;
	}
	
	if (!is_array($champs)) 
		$champs = array($champs);
				
	// on recupere juste les differentes tables a mettre a jour
	$tables_modifiees = array();
	foreach ($champs as $c){ 
		if ($table = $c->table) {
			$tables_modifiees[$table] = $table;
		} else {
			// ici on est bien ennuye, vu qu'on ne pourra pas creer ce champ.
			extras_log("Aucune table trouvee pour le champs extras ; il ne pourra etre cree :", true);
			extras_log($c, true);
		}
	}	

	if (!$tables_modifiees) {
		return false;
	}
	
	$tables = lister_tables_objets_sql();
	$tables = array_intersect_key($tables, $tables_modifiees);
	foreach ($champs as $c) {
		if (!isset($tables[$c->table]['field'][$c->champ])) {
			$tables[$c->table]['field'][$c->champ] = $c->sql;
		}
	};
	

	// executer la mise a jour
	include_spip('base/create');
	foreach ($tables as $table => $desc) {
		creer_ou_upgrader_table($table, $desc, true, true);
	}

	// pour chaque champ a creer, on verifie qu'il existe bien maintenant !
	$trouver_table = charger_fonction('trouver_table','base');
	$trouver_table(''); // recreer la description des tables.
	$retour = true;
	foreach ($champs as $c){
		if ($objet = table_objet($c->table)) {
			$desc = $trouver_table($objet);
			if (!isset($desc['field'][$c->champ])) {
				extras_log("Le champ extra '" . $c->champ . "' sur $objet n'a pas ete cree :(", true);
				$retour = false;
			}
		} else {
			$retour = false;
		}
	}
	return $retour;
}

/**
 * Desinstaller des champs extras
 * et gerer la suppression de la meta du plugin concernant 
 * la base de donnee
 */
function desinstaller_champs_extras($champs, $nom_meta_base_version) {
	vider_champs_extras($champs);
	effacer_meta($nom_meta_base_version);	
}

/**
 * Supprime les champs extras 
 * @param $champs : objet ChampExtra ou tableau d'objets ChampExtra
 */
function vider_champs_extras($champs) {
	if (!is_array($champs)) 
		$champs = array($champs);
		
	// on efface chaque champ trouve
	foreach ($champs as $c){ 
		if ($table = $c->table and $c->champ and $c->sql) {
			sql_alter("TABLE $table DROP $c->champ");
		}
	}	
}



/**
 * 
 * Rechercher les champs non declares mais existants
 * dans la base de donnee en cours
 * (code d'origine : _fil_)
 * 
 */

// liste les tables et les champs que le plugin et spip savent gerer
function extras_champs_utilisables($connect='') {
	$tout = extras_champs_anormaux($connect);
	$objets = cextras_objets_valides();
	return array_intersect_key($tout, $objets);
}

// Liste les champs anormaux par rapport aux definitions de SPIP
// (aucune garantie que $connect autre que la connexion principale fasse quelque chose)
function extras_champs_anormaux($connect='') {
	static $tout = false;
	if ($tout !== false) {
		return $tout;
	}
	// recuperer les tables et champs de la base de donnees
	// les vrais de vrai dans la base sql...
	$tout = extras_base($connect);

	// recuperer les champs SPIP connus
	// si certains ne sont pas declares alors qu'ils sont presents
	// dans la base sql, on pourra proposer de les utiliser comme champs
	// extras (plugin interface).
	include_spip('base/objets');
	$tables_spip = lister_tables_objets_sql();

	// chercher ce qui est different
	$ntables = array();
	$nchamps = array();
	// la table doit être un objet editorial
	$tout = array_intersect_key($tout, $tables_spip);
	foreach ($tout as $table => $champs) {
		// la table doit être un objet editorial principal
		if ($tables_spip[$table]['principale'] == 'oui') {
			// pour chaque champ absent de la déclaration, on le note dans $nchamps.
			foreach($champs as $champ => $desc) {
				if (!isset($tables_spip[$table]['field'][$champ])) {
					if (!isset($nchamps[$table])) {
						$nchamps[$table] = array(); 
					}
					$nchamps[$table][$champ] = $desc;
				}
			}
		}
	}

	if($nchamps) {
		$tout = $nchamps;
	} else {
		$tout = array();
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

// liste les tables dispos dans la connexion $connect
function extras_tables($connect='') {
	$a = array();
	$taille_prefixe = strlen( $GLOBALS['connexions'][$connect ? $connect : 0]['prefixe'] );

	if ($s = sql_showbase(null, $connect)) {
		while ($t = sql_fetch($s, $connect)) {
				$t = 'spip' . substr(array_pop($t), $taille_prefixe);
				$a[] = $t;
		}
	}
	return $a;
}


// liste les champs dispos dans la table $table de la connexion $connect
function extras_champs($table, $connect) {
	$desc = sql_showtable($table, true, $connect);
	if (is_array($desc['field'])) {
		return $desc['field'];
	} else {
		return array();
	}
}

?>
