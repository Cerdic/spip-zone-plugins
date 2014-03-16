<?php



/* ----------------------- OBJETS ----------------------- */

/**
 * Surcharge de l'insertion standard d'un objet relecture :
 * - informations sur l'article
 * - date de fin des commentaires
 *
 * Surcharge de l'insertion standard d'un objet commentaire :
 * - element et repere du commentaire dans le texte de cet element
 *
 * @param array $flux
 * @return array
 *
**/
function bspe_pre_boucle($flux) {

	return $flux;
}

?>
