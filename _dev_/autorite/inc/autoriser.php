<?php

// Ceci est une surcharge de inc/autoriser.php
//
// Voir le fichier fonds/cfg_autorite.html pour la definition des reglages
// et bien sur "ecrire/?exec=cfg&cfg=autorite"

if (!defined("_ECRIRE_INC_VERSION")) return;


define ('_DEBUG_AUTORISER', false);

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
	function autorisation_wiki_visiteur($qui) {
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
			$r['id_secteur'] == $GLOBALS['autorite']['espace_wiki']
			AND autorisation_wiki_visiteur($qui)
		)
		OR (
			in_array($qui['statut'], array('0minirezo', '1comite'))

			# si on commente cette ligne : tous les articles sont modifiables
			AND (
				$GLOBALS['autorite']['auteur_mod_article']
				OR in_array($r['statut'], array('prop','prepa', 'poubelle'))
			)
			AND spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur']))
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
	// et si on est bien redacteur
	$s = spip_query(
	"SELECT id_secteur FROM spip_rubriques WHERE id_rubrique="._q($id));
	$r = spip_fetch_array($s);
	if ($r['id_secteur'] == $GLOBALS['autorite']['espace_wiki']
	AND autorisation_wiki_visiteur($qui))
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
## autoriser_forum_article_moderer
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



// Noter les erreurs pour les afficher dans le panneau de config
if (serialize($autorite_erreurs) != $GLOBALS['meta']['autorite_erreurs']) {
	include_spip('inc/meta');
	ecrire_meta('autorite_erreurs', serialize($autorite_erreurs));
	ecrire_metas();
	spip_log('Erreur autorite : '.join(', ', $autorite_erreurs));
}
unset($autorite_erreurs);



?>
