<?php
/*
 * Plugin Comments
 * (c) 2010 Collectif
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/* pour que le pipeline ne rale pas ! */
function comments_autoriser(){}

/* Inserer des styles */
function comments_insert_head_css($flux){
	if ($f = find_in_path("css/comments.css"))
		$flux .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="all" />';
	return $flux;
}


/**
 * Generer les boutons d'admin des forum selon les droits du visiteur
 * en SPIP 2.1 uniquement
 * 
 * @param <type> $p
 * @return <type>
 */
function balise_BOUTONS_ADMIN_FORUM_dist($p) {
	if (($_id = interprete_argument_balise(1,$p))===NULL)
		$_id = champ_sql('id_forum', $p);

	if (function_exists('bouton_action'))
		$p->code = "
'<'.'?php
	if (\$GLOBALS[\'visiteur_session\'][\'statut\']==\'0minirezo\'
		AND (\$id = '.intval($_id).')
		AND	include_spip(\'inc/autoriser\')
		AND autoriser(\'moderer\',\'forum\',\$id)) {
			include_spip(\'inc/actions\');
			include_spip(\'inc/filtres\');
			echo \"<div class=\'boutons spip-admin actions modererforum\'>\"
			. bouton_action(_T(\'icone_supprimer_message\'),generer_action_auteur(\'instituer_forum\',\$id.\'-off\',ancre_url(self(),\'forum\')))
			. bouton_action(_T(\'SPAM\'),generer_action_auteur(\'instituer_forum\',\$id.\'-spam\',ancre_url(self(),\'forum\')))
			. \"</div>\";
		}
?'.'>'";
	else
		$p->code = "''";

	$p->interdire_scripts = false;

	return $p;
}


/**
 * Moderer le forum ?
 * = modifier l'objet correspondant (si forum attache a un objet)
 * = droits par defaut sinon (admin complet pour moderation complete)
 *
 * @param <type> $faire
 * @param <type> $type
 * @param <type> $id
 * @param <type> $qui
 * @param <type> $opt
 * @return <type>
 */
function autoriser_forum_moderer_dist($faire, $type, $id, $qui, $opt) {
	$row = sql_fetsel("*", "spip_forum", "id_forum=".intval($id));
	if (isset($row['objet']))
		return autoriser('modererforum', $row['objet'], $row['id_objet'], $qui, $opt);
	elseif($row['id_article'])
		return autoriser('modererforum', 'article', $row['id_article'], $qui, $opt);
	elseif($row['id_breve'])
		return autoriser('modererforum', 'breve', $row['id_breve'], $qui, $opt);
	elseif($row['id_rubrique'])
		return autoriser('modererforum', 'rubrique', $row['id_rubrique'], $qui, $opt);
	elseif($row['id_message'])
		return autoriser('modererforum', 'message', $row['id_message'], $qui, $opt);
	elseif($row['id_syndic'])
		return autoriser('modererforum', 'site', $row['id_syndic'], $qui, $opt);
	return false;
}


/**
 * surcharger les boucles FORUMS
 * pour afficher uniquement les forums public meme en preview
 *
 * @param <type> $boucle
 * @return <type>
 */
function comments_pre_boucle($boucle){
	if ($boucle->type_requete == 'forums') {
		$id_table = $boucle->id_table;
		$mstatut = $id_table .'.statut';
		// Par defaut, selectionner uniquement les forums sans mere
		// Les criteres {tout} et {plat} inversent ce choix
		if (!isset($boucle->modificateur['tout']) AND !isset($boucle->modificateur['plat'])) {
			array_unshift($boucle->where,array("'='", "'$id_table." ."id_parent'", 0));
		}
		// Restreindre aux elements publies
		if (!$boucle->modificateur['criteres']['statut']) {
				array_unshift($boucle->where,array("'='", "'$mstatut'", "'\\'publie\\''"));
		}
	}
	return $boucle;
}

/**
* Vérifier le formulaire de forum :
*
* - Utiliser le define _FORUM_LONGUEUR_MINI
*
* @param array $flux
* @return array $flux
*/
function comments_formulaire_verifier($flux){
	if ($flux['args']['form']=='forum'){
		// Si pas d'erreurs sur le texte et que l'on n'est pas en validation finale
		if(!$flux['data']['texte'] && !_request('confirmer_previsu_forum')){
			if (strlen($texte = _request('texte')) < _FORUM_LONGUEUR_MINI
			AND $GLOBALS['meta']['forums_texte'] == 'oui'){
				unset($flux['data']['previsu']);
				$flux['data']['texte'] = _T('comments:forum_attention_peu_caracteres',
					array(
						'compte' => strlen($texte),
						'min' => _FORUM_LONGUEUR_MINI
					));
			}
		}else{
			if (strlen($texte = _request('texte')) < _FORUM_LONGUEUR_MINI
				AND $GLOBALS['meta']['forums_texte'] == 'oui'){
					unset($flux['data']['previsu']);
					$flux['data']['texte'] = _T('comments:forum_attention_peu_caracteres',
						array(
							'compte' => strlen($texte),
							'min' => _FORUM_LONGUEUR_MINI
						));
			}else{
				unset($flux['data']['texte']);
				if(!_request('confirmer_previsu_forum')){
					$doc = &$_FILES['ajouter_document'];
					$flux['data']['previsu'] = inclure_previsu($texte, _request('titre'), _request('url_site'), _request('nom_site'), _request('ajouter_mot'), $doc,
						intval(_request('id_rubrique')), intval(_request('id_forum')), intval(_request('id_article',0)), intval(_request('id_breve')), intval(_request('id_syndic')));
				}
			}
		}
	}
	return $flux;
}
/**
 * Traiter le formulaire de forum :
 *
 * - ne pas rediriger en fin de traitement si pas d'url demandee explicitement
 *   et si on est pas sur la ?page=forum
 *
 * - preparer un message en cas de moderation
 *
 * @param <type> $flux
 * @return <type>
 */
function comments_formulaire_traiter($flux){
	if ($flux['args']['form']=='forum'
		){
		// args :
		// $titre, $table, $type, $script,
		// $id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic,
		// $ajouter_mot, $ajouter_groupe, $afficher_texte, $url_param_retour
		// si pas d'url de retour explicite
		$redirect = $flux['data']['redirect'];
		
		$f = chercher_filtre('info_plugin');
		if($f)
			$version_forum = $f('forum', 'version');

		if(!$version_forum OR ($version_forum < '1')){
			if (!isset($flux['args']['args'][12]) OR !$flux['args']['args'][12]){
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
		}
		$id_forum = $flux['data']['id_forum'];
		include_spip('base/abstract_sql');
		$statut = sql_getfetsel('statut','spip_forum','id_forum='.intval($id_forum));
		if ($statut=='publie'){
			// le message est OK, il suffit de mettre une ancre !
			$flux['data']['message_ok'] = 
			  _T('comments:reponse_comment_ok')
				. "<script type='text/javascript'>jQuery(function(){
			jQuery('#formulaire_forum .reponse_formulaire').detach().appendTo(jQuery('#forum$id_forum').parent()).addClass('success');
			jQuery('#forum$id_forum').parent().positionner();
			//window.location.hash='forum$id_forum';
			});</script>";
			;
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