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
	return true;
}
function autoriser_mots_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_sites_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
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
	return true;
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
	return true;
}
function autoriser_referers_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_forum_reactions_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_petitions_reactions_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}


/**
 * Administration
 */

function autoriser_admin_vider_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_admin_sauvegarder_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_admin_restaurer_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_admin_maintenir_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}


/**
 * Configuration
 */

function autoriser_config_identite_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_config_lang_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_config_contenu_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_config_interactivite_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_config_avancee_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}
function autoriser_admin_plugin_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}

/*

	// autoriser('configurer') => forcement admin complet (ou webmestre)
	if (autoriser('configurer')) {
		$boutons_admin['configuration']=
		  new Bouton('administration-48.png', 'icone_configuration_site');
	}
	// autres admins (restreints ou non webmestres) peuvent aller sur les backups
	else
	if (autoriser('sauvegarder', 'admin_tech')) {
		$boutons_admin['admin_tech']=
		  new Bouton('administration-48.png', 'texte_sauvegarde_base');
	}

	$boutons_admin['espacement']=null;

	$urlAide= generer_url_ecrire('aide_index')."&amp;var_lang=$spip_lang";
	$boutons_admin['aide_index']=
		  new Bouton('aide-48'.$spip_lang_rtl.'.png', 'icone_aide_ligne',
					 $urlAide, null, "javascript:window.open('$urlAide', 'spip_aide', 'scrollbars=yes,resizable=yes,width=740,height=580');", 'aide_spip');

	$boutons_admin['visiter']=
		new Bouton("visiter-48$spip_lang_rtl.png", 'icone_visiter_site',
		url_de_base());

	// les sous menu des boutons, que si on est admin
	if ($GLOBALS['connect_statut'] == '0minirezo'
	AND $GLOBALS['connect_toutes_rubriques']) {

	// sous menu edition

	$sousmenu=array();

	$nombre_articles = sql_fetsel('id_article', 'spip_auteurs_articles', "id_auteur=".$GLOBALS['connect_id_auteur']);

	if ($nombre_articles > 0) {
		$sousmenu['articles_page']=
		  new Bouton('article-24.gif', 'icone_tous_articles');
	}

	if ($GLOBALS['meta']["activer_breves"] != "non") {
		$sousmenu['breves']=
		  new Bouton('breve-24.gif', 'icone_breves');
	}

	$articles_mots = $GLOBALS['meta']['articles_mots'];
	if ($articles_mots != "non") {
			$sousmenu['mots_tous']=
			  new Bouton('mot-cle-24.gif', 'icone_mots_cles');
	}

	$activer_sites = $GLOBALS['meta']['activer_sites'];
	if ($activer_sites<>'non')
			$sousmenu['sites_tous']=
			  new Bouton('site-24.gif', 'icone_sites_references');

	$n = sql_countsel('spip_documents_liens', 'id_objet>0 AND objet=\'rubrique\'');
	if ($n) {
			$sousmenu['documents_liste']=
			  new Bouton('doc-24.gif', 'icone_doc_rubrique');
	}
	$boutons_admin['naviguer']->sousmenu= $sousmenu;

	// sous menu forum
	$sousmenu=array();

	if (sql_countsel('spip_signatures'))
		$sousmenu['controle_petition']=
			new Bouton("suivi-petition-24.gif", "icone_suivi_pettions");

	// Si le forum a ete desactive, mais qu'il y a un sous-menu de suivi
	// des forums ou des petitions, on colle ce suivi sous le menu "a suivre"
	if ($sousmenu) {
		if (isset($boutons_admin['forum']))
			$boutons_admin['forum']->sousmenu= $sousmenu;
		else
			$boutons_admin['accueil']->sousmenu= $sousmenu;
	}



	// sous menu auteurs

	$sousmenu=array();

	if (avoir_visiteurs(true))
		$sousmenu['auteurs'] =
			new Bouton("fiche-perso.png", 'icone_afficher_visiteurs', null, "statut=!1comite,0minirezo,nouveau");

	$sousmenu['auteur_infos']=
		new Bouton("auteur-24.gif", "icone_creer_nouvel_auteur", null, 'new=oui');

	$boutons_admin['auteurs']->sousmenu= $sousmenu;


	// sous menu configuration
	$sousmenu = array();
	if (autoriser('configurer', 'lang')) {
		$sousmenu['config_lang'] =
			new Bouton("langues-24.gif", "icone_gestion_langues");
		//$sousmenu['espacement'] = null; // les espacements debloquent si on a des icones sur 2 lignes
	}

	if (autoriser('sauvegarder')) {
		$sousmenu['admin_tech']=
			new Bouton("base-24.gif", "icone_maintenance_site");
	}
	if (autoriser('configurer', 'admin_vider')) {
		$sousmenu['admin_vider']=
			new Bouton("cache-24.gif", "onglet_vider_cache");
	}

	// Si _DIR_PLUGINS est definie a '', pas de bouton
	if (_DIR_PLUGINS
	AND autoriser('configurer', 'admin_plugin')) {
		$sousmenu['admin_plugin']=
			new Bouton("plugin-24.gif", "icone_admin_plugin");
	}

	if ($sousmenu)
		$boutons_admin['configuration']->sousmenu= $sousmenu;

	} // fin si admin
	*/

?>