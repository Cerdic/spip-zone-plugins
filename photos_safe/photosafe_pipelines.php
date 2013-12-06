<?php

include_spip('inc/config');
include_spip('photosafe_fonctions');
include_spip('inc/documents');

function photosafe_post_edition($flux) {

	if (($flux['args']['table'] == 'spip_documents')) {
		/* On nettoie les données exif si l'option est cochée */
		$test=lire_config('photosafe/rm_exif');
		spip_log("la variable de config : $test", 'photosafe');
		if (lire_config('photosafe/rm_exif')=='on')
		{ 
			$id_photo = $flux['args']['id_objet'];
			/*debug*/
			spip_log($flux['args'], 'photosafe');
			spip_log($flux['data'], 'photosafe');
			spip_log($id_photo, 'photosafe');
			/*fin debug*/
			
			if ($flux['data']['extension']=='jpg'){
				$nom_photo = sql_fetsel('fichier', 'spip_documents', 'id_document= '.intval($id_photo));
				$nom_photo['fichier'] = get_spip_doc($nom_photo['fichier']);
				photosafe_rm_exif(realpath($nom_photo['fichier']));
				/*debug*/
				spip_log($nom_photo['fichier'], 'photosafe');
			}
			
		}
	}
	
	return $flux;
}


?>