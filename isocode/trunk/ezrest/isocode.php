<?php
/**
 * Ce fichier contient l'ensemble des fonctions de service spécifiques à une ou plusieurs collections.
 *
 * @package SPIP\ISOCODE\EZCOLLECTION\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -----------------------------------------------------------------------
// -------------------------- COLLECTION ZONES ---------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la liste des régions du monde de la table spip_m49regions éventuellement filtrées par les critères
 * additionnels positionnés dans la requête.
 *
 * @param array $conditions    Conditions à appliquer au select
 * @param array $filtres       Tableau des critères de filtrage additionnels à appliquer au select.
 * @param array $configuration Configuration de la collection utile pour savoir quelle fonction appeler pour
 *                             construire chaque filtre.
 *
 * @return array Tableau des plugins dont l'index est le préfixe du plugin.
 *               Les champs de type id ou maj ne sont pas renvoyés.
 */
function zones_collectionner($conditions, $filtres, $configuration) {

	// Initialisation de la collection
	$zones = array();

	// Récupérer la liste des pays (filtrée ou pas).
	// Si la liste est filtrée par continent ou région, on renvoie aussi les informations sur ce continent ou
	// cette région.
	$from = 'spip_m49regions';
	// -- Tous le champs sauf les labels par langue et la date de mise à jour.
	$description_table = sql_showtable($from);
	$champs = array_keys($description_table['field']);
	$select = array_diff($champs, array('label_fr', 'label_en', 'maj'));

	// -- Initialisation du where avec les conditions sur la table des dépots.
	$where = array();
	// -- Si il y a des critères additionnels on complète le where en conséquence en fonction de la configuration.
	if ($conditions) {
		$where = array_merge($where, $conditions);
	}

	$zones['zones'] = sql_allfetsel($select, $from, $where);

	return $zones;
}


// -----------------------------------------------------------------------
// -------------------------- COLLECTION PAYS ----------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la liste des pays de la table spip_iso3166countries éventuellement filtrés par les critères
 * additionnels positionnés dans la requête.
 *
 * @param array $conditions    Conditions à appliquer au select
 * @param array $filtres       Tableau des critères de filtrage additionnels à appliquer au select.
 * @param array $configuration Configuration de la collection utile pour savoir quelle fonction appeler pour
 *                             construire chaque filtre.
 *
 * @return array Tableau des plugins dont l'index est le préfixe du plugin.
 *               Les champs de type id ou maj ne sont pas renvoyés.
 */
function pays_collectionner($conditions, $filtres, $configuration) {

	// Initialisation de la collection
	$pays = array();

	// Récupérer la liste des pays (filtrée ou pas).
	// Si la liste est filtrée par continent ou région, on renvoie aussi les informations sur ce continent ou
	// cette région.
	$from = array('spip_iso3166countries');
	// -- Tous le champs sauf les labels par langue et la date de mise à jour.
	$description_table = sql_showtable($from[0]);
	$champs = array_keys($description_table['field']);
	$select = array_diff($champs, array('label_fr', 'label_en', 'maj'));

	// -- Initialisation du where avec les conditions sur la table des dépots.
	$where = array();
	// -- Si il y a des critères additionnels on complète le where en conséquence en fonction de la configuration.
	if ($conditions) {
		$where = array_merge($where, $conditions);
	}

	$pays['pays'] = sql_allfetsel($select, $from, $where);

	return $pays;
}

/**
 * Détermine si la valeur du critère de région d'appartenance du pays est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec un code à 3 chiffres.
 *
 * @param string $zone   La valeur du critère région, soit son code ISO 3166-1 numérique (3 chiffres).
 * @param array  $erreur Bloc d'erreur préparé au cas où la vérification retourne une erreur. Dans ce cas, le bloc
 *                       et complété et renvoyé.
 *
 * @return bool `true` si la valeur est valide, `false` sinon.
 */
function pays_verifier_filtre_zone($zone, &$erreur) {
	$est_valide = true;

	if (!preg_match('#^[0-9]{3}$#', $zone)) {
		$est_valide = false;
		$erreur['type'] = 'zone_nok';
	}

	return $est_valide;
}

/**
 * Détermine si la valeur du continent d'appartenance du pays est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec une code à deux lettres
 * majuscules.
 *
 * @param string $continent La valeur du critère région, soit son code ISO 3166-1 numérique (3 chiffres).
 * @param array  $erreur    Bloc d'erreur préparé au cas où la vérification retourne une erreur. Dans ce cas, le bloc
 *                          et complété et renvoyé.
 *
 * @return bool `true` si la valeur est valide, `false` sinon.
 */
