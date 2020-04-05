<?php
/**
 * Fonctions utiles au plugin Court-jus
 *
 * @plugin	   Court-jus
 * @copyright  2014
 * @author	   Phenix
 * @licence	   GNU/GPL
 * @package	   SPIP\Courtjus\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Créer la balise #URL_RUBRIQUE et y affecter les fonctions du courtjus
 *
 * @param champ $p
 * @access public
 * @return champ
 */
function balise_URL_RUBRIQUE_dist($p) {

	$id_rubrique = interprete_argument_balise(1, $p);
	if (!$id_rubrique) {
		$id_rubrique = champ_sql('id_rubrique', $p);
	}

	$code = "courtjus_calculer_rubrique($id_rubrique)";
	$p->code = $code;
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Calculer l'url de la rubrique
 *
 * @param int $id_rubrique
 * @access public
 * @return string
 */
function courtjus_calculer_rubrique($id_rubrique) {
	include_spip('inc/config');

	$exclusion_mot = lire_config('courtjus/mot_exclusion');
	if (!empty($exclusion_mot)) {
		// Construire le where
		$where = array(
			sql_in('id_mot', $exclusion_mot),
			'objet='.sql_quote('rubrique'),
			'id_objet='.intval($id_rubrique)
		);

		// Présence d'un mot clé d'exclusion ?
		$exclusion = sql_getfetsel('id_objet', 'spip_mots_liens', $where);
		if (!is_null($exclusion)) {
			return generer_url_entite($id_rubrique, 'rubrique', '', '', true);
		}
	}

	// Secteur exclu ?
	$secteur_exclusion = lire_config('courtjus/secteur_exclusion');
	if (!empty($secteur_exclusion)) {
		// On récupère le secteur de la rubrique
		$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique));
		if (in_array($id_secteur, $secteur_exclusion)) {
			return generer_url_entite($id_rubrique, 'rubrique', '', '', true);
		}
	}

	$par_rubrique = lire_config('courtjus/squelette_par_rubrique');
	// Si on n'intervient pas sur les squelettes par rubrique
	if (empty($par_rubrique)) {
		// Si on trouve une squelette spécifique à cette rubrique,
		// et que l'option est activé, on renvoie l'URL rubrique
		// Cela ne gère pas pour les sous-rubrique cependant
		if (find_in_path('rubrique='.$id_rubrique.'.html')
			or find_in_path('rubrique-'.$id_rubrique.'.html')) {
			return generer_url_entite($id_rubrique, 'rubrique', '', '', true);
		}

		// Pour gérer les fichiers rubrique-X parent, on va tester chaque parent
		include_spip('public/quete');
		$parent = quete_parent($id_rubrique);
		do {
			if (find_in_path('rubrique-'.$parent.'.html')) {
				return generer_url_entite($id_rubrique, 'rubrique', '', '', true);
			}
		} while (($parent = quete_parent($parent)) > 0);
	}

	$objets_in_rubrique = courtjus_objets_in_rubrique($id_rubrique);

	// On récupère l'éventuel objet de redirection
	$objet = courtjus_trouver_objet($id_rubrique, $objets_in_rubrique);

	if ($objet) {
		return $objet;

		// Sinon, on cherche les enfant de la rubrique
		// et on cherche un objet dedans
	} elseif (lire_config('courtjus/rubrique_enfant') and count($objets_in_rubrique) <= 0) {

		// On chercher parmit les enfants de la rubrique
		$objet = courtjus_trouver_objet_enfant($id_rubrique, $objets_in_rubrique);

		// Si on a trouver un objet enfant.
		if ($objet) {
			return $objet;
		}
	}

	return generer_url_entite($id_rubrique, 'rubrique', '', '', true);
}

/**
 * Fonction récurcive de recherche dans les sous-rubriques
 *
 * @param int $id_rubrique
 * @access public
 * @return string
 */
function courtjus_trouver_objet_enfant($id_rubrique, $objets_in_rubrique) {

	// Chercher les enfants de la rubrique
	$enfants = courtjus_quete_enfant($id_rubrique);

	// On cherche un éventuel objet dans les premiers enfants
	$objet = false;
	while (list($key,$enfant) = each($enfants) and !$objet) {
		$objets_in_rubrique = courtjus_objets_in_rubrique($enfant);

		$objet = courtjus_trouver_objet($enfant, $objets_in_rubrique);

		// S'il n'y a pas d'objet au premier niveau on lance la récurcivité
		// pour trouver continuer de descendre dans la hiérachie.
		if (!$objet) {
			$objet = courtjus_trouver_objet_enfant($enfant, $objets_in_rubrique);
		}
	}
	// On renvoie l'url
	return $objet;
}


/**
 * Renvoie le tableau des objets qui possède un id_rubrique.
 * (sans la table spip_rubrique)
 *
 * @access public
 * @return array
 */
function courtjus_trouver_objet_rubrique() {
	// On va cherché les différent objets intaller sur SPIP
	$objets = lister_tables_objets_sql();

	// On va filtrer pour n'avoir que les objet avec un id_rubrique
	$objet_in_rubrique = array();
	foreach ($objets as $table => $data) {
		// Si on trouve "id_rubrique" dans la liste des champs, on garde
		// On exclue la table des rubriques de SPIP automatiquement
		// On exclu aussi éléments marqué comme exclu dans la config
		if (array_key_exists('id_rubrique', $data['field'])
			and $table != table_objet_sql('rubrique')
			and !in_array($table, lire_config('courtjus/objet_exclu'))) {
			// On garde le champ qui fait office de titre pour l'objet
			// dans le tableau afin de pouvoir faire un classement par num titre.
			$objet_in_rubrique[] = array($table, $data['titre']);
		}
	}

	return $objet_in_rubrique;
}

