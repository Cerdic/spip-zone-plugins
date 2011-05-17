<?php

// ajouter le lien oembed dans le head des pages publiques
function oembed_affichage_final($page) {
	if (!$GLOBALS['html']) return $page;
	if ($url_oembed = url_absolue(parametre_url($GLOBALS['meta']['adresse_site'] . '/services/oembed/','url',url_absolue(self())))) {
		$page = preg_replace(',</head>,i',
			"\n".'<link rel="alternate" type="application/json+oembed" href="'.$url_oembed.'&format=json" />'.
			"\n".'<link rel="alternate" type="text/xml+oembed" href="'.$url_oembed.'&format=xml" />'."\n".'\0',
			$page, 1);
	}
	return $page;
}

// insertion des traitements oembed dans l'ajout des documents distants
function oembed_renseigner_document_distant($flux) {
	include_spip('inc/config');
	include_spip('inc/oembed');
	// on tente de récupérer les données oembed
	if ($data = oembed_recuperer_data($flux['source'],lire_config('oembed/maxwidth','480'),lire_config('oembed/maxheight','295'))){
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
		if (($data['type']=='video') OR ($data['type']=='rich') OR ($data['type']=='link')) {
			if ($data['type']=='link')
				$data['html'] = '<a href="' . $flux['source'] . '">' . sinon($data['title'],$flux['source']) . '</a>';
			// créer une copie locale du contenu html
			// cf recuperer_infos_distantes()
			$doc['fichier'] = _DIR_RACINE . nom_fichier_copie_locale($flux['source'], 'html');
			ecrire_fichier($doc['fichier'], $data['html']);
			// set_spip_doc() pour récupérer le chemin du fichier relatif a _DIR_IMG
			$doc['fichier'] = set_spip_doc($doc['fichier']);
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
					// pour ne pas se retrouver avec l'url de la vignette dans l'input du formulaire au retour
					set_request('url','');
				}
			}
		}
	}
	return $flux;
}

function oembed_pre_propre($texte) {
	if (lire_config('oembed/embed_auto','oui')!='non') {
		include_spip('inc/oembed');
		foreach (extraire_balises($texte, 'a') as $lien) {
			if ($url = extraire_attribut($lien, 'href')
			# seuls les autoliens beneficient de la detection oembed
			AND preg_match(',\bauto\b,', extraire_attribut($lien, 'class'))
			AND oembed_verifier_provider($url)) {
				$fond = recuperer_fond('modeles/oembed',array('url'=>$url));
				if ($fond = trim($fond))
					$texte = str_replace($lien, $fond, $texte);
			}
		}
	}
	return $texte;
}

?>