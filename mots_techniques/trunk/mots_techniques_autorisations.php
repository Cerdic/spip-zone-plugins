<?php

/**
 * Fichier gérant les autorisations du plugin
 *
 * @package Mots_Techniques\Autorisations
**/

/** Fonction d'appel du pipeline **/
function mots_techniques_autoriser(){}

/**
 * Autorisation de voir le champs extra technique sur les groupes
 *
 * Il est hérité du parent. Toujours vrai
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_spip_groupes_mots_voirextra_technique_dist($faire,$type,$id,$qui,$opt) {
	return true;
}

/**
 * Autorisation de voir le champs extra technique sur les groupes
 *
 * On le limite aux groupes racine (si plugin gma - groupes mots arborescents)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_spip_groupes_mots_modifierextra_technique_dist($faire,$type,$id,$qui,$opt) {
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('spip_groupes_mots');
	if (!isset($desc['field']['id_groupe_racine'])) {
		return true;
	}

	// si c'est une creation de groupe
	// on retourne false si on crée un goupe dans un parent connu
	if ($id == 'oui'){
		return (bool)!_request('id_parent');
	}

	// sinon on cherche la racine du groupe
	$id_racine = sql_getfetsel('id_groupe_racine', 'spip_groupes_mots', 'id_groupe=' . sql_quote($id));
	// vrai si la racine est notre groupe 
	return ($id_racine == $id);
}

?>