/**
 * Retrouver les objets contenu dans une rubrique
 *
 * @param int $id_rubrique
 * @access public
 * @return array
 */
function courtjus_objets_in_rubrique($id_rubrique) {
	// On va compter le nombre d'objet présent dans la rubrique
	$tables = courtjus_trouver_objet_rubrique();

	// on va compter le nombre d'objet qu'il y a dans la rubrique.
	$objets_in_rubrique = array();

	// On boucle sur tout les table qui pourrait être ratacher à une rubrique
	foreach ($tables as $table) {
		// Simplification des variables. On a besoin du titre pour trouver le num titre
		list($table, $titre) = $table;
		// L'objet
		$objet = table_objet($table);
		// l'identifiant de l'objet
		$champs_id = id_table_objet($table);
		// Le champ qui contient la date
		$champ_date = objet_info($objet, 'date');

		// Les champs qui seront utilisé pour la requête.
		$champs = array(
			$champs_id,
			$titre,
			// Convertir la date de l'objet en timestamp, cela permettra une comparaison rapide
			'UNIX_TIMESTAMP('.$champ_date.') AS '.$champ_date
		);

		// Le where
		$where = array(
			'id_rubrique='.intval($id_rubrique),
			'statut='.sql_quote('publie')
		);

		// Est-ce qu'il faut prendre en compte la langue ?
		include_spip('formulaires/configurer_multilinguisme');
		if (table_supporte_trad($table)) {
			$where[] = 'lang='.sql_quote($GLOBALS['spip_lang']);
		}

		// On récupère les objets de la rubrique.
		$objets_rubrique = sql_allfetsel($champs, $table, $where);

		// On boucle sur les objets à l'intérique de la rubrique.
		foreach ($objets_rubrique as $objet_rubrique) {

			$num_titre = recuperer_numero($objet_rubrique['titre']);

			// On créer le tableau contenant les données de l'objet
			$objets_in_rubrique[] = array(
				'id_objet' => $objet_rubrique[$champs_id],
				'objet' => $objet,
				'num_titre' => $num_titre,
				'date' => $objet_rubrique[$champ_date]
			);
		}
	}

	return $objets_in_rubrique;
}


/**
 * Fonction qui traite les objets d'une rubrique et renvoie l'url du court-cuircuit.
 *
 * @param int $id_rubrique
 * @access public
 * @return string
 */
function courtjus_trouver_objet($id_rubrique, $objets_in_rubrique) {

	// Aller chercher les filtres
	include_spip('inc/filtres');
	include_spip('inc/config');

	// On récupère le configuration du plugin
	$config = lire_config('courtjus');

	// Maintenant qu'on a le tableau des objets de la rubrique on compte
	$nb_objet = count($objets_in_rubrique);

	// Si on est à 0 objet, on descend dans une sous rubrique
	if ($nb_objet <= 0) {
		// On renvoie false pour déclencher éventuellement la recherche dans une sous rubrique
		return false;
	} elseif ($nb_objet == 1) {
		// Un seul objet dans la rubrique, on renvoie le tableau
		return generer_url_entite($objets_in_rubrique[0]['id_objet'], $objets_in_rubrique[0]['objet'], '', '', true);
	} elseif ($nb_objet > 1
			  and array_sum(array_column($objets_in_rubrique, 'num_titre')) > 0
			  and $config['num_titre'] == 'on') {
		// S'il y plusieurs objets dans la rubrique et que le mode "par num titre"
		// est activé, on regiride sur le num titre le plus petit.

		// On créer un tableau avec uniquement les num titre
		$minmax = array_column($objets_in_rubrique, 'num_titre');

		// On va filtrer ce tableau pour n'avoir que des nombres à tester
		$minmax = array_filter($minmax, 'is_numeric');

		// On recherche l'index dans le tableau minmax
		$index = array_search(min($minmax), $minmax);

		// Créer l'URL de redirection
		return generer_url_entite(
			$objets_in_rubrique[$index]['id_objet'],
			$objets_in_rubrique[$index]['objet'],
			'',
			'',
			true
		);
	} elseif ($nb_objet > 1
			  and $config['recent'] == 'on') {
		// Si le mode par article le plus récent est activé

		// On créer un tableau avec uniquement les timestamps des dates
		$minmax = array_column($objets_in_rubrique, 'date');

		// On va filtrer ce tableau pour n'avoir que des nombres à tester
		$minmax = array_filter($minmax, 'is_numeric');

		// On recherche l'index avec le timestamp le plus grand
		$index = array_search(max($minmax), $minmax);

		// Créer l'URL de redirection
		return generer_url_entite(
			$objets_in_rubrique[$index]['id_objet'],
			$objets_in_rubrique[$index]['objet'],
			'',
			'',
			true
		);
	}
}

/**
 * Renvoie tout les enfants direct d'une rubrique
 *
 * @param int $id_rubrique
 * @access public
 * @return array
 */
function courtjus_quete_enfant($id_rubrique) {

	include_spip('inc/filtres');

	// On récupère tous les enfants direct.
	$enfants = sql_allfetsel('id_rubrique, titre', table_objet_sql('rubrique'), 'id_parent='.intval($id_rubrique));

	// On va chercher un éventuel num_titre dans les titre
	foreach ($enfants as $index => $enfant) {
		if ($num_titre = recuperer_numero($enfant['titre'])) {
			$enfants[$index]['num_titre'] = $num_titre;
		}
	}

	// On simplifie le tableau pour n'avoir que des id
	$enfants = array_column($enfants, 'id_rubrique', 'num_titre');
	ksort($enfants);
	return $enfants;
}
