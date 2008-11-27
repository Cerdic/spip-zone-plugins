<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */


function autoriser_article_creerevenementdans_dist($faire,$quoi,$id,$qui,$options){
	if (!$id) return false; // interdit de creer un evenement sur un article vide !
	// si on a le droit de modifier l'article alors on a le droit d'y creer un evenement !
	return autoriser('modifier','article',$id);
}


?>