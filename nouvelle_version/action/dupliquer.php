<?php
/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 2.0
 * Licence GPL (c) 2011
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

/**
 * Duplique un article dans la rubrique
 * - Conserve le contenu de l'article source
 * - Conserve les logos de l'article source
 * - Conserve le statut de publication de l'article source
 */
function dupliquer_article($article,$rubrique){
	include_spip('action/editer_article');
	include_spip('inc/modifier_article');
	include_spip('inc/modifier');

	// On lit l'article qui va etre dupliqué
	$champs = array('*');
	$from = 'spip_articles';
	$where = array( 
		"id_article=".$article
	);
	$infos = sql_allfetsel($champs, $from, $where);
	// On choisi les champs que l'on veut conserver
	// TODO éventuellement passer cette variable en CFG pour choisir depuis SPIP les champs à conserver ?
	$champs_dupliques = array(
		'surtitre','titre','soustitre','descriptif','chapo','texte','ps','accepter_forum','lang','langue_choisie','nom_site','url_site'
	);
	foreach ($champs_dupliques as $key => $value) {
		$infos_de_l_article[$value] = $infos[0][$value];
	}
	
	// On cherche ses mots clefs
	$mots_clefs_de_l_article = lire_les_mots_clefs($article,'article');

	//////////////
	// ON DUPLIQUE
	//////////////
	// On le clone avec les champs choisis ci-dessus, il sera NON publié par défaut
	$id_article = insert_article($rubrique);
	revision_article($id_article, $infos_de_l_article);
	
	// On lui rend son statut
	//$maj_statut_article = sql_updateq("spip_articles", array('statut' => $infos[0]['statut']), "id_article=".$id_article);
	
	// On lui rend met version_of à 1
	$maj_statut_article = sql_updateq("spip_articles", array('version_of' => $article), "id_article=".$id_article);

	// On lui remet ses mots clefs
	remettre_les_mots_clefs($mots_clefs_de_l_article,$id_article,'article');
	
	// On lui copie ses logos
	dupliquer_logo($article,$id_article,'article',false);
	dupliquer_logo($article,$id_article,'article',true);
	
	return $id_article;
}


function lire_les_mots_clefs($id,$type){
	$champs = array('id_mot');
	$from = 'spip_mots_'.$type.'s';
	$where = array( 
		"id_$type=".$id
	);
	$mots_clefs = sql_allfetsel($champs, $from, $where);
	
	return $mots_clefs;
}
function remettre_les_mots_clefs($mots,$id,$type){
	foreach($mots as $champ => $valeur){
		$n = sql_insertq(
			'spip_mots_'.$type.'s',
			array(
				'id_mot' => $valeur['id_mot'],
				'id_'.$type => $id
			)
		);
	}
	
	return true;
}

/* FONCTION HONTEUSEMENT ADAPTEE DE DOCUCOPIEUR ==> http://www.spip-contrib.net/DocuCopieur */
/* cette fonction realise la copie d'un logo d'article et de son logo de survol */
/* vers le nouvel article. */
function dupliquer_logo($id_source, $id_destination, $type='article', $bsurvol = false ){
	include_spip('action/iconifier');
	global $formats_logos;

	if ( $bsurvol == true )
	{
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
