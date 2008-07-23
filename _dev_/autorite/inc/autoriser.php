<?php

// Ceci est une surcharge de inc/autoriser.php
//
// Voir le fichier fonds/cfg_autorite.html pour la definition des reglages
// et bien sur "ecrire/?exec=cfg&cfg=autorite"

if (!defined("_ECRIRE_INC_VERSION")) return;

define ('_DEBUG_AUTORISER', false);
define ('_ID_WEBMESTRES', '1'); // '1:5:90' a regler dans mes_options

$GLOBALS['autorite'] = @unserialize($GLOBALS['meta']['autorite']);
$autorite_erreurs = array();

// Compatibilite 1.92 : on a besoin de sql_fetch
if ($GLOBALS['spip_version_code'] < '1.93'
AND $f = charger_fonction('compat_autorite', 'inc'))
	$f(array('sql_fetch','sql_count'));


//
// Les DEFINE
//

if ($GLOBALS['autorite']['statut_auteur_creation']) {
	if (defined('_STATUT_AUTEUR_CREATION'))
		$autorite_erreurs[] = 'statut_auteur_creation';
	else {
		switch($GLOBALS['autorite']['statut_auteur_creation']) {
			case 'visiteur':
				define('_STATUT_AUTEUR_CREATION', '6forum');
			case 'admin':
				define('_STATUT_AUTEUR_CREATION', '0minirezo');
		}
	}
}

if ($GLOBALS['autorite']['statut_auteur_rubrique']) {
	if (defined('_STATUT_AUTEUR_RUBRIQUE'))
		$autorite_erreurs[] = 'statut_auteur_rubrique';
	else {
		switch($GLOBALS['autorite']['statut_auteur_rubrique']) {
			case '1':
				define('_STATUT_AUTEUR_RUBRIQUE', '0minirezo,1comite');
			case '2':
				define('_STATUT_AUTEUR_RUBRIQUE', '0minirezo,1comite,6forum');
		}
	}
}

if ($GLOBALS['autorite']['statut_ignorer_admins_restreints'] == 'oui') {
	if (defined('_ADMINS_RESTREINTS'))
		$autorite_erreurs[] = 'ignorer_admins_restreints';
	else
		define('_ADMINS_RESTREINTS', false);
}


// Charger les versions *_dist des fonctions
include _DIR_RESTREINT.'inc/autoriser.php';


//
// Les FONCTIONS
//



##
## une fonction qui gere les droits wiki
##
if ($GLOBALS['autorite']['espace_wiki']) {
	if (!function_exists('autorisation_wiki_visiteur')) {
	function autorisation_wiki_visiteur($qui, $id_secteur) {
		// espace_wiki est un array(secteur1, secteur2), ou un id_secteur
		if (
			(is_array($GLOBALS['autorite']['espace_wiki'])
			AND !in_array($id_secteur,$GLOBALS['autorite']['espace_wiki']))
		AND
			$id_secteur != $GLOBALS['autorite']['espace_wiki']
		)
			return false;

		switch($qui['statut']) {
			case '0minirezo':
			case '1comite':
				if ($GLOBALS['autorite']['espace_wiki_redacteurs'])
					return true;
			case '6forum':
				if ($GLOBALS['autorite']['espace_wiki_visiteurs'])
					return true;
			default:
				if ($GLOBALS['autorite']['espace_wiki_anonyme'])
					return true;
		}
		return false;
	}
	} else
		$autorite_erreurs[] = 'autorisation_wiki_visiteur';
}


##
## une fonction qui gere les droits wiki géré par mot clef
##
if ($GLOBALS['autorite']['espace_wiki_motsclef']) {
	if (!function_exists('autorisation_wiki_motsclef_visiteur')) {
	function autorisation_wiki_motsclef_visiteur($qui, $id_article) {

	    //determine les mots clef affectés à l'article
	    $s = spip_query(
	    "SELECT id_mot FROM spip_mots_articles WHERE id_article=".$id_article);

	    //obtient la liste des mots clefs affecté à l'article
        while ( $r = sql_fetch($s) ) { 
            $array_mot[] = $r['id_mot'];
        }	    
        
        //aucun mot clef d'affecter à l'article, rien à faire
        if (is_null($array_mot))
            return false;
	            	    	    
	    //vérification que l'article posséde un mot clef correspondant au staut du visiteur
		switch($qui['statut']) {
			case '0minirezo':
			case '1comite':
				if (in_array($GLOBALS['autorite']['espace_wiki_motsclef_redacteurs'],$array_mot))
					return true;
			case '6forum':
				if (in_array($GLOBALS['autorite']['espace_wiki_motsclef_visiteurs'],$array_mot))
					return true;
			default:
				if (in_array($GLOBALS['autorite']['espace_wiki_motsclef_anonyme'],$array_mot))
					return true;
		}
		return false;
	}
	} else
		$autorite_erreurs[] = 'autorisation_wiki_motsclef_visiteur';
}


