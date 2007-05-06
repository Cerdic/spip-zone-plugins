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


// Charger les versions *_dist des fonctions
include _DIR_RESTREINT.'inc/autoriser.php';


##
## Dire aux crayons si les visiteurs anonymes ont des droits
##
if ($GLOBALS['autorite']['espace_wiki']
AND $GLOBALS['autorite']['espace_wiki_anonyme']) {
	if (!function_exists('analyse_droits_rapide')) {
	function analyse_droits_rapide() {
		return true;
	}
	}
	else
		$autorite_erreurs[] = 'analyse_droits_rapide';
}


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
## autoriser_article_modifier
##
if ($GLOBALS['autorite']['auteur_mod_article']
OR $GLOBALS['autorite']['espace_wiki']
OR $GLOBALS['autorite']['redacteur_mod_article']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_article_modifier')) {
function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
	$s = spip_query(
	"SELECT id_rubrique,id_secteur,statut FROM spip_articles WHERE id_article="._q($id));
	$r = spip_fetch_array($s);
	include_spip('inc/auth');
	return
		autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
		OR (
			// Cas du wiki, on appelle la fonction qui verifie les droits wiki
			$GLOBALS['autorite']['espace_wiki']
			AND autorisation_wiki_visiteur($qui, $r['id_secteur'])
		)
		OR (
			in_array($qui['statut'], array('0minirezo', '1comite'))
			AND (
				$GLOBALS['autorite']['auteur_mod_article']
				OR in_array($r['statut'], array('prop','prepa', 'poubelle'))
			)
			AND spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur']))
		)
		OR (
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
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_rubrique_publierdans')) {
function autoriser_rubrique_publierdans($faire, $type, $id, $qui, $opt) {
	// Si on est deja autorise en standard, dire 'OK'
	if (autoriser_rubrique_publierdans_dist($faire, $type, $id, $qui, $opt))
		return true;

	// Sinon, verifier si la rubrique est wiki
	// et si on est bien enregistre (sauf cas de creation anonyme explicitement autorisee)
	$s = spip_query(
	"SELECT id_secteur FROM spip_rubriques WHERE id_rubrique="._q($id));
	$r = spip_fetch_array($s);

	if (autorisation_wiki_visiteur($qui, $r['id_secteur'])
	AND (
		$GLOBALS['autorite']['espace_wiki_rubrique_anonyme']
		OR $qui['statut']
	))
		return true;

	// par defaut, NIET
	return false;
}
} else
	$autorite_erreurs[] = 'autoriser_rubrique_publierdans';
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
			AND spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur']))
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
			AND spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur']))
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
		if (spip_num_rows($s))
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
	if ($GLOBALS['autorite']['configurer'] == 'webmestre')
		return autoriser('webmestre');
	else
		return autoriser(''); // autorisation par defaut
}
} else
	$autorite_erreurs[] = 'autoriser_configurer';
}

##
## autoriser_backup (faire un backup partiel ou complet)
##
if ($GLOBALS['autorite']['backup']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_backup')) {
function autoriser_backup($faire, $type, $id, $qui, $opt) {

	if ($GLOBALS['autorite']['backup'] == 'webmestre')
		return autoriser('webmestre');

	if ($GLOBALS['autorite']['backup'] == 'admin')
		return
			$qui['statut'] == '0minirezo'
			AND !$qui['restreint'];

	// version normale
	if ($GLOBALS['autorite']['backup'] == '')
		return
			$qui['statut'] == '0minirezo';
}
} else
	$autorite_erreurs[] = 'autoriser_backup';
}

##
## autoriser_destroy (vider la base de donnees)
##
if ($GLOBALS['autorite']['destroy']
OR false // autre possibilite de surcharge ?
) {
if (!function_exists('autoriser_destroy')) {
function autoriser_destroy($faire, $type, $id, $qui, $opt) {

	if ($GLOBALS['autorite']['destroy'] == 'webmestre')
		return autoriser('webmestre');

	if ($GLOBALS['autorite']['destroy'] == 'non')
		return false;

	// Par defaut, idem configuration
	return autoriser('configurer');
}
} else
	$autorite_erreurs[] = 'autoriser_destroy';
}

if ($autorite_erreurs) $GLOBALS['autorite_erreurs'] = $autorite_erreurs;

?>
