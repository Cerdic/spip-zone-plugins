<?php

/**
 * Compile la balise #COULEUR
 *
 * Renvoie la couleur associée à un objet
 *
 * Elle accepte 2 paramètres :
 * - #COULEUR{parent} pour prendre la couleur du parent en fallback
 * - #COULEUR{parent,recursif} même chose, mais cherche le parent récursivement
 * Attention, ne fonctionne que s'il y a l'API de déclaration des parents.
 *
 * @see
 * https://programmer.spip.net/Recuperer-objet-et-id_objet
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_COULEUR($p) {

	// On prend nom de la clé primaire de l'objet pour calculer sa valeur
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
	$id_objet  = champ_sql($_id_objet, $p);
	$objet     = $p->boucles[$p->id_boucle]->id_table;
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

	$p->code = "objet_couleur('$objet', $id_objet, $_parent, $_recursif)";

	return $p;
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
