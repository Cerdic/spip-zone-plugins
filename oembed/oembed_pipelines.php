<?php

function oembed_renseigner_document_distant($flux) {

	include_spip('inc/oembed');

	// on tente de récupérer les données oembed
	if ($data = oembed_recuperer_data($flux['source'])){
		// on recupere les infos du document distant
		if ($flux = recuperer_infos_distantes($data['url'])) {
			unset($flux['body']);
			$flux['distant'] = 'oui';
			$flux['mode'] = 'document';
			$flux['fichier'] = set_spip_doc($data['url']);
			// et on complète par les infos oembed
			$flux['titre'] = $data['title'];
			$flux['credits'] = $data['author_name'];
			return $flux;
		}
	}
	
	return $flux;
}

?>