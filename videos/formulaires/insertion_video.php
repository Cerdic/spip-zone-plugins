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
	$url = _request('url');
	
	if(preg_match('/dailymotion/',$url)){
		set_request('type','dailym');
		$laVideo = preg_replace('#/video/#','',parse_url($url,PHP_URL_PATH));
	}
	else if(preg_match('/vimeo/',$url)){
		set_request('type','vimeo');
		$laVideo = preg_replace('#/#','',parse_url($url,PHP_URL_PATH));
	}
	else if(preg_match('/youtube/',$url)){
		set_request('type','youtube');
		$lUrl = explode('&',preg_replace('/\?/','',parse_url($url,PHP_URL_QUERY)));
		foreach($lUrl as $clef=>$valeur){
			if(preg_match('/v=/',$valeur)) $laVideo = preg_replace('#v=#','',$valeur);
		}
	}
	else if(preg_match('/youtu.be/',$url)){
		set_request('type','youtube');
		$laVideo = preg_replace('#http://youtu.be/#','',$url);
	}

	if(!$laVideo) $erreurs['pas_valide'] = _T('Adresse non valide.');
	else set_request('laVideo',$laVideo);
	return $erreurs;
}

function formulaires_insertion_video_traiter_dist($id_article){
	include_spip('inc/acces');
		
	// $fichier = serialize(array(_request('type'),_request('laVideo'))); // pour tout ranger dans #FICHIER
	$type = _request('type');
	$fichier = _request('laVideo');
	$titreArticle = sql_getfetsel('titre','spip_articles',"id_article=".$id_article);
	$titre = $type."-".$fichier."-".$titreArticle;
	
	// On va pour l'instant utiliser le champ extension pour stocker la source
	$champs = array(
		'titre'=>$titre,
		'extension'=>$type,
		'date' => date("Y-m-d H:i:s",time()),
		'fichier'=>$fichier,
		'distant'=>'oui'
	);
	/*
		TODO IF Mediatheque
		$champs['statut'] = 'publie';
		$champs['extrait'] = 'err';
	*/
	$document = sql_insertq('spip_documents',$champs);
	$document_lien = sql_insertq(
		'spip_documents_liens',
		array(
			'id_document'=>$document,
			'id_objet'=>$id_article,
			'objet'=>'article',
			'vu'=>'non'
		)
	);
	
	/*
		TODO Passer en variable de langue
	*/
	$message_ok = "La vidéo '"._request('type').":"._request('laVideo')."' a bien été ajoutée.";
	return array("message_ok" => $message_ok);
}