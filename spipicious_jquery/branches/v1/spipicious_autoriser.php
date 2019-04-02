<?php
/**
 * SPIP.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational (http://www.erational.org)
 *
 * © 2007-2013 - Distribue sous licence GNU/GPL
 * 
 * Fichier des autorisations du plugin
 * 
 * @package SPIP\SPIPicious\Autorisations
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer l'utilisation du pipeline
 * Cela évite de recalculer les pipeline tout le temps
 */
function spipicious_autoriser(){}

/**
 * Fonction définissant qui est autorisé à tagger
 * Se base sur la configuration de spip.icio.us
 *
 * @param string $faire 
 * 		L'action d'autoriser
 * @param string $type 
 * 		L'objet sur lequel on souhaite tagger, peut être article,rubrique,breve,forum...
 * @param int $id 
 * 		L'id de l'objet type sur lequel on souhaite ajouter un tag
 * @param array $qui 
 * 		La session visiteur
 * @param array $opt
 * 		Les options mais pas utilisées
 * @return bool 
 * 		true si ok, false sinon
 */
function autoriser_tagger_spipicious_dist($faire, $type, $id, $qui, $opt){
	return  (in_array($qui['statut'],lire_config('spipicious/people',array('0minirezo'))));
}

?>