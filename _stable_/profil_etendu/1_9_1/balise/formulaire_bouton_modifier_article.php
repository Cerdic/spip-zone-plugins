<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


global $balise_FORMULAIRE_BOUTON_MODIFIER_ARTICLE_collecte;
$balise_FORMULAIRE_BOUTON_MODIFIER_ARTICLE_collecte = array('id_article','id_rubrique','statut');

function balise_FORMULAIRE_BOUTON_MODIFIER_ARTICLE_stat($args, $filtres) {

	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0] && !$args[1])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_BOUTON_MODIFIER_ARTICLE',
					'motif' => 'ARTICLES')), '');

	if ($args[3])
	    return (array($args[3],$args[4],$args[5]));
	else
	    return (array($args[0],$args[1],$args[2]));
}

function balise_FORMULAIRE_BOUTON_MODIFIER_ARTICLE_dyn($id_article, $id_rubrique, $statut_article) {

	if (!$GLOBALS["auteur_session"]) 
		return '';
	global $connect_toutes_rubriques,$connect_id_rubrique;
	$gestion_droits=(lire_meta('gestion_droits')=="oui");
	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
	$flag_auteur = false;
	
	include_ecrire("inc_auth.php3");

	if ($gestion_droits) {
		if (($auteur_statut=='0minirezo') && $connect_toutes_rubriques){
				$gestion_droits=false;
				$droit_vue='Tout';
				$droit_modif='Non';
		}
		else{

			$query = "SELECT auteurs.droit_vue,auteurs.droit_modif FROM spip_auteurs_articles as articles,spip_auteurs as auteurs WHERE articles.id_auteur=auteurs.id_auteur AND articles.id_article=".$id_article." AND articles.id_auteur=".$id_auteur_session;
			$result_auteur = spip_query($query);
			$flag_auteur = (spip_num_rows($result_auteur) > 0);
			if ($row=spip_fetch_array($result_auteur)){
				$droit_vue=$row['droit_vue'];
				$droit_modif=$row['droit_modif'];
			}else{
				$droit_vue='Rien';
				$droit_modif='Non';
			}
		}
	}
	else {
		$query = "SELECT * FROM spip_auteurs_articles WHERE id_article=".$id_article." AND id_auteur=".$id_auteur_session;
		$result_auteur = spip_query($query);
		$flag_auteur = (spip_num_rows($result_auteur) > 0);
	}

	$flag_modif= ((($connect_toutes_rubriques OR $connect_id_rubrique[$id_rubrique]) && ($auteur_statut=='0minirezo'))
	OR ($flag_auteur AND ($statut_article == 'prepa' OR $statut_article == 'prop' OR $statut_article == 'poubelle'))
	OR ($flag_auteur AND ($statut_article == 'publie') and ($droit_modif=='Oui')));
	
	$form_prefixe= 'fma'.$id_article.'_';
	$editer= (_request($form_prefixe.'editer')=='oui');

	if ($flag_modif){ 
		$url = new Link();
		if ($editer){
			$url = new Link();
			$url->delVar($form_prefixe.'editer');
			$url = $url->getUrl();
			return array('formulaire_bouton', 0,
				array(
						'javascript' => "",
						'image' => 'article-24.gif',
						'action' => 'article-24',
						'action_alt' => "retour",//_T('icone_retour'),
						'url' => $url
				));
		}
		else {
			$url->addVar($form_prefixe.'editer','oui');
			$url = $url->getUrl();
			//include_ecrire("inc_version.php3");
			return array('formulaire_bouton', 0,
				array(
						'javascript' => "",
						'image' => 'article-24.gif',
						'action' => 'edit',
						'action_alt' => "Modifier cet article",//_T('texte_modifier_article'),
						'url' => $url
				));
		}
	}
	return '';
}

?>