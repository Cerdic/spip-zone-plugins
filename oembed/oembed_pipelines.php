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

	$ins = '<link rel="alternate" type="application/json+oembed" href="<?php include_spip(\'inc/filtres_mini\');echo parametre_url(url_absolue("'.parametre_url($service,'format','json').'"),"url",url_absolue(self()));?>" />'."\n";
	/*
	$ins .= '<link rel="alternate" type="text/xml+oembed" href="<?php echo parametre_url(url_absolue("'.parametre_url($service,'format','xml').'"),"url",url_absolue(self()));?>" />'."\n";
	*/
	$ins = "<?php if (!in_array(_request(_SPIP_PAGE),array('login')) AND strpos(\$_SERVER['REQUEST_URI'],'debut_')===false){?>$ins<?php } ?>";

	return $head.$ins;
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
	$medias = array('photo' => 'image','video' => 'video', 'sound'=>'audio');
	include_spip('inc/config');
	include_spip('inc/oembed');
	// on tente de récupérer les données oembed
	if ($data = oembed_recuperer_data($flux['source'])){
		// si on a recupere une URL c'est direct un doc distant
		if (isset($data['url'])
			AND $data['type']!=='rich'
			// on recupere les infos du document distant
			AND $doc = recuperer_infos_distantes($data['url'])) {
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
		elseif(isset($data['html']) OR $data['type']=='link'){
			if ($data['type']=='link')
				$data['html'] = '<a href="' . $flux['source'] . '">' . sinon($data['title'],$flux['source']) . '</a>';
			// créer une copie locale du contenu html
			// cf recuperer_infos_distantes()
			// generer un nom de fichier unique : on l'index sur l'id du prochain document + uniqid
			$id = sql_getfetsel("id_document","spip_documents","","","id_document DESC","0,1");
			include_spip("inc/acces");
			$id = "id$id-".creer_uniqid();
			$id = substr(md5($id),0,7);
			$doc['fichier'] = _DIR_RACINE . nom_fichier_copie_locale($flux['source'], "html");
			$doc['fichier'] = preg_replace(",\.html$,i","-$id.html",$doc['fichier']);
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
			if (
			     (isset($data['thumbnail_url']) AND $v=$data['thumbnail_url'])
			  OR (isset($data['image']) AND $v=$data['image'])
			) {
				spip_log('ajout de la vignette '.$v.' pour '.$flux['data']['oembed'],'oembed.'._LOG_DEBUG);
				// cf formulaires_illustrer_document_traiter_dist()
				$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				$files = false;
				if (preg_match(",^(\w+:)?//,",$v)){
					$files = array(
						array(
							'name' => basename($v),
							'tmp_name' => $v,
							'distant' => true,
						)
					);
				}
				elseif (file_exists($v)) {
					$files = array(array(
						'name' => basename($v),
						'tmp_name' => $v
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

	// si oembed/embed_auto==oui on oembed les liens qui sont tous seuls sur une ligne
	// (mais jamais les liens inline dans le texte car ca casse trop l'ancien contenu)
	if (stripos($texte,"<a")!==false
	  AND stripos($texte,"auto")!==false
	  AND stripos($texte,"spip_out")!==false
		AND lire_config('oembed/embed_auto','oui')!='non'
	  AND strpos($texte,"\n")!==false) {
		preg_match_all(",(^|(?:\r?\n\r?\n)) *(<a\b[^>]*>[^\r\n]*</a>) *((?:\r?\n\r?\n)|$),Uims",trim($texte),$matches,PREG_SET_ORDER);
		if (count($matches)){

			$replace = array();

			include_spip('inc/oembed');
			foreach ($matches as $match) {
				if (!isset($replace[$match[0]])
				  AND preg_match(',\bauto\b,', extraire_attribut($match[2], 'class'))
				  AND !is_null($emb = oembed_embarquer_lien($match[2]))) {
					if ($wrap_embed_html = charger_fonction("wrap_embed_html","inc",true)){
						$emb = $wrap_embed_html($match[2],$emb);
					}
					$replace[$match[0]] = $match[1] . echappe_html("<html>$emb</html>") . $match[3];
				}
			}

			if (count($replace))
				$texte = str_replace(array_keys($replace), array_values($replace), $texte);
		}
	}
	return $texte;
}

include_spip('inc/config');
if (!function_exists('lire_config')) { function lire_config($a=null,$b=null) { return $b; } }

