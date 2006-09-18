<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

function balise_FORMULAIRE_BOUTON_PROPOSER_ARTICLE($p) {
  return calculer_balise_dynamique($p,'FORMULAIRE_BOUTON_PROPOSER_ARTICLE',array('id_rubrique'));
}

function balise_FORMULAIRE_BOUTON_PROPOSER_ARTICLE_stat($args, $filtres) {

	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0] && !$args[1])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_BOUTON_PROPOSER_ARTICLE',
					'motif' => 'ARTICLES')), '');

	if ($args[1])
	    return (array($args[1]));
	else
	    return (array($args[0]));
}

function balise_FORMULAIRE_BOUTON_PROPOSER_ARTICLE_dyn($id_rubrique) {

	if (!$GLOBALS["auteur_session"]) 
		return '';
	

	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];

	global $connect_toutes_rubriques;//,$gestion_droits,$droit_vue,$droit_modif,$connect_id_rubrique;

//	include_spip("inc/auth");

	if (($connect_toutes_rubriques /*OR $connect_id_rubrique[$id_rubrique]*/) 
				&& (($auteur_statut=='0minirezo')
					||($auteur_statut=='1comite')
					||($auteur_statut=='6forum'))) { 
/*		$url = new Link("page.php3");
		$url->addVar("fond","ecrire_article");
		$url->addVar("id_rubrique",$id_rubrique);
		$url = $url->getUrl();
*/
		$url="spip.php?page=article_new&id_rubrique=".$id_rubrique;
		return array('formulaire_bouton', 0,
			array(
//						'javascript' => "onclick=\"window.open(this.href,'Proposer_un_article', 'scrollbars=yes, resizable=yes, width=740, height=580'); return false;\"",
						'image' => 'article-24.gif',
						'action' => 'creer',
						'url' => $url,
//						'target' => '_blank'
				));
	}
	return '';
}

?>