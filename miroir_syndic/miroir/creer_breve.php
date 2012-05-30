<?php
/*
 * Plugin miroir_syndic
 * (c) 2006-2012 Fil, Cedric
 * Distribue sous licence GPL
 *
 */

// une nouvelle breve : la creer
function miroir_creer_breve_dist($t) {
	lang_select(trim(preg_replace(',[-_].*,', '', $t['lang'])));
	$lang = $GLOBALS['spip_lang'];
	lang_select();

	spip_log('insert', 'miroirsyndic');

	include_spip('action/editer_breve');
	include_spip('inc/autoriser');
	autoriser_exception('publierdans','rubrique',$t['id_rubrique']); // se donner temporairement le droit
	if ($id_breve = breve_inserer($t['id_rubrique'])) {
		autoriser_exception('modifier','breve',$id_breve); // se donner temporairement le droit
		breve_modifier($id_breve,array(
				'titre'=>$t['titre'],
				'lien_titre'=>$t['titre'],
				'lien_url'=>$t['url'],
				'statut'=>'prop',
				'date_heure'=>$t['date'],
				'lang' => $lang,
		));
		autoriser_exception('modifier','breve',$id_breve,false); // revenir a la normale
	}
	autoriser_exception('publierdans','rubrique',$t['id_rubrique'], false);

	spip_log("Creation breve $id_breve", 'miroirsyndic');
	return $id_breve;
}