<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// *********
// Config 
// *********

$nl = "\n";
$br = '<br />';
$hr = '<hr />';

// *********
// Fichier des objets à supprimer
// *********

$fichier_del = trouver_fichier_prefixe(SITRA_DIR,'('.SITRA_ID_SITE.')_DEL_ListeOI_');
	
if (!$fichier_del) {
	message($nl.'Pas de fichier DEL_ListeOI','erreur');
	continue;
}

$fichier_del = SITRA_DIR.$fichier_del;

message($nl.'/// Fichier  DEL_ListeOI : '.$fichier_del.' ///');
$xml = simplexml_load_file($fichier_del);

if ($xml -> identifier) {
	foreach ($xml -> identifier as $id_sitra) {
		$where = 'id_sitra = \''.$id_sitra.'\'';

		message('Traitement objet : '.$id_sitra);
		// suppression des docs eventuels (image principale et secondaires et logo)
		$docs = sql_allfetsel(array('url_doc','lien'),'spip_sitra_docs',$where);
		foreach($docs as $doc) {
			switch ($doc['lien']) {
				// image importée dans le zip stockée en local
				case 'N':
					$url_img = url_image_locale($doc['url_doc']);
					suppr_doc($url_img);
				break;
				// doc obtenu par copie distante
				case 'O':
					$url_doc = copie_locale($doc['url_doc'],'test');
					suppr_doc($url_doc);
				break;
			} // fin switch
		} // fin foreach
		
		// suppression des données
		sql_delete('spip_sitra_objets', $where);
		sql_delete('spip_sitra_objets_details', $where);
		sql_delete('spip_sitra_categories', $where);
		sql_delete('spip_sitra_docs', $where);
		sql_delete('spip_sitra_docs_details', $where);
		sql_delete('spip_sitra_selections', $where);
		sql_delete('spip_sitra_criteres', $where);
		message('Fin suppression données pour : '.$id_sitra);
	} // fin foreach identifier on passe à l'objet suivant
} // fin if identifier
	
// si pas en mode debug on supprime le fichier importé
if (!SITRA_DEBUG) {
	unlink($fichier_del);
	message('Suppression fichier '.$fichier_del);
}

message('/// Fin traitement fichier DEL_ListeOI ///');

?>