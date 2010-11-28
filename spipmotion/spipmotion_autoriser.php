<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer l'utilisation du pipeline
 * Cela évite de recalculer les pipeline tout le temps
 */
function spipmotion_autoriser(){}

/**
 * Fonction d'autorisation de relance d'encodage en erreur
 * Seuls les personnes suivantes peuvent relancer l'encodage :
 * -* Les personnes qui ont mis en ligne le document (id_auteur dans spip_spipmotion_attentes)
 * -* Les personnes autorisées à configurer le site
 * 
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 */
function autoriser_relancerencodage_spipmotion_dist($faire, $type, $id, $qui, $opt){
	$id_auteur = sql_getfetsel('id_auteur','spip_spipmotion_attentes','id_spipmotion_attente='.intval($id));
	return ($qui['id_auteur'] == $id_auteur) OR autoriser('configurer','','',$qui,$opt);
}
?>