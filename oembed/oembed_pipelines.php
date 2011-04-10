<?php

// insertion des traitements oembed dans l'ajout des documents distants
function oembed_renseigner_document_distant($flux) {
	include_spip('inc/config');
	include_spip('inc/oembed');
	// on tente de récupérer les données oembed
	if ($data = oembed_recuperer_data($flux['source'],lire_config('oembed/maxwidth','480'),lire_config('oembed/maxheight','295'))){
		// une image ?
		if ($data['type']=='photo') {
			// on recupere les infos du document distant
			if ($doc = recuperer_infos_distantes($data['url'])) {
				unset($doc['body']);
				$doc['distant'] = 'oui';
				$doc['mode'] = 'document';
				$doc['fichier'] = set_spip_doc($data['url']);
				// et on complète par les infos oembed
				$doc['oembed'] = $flux['source'];
				$doc['titre'] = $data['title'];
				$doc['credits'] = $data['author_name'];
				return $doc;
			}
		}
		// une video ?
		if ($data['type']=='video') {
			// créer une copie locale du contenu html
			// cf recuperer_info_distantes()
			$doc['fichier'] = _DIR_RACINE . nom_fichier_copie_locale($flux['source'], 'html');
			ecrire_fichier($doc['fichier'], $data['html']);
			$doc['extension'] = 'html';
			$doc['taille'] = strlen($data['html']); # a peu pres
			$doc['distant'] = 'non';
			$doc['mode'] = 'document';
			$doc['oembed'] = $flux['source'];
			$doc['titre'] = $data['title'];
			$doc['credits'] = $data['author_name'];
			return $doc;
		}
	}
	return $flux;
}

// attacher la vignette si disponible pour les documents oembed
function oembed_post_edition($flux) {
	if($flux['args']['action']=='ajouter_document' AND $flux['data']['oembed']){
		$id_document = $flux['args']['id_objet'];
		if ($data = oembed_recuperer_data($flux['data']['oembed'],lire_config('oembed/maxwidth'),lire_config('oembed/maxheight'))){
			// vignette disponible ? la recupérer et l'associer au document
			if ($data['thumbnail_url']) {
				// cf formulaires_illustrer_document_traiter_dist()
				$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				include_spip('inc/joindre_document');
				set_request('url',$data['thumbnail_url']);
				set_request('joindre_distant','oui');
				$files = joindre_trouver_fichier_envoye();
				$ajoute = action_ajouter_documents_dist('new',$files,'',0,'vignette');
				if (is_int(reset($ajoute))){
					$id_vignette = reset($ajoute);
					include_spip('action/editer_document');
					document_set($id_document,array("id_vignette" => $id_vignette,'mode'=>'document'));
				}
			}
		}
	}
	return $flux;
}

?>