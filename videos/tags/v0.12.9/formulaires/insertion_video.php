<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_insertion_video_charger_dist($id_objet, $objet) {
	$valeurs = array(
		'id_objet' => $id_objet,
		'objet' => $objet,
		'video_url' => ''
	);

	return $valeurs;
}

function formulaires_insertion_video_verifier_dist($id_objet, $objet) {
	$erreurs = array();
	// Retirer les trucs qui emmerdent : tous les arguments d'ancre / les espaces foireux les http://, https:// et les www. éventuels
	$url = preg_replace('%(#.*$|https?://|www.)%', '', trim(_request('video_url')));

	// ToDo : blinder un peu le controle des url
	if (preg_match('/dailymotion/', $url)) {
		set_request('type', 'dist_daily');
		$lavideo = preg_replace('#dailymotion\.com/video/#', '', $url);
	} elseif (preg_match('/vimeo/', $url)) {
		set_request('type', 'dist_vimeo');
		$lavideo = preg_replace('#vimeo\.com/#', '', $url);
	} elseif (preg_match('/(youtube|youtu\.be)/', $url)) {
		set_request('type', 'dist_youtu');
		$lavideo = preg_replace('#(youtu\.be/|youtube\.com/watch\?v=|&.*$|\?hd=1)#', '', $url);
	}

	if (!$lavideo) {
		$erreurs['message_erreur'] = _T('videos:erreur_adresse_invalide');
	} else {
		set_request('lavideo', $lavideo);
	}

	return $erreurs;
}

