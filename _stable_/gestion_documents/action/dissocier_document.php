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


function action_dissocier_document_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$arg = explode('-',$arg);

	list($id_objet, $objet, $document) = $arg;
	$suppr=false;
	if (count($arg)>3 AND end($arg)=='suppr')
		$suppr = true;
	if ($id_objet=intval($id_objet)	AND autoriser('modifier',$objet,$id_objet))
		dissocier_document($document, $objet, $id_objet, $suppr);
}

// http://doc.spip.org/@supprimer_lien_document
function supprimer_lien_document($id_document, $objet, $id_objet, $supprime = false) {
	if (!$id_document = intval($id_document))
		return false;

	// D'abord on ne supprime pas, on dissocie
	sql_delete("spip_documents_liens",
		$z = "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet)." AND id_document=".$id_document);

	// Si c'est une vignette, l'eliminer du document auquel elle appartient
	sql_updateq("spip_documents", array('id_vignette' => 0), "id_vignette=".$id_document);

	// On supprime ensuite s'il est orphelin
	// et si demande
	if ($supprime AND !sql_countsel('spip_documents_liens', 'id_document='.$id_document)){
		$supprimer_document = charger_fonction('supprimer_document','action');
		return $supprimer_document($id_document);
	}
}

function dissocier_document($document, $objet, $id_objet, $supprime = false){
	if ($id_document=intval($document)) {
		supprimer_lien_document($id_document, $objet, $id_objet, $supprime);
	}
	else {
		$obj = "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet);
		$typdoc = sql_in('docs.extension', array('gif', 'jpg', 'png'), $sign  ? '' : 'NOT');

		$s = sql_select('docs.id_document AS id_doc', 
			"spip_documents AS docs LEFT JOIN spip_documents_liens AS l ON l.id_document=docs.id_document",
			"$obj AND docs.mode=".sql_quote($document)." AND $typdoc");
		while ($t = sql_fetch($s)) {
			supprimer_lien_document($t['id_doc'], $objet, $id_objet, $supprime);
		}
	}

	// pas tres generique ca ...
	if ($objet == 'rubrique') {
		include_spip('inc/rubriques');
		depublier_branche_rubrique_if($id);
	}
}
?>
