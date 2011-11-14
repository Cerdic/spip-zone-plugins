<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// ajouter le lien oembed dans le head des pages publiques
function oembed_affichage_final($page) {
	if (!$GLOBALS['html']) return $page;
	if ($url_oembed = url_absolue(parametre_url($GLOBALS['meta']['adresse_site'] . '/services/oembed/','url',url_absolue(self())))) {
		$page = preg_replace(',</head>,i',
			"\n".'<link rel="alternate" type="application/json+oembed" href="'.$url_oembed.'&amp;format=json" />'.
			"\n".'<link rel="alternate" type="text/xml+oembed" href="'.$url_oembed.'&amp;format=xml" />'."\n".'\0',
			$page, 1);
	}
	return $page;
}

/**
 * Generer un apercu pour les oembed sur le formulaire d'edition document
 * @param $flux
 * @return
 */
function oembed_formulaire_charger($flux){
	if ($flux['args']['form']=='editer_document'){
		if ($flux['data']['oembed']
		  AND !isset($flux['data']['apercu']))
			$flux['data']['_inclus'] = 'embed';
	}
	return $flux;
}

/**
 * Inserer une explication dans le form d'upload
 * @param $flux
 * @return array
 */
function oembed_recuperer_fond($flux){
	if ($flux['args']['fond']=='formulaires/inc-upload_document'){
		$rows = sql_allfetsel('scheme','spip_oembed_providers');
		$hosts = array();
		foreach($rows as $row){
			$h = parse_url($row['scheme'],PHP_URL_HOST);
			$hosts[trim(preg_replace(",^(\*|www)\.,i","",$h))]=true;
		}
		$hosts = implode(', ',array_keys($hosts));
		$i = _T('oembed:explication_upload_url',array('hosts'=>$hosts));
		$i = "<p class='explication small'>$i</p>";
		$flux['data'] = str_replace($t="<!--editer_url-->",$t.$i,$flux['data']);
	}
	return $flux;
}

// insertion des traitements oembed dans l'ajout des documents distants
function oembed_renseigner_document_distant($flux) {
	$medias = array('photo' => 'image','video' => 'video', 'sound' => 'audio');
	include_spip('inc/config');
	include_spip('inc/oembed');
	// on tente de récupérer les données oembed
	if ($data = oembed_recuperer_data($flux['source'])){
		// si on a recupere une URL c'est direct un doc distant
		if (isset($data['url'])) {
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
				if (isset($medias[$data['type']]))
					$doc['media'] = $medias[$data['type']];
				return $doc;
			}
		}
		elseif(isset($data['html']) OR $data['type']=='link'){
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
			if (isset($medias[$data['type']]))
				$doc['media'] = $medias[$data['type']];
			return $doc;
		}
	}
	return $flux;
}

// attacher la vignette si disponible pour les documents oembed
function oembed_post_edition($flux) {
	if($flux['args']['action']=='ajouter_document' AND $flux['data']['oembed']){
		$id_document = $flux['args']['id_objet'];
		if ($data = oembed_recuperer_data($flux['data']['oembed'])){
			// vignette disponible ? la recupérer et l'associer au document
			if ($data['thumbnail_url']) {
				spip_log('ajout de la vignette'.$data['thumbnail_url'].' pour '.$flux['data']['oembed'],'oembed.'._LOG_DEBUG);
				// cf formulaires_illustrer_document_traiter_dist()
				$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				if (preg_match(",^\w+://,",$data['thumbnail_url'])){
					include_spip('inc/joindre_document');
					set_request('url',$data['thumbnail_url']);
					set_request('joindre_distant','oui');
					$files = joindre_trouver_fichier_envoye();
				}
				elseif (file_exists($data['thumbnail_url'])) {
					$files = array(array(
						'name' => basename($data['thumbnail_url']),
						'tmp_name' => $data['thumbnail_url']
					));
				}
				$ajoute = action_ajouter_documents_dist('new',$files,'',0,'vignette');
				if (is_int(reset($ajoute))){
					$id_vignette = reset($ajoute);
					include_spip('action/editer_document');
					document_set($id_document,array("id_vignette" => $id_vignette,'mode'=>'document'));
					// pour ne pas se retrouver avec l'url de la vignette dans l'input du formulaire au retour
					set_request('url','');
				}
			}
			else
				spip_log('pas de vignette pour '.$flux['data']['oembed'],'oembed.'._LOG_DEBUG);
		}
	}
	return $flux;
}

function oembed_pre_propre($texte) {
	include_spip('inc/config');
	if (lire_config('oembed/embed_auto','oui')!='non') {
		include_spip('inc/oembed');
		foreach (extraire_balises($texte, 'a') as $lien) {
			if ($url = extraire_attribut($lien, 'href')
			# seuls les autoliens beneficient de la detection oembed
			AND preg_match(',\bauto\b,', extraire_attribut($lien, 'class'))
			AND (oembed_verifier_provider($url) OR (lire_config('oembed/detecter_lien','non')=='oui'))) {
				$fond = recuperer_fond('modeles/oembed',array('url'=>$url));
				if ($fond = trim($fond))
					$texte = str_replace($lien, $fond, $texte);
			}
		}
	}
	return $texte;
}

?>