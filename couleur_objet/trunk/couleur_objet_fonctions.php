<?php

/**
 * Compile la balise `#COULEUR`
 *
 * Renvoie la couleur associée à un objet, dans une boucle
 * - `#COULEUR`
 *
 * Avec l'API de déclaration des parents (plugin declarer_parent)
 * Elle permet de demander la couleur du/des parents en fallback :
 * - `#COULEUR{parent}` pour prendre la couleur du parent en fallback
 * - `#COULEUR{parent,recursif}` même chose, mais cherche le parent récursivement
 *
 * @see balise_COULEUR_OBJET_dist() 
 * @see balise_COULEUR_OBJET_HIERARCHIE_dist() 
 * @see https://programmer.spip.net/Recuperer-objet-et-id_objet
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_COULEUR($p) {
	$b = index_boucle($p);
	if ($b === '') {
		$msg = array(
			'zbug_champ_hors_boucle',
			array('champ' => '#' . $p->nom_champ)
		);
		erreur_squelette($msg, $p);
		return $p;
	}

	$boucle = &$p->boucles[$b];
	$_objet    = "'$boucle->id_table'";
	$_id_objet  = champ_sql($boucle->primary, $p);

	// 1er paramètre : prendre le parent en fallback
	// On vérifie juste si le texte est présent, peu importe la valeur
	$_parent = "false";
	if (($v = interprete_argument_balise(1, $p)) !== null) {
		$_parent = "strlen($v) ? true : false";
	}
	// 2ème paramètre : chercher le parent récursivement
	// On vérifie juste si le texte est présent, peu importe la valeur
	$_recursif = "false";
	if (($v2 = interprete_argument_balise(2, $p)) !== null) {
		$_recursif = "strlen($v2) ? true : false";
	}

	$p->code = "objet_couleur($_objet, $_id_objet, $_parent, $_recursif)";

	return $p;
}


/**
 * Compile la balise `#COULEUR_OBJET`
 *
 * Renvoie la couleur associée à un objet dont on transmet le type et identifiant
 *
 * Dans une boucle, par exemple ARTICLES, prendra la couleur défini pour l’article, 
 * sinon sur sa rubrique, sinon sur la rubrique parente, etc. :
 *  - `#COULEUR_OBJET_HIERARCHIE`
 * 
 * Elle accepte 2 paramètres :
 * - `#COULEUR_OBJET{rubrique, 3}`
 * - `#COULEUR_OBJET{article, 8}`
 * 
 * @see balise_COULEUR_OBJET_HIERARCHIE_dist() 
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @param array $options
 *     - bool 'hierarchie' (false) : Remonter la hiérarchie si pas de couleur à l’objet ?
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_COULEUR_OBJET_dist($p, $options = array()) {

	$options = $options + array(
		'hierarchie' => false,
	);

	// Recherche des paramètres objet et id_objet
	$_objet = interprete_argument_balise(1, $p);
	$_id_objet = interprete_argument_balise(2, $p);

	// Sinon, on utilise la boucle courante
	if ($_objet === null and $_id_objet === null) {
		$b = index_boucle($p);
		if ($b === '') {
			$msg = array(
				'zbug_champ_hors_boucle',
				array('champ' => '#' . $p->nom_champ)
			);
			erreur_squelette($msg, $p);
			return $p;
		}
		$boucle = &$p->boucles[$b];
		$_objet     = "'$boucle->id_table'";
		$_id_objet  = champ_sql($boucle->primary, $p);
	}

	$_parent = $_recursif = "false";
	if ($options['hierarchie']) {
		$_parent = $_recursif = "true";
	}

	$p->code = "objet_couleur($_objet, $_id_objet, $_parent, $_recursif)";

	return $p;
}

/**
 * Compile la balise `#COULEUR_OBJET_HIERARCHIE`
 *
 * Renvoie la couleur associée à un objet, en remontant la hiérarchie de ses parents 
 * jusqu’à trouver une couleur définie.
 * Nécessite l'API de déclaration des parents (plugin declarer_parent)
 * 
 * Dans une boucle, par exemple ARTICLES, prendra la couleur défini pour l’article, 
 * sinon sur sa rubrique, sinon sur la rubrique parente, etc. :
 *  - `#COULEUR_OBJET_HIERARCHIE`
 * 
 * On peut transmettre le type et identifiant, pour cibler un élément spécifique :
 * - `#COULEUR_OBJET_HIERARCHIE{rubrique, 3}`
 * - `#COULEUR_OBJET_HIERARCHIE{article, 8}`
 * 
 * @uses balise_COULEUR_OBJET_dist()
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_COULEUR_OBJET_HIERARCHIE_dist($p) {
	return balise_COULEUR_OBJET_dist($p, array('hierarchie' => true));
}

/**
 * Trouver la couleur d'un objet ou de son parent
 *
 * @uses objet_lire_couleur()
 *
 * @param string $objet
 *     Type de l'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param boolean $fallback_parent
 *     true pour chercher la couleur du parent en fallback
 * @param boolean $fallback_recursif
 *     true pour chercher les parents récursivement
 * @return string|false
 *     La couleur ou false si rien trouvé
 */
function objet_couleur($objet, $id_objet, $fallback_parent = false, $fallback_recursif = false) {
	include_spip('inc/couleur_objet');
	return objet_lire_couleur($objet, $id_objet, $fallback_parent, $fallback_recursif);
}
