<?php
/*
 * Plugin Comments
 * (c) 2010 Collectif
 * Distribue sous licence GPL
 *
 */

/* pour que le pipeline ne rale pas ! */
function comments_autoriser(){}

/**
 *
 * Inserer des styles
 *
 * @param string $flux
 * @return string
 */
function comments_insert_head_css($flux){
	if ($f = find_in_path("css/comments.css"))
		$flux .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="all" />';
	return $flux;
}


/**
 * Generer les boutons d'admin des forum selon les droits du visiteur
 * en SPIP 2.1 uniquement
 * 
 * @param object $p
 * @return object
 */
function balise_BOUTONS_ADMIN_FORUM_dist($p) {
	if (($_id = interprete_argument_balise(1,$p))===NULL)
		$_id = champ_sql('id_forum', $p);

		$p->code = "
'<'.'?php
	if (\$GLOBALS[\'visiteur_session\'][\'statut\']==\'0minirezo\'
		AND (\$id = '.intval($_id).')
		AND	include_spip(\'inc/autoriser\')
		AND autoriser(\'moderer\',\'forum\',\$id)) {
			include_spip(\'inc/actions\');include_spip(\'inc/filtres\');
			echo \"<div class=\'boutons spip-admin actions modererforum\'>\"
			. bouton_action(_T(\'forum:icone_supprimer_message\'),generer_action_auteur(\'instituer_forum\',\$id.\'-off\',ancre_url(self(),\'forum\')))
			. bouton_action(_T(\'forum:icone_bruler_message\'),generer_action_auteur(\'instituer_forum\',\$id.\'-spam\',ancre_url(self(),\'forum\')))
			. \"</div>\";
		}
?'.'>'";

	$p->interdire_scripts = false;
	return $p;
}

/**
 * Traiter le formulaire de forum :
 *
 * - ne pas rediriger en fin de traitement si pas d'url demandee explicitement
 *   et si on est pas sur la ?page=forum
 *
 * - preparer un message en cas de moderation
 *
 * @param array $flux
 * @return array
 */
function comments_formulaire_traiter($flux){
	if ($flux['args']['form']=='forum'
		){
		// args :
		// $objet,$id_objet, $id_forum,$ajouter_mot, $ajouter_groupe, $afficher_previsu, $retour
		// si pas d'url de retour explicite
		$redirect = $flux['data']['redirect'];
		if (!isset($flux['args']['args'][6]) OR !$flux['args']['args'][6]){
			// si on est pas sur la page forum, on ne redirige pas
			// mais il faudra traiter l'ancre
			if (!($p=_request('page')) OR $p!=='forum'){
				unset($flux['data']['redirect']);
				// mais on le remet editable !
				$flux['data']['editable']=true;
				// vider la saisie :
				set_request('texte');
				set_request('titre');
				set_request('url_site');
				set_request('ajouter_groupe');
				set_request('ajouter_mot');
				set_request('id_forum');
			}
		}

		$id_forum = $flux['data']['id_forum'];
		include_spip('base/abstract_sql');
		$statut = sql_getfetsel('statut','spip_forum','id_forum='.intval($id_forum));
		if ($statut=='publie'){
			// le message est OK, il suffit de mettre une ancre !
			$flux['data']['message_ok'] = 
			  _T('comments:reponse_comment_ok');
			if (!isset($flux['data']['redirect'])){
				$flux['data']['message_ok'] .=
						"<script type='text/javascript'>function move_comment$id_forum(){
jQuery('#formulaire_forum .reponse_formulaire').detach().appendTo(jQuery('#forum$id_forum').parent()).addClass('success');
jQuery('#forum$id_forum').parent().positionner();
//window.location.hash='forum$id_forum';
}
jQuery(function(){jQuery('.comments-posts').ajaxReload({callback:move_comment$id_forum})});</script>";

			}
		}
		else {
			// dire que le message a ete modere
			$flux['data']['message_ok'] = _T('comments:reponse_comment_modere');
		}
		
		$res = $flux['data'];
	#var_dump($flux);
	}
	#die('paf');
	return $flux;

}
?>