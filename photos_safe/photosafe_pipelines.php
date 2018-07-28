<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');
include_spip('inc/documents');



function rm_exif($file){
	$cmdligne = "mat ".$file;
	exec($cmdligne,$output);
	return $output[1];
}


function photosafe_formulaire_traiter($flux){
    	if (($flux['args']['form']=='editer_logo') or ($flux['args']['form']=='uploadhtml5')){
		$chercher_logo = charger_fonction('chercher_logo','inc');
		$id = $flux['args']['args'][1];
		$type = $flux['args']['args'][0];
		$logo = $chercher_logo($id, id_table_objet($type));
		$logo_file = $logo[0];
		if ($logo_file){
			$exif_out = rm_exif($logo_file);
		}
    	}
    	return $flux;
}

function photosafe_post_edition($flux) {

	if($flux['args']['action']=='ajouter_document')
	{

			$id_photo = $flux['args']['id_objet'];

			$photo_ok = isset($GLOBALS['meta']['formats_graphiques'])
			? (strpos($GLOBALS['meta']['formats_graphiques'], $flux['data']['extension'])!==false)
			: false;

			if ($photo_ok){
				$res=sql_select("fichier",'spip_documents','id_document= '.intval($id_photo));
				while ($nom_photo = sql_fetch($res)){
					$filename = realpath(get_spip_doc($nom_photo['fichier']));
					$exif_out = rm_exif($filename);
				}
			}


	}

	return $flux;
}

/* Alerter si le Mat n'est pas installé */
function photosafe_alertes_auteur($flux) {
	if (autoriser('webmestre', $flux['args']['id_auteur'])
		AND (!lire_config('photosafe/mat'))
		) {
			$flux['data'][] = _T('avis_attention'). ' '
				. _L("Photosafe est activé mais MAT n'est pas présent sur le serveur");
	}

	return $flux;
}


?>
