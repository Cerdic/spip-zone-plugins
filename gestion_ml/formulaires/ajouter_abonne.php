<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_ajouter_abonne_charger_dist($liste){
	$valeurs = array('email'=>'','liste'=>$liste);
   return $valeurs;
}


// la saisie a ete validee, on peut agir
function formulaires_ajouter_abonne_traiter_dist(){
	
	include_spip('inc/gestionml_api');
	return gestionml_api_ajouter_email(_request('liste'),_request('email')) ;
}


?>
