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
		// suppression des images eventuelles (image principale et secondaires et logo)
		$images = sql_allfetsel(array('url_image','lien'),'spip_sitra_images',$where);
		foreach($images as $image) {
			switch ($image['lien']) {
				// image importée dans le zip stockée en local
				case 'N':
					$url_img = url_image_locale($image['url_image']);
					suppr_image($url_img);
				break;
				// image obtenue par copie distante
				case 'O':
					$url_img = copie_locale($image['url_image'],'test');
					suppr_image($url_img);
				break;
			} // fin switch
		} // fin foreach
		
		// suppression des données
		sql_delete('spip_sitra_objets', $where);
		sql_delete('spip_sitra_objets_details', $where);
		sql_delete('spip_sitra_categories', $where);
		sql_delete('spip_sitra_images', $where);
		sql_delete('spip_sitra_images_details', $where);
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