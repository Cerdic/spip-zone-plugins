<?php
/**
 * ePUB reader
 * Lecteur de fichiers ePUB
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2011-2012 - Distribué sous licence GNU/GPL
 *
 * Fichier de fonctions du plugin
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Créer le js qui sera utilisé par monocle
 * 
 * @param $id_document int l'identifiant numérique du document
 */
function epubreader_creer_js($id_document,$id=false,$hauteur=600){
	$creer_js = charger_fonction('epubreader_creerjs','inc');
	return $creer_js($id_document,$id,$hauteur);
}
?>