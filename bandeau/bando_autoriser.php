<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function bando_autoriser(){}

/**
 * Edition
 */

function autoriser_articles_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_rubriques_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_auteurs_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_breves_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return 	($GLOBALS['meta']["activer_breves"] != "non");
}
function autoriser_mots_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return 	($GLOBALS['meta']["articles_mots"] != "non");
}
function autoriser_sites_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return 	($GLOBALS['meta']["activer_sites"] != "non");
}

/**
 * Suivi
 */

function autoriser_synchro_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_forum_interne_suivi_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return $GLOBALS['meta']['forum_prive']!=='non';
}
function autoriser_forum_admin_suivi_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return $GLOBALS['visiteur_session']['statut']=='0minirezo';
}
function autoriser_suivi_revisions_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_messagerie_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return $GLOBALS['meta']['messagerie_agenda']!=='non';
}
function autoriser_calendrier_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return $GLOBALS['meta']['messagerie_agenda']!=='non';
}


/**
 * Reactions
 */

function autoriser_statistiques_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return isset($GLOBALS['visiteur_session']['statut']) and $GLOBALS['visiteur_session']['statut']=='0minirezo';
}
function autoriser_referers_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return isset($GLOBALS['visiteur_session']['statut']) and $GLOBALS['visiteur_session']['statut']=='0minirezo';
}
function autoriser_forum_reactions_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('publierdans','rubrique',_request('id_rubrique'));
}
function autoriser_petitions_reactions_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return sql_countsel('spip_signatures')>0;
}
function autoriser_visiteurs_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	include_spip('inc/presentation');
	return avoir_visiteurs(true);
}


/**
 * Administration
 */

function autoriser_admin_vider_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('configurer', 'admin_vider');
}
function autoriser_admin_sauvegarder_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('sauvegarder');
}
function autoriser_admin_restaurer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('configurer', 'admin_tech');;
}
function autoriser_admin_maintenir_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('configurer', 'admin_tech');;
}


/**
 * Configuration
 */

function autoriser_config_identite_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('configurer');
}
function autoriser_config_lang_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
return autoriser('configurer', 'lang');
}
function autoriser_config_contenu_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
return autoriser('configurer');
}
function autoriser_config_interactivite_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
return autoriser('configurer');
}
function autoriser_config_avancee_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
return autoriser('configurer');
}
function autoriser_admin_plugin_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('configurer', 'admin_plugin');
}

/**
 * Infos perso
 */

function autoriser_infos_perso_onglet_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_config_langage_onglet_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_config_preferences_onglet_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}

/**
 * Outils rapides
 */

function select_rubrique_insertion($condition=""){
	static $rubriques = array();
	if (!isset($rubriques[$condition])){
		$in = !$GLOBALS['connect_id_rubrique'] ? ''
			: sql_in('id_rubrique', $GLOBALS['connect_id_rubrique']);
		if ($condition)
			$in .= ($in?" AND ":""). $condition;
		$rubriques[$condition] = sql_getfetsel('id_rubrique', 'spip_rubriques', $in, '',  'id_rubrique DESC',  1);
	}
	return $rubriques[$condition];
}


function autoriser_rubrique_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creerrubriquedans','rubrique',_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null));
}

function autoriser_article_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	if (!$id_rubrique = intval(_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null)))
		$id_rubrique = select_rubrique_insertion();
	return autoriser('creerarticledans','rubrique',$id_rubrique);
}

function autoriser_auteur_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creer','auteur');
}

function autoriser_mot_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return
		($GLOBALS['meta']['articles_mots'] != 'non' OR sql_countsel('spip_groupes_mots'))
		AND autoriser('creer','mot');
}

function autoriser_site_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	if (!$id_rubrique = intval(_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null)))
		$id_rubrique = select_rubrique_insertion();
	return autoriser('creersitedans','rubrique',$id_rubrique);
}

function autoriser_breve_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	if (!$id_rubrique = intval(_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null)))
		$id_rubrique = select_rubrique_insertion("id_parent=0");
	return autoriser('creerbrevedans','rubrique',$id_rubrique);
}

?>
