<?php
/**
 * API de vérification : vérification de la validité d'un identifiant
 *
 * @copyright  2019
 * @author     tcharlss
 * @licence    GNU/GPL
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifie la validité d'un identifiant
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   unicite => true ou 'on', dans ce cas là donner aussi au minimum `objet`
 *   objet
 *   id_objet
 * @return string
 *   Retourne une chaîne vide si c'est valide, sinon une chaîne expliquant l'erreur.
 */
function verifier_identifiant_dist($identifiant, $options = array()) {
	$erreur = '';

	// Longueur max
	$longueur_max = 255;
	if (($longueur = strlen($identifiant)) > $longueur_max) {
		$erreur = _T(
			'identifiant:erreur_champ_identifiant_taille',
			array('nb' => $longueur, 'nb_max' => $longueur_max)
		);
	}

	// Format : caractères alphanumériques en minuscules ou "_"
	if (!$erreur) {
		if (
			(
				function_exists('slugify')
				and $slug = slugify($identifiant, '', array('longueur_maxi' => $longueur_max))
				and $slug != $identifiant
			)
			or !preg_match('/^[a-z0-9_]+$/', $identifiant)
		) {
			$erreur = _T('identifiant:erreur_champ_identifiant_format');
		}
	}

	// Unicité : doit être unique pour ce type d'objet, sauf autres langues.
	if (
		!$erreur
		and !empty($options['unicite'])
		and !empty($options['objet'])
	) {
		include_spip('base/objets');
		$trouver_table = charger_fonction('trouver_table', 'base');
		$objet         = $options['objet'];
		$id_objet      = (isset($options['id_objet']) ? $options['id_objet'] : 0);
		$table_objet   = table_objet_sql($objet);
		$cle_objet     = id_table_objet($objet);
		$desc          = $trouver_table($table_objet);
		$where = array(
			$cle_objet . '!=' . intval($id_objet),
			'identifiant=' . sql_quote($identifiant)
		);
		// l'identifiant a le droit d'être utilisé dans une autre langue
		if (isset($desc['field']['lang'])) {
			$lang    = sql_getfetsel('lang', $table_objet, $cle_objet . '=' . intval($id_objet));
			$where[] = 'lang='.sql_quote($lang);
		}
		$doublon = sql_getfetsel($cle_objet, $table_objet, $where);
		if ($doublon) {
			$erreur = _T('identifiant:erreur_champ_identifiant_doublon_objet', array('objet' => $objet, 'id_objet' => $doublon));
		}
	}

	return $erreur;
}
