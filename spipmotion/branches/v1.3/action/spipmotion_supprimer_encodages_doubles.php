<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@supprimer_document
function action_spipmotion_supprimer_encodages_doubles_dist() {
	include_spip('inc/autoriser');
	if (!autoriser('webmestre')){
		return false;
	}
	include_spip('action/dissocier_document');
	$documents = sql_select('*','spip_documents','mode="conversion" AND fichier LIKE "%-encoded\-%"');
	while($document = sql_fetch($documents)){
		$fichier_original = preg_replace('/\-encoded\-[0-9]?/','-encoded',$document['fichier']);
		if($fichier_orig_present = sql_getfetsel('fichier','spip_documents','fichier='.sql_quote($fichier_original))){
			$lien_doc = sql_fetsel('id_objet,objet','spip_documents_liens','id_document='.intval($document['id_document']));
			supprimer_lien_document($document['id_document'], $lien_doc['objet'], $lien_doc['id_objet'], true, false);
		}
	}
	return true;
}

?>