##
## autoriser_article_modifier
##
if ($GLOBALS['autorite']['auteur_mod_article']
OR $GLOBALS['autorite']['espace_wiki']
OR $GLOBALS['autorite']['espace_wiki_motsclef']
OR $GLOBALS['autorite']['redacteur_mod_article']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_article_modifier')) {
function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
	$s = spip_query(
	"SELECT id_rubrique,id_secteur,statut FROM spip_articles WHERE id_article="._q($id));
	$r = sql_fetch($s);
	include_spip('inc/auth');
	return
		autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
		OR (
			// Cas du wiki, on appelle la fonction qui verifie les droits wiki
			$GLOBALS['autorite']['espace_wiki']
			AND autorisation_wiki_visiteur($qui, $r['id_secteur'])
		)
		OR (
			// Cas du wiki par mot clefs, on appelle la fonction qui verifie les droits wiki
			$GLOBALS['autorite']['espace_wiki_motsclef']
			AND autorisation_wiki_motsclef_visiteur($qui, _q($id))
		)
		OR (
			// auteur autorise a modifier son article
			// (sauf si l'article est refuse ou l'auteur mis a la poubelle)
			$GLOBALS['autorite']['auteur_mod_article']
			AND in_array($qui['statut'],
				array('0minirezo', '1comite', '6forum'))
			AND in_array($r['statut'],
				array('publie', 'prop', 'prepa', 'poubelle'))
			AND sql_fetch(spip_query("SELECT * FROM spip_auteurs_articles WHERE id_auteur=".intval($qui['id_auteur'])." AND id_article=".intval($id)))
		)
		OR (
			// un redacteur peut-il modifier un article propose ?
			$GLOBALS['autorite']['redacteur_mod_article']
			AND in_array($qui['statut'], array('0minirezo', '1comite'))
			AND $r['statut']=='prop'
		);
}
} else
	$autorite_erreurs[] = 'autoriser_article_modifier';
}


##
## autoriser_rubrique_publierdans
##
if ($GLOBALS['autorite']['espace_wiki']
OR $GLOBALS['autorite']['publierdans'] 
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_rubrique_publierdans')) {
function autoriser_rubrique_publierdans($faire, $type, $id, $qui, $opt) {
	
	// Si on est deja autorise en standard, dire 'OK'
	if (!$GLOBALS['autorite']['publierdans']
		&& autoriser_rubrique_publierdans_dist($faire, $type, $id, $qui, $opt))
			return true;
	
	// Verifions qui a le droit
	// 1 : webmestre
	// 2 : admin complet
	// 4 : admin restreint
	// 8 : redacteur
	// cas du redacteur : attention, il faut verifier 
	// aussi qu'il est l'auteur de l'objet publie...

	if (($GLOBALS['autorite']['publierdans'] & 1)
		&& autoriser('webmestre', $type, $id, $qui, $opt))
			return true;		
	if (($GLOBALS['autorite']['publierdans'] & 2)
		&& ($qui['statut'] == '0minirezo')
		&& (!$qui['restreint']))
			return true;
	if (($GLOBALS['autorite']['publierdans'] & 4)	
		&& ($qui['statut'] == '0minirezo')
		&& ($qui['restreint'] AND $id AND in_array($id, $qui['restreint'])))
			return true;
	/*	 
	if (($GLOBALS['autorite']['publierdans'] & 8)
		&& ($qui['statut'] == '1comite'))
			return true;
	*/


	// Sinon, verifier si la rubrique est wiki
	// et si on est bien enregistre (sauf cas de creation anonyme explicitement autorisee)
	if ($GLOBALS['autorite']['espace_wiki']) {
		$s = spip_query(
		"SELECT id_secteur FROM spip_rubriques WHERE id_rubrique="._q($id));
		$r = sql_fetch($s);

		if (autorisation_wiki_visiteur($qui, $r['id_secteur'])
		AND (
			$GLOBALS['autorite']['espace_wiki_rubrique_anonyme']
			OR $qui['statut']
		))
			return true;
	}
	
	// par defaut, NIET
	return false;
}
} else
	$autorite_erreurs[] = 'autoriser_rubrique_publierdans';
}


