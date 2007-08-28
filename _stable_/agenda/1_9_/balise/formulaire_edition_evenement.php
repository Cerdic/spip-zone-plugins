<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation
global $balise_FORMULAIRE_EDITION_EVENEMENT_collecte;
$balise_FORMULAIRE_EDITION_EVENEMENT_collecte = array('id_evenement','id_article');

function balise_FORMULAIRE_EDITION_EVENEMENT ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_EDITION_EVENEMENT', array('id_evenement', 'id_article'));
}

function balise_FORMULAIRE_EDITION_EVENEMENT_stat($args, $filtres) {
	return $args;
}
 
function balise_FORMULAIRE_EDITION_EVENEMENT_dyn($id_evenement = 0, $id_article = 0) {
	$url = parametre_url(self(),'ajout_evenement','');
	// nettoyer l'url qui est passee par htmlentities pour raison de securités
	$url = str_replace("&amp;","&",$url);
	if ($retour=='') $retour = $url;

	$res = spip_query("SELECT * FROM spip_articles WHERE id_article="._q($id_article));
	if (!spip_num_rows($res)) return;

	$flag_modif = (_request('evenement_modif') && _request('id_evenement') == $id_evenement);
	$flag_ajout = _request('evenement_insert');
	$flag_supp = _request('supp_evenement')!==NULL;
	if ($flag_supp) {
		$_GET['supp_evenement']=$id_evenement;
		$_GET['id_article']=$id_article;
	}

	if ($flag_ajout || $flag_modif || $flag_supp){
		include_spip("inc/agenda_gestion");
		Agenda_action_formulaire_article($id_article);
	}

	
	$evenement_action='evenement_insert';
	$valeurs=array('mots'=>array(),'dates'=>array(),'evenement_horaire'=>'oui');

	$formulaire_actif = _request('ajout_evenement')!=NULL;
	
	// les champs
	$res = spip_query("SELECT * FROM spip_evenements WHERE id_evenement="._q($id_evenement)." AND id_article="._q($id_article));
	if ($row = spip_fetch_array($res,SPIP_ASSOC)){
		$evenement_action='evenement_modif';
		foreach($row as  $k=>$val){
			$valeurs["evenement_$k"]=$val;
		}
		// les mots
		$res = spip_query("SELECT * FROM spip_mots_evenements WHERE id_evenement="._q($id_evenement));
		while ($row=spip_fetch_array($res)){
			$valeurs['mots'][]=$row['id_mot'];
		}
		
		$res = spip_query("SELECT date_debut FROM spip_evenements WHERE id_evenement_source="._q($id_evenement));
		while ($row=spip_fetch_array($res)){
			$valeurs['repetitions'][] = date('m/d/Y',strtotime($row['date_debut']));
		}
		$formulaire_actif = true;
	}
	$t=time();
	$valeurs["date_evenement_debut"]=isset($valeurs["evenement_date_debut"])?$valeurs["evenement_date_debut"]:date('Y-m-d H:i:00',$t);
	$valeurs["date_evenement_fin"]=isset($valeurs["evenement_date_fin"])?$valeurs["evenement_date_fin"]:date('Y-m-d H:i:00',$t+3600);

	if (!$formulaire_actif) return;

	return array('formulaires/formulaire_edition_evenement', 0, 
		array(
			#'erreur_message'=>isset($erreur['@'])?$erreur['@']:'',
			#'erreur'=>serialize($erreur),
			#'reponse'=>filtrer_entites($reponse),
			'id_article' => $id_article,
			'id_evenement' => $id_evenement,
			'evenement_action' => $evenement_action,
			'self' => $url,
			'valeurs' => serialize($valeurs),
			'url_validation' => str_replace("&amp;","&",$url_validation),
			#'affiche_sondage' => $affiche_sondage,
			#'formok' => filtrer_entites($formok),
			#'formactif' => $formactif,
		));
}

?>
