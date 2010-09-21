<?php
/**
 * Plugin mots-auteurs pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 * Grâce au soutien actif de Matthieu Marcillaud - Magraine
 *
 */
// fonction du pipeline, n'a rien a faire.
function autoriser_mots_auteurs() {}


function autoriser_auteur_editermots_dist($faire,$quoi,$id,$qui,$opts) {
	return autoriser_rubrique_editermots_dist($faire,'auteur',0,$qui,$opts);
}



?>