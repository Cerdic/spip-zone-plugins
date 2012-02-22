<?php

# la projection d'un objet c'est le contenu de cet objet au format
# HTML pour affichage plaisant mais contenant toutes les données
# pour permettre une recopie

# dans quel répertoire on fait ça… local/projection/


function projection($objet, $id_objet) {
	spip_log("projection $objet:$id_objet", "projection");
	spip_log($_SERVER['REQUEST_URI'], 'projection'); # verifier qu'on s'execute bien sur le cron

	if ($projection = charger_fonction('projection_'.$objet, 'inc', true)) {
	spip_log("a $projection", 'projection');
		$projection($objet, $id_objet);
	} else {
		projection_dist($objet, $id_objet);
	}

	spip_log("a $projection", 'projection');
}


function projection_dist($objet, $id_objet) {
	spip_log("Je ne sais pas faire la projection de $objet:$id_objet", "projection");

}

function inc_projection_articles_dist($objet, $id_objet) {
	if (!$dir = projection_dir($objet, $id_objet)) {
		spip_log('echec', 'projection');
		return false;
	}

	# contenu à enregistrer
	include_spip('abstract_sql');
	$obj = sql_fetsel('*', table_objet($objet), id_table_objet($objet).'='.$id_objet);
	# todo : retirer les champs inutiles, ajouter les jointures (auteurs, mots, documents)

	# fichier de projection
	$type = objet_type($objet); # 'article'
	$f = $dir.$type.'-'.$id_objet.'.yaml';
	spip_log($f, 'projection');

	# recuperer la representation complete de l'objet
	$representation = projection_representation($obj, $type);

	# on l'écrit et zou
	return ecrire_fichier($f, $representation);
}



function projection_dir($objet, $id_objet) {
	if ($p = sous_repertoire(_DIR_VAR, 'projection')
	AND $p = sous_repertoire($p,$objet))  # on pourrait organiser par rubrique
		return $p;
}

# à noter : json_encode est temporaire, on veut un vrai format
# avec de belles propriétés
function projection_representation($obj, $type) {
	include_spip('inc/yaml');

	// fallback json si yaml absent
	if (!function_exists('yaml_encode'))
		return json_encode($obj);

	// eliminer les champs vides ou null ou ayant une valeur par defaut
	$data = array_filter($obj);
	if ($data['date_redac'] == '0000-00-00 00:00:00')
		unset($data['date_redac']);
	if ($data['statut'] == 'publie')
		unset($data['statut']);

	// eliminer les champs inutiles
	foreach (explode(' ',
	'id_article id_rubrique id_secteur export date_modif lang langue_choisie accepter_forum maj'
	) as $i)
		unset($data[$i]);

	//
	// ajouter les jointures
	//
	
	# authors
	if (count($auteurs = sql_allfetsel('nom, bio', 'spip_auteurs a left join spip_auteurs_articles b on a.id_auteur=b.id_auteur', 'b.id_article='.$obj['id_article']))) {
		$data['authors'] = array_filter(array_map('projection_auteur', $auteurs));
		if (count($data['authors']) == 1)
			$data['authors'] = array_pop($data['authors']);
	} else
		$data['error'] .= sql_error();

	# tags
	if (count($mots = sql_allfetsel('titre, descriptif, texte', 'spip_mots a left join spip_mots_articles b on a.id_mot=b.id_mot', 'b.id_article='.$obj['id_article']))) {
		$data['tags'] = array_filter(array_map('projection_mot', $mots));
	} else
		$data['error'] .= sql_error();

	# documents
	if (count($docs = sql_allfetsel('titre, descriptif, fichier, a.id_document, vu, mode', 'spip_documents a left join spip_documents_liens b on a.id_document=b.id_document', "b.objet='$type' AND b.id_objet=".$obj['id_article']))) {
		$data['docs'] = array_filter(array_map('projection_doc', $docs));
	} else
		$data['error'] .= sql_error();

	# category
	if ($rub = sql_allfetsel('titre, descriptif, texte', 'spip_rubriques', "id_rubrique=".$obj['id_rubrique'])) {
		$data['category'] = projection_rubrique($rub[0]);
	} else
		$data['error'] .= sql_error();


	## le texte, c'est l'essentiel mais il ne figure pas dans l'entete
	unset($data['texte']);

	$rep = "##### projection de l'article $obj[id_article]\n"
		. "--- # metadata\n"
		. yaml_encode(array_filter($data))
		. "--- # content\n";

	$rep .= $obj['texte'];

	return $rep;
}


function projection_auteur($auteur) {
	$auteur = array_filter($auteur);
	if (count($auteur) == 1
	AND isset($auteur['nom']))
		$auteur = $auteur['nom'];
	return $auteur;
}
function projection_mot($mot) {
	$mot = array_filter($mot);
	if (count($mot) == 1
	AND isset($mot['titre']))
		$mot = $mot['titre'];
	return $mot;
}
function projection_doc($doc) {
	$doc = array_filter($doc);

	// URL absolue de maniere a pouvoir exporter
	if (isset($doc['fichier'])
	AND !preg_match(',://,', $fichier))
		$doc['fichier'] = url_absolue(_DIR_IMG.$doc['fichier']);

	if ($doc['vu'] == 'non') {
		unset($doc['id_document']);
		if ($doc['mode'] == 'image')
			$doc = array();
	}
	unset($doc['vu']);
	if ($doc['mode'] == 'document') unset($doc['mode']);

	if (count($doc) == 1
	AND isset($doc['fichier']))
		$doc = $doc['fichier'];
	return $doc;
}
function projection_rubrique($rub) {
	$rub = array_filter($rub);
	if (count($rub) == 1
	AND isset($rub['titre']))
		$rub = $rub['titre'];
	return $rub;
}

