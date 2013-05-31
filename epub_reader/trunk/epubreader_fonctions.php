<?php
/**
 * ePUB reader
 * Lecteur de fichiers ePUB
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2011-2013 - Distribué sous licence GNU/GPL
 *
 * Fichier de fonctions du plugin
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Créer le js qui sera utilisé par monocle
 * 
 * @param int $id_document
 * 		l'identifiant numérique du document
 * @param string $id 
 * 		l'identifiant dans le dom
 * @param int $hauteur
 * 		La hauteur d'affichage
 * @return string|false
 * 		le code js utilisable par Monocle ou false
 */
function epubreader_creer_js($id_document,$id=false,$hauteur=600){
	$creer_js = charger_fonction('epubreader_creerjs','inc');
	return $creer_js($id_document,$id,$hauteur);
}
?>