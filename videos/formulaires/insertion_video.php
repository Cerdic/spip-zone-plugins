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

	if(!$lavideo) $erreurs['message_erreur'] = _T('videos:erreur_adresse_invalide');
	else set_request('lavideo',$lavideo);

	return $erreurs;
}

function formulaires_insertion_video_traiter_dist($id_objet,$objet){
	include_spip('inc/acces');	
	$type = _request('type');
	$fichier = _request('lavideo');
	$url = _request('video_url');
	
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
		$titre = sql_getfetsel('titre',table_objet_sql($objet),id_table_objet($objet)."=".$id_objet);
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
				'id_objet'=>$id_objet,
				'objet'=>$objet,
				'vu'=>'non'
			)
		);
	}
	
	$message_ok = _T('videos:confirmation_ajout', array('type'=>$type,'titre'=>$titre));
	return array("message_ok" => $message_ok);
}