<?php
function formulaires_insertion_video_charger_dist($id_article){
	$valeurs = array(
		'id_article' => $id_article,
		'url' => ''
		);
	return $valeurs;
}

function formulaires_insertion_video_verifier_dist(){
	$erreurs = array();
	// Retirer les trucs qui emmerdent : tous les arguments d'ancre / les espaces foireux les http:// et les www. éventuels
	$url = preg_replace('%(#.*$|http://|www.)%', '', trim(_request('url')));
	
	if(preg_match('/dailymotion/',$url)){
		set_request('type','dailym');
		$laVideo = preg_replace('#dailymotion\.com/video/#','',$url);
	}
	else if(preg_match('/vimeo/',$url)){
		set_request('type','vimeo');
		$laVideo = preg_replace('#vimeo\.com/#','',$url);
	}
	else if(preg_match('/(youtube|youtu\.be)/',$url)){
		set_request('type','youtube');
		$laVideo = preg_replace('#(youtu\.be/|youtube\.com/watch\?v=|&.*$|\?hd=1)#','',$url);
	}

	if(!$laVideo) $erreurs['pas_valide'] = _T('Adresse non valide.');
	else set_request('laVideo',$laVideo);

	return $erreurs;
}

function formulaires_insertion_video_traiter_dist($id_article){
	include_spip('inc/acces');	
	$type = _request('type');
	$fichier = _request('laVideo');
	$url = _request('url');
	
	if(!preg_match('/youtu\.be/',$url)){
		/*
			TODO Si on veut être compatible gentiment, il faut tester si on est bien en PHP5 sinon il ne faut pas utiliser la Classe Videopian et décommenter les 3 lignes suivantes
		*/
		include_spip('lib/Videopian'); // http://www.upian.com/upiansource/videopian/
		$Videopian = new Videopian();
		/*
			TODO Peut être qu'il serait bien de catcher l'erreur éventuelle par exemple en cas de refus de connexion par le serveur distant
		*/
		$infosVideo = $Videopian->get($url);		
		$titre = $infosVideo->title;
		$descriptif = $infosVideo->description;
		// $logoDocument = $infosVideo->thumbnails->0->url; // A brancher sur la copie de document
	}
	else{
		$titre = sql_getfetsel('titre','spip_articles',"id_article=".$id_article);
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
	if(filtre_info_plugin_dist('gestdoc','est_actif')){
		// Récupérer les infos
		$taille = $infosVideo->duration;
		$auteur = $infosVideo->author;
		// Remplir quelques champs de plus
		$champs['taille'] = $taille;
		$champs['credits'] = $auteur;
		$champs['statut'] = 'publie';
	}

	$document = sql_insertq('spip_documents',$champs);
	if($document){
		$document_lien = sql_insertq(
			'spip_documents_liens',
			array(
				'id_document'=>$document,
				'id_objet'=>$id_article,
				'objet'=>'article',
				'vu'=>'non'
			)
		);
	}
	
	$message_ok = _T('videos:confirmation_ajout', array('type'=>$type,'titre'=>$titre));
	return array("message_ok" => $message_ok);
}