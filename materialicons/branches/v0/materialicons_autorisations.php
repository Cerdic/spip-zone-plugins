<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}
/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function materialicons_autoriser() { }




/**
 * Autorisation de supprimer (annonce)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_materialicons_modifier_dist($faire, $type, $id, $qui, $opt) {
	// soit on est webmestre soit on est auteur de l'article
	return ($qui['statut'] == '0minirezo' and !$qui['restreint'])
		or ($auteurs = auteurs_objet($type, $id) and in_array($qui['id_auteur'], $auteurs));

}
