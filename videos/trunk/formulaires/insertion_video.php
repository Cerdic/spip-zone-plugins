<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function formulaires_insertion_video_charger_dist($id_objet,$objet){
	$valeurs = array(
		'id_objet' => $id_objet,
		'objet' => $objet,
		'video_url' => ''
		);
	return $valeurs;
}

function formulaires_insertion_video_verifier_dist($id_objet,$objet){
	$erreurs = array();
	// Retirer les trucs qui emmerdent : tous les arguments d'ancre / les espaces foireux les http://, https:// et les www. éventuels
	$url = preg_replace('%(#.*$|https?://|www.)%', '', trim(_request('video_url')));

	// ToDo : blinder un peu le controle des url
	if(preg_match('/dailymotion/',$url)){
		set_request('type','dist_daily');
		$lavideo = preg_replace('#dailymotion\.com/video/#','',$url);
	}
	else if(preg_match('/vimeo/',$url)){
		set_request('type','dist_vimeo');
		$lavideo = preg_replace('#vimeo\.com/#','',$url);
	}
	else if(preg_match('/(youtube|youtu\.be)/',$url)){
		set_request('type','dist_youtu');
		$lavideo = preg_replace('#(youtu\.be/|youtube\.com/watch\?v=|&.*$|\?hd=1)#','',$url);
	}
	/* On ne peut plus inserer les videos culture box
	else if(preg_match('/culturebox/',$url)){
		set_request('type','dist_cubox');
		// Lien de type http://culturebox.france3.fr/#/roman/32428/l_or-et-la-toise-le-nouveau-roman-de-brice-tarvel
		// On explode sur les slash et on recupere l'avant dernier element
		$result=explode("/",_request('video_url'));
		if(sizeof($result)>2)
			$lavideo = $result[sizeof($result)-2];
	}*/
	
	if(!$lavideo) $erreurs['message_erreur'] = _T('videos:erreur_adresse_invalide');
	else set_request('lavideo',$lavideo);

	return $erreurs;
}

function formulaires_insertion_video_traiter_dist($id_objet,$objet){
	include_spip('inc/acces');	
	$type = _request('type');
	$fichier = _request('lavideo');
	$url = _request('video_url');

	$titre = ""; $descriptif = ""; $id_vignette = "";

	// On tente de récupérer titre et description à l'aide de Videopian
	if(!preg_match('/culture/',$url) && (version_compare(PHP_VERSION, '5.2') >= 0)) {
		/*
			TODO
			Question ouverte : pourquoi ne pas utiliser => http://oohembed.com/ ? Nécessite quand même PHP5 (json) et semble faire pareil (mieux ?)
			- Inconvénient : dépend d'un service distant alors que là, c'est dans le plugin, ça marche direct
			- Avantage : sûrement mieux maintenu à jour, utilise JSON donc boucle DATA envisageables, réponse plus propre
		*/

		include_spip('lib/Videopian'); // http://www.upian.com/upiansource/videopian/
		$Videopian = new Videopian();
		if($Videopian) {
			$infosVideo = $Videopian->get($url);
			$titre = $infosVideo->title;
			$descriptif = $infosVideo->description;
		  $nbVignette = abs(count($infosVideo->thumbnails)-1);  // prendre la plus grande vignette       
      $logoDocument = $infosVideo->thumbnails[$nbVignette]->url;
      $logoDocument_width = $infosVideo->thumbnails[$nbVignette]->width;
      $logoDocument_height = $infosVideo->thumbnails[$nbVignette]->height;      
		} else {
			//echo 'Exception reçue : ',  $e->getMessage(), "\n";
			spip_log("L'ajout automatique du titre et de la description a echoué","Plugin Vidéo(s)");
		}
	}

	
	// On va pour l'instant utiliser le champ extension pour stocker le type de source
	$champs = array(
		'titre'=>$titre,
		'extension'=>$type,
		'date' => date("Y-m-d H:i:s",time()),
		'descriptif' => $descriptif,
		'fichier'=>$fichier,
		'distant'=>'oui'
	);
	
	/** Gérer le cas de la présence des champs de Médiathèque (parce que Mediatheque c'est le BIEN mais c'est pas toujours activé) **/
	$trouver_table=charger_fonction('trouver_table','base');	
	$desc = $trouver_table('spip_documents');
	if(array_key_exists('taille',$desc['field'])) if($infosVideo) $champs['taille'] = $infosVideo->duration;
	if(array_key_exists('credits',$desc['field'])) if($infosVideo) $champs['credits'] = $infosVideo->author;
	if(array_key_exists('statut',$desc['field'])) $champs['statut'] = 'publie';
  if(array_key_exists('media',$desc['field'])) $champs['media'] = 'video'; 

	/* Cas de la présence d'une vignette à attacher */
	if($logoDocument){
		include_spip('inc/distant');
		if($fichier = preg_replace("#IMG/#", '', copie_locale($logoDocument))){ // set_spip_doc ne fonctionne pas... Je ne sais pas pourquoi
			$champsVignette['fichier'] = $fichier;
			$champsVignette['mode'] = 'vignette';       
      // champs extra à intégrer ds SPIP 3
      if(array_key_exists('statut',$desc['field'])) $champsVignette['statut'] = 'publie';
      if(array_key_exists('media',$desc['field']))  $champsVignette['media'] = 'image';
       
			
			// Recuperer les tailles
			$champsVignette['taille'] = @intval(filesize($fichier));
			$size_image = @getimagesize($fichier);
			$champsVignette['largeur'] = intval($size_image[0]);
			$champsVignette['hauteur'] = intval($size_image[1]);
			// $infos['type_image'] = decoder_type_image($size_image[2]);
      if ($champsVignette['largeur']==0) {              // en cas d'echec, recuperer les infos videopian
           $champsVignette['largeur'] = $logoDocument_width;
           $champsVignette['hauteur'] = $logoDocument_height;
      }
     
			// Ajouter
			$id_vignette = sql_insertq('spip_documents',$champsVignette);
			if($id_vignette) $champs['id_vignette'] = $id_vignette;
		}
		else{ spip_log("Echec de l'insertion du logo $logoDocument pour la video $document","Plugin Vidéo(s)"); }
	}
	
	$document = sql_insertq('spip_documents',$champs);
	if($document){
		$document_lien = sql_insertq(
			'spip_documents_liens',
			array(
				'id_document'=>$document,
				'id_objet'=>$id_objet,
				'objet'=>$objet,
				'vu'=>'non'
			)
		);
	}
	
	$message_ok = _T('videos:confirmation_ajout', array('type'=>$type,'titre'=>$titre,'id_document'=>$document));    // faut il laisser le type ? c'est un peu cryptique pour le redacteur
	return array("message_ok" => $message_ok);
}