<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2013 - Distribue sous licence GNU/GPL
 *
 * Formulaire d'édition par lot de médias
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_media_edition_lot_charger_dist(){
	$valeurs = formulaires_editer_objet_charger('article',$id_article,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	$diogene = sql_fetsel('*','spip_diogenes','objet="emballe_media"');
	$valeurs['id_secteur'] = $valeurs['id_parent'] = $diogene['id_secteur'];
	$valeurs['id_diogene'] = $diogene['id_diogene'];
	if($diogene['champs_ajoutes'])
		$valeurs['diogene_args'] = array('champs_ajoutes'=>$diogene['champs_ajoutes']);
	$valeurs['statuts_medias'] = _request('statuts_medias') ? _request('statuts_medias') : 'prepa';	
	if(_request('change_statut_prop')){
		$valeurs['debut_medias'] = 0;
		$valeurs['statuts_medias'] = 'prop';
		set_request('statuts_medias','prop');
	}
	if(_request('change_statut_publie')){
		$valeurs['debut_medias'] = 0;
		$valeurs['statuts_medias'] = 'publie';
		set_request('statuts_medias','publie');
	}
	if(_request('change_statut_prepa')){
		$valeurs['debut_medias'] = 0;
		$valeurs['statuts_medias'] = 'prepa';
		set_request('statuts_medias','prepa');
	}
	
	$valeurs['statut'] = $valeurs['statuts_medias'];
	unset($valeurs['id_article']);
	$valeurs['medias'] = _request('medias');
	set_request('statut',$valeurs['statut']);
	$valeurs['_pipeline'] = array('editer_contenu_objet',array('type'=>'article'));

	$valeurs['config'] = $GLOBALS['meta'];
	include_spip('inc/autoriser');
	if(autoriser('publierdans','rubrique',$diogene['id_secteur']))
		$valeurs['autoriser_publier'] = 'oui';

	return $valeurs;
}

function formulaires_media_edition_lot_verifier_dist(){
	$erreurs = array();
	if(_request('change_statut_prop') OR _request('change_statut_publie') OR _request('change_statut_prepa')){
		return $erreurs;
	}
	if((!_request('medias') OR count(_request('medias')) == 0) && !_request('change_statut_prop') && !_request('change_statut_publie') && !_request('change_statut_prepa')){
		$erreurs['medias'] = _T('emballe_medias:erreur_lot_selection_medias');
	}
	else{
		$medias = _request('medias');
		
		foreach(objet_info('article','champs_editables') as $champ){
			if(_request($champ) && _request($champ) == '')
				set_request($champ,false);
		}
		
		$erreurs = formulaires_editer_objet_verifier('article',$medias[0]);
		
		if(_request('titre') != '' && !_request('forcer_traiter_titre')){
			foreach($medias as $id_article){
				$titre = sql_getfetsel('titre','spip_articles','id_article='.intval($id_article));
				if(!preg_match(',^(IMG \d+|img \d+|imgp\d+|DSC\d+|photo\d+|image\.).*,',$titre)){
					$erreurs['titre_demander_validation'] = _T('emballe_medias:erreur_demander_validation_titre');
					break;
				}
			}
		}
	}
	if (count($erreurs)) $erreurs['message_erreur'] = _T('emballe_medias:verifier_formulaire');
	
	return $erreurs;
}

function formulaires_media_edition_lot_traiter_dist(){
	$res = array('editable'=> true);
	if(_request('change_statut_tout') || _request('change_statut_publie') || _request('change_statut_prepa') || _request('change_statut_prop')){
		return $res;
	}

	$medias_a_traiter = _request('medias');
	
	$statut = _request('statut');
	if(($statut && !in_array($statut,array('prop','prepa','publie','poubelle'))) || ($statut == ''))
		set_request('statut',NULL);

	$titre = _request('titre');
	if($titre != ''){
		$i = 1;
		$liste_medias = implode(',',$medias_a_traiter);
		$last_titre = sql_getfetsel('titre','spip_articles',"titre REGEXP ".sql_quote("^$titre [[:alnum:]]+")." AND id_article NOT IN ($liste_medias)",'maj desc');
		if($last_titre){
			preg_match(',\d+$,',$last_titre,$matches);
			$i = array_pop($matches);
			$i++;
		}
	}
	foreach($medias_a_traiter as $id_article){
		if($titre == ''){
			$titre_envoi = sql_getfetsel('titre','spip_articles','id_article='.intval($id_article));
			set_request('titre',$titre_envoi);
		}else{
			$titre_envoi = $titre." ".$i;
			set_request('titre',$titre_envoi);
			$i++;
		}
		$res_id = formulaires_editer_objet_traiter('article',$id_article,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	}
	$res['message_ok'] = singulier_ou_pluriel(count($medias_a_traiter),'emballe_medias:message_medias_maj_un','emballe_medias:message_medias_maj_nb');
	if($statut != _request('statuts_medias')){
		$texte_statuts = objet_info('article','statut_titres');
		$texte_statut = isset($texte_statuts[$statut]) ? _T($texte_statuts[$statut]) : $statut;
		$res['message_ok'] .= "<br />".singulier_ou_pluriel(count($medias_a_traiter),'emballe_medias:message_medias_maj_statut_un','emballe_medias:message_medias_maj_statut_nb','nb',array('statut'=>$texte_statut));
	}
	$id_diogene = sql_getfetsel('id_diogene','spip_diogenes','objet="emballe_media"');
	set_request('titre','');
	$res['message_ok'] .= '<script type="text/javascript">if (window.jQuery) jQuery(".description_emballe_media,.diogene_'.$id_diogene.'").ajaxReload();</script>';
	return $res;
}

?>