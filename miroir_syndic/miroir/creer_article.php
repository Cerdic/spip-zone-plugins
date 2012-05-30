<?php
/*
 * Plugin miroir_syndic
 * (c) 2006-2012 Fil, Cedric
 * Distribue sous licence GPL
 *
 */

// un nouvel article : le creer
function miroir_creer_article_dist($t) {
	lang_select(trim(preg_replace(',[-_].*,', '', $t['lang'])));
	$lang = $GLOBALS['spip_lang'];
	lang_select();

	spip_log('insert', 'miroirsyndic');

	include_spip('action/editer_article');
	include_spip('inc/autoriser');
	autoriser_exception('publierdans','rubrique',$t['id_rubrique']); // se donner temporairement le droit
	if ($id_article = insert_article($t['id_rubrique'])) {
		autoriser_exception('modifier','article',$id_article); // se donner temporairement le droit
		articles_set($id_article,array(
				'titre'=>$t['titre'],
				'nom_site'=>$t['titre'],
				'url_site'=>$t['url'],
				'statut'=>'prop',
				'date'=>$t['date'],
				'lang' => $lang,
		));
		autoriser_exception('modifier','article',$id_article,false); // revenir a la normale
	}
	autoriser_exception('publierdans','rubrique',$t['id_rubrique'], false);

	spip_log("Creation article $id_article", 'miroirsyndic');
	return $id_article;
}