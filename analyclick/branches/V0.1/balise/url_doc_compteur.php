<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_URL_DOC_COMPTEUR_dist ($p) 
{	return calculer_balise_dynamique(
		$p,
		'URL_DOC_COMPTEUR',
		array( 'id_document' )
	);
}

function balise_URL_DOC_COMPTEUR_stat($args, $filtres) 
{	return $args;
}

function balise_URL_DOC_COMPTEUR_dyn($id_document) 
{	$securiser_action = charger_fonction('securiser_action', 'inc');
	return array
	(	"formulaires/doc_compteur",
		0,
		// Lien pour le comptage avant telechargement
		array('url' => $securiser_action("telecharger",$id_document))
	);
}

?>