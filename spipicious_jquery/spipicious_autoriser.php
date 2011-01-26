<?php
/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * Erational
 *
 * © 2007-2011 - Distribue sous licence GNU/GPL
 * 
 * Fichier des autorisations du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer l'utilisation du pipeline
 * Cela évite de recalculer les pipeline tout le temps
 *
 * @return
 */
function spipicious_autoriser(){}

/**
 * Fonction définissant qui est autorisé à tagger
 * Se base sur la configuration de spip.icio.us
 *
 * @param string $faire l'action d'autoriser
 * @param string $type peut être article,rubrique,breve,forum...
 * @param int $id l'id du type sur lequel on souhaite ajouter un tag
 * @param array $qui en général $visiteur_session
 * @param object $opt
 * @return quelquechose (si oui) / rien (si non)
 */
function autoriser_tagger_spipicious_dist($faire, $type, $id, $qui, $opt){
	return  (in_array($qui['statut'],lire_config('spipicious/people',array('0minirezo'))));
}

?>