##
## autoriser_rubrique_creerrubriquedans
##
if ($GLOBALS['autorite']['interdire_creer_secteur']
OR $GLOBALS['autorite']['interdire_creer_sousrub']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_rubrique_creerrubriquedans')) {
function autoriser_rubrique_creerrubriquedans($faire, $type, $id, $qui, $opt) {
	if ($id == 0
	AND $GLOBALS['autorite']['interdire_creer_secteur'])
		return
			$GLOBALS['autorite']['interdire_creer_rub_sauf_webmestre']
			AND autoriser('webmestre');

	if ($id != 0
	AND $GLOBALS['autorite']['interdire_creer_sousrub'])
		return
			$GLOBALS['autorite']['interdire_creer_rub_sauf_webmestre']
			AND autoriser('webmestre');

	return
		autoriser_rubrique_creerrubriquedans_dist($faire, $type, $id, $qui, $opt);
}
} else
	$autorite_erreurs[] = 'autoriser_rubrique_creerrubriquedans';
}



##
## autoriser_auteur_modifier
##
if ($GLOBALS['autorite']['auteur_mod_email']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_auteur_modifier')) {
function autoriser_auteur_modifier($faire, $type, $id, $qui, $opt) {
	if ($GLOBALS['autorite']['auteur_mod_email']) {
		unset($opt['email']);
	}
	return autoriser_auteur_modifier_dist($faire, $type, $id, $qui, $opt);
}
} else
	$autorite_erreurs[] = 'autoriser_auteur_modifier';
}


##
## autoriser_modererforum
##
if ($GLOBALS['autorite']['auteur_modere_forum']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_modererforum')) {
function autoriser_modererforum($faire, $type, $id, $qui, $opt) {
	return
		autoriser('modifier', $type, $id, $qui, $opt)
		OR (
			$GLOBALS['autorite']['auteur_modere_forum']
			AND $type == 'article'
			AND in_array($qui['statut'], array('0minirezo', '1comite'))
			AND sql_fetch(spip_query("SELECT * FROM spip_auteurs_articles WHERE id_auteur=".intval($qui['id_auteur'])." AND id_article=".intval($id)))
		);
}
} else
	$autorite_erreurs[] = 'autoriser_modererforum';
}

##
## autoriser_modererpetition
##
if ($GLOBALS['autorite']['auteur_modere_petition']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_modererpetition')) {
function autoriser_modererpetition($faire, $type, $id, $qui, $opt) {
	return
		autoriser('modifier', $type, $id, $qui, $opt)
		OR (
			$GLOBALS['autorite']['auteur_modere_petition']
			AND $type == 'article'
			AND in_array($qui['statut'], array('0minirezo', '1comite'))
			AND sql_fetch(spip_query("SELECT * FROM spip_auteurs_articles WHERE id_auteur=".intval($qui['id_auteur'])." AND id_article=".intval($id)))
		);
}
} else
	$autorite_erreurs[] = 'autoriser_modererpetition';
}


##
## autoriser_voirstats
##
if ($GLOBALS['autorite']['redacteurs_lire_stats']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_voirstats')) {
function autoriser_voirstats($faire, $type, $id, $qui, $opt) {
	return
		$GLOBALS['autorite']['redacteurs_lire_stats']
			? in_array($qui['statut'], array('0minirezo', '1comite'))
			: $qui['statut'] == '0minirezo';
}
} else
	$autorite_erreurs[] = 'autoriser_voirstats';
}


// Autoriser a modifier un groupe de mots $id
// y compris en ajoutant/modifiant les mots lui appartenant
// http://doc.spip.org/@autoriser_groupemots_modifier
##
## autoriser_groupemots_modifier
##
if ($GLOBALS['autorite']['editer_mots']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_groupemots_modifier')) {
function autoriser_groupemots_modifier($faire, $type, $id, $qui, $opt) {
	return (
		$qui['statut'] == '0minirezo'
		AND (
			!$qui['restreint']
			OR
			$GLOBALS['autorite']['editer_mots'] >= 1
		)
	) OR (
		$qui['statut'] == '1comite'
		AND $GLOBALS['autorite']['editer_mots'] >= 2
	);
}
	# signaler un risque de bug avec un autoriser_mot_modifier personnalise
	if (function_exists('autoriser_mot_modifier'))
		$autorite_erreurs[] = 'autoriser_mot_modifier';
} else
	$autorite_erreurs[] = 'autoriser_groupemots_modifier';
}

