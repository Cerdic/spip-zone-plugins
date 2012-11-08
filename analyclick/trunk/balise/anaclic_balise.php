<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr) V0.1
* @author: Pierre KUHN V1
*
* Copyright (c) 2011-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Lien pour le comptage avant telechargement
function generer_url_compteur($id_objet)
{	return generer_url_action ('telecharger', "arg=$id_objet", true);
}

/** Balise url vers telechargement du document */
function balise_URL_COMPTEUR_dist($p) 
{	$p->code = "generer_url_doc_compteur(" . champ_sql('id_objet',$p) . ")";
	$p->interdire_scripts = false;	
	return $p;
}

/** Balise pour le comptage du nombre de telechargements d'un document */
function balise_COMPTEUR_TELECHARGEMENT_dist($p) 
{	if (!($debut = interprete_argument_balise(1,$p))) $debut='null';
	if (!($fin = interprete_argument_balise(2,$p))) $fin='null';
	$p->code = "anaclic_compter(" . champ_sql('id_objet',$p) . ", $debut, $fin)";
	$p->interdire_scripts = false;	
	return $p;
}

?>