function formulaires_insertion_video_traiter_dist($id_objet, $objet) {
	include_spip('inc/acces');
	$type = _request('type');
	$fichier = _request('lavideo');
	$url = _request('video_url');

	$titre = "";
	$descriptif = "";
	$id_vignette = "";

	// On tente de récupérer titre et description à l'aide de Videopian
	if (!preg_match('/culture/', $url) && (version_compare(PHP_VERSION, '5.2') >= 0)) {
		/*
			TODO
			Question ouverte : pourquoi ne pas utiliser => http://oohembed.com/ ? Nécessite quand même PHP5 (json) et semble faire pareil (mieux ?)
			- Inconvénient : dépend d'un service distant alors que là, c'est dans le plugin, ça marche direct
			- Avantage : sûrement mieux maintenu à jour, utilise JSON donc boucle DATA envisageables, réponse plus propre
		*/

		include_spip('lib/Videopian'); // http://www.upian.com/upiansource/videopian/

		try {
			$Videopian = new Videopian();
			if ($Videopian) {
				$infosVideo = $Videopian->get($url);
				$titre = $infosVideo->title;
				$descriptif = $infosVideo->description;
				$nbVignette = abs(count($infosVideo->thumbnails) - 1);  // prendre la plus grande vignette
				$logoDocument = $infosVideo->thumbnails[$nbVignette]->url;
				$logoDocument_width = $infosVideo->thumbnails[$nbVignette]->width;
				$logoDocument_height = $infosVideo->thumbnails[$nbVignette]->height;
				if (isset($infosVideo->thumbnails[$nbVignette]->weight)) {
					$logoDocument_weight = $infosVideo->thumbnails[$nbVignette]->weight;
				}
			}
		}
		catch (Exception $e) {
			spip_log("Echec ajout automatique titre+description : ".$e->getMessage(), 'videos' . _LOG_ERREUR);
			return array('message_erreur' => $e->getMessage());
		}

	}


	// On va pour l'instant utiliser le champ extension pour stocker le type de source
	$champs = array(
		'titre' => $titre,
		'extension' => $type,
		'date' => date("Y-m-d H:i:s", time()),
		'descriptif' => $descriptif,
		'fichier' => $fichier,
		'distant' => 'oui'
	);

	/** Gérer le cas de la présence des champs de Médiathèque (parce que Mediatheque c'est le BIEN mais c'est pas toujours activé) **/
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('spip_documents');

	if (array_key_exists('taille', $desc['field'])) {
		if ($infosVideo and isset($infosVideo->duration)) {
			$champs['taille'] = $infosVideo->duration;
		}
	}
	if (array_key_exists('credits', $desc['field'])) {
		if ($infosVideo and isset($infosVideo->author)) {
			$champs['credits'] = $infosVideo->author;
		}
	}
	if (array_key_exists('statut', $desc['field'])) {
		$champs['statut'] = 'publie';
	}
	if (array_key_exists('media', $desc['field'])) {
		$champs['media'] = 'video';
	}

	/* Cas de la présence d'une vignette à attacher */
	if ($logoDocument) {

		include_spip('inc/distant');
		// Dans las d'une mutu apparement le chemin n'est pas correct
		// on supprime donc tout ce qui peut se trouver avant IMG
		// http://lumadis.be/regex/test_regex.php?id=2543
		// Dans le cas d'une mutu sites/spip_site.tld/IMG/
		// Site spip seul
		// IMG/


		// cas de youtube : la vignette a un basename qui est toujours /hqdefault.jpg ou /maxresdefault.jpg
		// du coup si on a beaucoup de videos il y a collision sur les nom_fichier_local
		// on ameliore ca en retirant ce segment pour calculer le nom du fichier local, car il est precede de l'ID de la video
		$filename = $logoDocument;
		if ($type == "dist_youtu") {
			if (strncmp(basename($filename), "maxresdefault.", 14) == 0){
				$filename = str_replace("/maxresdefault.", ".", $filename);
			}
			if (strncmp(basename($filename), "hqdefault.", 10) == 0){
				$filename = str_replace("/hqdefault.", ".", $filename);
			}
		}
		$filename = fichier_copie_locale($filename);
		$filename = copie_locale($logoDocument, 'auto', $filename);
		//var_dump($filename);

		if ($fichier = preg_replace("#[a-z0-9/\._-]*IMG/#i", '', $filename)) {
			$champsVignette['fichier'] = $fichier;
			$champsVignette['mode'] = 'vignette';

			// champs extra à intégrer ds SPIP 3
			if (array_key_exists('statut', $desc['field'])) {
				$champsVignette['statut'] = 'publie';
			}
			if (array_key_exists('media', $desc['field'])) {
				$champsVignette['media'] = 'image';
			}


			// Recuperer les tailles
			$champsVignette['taille'] = @intval(filesize($fichier));
			$size_image = @getimagesize($fichier);
			$champsVignette['largeur'] = intval($size_image[0]);
			$champsVignette['hauteur'] = intval($size_image[1]);
			// $infos['type_image'] = decoder_type_image($size_image[2]);
			if ($champsVignette['largeur'] == 0) {              // en cas d'echec, recuperer les infos videopian
				$champsVignette['largeur'] = $logoDocument_width;
				$champsVignette['hauteur'] = $logoDocument_height;
				if (isset($logoDocument_weight)) {
					$champsVignette['taille'] = $logoDocument_weight;
				}
			}

			// Ajouter
			include_spip('action/editer_document');
			$id_vignette = document_inserer(null, $champsVignette);
			if ($id_vignette) {
				$champs['id_vignette'] = $id_vignette;
			}

		} else {
			spip_log("Echec de l'insertion du logo $logoDocument pour la video $document", 'Videos');
		}
	}

	include_spip('action/editer_document');
	$document = document_inserer(null, $champs);
	if ($document and $id_objet) {
		include_spip('action/editer_liens');
		objet_associer(
			array('document' => $document),
			array($objet => $id_objet),
			array('vu' => 'non')
		);
	}

	// faut il laisser le type ? c'est un peu cryptique pour le redacteur
	$message_ok = _T('videos:confirmation_ajout', array(
		'type' => $type,
		'titre' => $titre,
		'id_document' => $document
	));

	// recharger les documents après ajout d'un nouveau
	$message_ok .= "\n<script type='text/javascript'>if (window.jQuery) jQuery('#documents').ajaxReload();</script>";

	return array("message_ok" => $message_ok);
}
