<?php
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
	// Retirer les trucs qui emmerdent : tous les arguments d'ancre / les espaces foireux les http:// et les www. éventuels
	$url = preg_replace('%(#.*$|http://|www.)%', '', trim(_request('video_url')));

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
	else if(preg_match('/culturebox/',$url)){
		set_request('type','dist_cubox');
		// Lien de type http://culturebox.france3.fr/#/roman/32428/l_or-et-la-toise-le-nouveau-roman-de-brice-tarvel
		// On explode sur les slash et on recupere l'avant dernier element
		$result=explode("/",_request('video_url'));
		if(sizeof($result)>2)
			$lavideo = $result[sizeof($result)-2];
	}

	if(!$lavideo) $erreurs['message_erreur'] = _T('videos:erreur_adresse_invalide');
	else set_request('lavideo',$lavideo);

	return $erreurs;
}

function formulaires_insertion_video_traiter_dist($id_objet,$objet){
	include_spip('inc/acces');	
	$type = _request('type');
	$fichier = _request('lavideo');
	$url = _request('video_url');

	$titre = "";
	$descriptif = "";

	// On tente de récupérer titre et description à l'aide de Videopian
	if(!preg_match('/culture/',$url) && (version_compare(PHP_VERSION, '5.2') >= 0)) {

		include_spip('lib/Videopian'); // http://www.upian.com/upiansource/videopian/
		$Videopian = new Videopian();
		try {
			$infosVideo = $Videopian->get($url);		
			$titre = $infosVideo->title;
			$descriptif = $infosVideo->description;
			// $logoDocument = $infosVideo->thumbnails->0->url; // A brancher sur la copie de document
		} catch (Exception $e) {
			//echo 'Exception reçue : ',  $e->getMessage(), "\n";
			spip_log("L\'ajout automatique du titre et de la description a echoue","Plugin Videos");
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
	
	/** Gérer le cas de la présence de Médiathèque (parce que Mediatheque c'est le BIEN) **/
	if(filtre_info_plugin_dist('medias','est_actif')){
		if($infosVideo) {
			// Récupérer les infos
			$taille = $infosVideo->duration;
			$auteur = $infosVideo->author;
			// Remplir quelques champs de plus
			$champs['taille'] = $taille;
			$champs['credits'] = $auteur;
		}
		$champs['statut'] = 'publie';
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
	
	$message_ok = _T('videos:confirmation_ajout', array('type'=>$type,'titre'=>$titre));
	return array("message_ok" => $message_ok);
}