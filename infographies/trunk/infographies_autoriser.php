<?php
/**
 * Infographies
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Distribué sous licence GNU/GPL
 *
 * Autorisations du plugin
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Qui peut supprimer un jeu de données
 * 
 * On ne peut le supprimer que si on est administrateur et qu'il n'y a pas de données liées
 */
function autoriser_infographiesdata_supprimer_dist($faire,$quoi,$id,$qui,$options){
	$donnees = sql_countsel('spip_infographies_donnees','id_infographies_data='.intval($id));
	
	return ($donnees == 0) && $qui['statut'] == '0minirezo';
}
?>