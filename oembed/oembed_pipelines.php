<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Inserer une CSS pour le contenu embed
 * @param $head
 * @return string
 */
function oembed_insert_head_css($head){
	$head .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/oembed.css').'" />'."\n";
	return $head;
}

/**
 * annoncer le service oembed dans le head des pages publiques
 *
 * @param string $head
 * @return string
 */
function oembed_insert_head($head) {
	$service = "oembed.api/";
	$head .= '<link rel="alternate" type="application/json+oembed" href="<?php include_spip(\'inc/filtres_mini\');echo parametre_url(url_absolue("'.parametre_url($service,'format','json').'"),"url",url_absolue(self()));?>" />'."\n";
	$head .= '<link rel="alternate" type="text/xml+oembed" href="<?php echo parametre_url(url_absolue("'.parametre_url($service,'format','xml').'"),"url",url_absolue(self()));?>" />'."\n";
	return $head;
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
		include_spip('inc/oembed');
		$providers = oembed_lister_providers();
		$hosts = array();
		foreach($providers as $scheme=>$endpoint){
			$h = parse_url($scheme,PHP_URL_HOST);
			$hosts[trim(preg_replace(",^(\*|www)\.,i","",$h))]=true;
		}
		$hosts = implode(', ',array_keys($hosts));
		$i = _T('oembed:explication_upload_url',array('hosts'=>$hosts));
		$i = "<p class='explication small'>$i</p>";
		$flux['data'] = str_replace($t="<!--editer_url-->",$t.$i,$flux['data']);
	}
	return $flux;
}

/**
 * insertion des traitements oembed dans l'ajout des documents distants
 * reconnaitre une URL oembed (car provider declare ou decouverte automatique active)
 * et la pre-traiter pour recuperer le vrai document a partir de l'url concernee
 *
 * @param array $flux
 * @return array
 */
function oembed_renseigner_document_distant($flux) {
	$medias = array('photo' => 'image','video' => 'video');
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
				if (isset($data['media']))
					$doc['media'] = $data['media'];
				elseif (isset($medias[$data['type']]))
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
			if (isset($data['media']))
				$doc['media'] = $data['media'];
			elseif (isset($medias[$data['type']]))
				$doc['media'] = $medias[$data['type']];
			return $doc;
		}
	}
	return $flux;
}

/**
 * attacher la vignette si disponible pour les documents oembed
 * on les reconnait via la presence d'un oembed non vide
 * on relance un appel a oembed_recuperer_data qui a garde la requete precendente en cache
 *
 * @param array $flux
 * @return array
 */
function oembed_post_edition($flux) {
	if($flux['args']['action']=='ajouter_document' AND $flux['data']['oembed']){
		$id_document = $flux['args']['id_objet'];
		if ($data = oembed_recuperer_data($flux['data']['oembed'])){
			// vignette disponible ? la recupérer et l'associer au document
			if ($data['thumbnail_url']) {
				spip_log('ajout de la vignette'.$data['thumbnail_url'].' pour '.$flux['data']['oembed'],'oembed.'._LOG_DEBUG);
				// cf formulaires_illustrer_document_traiter_dist()
				$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				$files = false;
				if (preg_match(",^\w+://,",$data['thumbnail_url'])){
					$files = array(
						array(
							'name' => basename($data['thumbnail_url']),
							'tmp_name' => $data['thumbnail_url'],
							'distant' => true,
						)
					);
				}
				elseif (file_exists($data['thumbnail_url'])) {
					$files = array(array(
						'name' => basename($data['thumbnail_url']),
						'tmp_name' => $data['thumbnail_url']
					));
				}
				if ($files
					AND $ajoute = action_ajouter_documents_dist('new',$files,'',0,'vignette')
				  AND is_int(reset($ajoute))){
					$id_vignette = reset($ajoute);
					include_spip('action/editer_document');
					document_modifier($id_document,array("id_vignette" => $id_vignette));
				}
			}
			else
				spip_log('pas de vignette pour '.$flux['data']['oembed'],'oembed.'._LOG_DEBUG);
		}
	}
	return $flux;
}

/**
 * Transformation auto des liens vers contenu oembed correspondant : trop la classe
 *
 * @param string $texte
 * @return mixed
 */
function oembed_pre_propre($texte) {
	include_spip('inc/config');
	if (lire_config('oembed/embed_auto','oui')!='non') {
		include_spip('inc/oembed');
		foreach (extraire_balises($texte, 'a') as $lien) {
			if ($url = extraire_attribut($lien, 'href')
			# seuls les autoliens beneficient de la detection oembed
			AND preg_match(',\bauto\b,', extraire_attribut($lien, 'class'))
			AND (oembed_verifier_provider($url) OR (lire_config('oembed/detecter_lien','non')=='oui'))) {
				$fond = recuperer_fond('modeles/oembed',array('url'=>$url,'lien'=>$lien));
				if ($fond = trim($fond))
					$texte = str_replace($lien, echappe_html("<html>$fond</html>"), $texte);
			}
		}
	}
	return $texte;
}

include_spip('inc/config');
if (!function_exists('lire_config')) { function lire_config($a=null,$b=null) { return $b; } }

?>