##
## Modifier un forum ?
## A noter : il n'existe pas d'interface dans SPIP, il faut utiliser les crayons
## TODO : cookie specialise (voir commentaires dans cfg_autorite.html)
##
if ($GLOBALS['autorite']['editer_forums']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_forum_modifier')) {
function autoriser_forum_modifier($faire, $type, $id, $qui, $opt) {

	// Le webmestre
	if ($GLOBALS['autorite']['editer_forums'] >= 1
	AND autoriser('webmestre', $type, $id, $qui, $opt))
		return true;

	// Les admins
	if (
		$GLOBALS['autorite']['editer_forums'] >= 2
		AND $qui['statut'] == '0minirezo'
		AND !$qui['restreint']
	)
		return true;

	// L'auteur du message (enregistre')
	// 2 = avec une periode de grace d'une heure
	// 3 = ad vitam
	if ($GLOBALS['autorite']['editer_forums'] >= 3
	AND isset($qui['id_auteur'])) {
		$q = "SELECT id_forum FROM spip_forum WHERE id_forum="._q($id)." AND id_auteur="._q($qui['id_auteur']);
		if ($GLOBALS['autorite']['editer_forums'] == 3)
			$q .= " AND date_heure > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
		$s = spip_query($q);
		if (sql_count($s))
			return true;
	}

	// par defaut
	return autoriser_forum_modifier_dist($faire, $type, $id, $qui, $opt);

}
} else
	$autorite_erreurs[] = 'autoriser_forum_modifier';
}

##
## Modifier une signature ?
## A noter : il n'existe pas d'interface dans SPIP, il faut utiliser les crayons
## TODO : cookie specialise (voir commentaires dans cfg_autorite.html)
##
if ($GLOBALS['autorite']['editer_signatures']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_signature_modifier')) {
function autoriser_signature_modifier($faire, $type, $id, $qui, $opt) {

	// Le webmestre
	if ($GLOBALS['autorite']['editer_signatures'] >= 1
	AND autoriser('webmestre', $type, $id, $qui, $opt))
		return true;

	// Les admins
	if (
		$GLOBALS['autorite']['editer_signatures'] >= 2
		AND $qui['statut'] == '0minirezo'
		AND !$qui['restreint']
	)
		return true;

	// par defaut
	return autoriser_signature_modifier_dist($faire, $type, $id, $qui, $opt);
}
} else
	$autorite_erreurs[] = 'autoriser_signature_modifier';
}


##
## autoriser_configurer (pages de configuration)
##
if ($GLOBALS['autorite']['configurer']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_configurer')) {
function autoriser_configurer($faire, $type, $id, $qui, $opt) {
	// TODO:
	// cas particulier : configurer les plugins doit etre bloque
	// en mode 'webmestre', sinon on pourrait desactiver autorite.
	// mais comment faire pour ne pas bloquer quelqu'un qui installe
	// ce plugin alors qu'il est id_auteur > 1 ?
#	if (in_array($type, array('plugins', 'admin_plugin')))
#		return autoriser('webmestre');

	if ($GLOBALS['autorite']['configurer'] == 'webmestre')
		return autoriser('webmestre');
	else
		return autoriser('x'); // autorisation par defaut
}
} else
	$autorite_erreurs[] = 'autoriser_configurer';
}

##
## autoriser_sauvegarder (faire un backup partiel ou complet)
##
if ($GLOBALS['autorite']['sauvegarder']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_sauvegarder')) {
function autoriser_sauvegarder($faire, $type, $id, $qui, $opt) {

	if ($GLOBALS['autorite']['sauvegarder'] == 'webmestre')
		return autoriser('webmestre');

	// admins y compris restreints
	if ($GLOBALS['autorite']['sauvegarder'] == 'minirezo')
		return
			$qui['statut'] == '0minirezo';

	// version normale
	if ($GLOBALS['autorite']['sauvegarder'] == ''
	OR $GLOBALS['autorite']['sauvegarder'] == 'admin' # jusque v0.7 de ce plugin
	)
		return
			$qui['statut'] == '0minirezo'
			AND !$qui['restreint'];
}
} else
	$autorite_erreurs[] = 'autoriser_sauvegarder';
}

##
## autoriser_detruire (vider la base de donnees)
##
if ($GLOBALS['autorite']['detruire']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_detruire')) {
function autoriser_detruire($faire, $type, $id, $qui, $opt) {

	if ($GLOBALS['autorite']['detruire'] == 'webmestre')
		return autoriser('webmestre');

	if ($GLOBALS['autorite']['detruire'] == 'non')
		return false;

	// Par defaut, idem configuration
	return autoriser('configurer');
}
} else
	$autorite_erreurs[] = 'autoriser_detruire';
}

if ($autorite_erreurs) $GLOBALS['autorite_erreurs'] = $autorite_erreurs;

?>
