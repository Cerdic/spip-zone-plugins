<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function trim_value(&$value){$value = trim($value);}

/**
 * Duplique un article dans la rubrique cible
 * - Conserve le contenu de l'article source
 * - Conserve les logos de l'article source
 * - Conserve le statut de publication de l'article source
 */
function dupliquer_article($id_article,$rubrique){
	include_spip('action/editer_article');
	include_spip('inc/modifier_article');
	include_spip('inc/modifier');
	include_spip('inc/config');
		
	// On lit l'article qui va etre dupliqué
	$infos = sql_fetsel("*", 'spip_articles', "id_article=".intval($id_article));

	// On choisi les champs que l'on veut conserver
	$champs_dupliques = explode(",", lire_config('duplicator/config/art_champs'));
	array_walk($champs_dupliques, 'trim_value');
	
	if ($champs_dupliques[0]==''){ $champs_dupliques = array( 'surtitre','titre','soustitre','descriptif','chapo','texte','ps','accepter_forum','lang','langue_choisie','nom_site','url_site' ); }
	
	// Si le plugin composition est présent
	if (test_plugin_actif('compositions')) {
		$champs_dupliques[] = 'composition';
		$champs_dupliques[] = 'composition_lock';
	}

	foreach ($champs_dupliques as $key => $value) {
		$infos_de_l_article[$value] = $infos[$value];
	}

	// On cherche ses mots clefs
	$mots_clefs_de_l_article = lire_les_mots_clefs($id_article,'article');

	//////////////
	// ON DUPLIQUE
	//////////////
	// On le clone avec les champs choisis ci-dessus, il sera NON publié par défaut
	$id_article = insert_article($rubrique);
	revision_article($id_article, $infos_de_l_article);
	
	// On lui rend son statut
	$maj_statut_article = sql_updateq("spip_articles", array('statut' => $infos[0]['statut']), "id_article=".$id_article);

	// On lui remet ses mots clefs
	remettre_les_mots_clefs($mots_clefs_de_l_article,$id_article,'article');
	
	// On lui copie ses logos
	dupliquer_logo($id_article,$id_article,'article',false);
	dupliquer_logo($id_article,$id_article,'article',true);
	
	/////////////////////////////////////
	// Duplication des url dans spip_url
	/////////////////////////////////////
	$where = array( 
		"id_objet=".intval($id_article),
		"type='article'",
	);
	$infos_url = sql_fetsel('*', 'spip_urls', $where, 'date', 'date DESC');
	
	$infos_url['id_objet'] = $id_article;
	$url = $infos_url['url'];
	//$infos_url['url']
	$u = preg_replace('/(.*)(-|,)\d*$/', '$1', $url, -1, $c); // supprimer le numéro à la fin
	if ($c == 0) $infos_url['url'] = $url.'-'.$id_article; // Ajoute le numéro de l'article
	else $infos_url['url'] = $u.'-'.$id_article;
	sql_insertq('spip_urls', $infos_url);
	
	return $id_article;
}

/**
 * Duplique une rubrique dans la rubrique qui la contient
 * - Conserve le contenu de la rubrique source
 * - Conserve les mots clefs de la rubrique source
 * - Conserve les articles de la rubrique source
 * 
 * @param int $id_rubrique
 * 	Identifiant numérique de la rubrique à dupliquer
 * @param int $cible null
 * 	Identifiant numérique de la rubrique dans laquelle insérer la copie
 * @param string $titre ' (copie)'
 * 	Chaîne de texte qui sera ajouté au titre de la rubrique copiée
 * @param bool $articles true
 * 	Doit on dupliquer également les articles (true/false)
 */
