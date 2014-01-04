<?php

# Cet outil a pour objectif d'aider a migrer des colonnes sql
# qui contiennent des noms de pays (en français, mais ce peut etre dans d'autres langues)
# en les remplacant par l'identifiant du pays de la table spip_pays.

# [todo]? Une evolution possible de ce script pourrait etre de permettre de migrer
# non pas vers l'id du pays, mais vers son code (fr)

# [usage]
# dans un squelette SPIP :
/*

#CACHE{0}
<?php
include_spip('outils/migration_nom_pays');
// pour une colonne 'pays' dans 'spip_mots'
// migre et affiche un resultat des operations
migrer_pays_table_et_bilan('spip_mots', 'pays'); // resume des erreurs uniquement
// ou
migrer_pays_table_et_bilan('spip_mots', 'pays', true); // bilan complet (erreurs + reussite)
?>

*/

include_spip('base/abstract_sql');
include_spip('inc/charsets'); // translitteration
include_spip('inc/texte'); // typo


/**
 * Transforme un nom de pays pour le simplifier
 * et pour tenter du coup d'en retrouver plus. 
 *
 * @param 
 * @return 
**/
function homogeneiser_nom_pays($nom) {
	$nom = trim($nom);
	$nom = html_entity_decode($nom, ENT_COMPAT | ENT_HTML401, 'UTF-8');
	$nom = typo($nom);
	$nom = mb_strtolower($nom);
	$nom = translitteration($nom);
	$nom = str_replace(array('-', '_'), ' ', $nom);
	return $nom;
}

/**
 * Migration d'une colonne d'une table qui contient des noms de pays (en francais)
 * en le remplacant par un identifiant de la table spip_pays
 *
 * La fonction peut etre appelee plusieurs fois :
 * les migrations deja faites ne seront pas impactees.
 * 
 * @param string $table_sql
 * 		Nom de la table SQL dont on veut migrer une colonne
 * @param string $colonne
 * 		Nom de la colonne de la table qui contient des noms de pays
 *
 * @return array
 * 		Tableau de deux tableaux :
 * 		- Liste des traitements oks
 * 		- Liste des erreurs.
 */
function migrer_pays_table($table_sql, $colonne) {
	static $pays_fr = array();

	// calcul des pays
	if (!$pays_fr) {
		// recuperer tous les pays
		$pays = sql_allfetsel(array('id_pays', 'nom'), 'spip_pays');
		// creer un tableau 'nom_fr' => 'id_pays'
		foreach ($pays as $p) {
			$pays_fr[ homogeneiser_nom_pays($p['nom']) ] = $p['id_pays'];
		}
		unset($pays);
	}

	$_id_objet = id_table_objet($table_sql);

	// recuperer tous les elements ayant un pays, tries par pays
	$elements = sql_allfetsel(array($_id_objet, $colonne), $table_sql, "$colonne != ''", '', $colonne);

	// on les groupes par nom de pays
	// 'france' => array(3,6,90)
	$elements_groupes = array();
	foreach ($elements as $e) {
		$id = array_shift($e);
		$nom_fr = array_shift($e);
		$nom_fr = homogeneiser_nom_pays($nom_fr);
		if (!is_array($elements_groupes[ $nom_fr ])) {
			$elements_groupes[ $nom_fr ] = array();
		}
		$elements_groupes[ $nom_fr ][] = $id;
	}
	unset ($elements);

	// pour chaque groupe, on modifie avec la correspondance id_pays
	$erreurs = array();
	$oks = array();
	foreach ($elements_groupes as $nom_fr => $ids) {
		if (is_int($nom_fr)) {
			continue;
		}
		if (count($ids)) {
			if (!isset($pays_fr[$nom_fr])) {
				// erreur pays introuvable
				$erreurs[$nom_fr] = "Pays '$nom_fr' introuvable dans spip_pays. Elements concernes : " . implode(',', $ids);
			} else {

				$id_pays = $pays_fr[$nom_fr];
				sql_updateq($table_sql, array($colonne => $id_pays), sql_in($_id_objet, $ids));
				$oks[$nom_fr] = "Pays '$nom_fr' charge. Elements concernes : " . implode(',', $ids);
			}
		}
	}

	return array($oks, $erreurs);
}


/**
 * La meme chose que  migrer_pays_table()
 * mais affiche un bilan de ce qui est fait.
 *
 * @param string $table_sql
 * 		Nom de la table SQL dont on veut migrer une colonne
 * @param string $colonne
 * 		Nom de la colonne de la table qui contient des noms de pays
 * 
**/
function migrer_pays_table_et_bilan($table, $colonne, $locace = false) {
	// lancer la migration
	list($oks, $erreurs) = migrer_pays_table($table, $colonne);

	if ($locace) {
		if ($erreurs) {
			echo "<h2>Quelques erreurs sont survenues</h2>";
			echo "<pre>\n" . print_r($erreurs, true) . "</pre>";
			echo "<h2>Les oks</h2>";
			echo "<pre>\n" . print_r($oks, true) . "</pre>";
		} else {
			echo "<h2>Tout s'est bien passé</h2>";
			echo "<pre>\n" . print_r($oks, true) . "</pre>";
		}
	} else {
		if ($erreurs) {
			echo "<p><strong>Certains pays n'ont pas étés trouvés :</strong> "
				. implode(', ', array_keys($erreurs)) . "</p>";
		}
	}
}

?>
