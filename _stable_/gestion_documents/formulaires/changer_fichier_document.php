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

function formulaires_changer_fichier_document_charger_dist($id_document){
	$valeurs = sql_fetsel('id_document,fichier,distant','spip_documents','id_document='.intval($id_document));
	if (!$valeurs)
		return array('editable'=>false);
		
	$valeurs['_hidden'] = "<input name='id_document' value='$id_document' type='hidden' />";
	
	return $valeurs;
}

function formulaires_changer_fichier_document_verifier_dist($id_document){
	$verifier = charger_fonction('verifier','formulaires/joindre_document');
	return $verifier($id_document);
}

function formulaires_changer_fichier_document_traiter_dist($id_document){
	$traiter = charger_fonction('traiter','formulaires/joindre_document');
	return $traiter($id_document);
}

?>
