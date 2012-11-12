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
function generer_url_doc_compteur($id_document)
{	return generer_url_action ('telecharger', "arg=$id_document", true);
}

/** Balise url vers telechargement du document */
function balise_URL_DOC_COMPTEUR_dist($p) 
{	$p->code = "generer_url_doc_compteur(" . champ_sql('id_document',$p) . ")";
	$p->interdire_scripts = false;	
	return $p;
}

?>
