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
	return true;
}
function autoriser_forum_admin_suivi_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return $GLOBALS['visiteur_session']['statut']=='0minirezo';
}
function autoriser_suivi_revisions_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_messagerie_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_calendrier_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}


/**
 * Reactions
 */

function autoriser_statistiques_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return $GLOBALS['visiteur_session']['statut']=='0minirezo';
}
function autoriser_referers_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return $GLOBALS['visiteur_session']['statut']=='0minirezo';
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
 * Outils rapides
 */

function autoriser_rubrique_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creerrubriquedans','rubrique',_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null));
}

function autoriser_article_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creerarticledans','rubrique',_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null));
}

function autoriser_auteur_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creer','auteur');
}

function autoriser_mot_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creer','mot');
}

function autoriser_site_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creersitedans','rubrique',_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null));
}

function autoriser_breve_creer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('creerbrevedans','rubrique',_request('id_rubrique',isset($opt['contexte'])?$opt['contexte']:null));
}

?>