function dupliquer_rubrique($id_rubrique,$cible=null,$titre=' (copie)',$articles = true){
	include_spip('action/editer_rubrique');
	include_spip('inc/config');
		
	// On lit la rubrique qui va etre dupliquée
	$infos = sql_fetsel('*', 'spip_rubriques', "id_rubrique=".intval($id_rubrique));

	// On choisi les champs que l'on veut conserver
	$champs_dupliques = explode(",", lire_config('duplicator/config/rub_champs'));
	array_walk($champs_dupliques, 'trim_value');
	
	if ($champs_dupliques[0]=="") $champs_dupliques = array('id_parent','titre','descriptif','texte','lang','langue_choisie');
	
	// Si le plugin composition est présent
	if (test_plugin_actif('compositions')) {
		$champs_dupliques[] = 'composition';
		$champs_dupliques[] = 'composition_lock';
		$champs_dupliques[] = 'composition_branche_lock';
	}
	
	foreach ($champs_dupliques as $key => $value) {
		$infos_de_la_rubrique[$value] = $infos[$value];
	}
	// Si une cible est spécifiée, on ecrase le champ id_parent
	if($cible) $infos_de_la_rubrique['id_parent'] = $cible;
	$infos_de_la_rubrique['titre'] .= $titre;

	// On cherche ses mots clefs
	$mots_clefs_de_la_rubrique = lire_les_mots_clefs($id_rubrique,'rubrique');
	
	// On cherche ses sous-rubriques
	$rubriques_de_la_rubrique = sql_allfetsel('id_rubrique', 'spip_rubriques', "id_parent=".intval($id_rubrique));

	//////////////
	// ON DUPLIQUE
	//////////////
	$id_nouvelle_rubrique = insert_rubrique($infos_de_la_rubrique['id_parent']);
	revisions_rubriques($id_nouvelle_rubrique,$infos_de_la_rubrique);
	// On la publie (pour activer l'aperçu)
	$maj_statut_rubrique = sql_updateq("spip_rubriques", array('statut' => 'publie'), "id_rubrique=".$id_nouvelle_rubrique);

	/////////////////////////////////////
	// Duplication des url dans spip_url
	/////////////////////////////////////
	$where = array( 
		"id_objet=".intval($id_rubrique),
		"type='rubrique'",
	);
	$infos_url = sql_fetsel('*', 'spip_urls', $where, 'date', 'date DESC');
	
	$infos_url['id_objet'] = $id_nouvelle_rubrique;
	$url = $infos_url['url'];
	//$infos_url['url']
	$u = preg_replace('/(.*)(-|,)\d*$/', '$1', $url, -1, $c); // supprimer le numéro à la fin
	if ($c == 0) $infos_url['url'] = $url.'-'.$id_nouvelle_rubrique; // Ajoute le numéro de l'article
	else $infos_url['url'] = $u.'-'.$id_nouvelle_rubrique;
	sql_insertq('spip_urls', $infos_url);
	
	// On lui remet ses mots clefs
	remettre_les_mots_clefs($mots_clefs_de_la_rubrique,$id_nouvelle_rubrique,'rubrique');

	// On lui remet ses articles
	if($articles){
		// On cherche ses articles
		$articles_de_la_rubrique = sql_allfetsel('id_article', 'spip_articles', "id_rubrique=".intval($id_rubrique));
		foreach($articles_de_la_rubrique as $champ => $valeur){
			$id_article = dupliquer_article($valeur['id_article'],$id_nouvelle_rubrique);
		}
	}

	// On lui copie ses logos
	dupliquer_logo($id_rubrique,$id_nouvelle_rubrique,'rubrique',false);
	dupliquer_logo($id_rubrique,$id_nouvelle_rubrique,'rubrique',true);

	pipeline('duplicator',array('objet'=>'rubrique','id_objet_origine' => $id_rubrique,'id_objet'=>$id_nouvelle_rubrique));
	// On lui remet ses sous-rubrique (+ mots clefs + articles + sous rubriques)
	foreach($rubriques_de_la_rubrique as $champ => $valeur){
		$id_rubrique = $valeur['id_rubrique'];
		$nouvelle_sous_rubrique = dupliquer_rubrique($id_rubrique,$id_nouvelle_rubrique,'',$articles);
	}
	
	return $id_nouvelle_rubrique;
}

function lire_les_mots_clefs($id,$type){
	$champs = array('id_mot');
	$from = 'spip_mots_liens';
	$where = array( 
		"id_objet=".intval($id),
		"objet=".sql_quote($type)
	);
	$mots_clefs = sql_allfetsel($champs, $from, $where);
	
	return $mots_clefs;
}

function remettre_les_mots_clefs($mots = array(),$id,$type){
	foreach($mots as $champ => $valeur){
		$n = sql_insertq(
			'spip_mots_liens',
			array(
				'id_mot' => $valeur['id_mot'],
				'id_objet' => intval($id),
				'objet' => $type
			)
		);
	}
	
	return true;
}

/* FONCTION HONTEUSEMENT ADAPTEE DE DOCUCOPIEUR ==> http://www.spip-contrib.net/DocuCopieur */
/* cette fonction realise la copie d'un logo d'article/rubrique et de son logo de survol */
/* vers le nouvel article/rubrique. */
function dupliquer_logo($id_source, $id_destination, $type='article', $bsurvol = false ){
	include_spip('action/iconifier');
	global $formats_logos;

	if ( $bsurvol == true ){
		$logo_type = 'off';	// logo survol
	} else  $logo_type = 'on';	// logo 

	$chercher_logo = charger_fonction('chercher_logo', 'inc');

	$logo_source = $chercher_logo($id_source, 'id_'.$type, $logo_type );
	$logo_source = $logo_source[0];
	if ( !file_exists($logo_source) ) return false;

	$size = @getimagesize($logo_source);
	$mime = !$size ? '': $size['mime'];
	$source['name'] = basename($logo_source);
	$source['type'] = $mime;
	$source['tmp_name'] = $logo_source;
	$source['error'] = 0;
	$source['size'] = @filesize($logo_source);

	action_spip_image_ajouter_dist(substr($type,0,3). $logo_type .$id_destination, 'local', $source );
	return true;
}