function pays_verifier_filtre_continent($continent, &$erreur) {
	$est_valide = true;

	if (!preg_match('#^[A-Z]{2}$#', $continent)) {
		$est_valide = false;
		$erreur['type'] = 'continent_nok';
	}

	return $est_valide;
}


// -----------------------------------------------------------------------
// ---------------------- COLLECTION SUBDIVISIONS ------------------------
// -----------------------------------------------------------------------

/**
 * Récupère la liste des subdivisions de la table spip_iso3166subdivisions éventuellement filtrés par les critères
 * additionnels positionnés dans la requête.
 *
 * @param array $conditions    Conditions à appliquer au select
 * @param array $filtres       Tableau des critères de filtrage additionnels à appliquer au select.
 * @param array $configuration Configuration de la collection utile pour savoir quelle fonction appeler pour
 *                             construire chaque filtre.
 *
 * @return array Tableau des subdivisions et par défaut des codes alternatifs et de la liste des pays.
 */
function subdivisions_collectionner($conditions, $filtres, $configuration) {

	// Initialisation de la collection
	$subdivisions = array();

	// Récupérer la liste des subdivisions (filtrée ou pas par pays ou par type de subdivision).
	$from = 'spip_iso3166subdivisions';
	// -- Tous le champs sauf les labels par langue et la date de mise à jour.
	$description_table = sql_showtable($from);
	$champs = array_keys($description_table['field']);
	$select = array_diff($champs, array('maj'));

	// -- Initialisation du where: aucune condition par défaut.
	$where = array();
	// -- Si il y a des critères additionnels on complète le where en conséquence.
	if ($conditions) {
		$where = array_merge($where, $conditions);
	}
	// -- Rangement de la liste dans l'index subdivisions
	$subdivisions['subdivisions'] = sql_allfetsel($select, $from, $where);

	// La liste est enrichie par défaut:
	// -- des codes alternatifs disponibles dans iso3166alternates
	// -- de la liste des pays concernés par les codes renvoyés
	// Ces données supplémentaires peuvent être exclues en utilisant le filtre 'exclure'
	//
	// -- Ajout des codes alternatifs si non exclus explicitement
	if (empty($filtres['exclure'])
		or (
			!empty($filtres['exclure'])
			and (strpos($filtres['exclure'], 'alternates') === false)
		)
	) {
		// on construit la condition sur la table de liens à partir des codes ISO des subdivisions
		$where = array();
		$codes_subdivision = array_column($subdivisions['subdivisions'], 'code_3166_2');
		$where[] = sql_in('code_3166_2', $codes_subdivision);

		$alternates = array();
		$codes = sql_allfetsel('*', 'spip_iso3166alternates', $where);
		if ($codes) {
			// On range les codes alternatifs par code ISO-3166-2
			foreach ($codes as $_alternate) {
				$alternates[$_alternate['code_3166_2']][] = $_alternate;
			}
		}
		$subdivisions['codes_alternatifs'] = $alternates;
	}

	// -- Ajout de la liste des pays concernés par les subdivisions sauf si exclu
	if (empty($filtres['exclure'])
		or (
			!empty($filtres['exclure'])
			and (strpos($filtres['exclure'], 'pays') === false)
		)
	) {
		// Liste des codes 3166-1 alpha2 et du nom multi
		$pays = array();
		foreach($subdivisions['subdivisions'] as $_subdivision) {
			if (!in_array($_subdivision['country'], $pays)) {
				$where = array('code_alpha2=' . sql_quote($_subdivision['country']));
				if ($nom = sql_getfetsel('label', 'spip_iso3166countries', $where)) {
					$pays[$_subdivision['country']] = $nom;
				}
			}
		}
		$subdivisions['pays'] = $pays;
	}

	return $subdivisions;
}

/**
 * Evite que le filtre exclure ne soit considéré comme une condition SQL.
 * Il sera traité dans la fonction collectionner pour supprimer des données dans le contenu de la requête.
 *
 * @param string $valeur Valeur du critère `exclure`.
 *
 * @return string Toujours la chaine vide.
 */
function subdivisions_conditionner_exclure($valeur) {

	return '';
}

/**
 * Calcule la condition du filtre pays pour lequel il est possible de passer une liste de codes de pays séparés
 * par une virgule.
 *
 * @param string $valeur Valeur du critère `exclure`.
 *
 * @return string Toujours la chaine vide.
 */
function subdivisions_conditionner_pays($valeur) {

	$condition = '';
	if ($valeur) {
		if (strpos($valeur, ',') === false) {
			$condition = 'country=' . sql_quote($valeur);
		} else {
			$pays = explode(',', $valeur);
			$condition = sql_in('country', $pays);
		}
	}

	return $condition;
}
