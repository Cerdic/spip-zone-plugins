<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Charger les valeurs par defaut d'un formulaire de configuration de metas
 *
 * @param string $form
 *   Le nom du formulaire
 * (nom de fichier sans extension dans formulaires/)
 * @param array $args
 *   Parametres passes au formulaire
 * (tableau de paires nom=>valeur )
 * @return array $valeurs_contexte | string $infos
 *   Appelle la fonction de chargement du formulaire si elle existe.
 *   Sinon va recuperer dans la table des metas toutes les valeurs presentes
 * (Le retour est un tableau de valeur associee a chaque champ en initialisation)
 *   En cas d'erreur, c'est le message d'erreur seulement
**/
function formulaires_configurer_metas_charger_dist($form, $args=array()) {
	$f = charger_fonction('charger', "formulaires/$form", FALSE);
	if ($f)
		return $f($form);
	else {
		$infos = formulaires_configurer_metas_infos($form); // recuper : meta
		if (!is_array($infos)) // formulaire absent ou erreur(s) dans le plugin.xml
			return $infos; // renvoyer l'erreur
		$valeurs = array();
		if (isset($infos['meta']) AND isset($GLOBALS[$infos['meta']])) // table des metas definie et chargee
		    $valeurs = $GLOBALS[$infos['meta']]; // revoyer les metas
		else // table des metas inconnue
			spip_log("Configurer_Metas ne peut charger la table des metas (inconnue de SPIP) pour '$form' : ".$infos['meta'], 'associaspip');
		return array_merge($valeurs, $args);
	}
}

/**
 * Verifier les saisies d'un formulaire de configuration de metas
 *
 * @param string $form
 *   Le nom du formulaire
 * (nom de fichier sans extension dans formulaires/)
 * @param array $args
 *   Parametres passes au formulaire
 * (tableau de paires nom=>valeur )
 * @return array $erreurs
 *   Appelle la fonction de verification du formulaire si elle existe.
 *   Sinon va s'assurer que les saisies potentiellement meta qui sont
 * obligatoires sont effectivement renseignees.
 * (Le retour est un tableau de message associe a chaque champ en erreur)
**/
function formulaires_configurer_metas_verifier_dist($form, $args=array()) {
	$f = charger_fonction('verifier', "formulaires/$form", TRUE);
	if ($f)
		return $f($form);
	else {
		include_spip('balise/formulaire_');
		$vars = formulaires_configurer_metas_recense($form, formulaire__charger('configurer_metas', $args, FALSE) );
		$erreurs = array();
		foreach($vars as $champ=>$obli) { // repris de formulaires_editer_objet_verifier() qui fait en plus un controle de contenu ...et ne s'applique qu'a une table principale...
			if ($obli AND !_request($champ)) { // champ obligatoire non renseigne
#				if (!isset($erreurs[$champ])) { // champ deja en erreur
#					$erreurs[$champ] = ''; // re-initialiser : cette erreur est prioritaire
#				}
				$erreurs[$champ] .= _T('spip:info_obligatoire');
			}
		}
		if ( count($erreurs) ) // il y a des champs obligatoires vides
			$erreurs['message_erreur'] = _T('asso:erreur_titre');
		return $erreurs;
	}
}

/**
 * Enregistrer les saisies d'un formulaire dans la table des metas
 *
 * @param string $form
 *   Le nom du formulaire
 * (nom de fichier sans extension dans formulaires/)
 * @param array $args
 *   Parametres passes au formulaire
 * (tableau de paires nom=>valeur )
 * @return void
 *   Appelle la fonction de traitement du formulaire si elle existe.
 *   Sinon va enregistrer chaque saisie potentiellement meta dans la table des
 * metas puis retourner sur la page d'accueil (doit avoir comme nom le prefixe
 * du plugin)
**/
function formulaires_configurer_metas_traiter_dist($form, $args=array()) {
	$f = charger_fonction('traiter', "formulaires/$form", TRUE);
	if ($f)
		return $f($form);
	else {
		$infos = formulaires_configurer_metas_infos($form); // recuperer : meta, prefixe
		if (!is_array($infos)) // formulaire absent ou erreur(s) dans le plugin.xml
			return $infos; // retourner l'erreur
		include_spip('balise/formulaire_');
		$vars = formulaires_configurer_metas_recense($form, formulaire__charger('configurer_metas', $args, FALSE) );
		foreach ( array_unique(array_keys($vars)) as $k) {
			$v = _request($k);
			ecrire_meta($k, is_array($v) ? serialize($v) : $v, 'oui', $infos['meta']);
		}
		$retour = _request('redirect');
		return $retour
			? array('redirect' => generer_url_ecrire($infos['prefix']), 'message_ok' => _T('ecrire:config_info_enregistree'), 'message_erreur' => '', )
			: ( !isset($infos['prefix'])
				? array()
				: array('redirect' => generer_url_ecrire($infos['prefix']), )
			);
	}
}

/**
 * Determiner la liste des noms des saisies d'un formulaire
 *
 * @param string $form
 *   Le nom du formulaire
 * (nom de fichier sans extension dans formulaires/)
 * @param array $args
 *   Parametres passes au formulaire
 * (tableau de paires nom=>valeur )
 * @return array $liste_metas
 *   Tableau des champs potentiellement de metas
 * (tableau de nom=>requis)
 * @note
 *   A refaire avec SAX ?
 * http://wiki.answers.com/Q/What_is_the_fullform_of_SAX_parser_in_HTML
 * http://www.xmlsoft.org/html/libxml-HTMLparser.html
 * http://www-master.ufr-info-p6.jussieu.fr/site-annuel-courant/Analyse-de-formulaires-avec-SAX
**/
function formulaires_configurer_metas_recense($form, $args=array()) {
	$liste_metas = array();
	if ($f = find_in_path($form.'.html', 'formulaires/') ) { // c'est un formulaire CVT...
#		spip_log("Configurer_Metas va recenser les metas dans : $f", 'associaspip');
		if ($charger_valeurs = charger_fonction("charger","formulaires/$form", FALSE) )
			$contexte = call_user_func_array($charger_valeurs, $args);
		else
			$contexte = array();
		$contexte['editable'] = ' ';
		$contenu = recuperer_fond("formulaires/$form", array_merge($liste_metas,$contexte) );
		$balises = array_merge(
			extraire_balises($contenu, 'input'),
			extraire_balises($contenu, 'textarea'),
			extraire_balises($contenu, 'select')
		); // liste des saisies prises en compte
		foreach ($balises as $b) { // nom de chaque balise retenue
			if ($n = extraire_attribut($b, 'name') // le nom est l'attribut "nome" exclusivement (pas id ou extrait de classe...)
				AND preg_match(",^([\w\-]+)(\[\w*\])*$,", $n, $r) // on ne prend que si le nom est valide (plus restrictif que W3C http://razzed.com/2009/01/30/valid-characters-in-attribute-names-in-htmlxml/ http://stackoverflow.com/questions/70579/what-are-valid-values-for-the-id-attribute-in-html ...)
				AND !in_array($n, array('formulaire_action','formulaire_action_args', 'hash', 'arg', 'action', 'exec', '_forcer_request', 'erreurs', 'message_ok', 'message_erreur', 'editable', 'redirect')) // on ne prend pas ces champs rajoutes par SPIP pour la securisation et d'autres automatismes
				AND !in_array(extraire_attribut($b,'type'), array('submit','reset')) // on ne prend pas les saisies d'action (pas plus qu'on n'a pris en en compte les "button"s
				AND !extraire_attribut($b, 'disabled') // on ne prend pas les champs desactives : ils ne sont normalement pas soumis
			) {
				$o = intval(
					extraire_attribut($b, 'required')=='required' // HTML5 avec required="required"...
					OR
					strpos('obligatoire', extraire_attribut($b, 'class'))!==FALSE // (X)HTML avec la classe Spipienne "obligatoire"
				);
				$liste_metas[$n] = $o;
			}
		}
		spip_log("Configurer_Metas trouve dans '$form' les metas suivants : ". implode(', ', array_unique(array_keys($liste_metas)) ), 'associaspip');
	} else {
		spip_log("Configurer_Metas ne peut recenser les metas de : $f", 'associaspip');
	}
	return $liste_metas;
}

// Repertoires potentiels des plugins, ce serait bien d'avoir ça ailleurs
// ca n'est pas lie a cette balise
// Attention a l'ordre:
// si l'un des 3 est un sous-rep d'un autre, le mettre avant.
// http://www.mail-archive.com/spip-zone@rezo.net/msg22383.html
// http://comments.gmane.org/gmane.comp.web.spip.zone/22606
define('_EXTRAIRE_PLUGIN', '@(' .  _DIR_PLUGINS_AUTO . '|' . _DIR_PLUGINS . '|' . _DIR_EXTENSIONS .')(.+)/formulaires/[^/]+$@');

/**
 * Recuperer la description XML du plugin et normaliser
 *
 * @param string $form
 *   Le nom du formulaire
 * (nom de fichier sans extension dans formulaires/)
 * Idealement, c'est aussi le prefixe du plugin precede de configurer_
 * @return array $infos
 *   Tableau d'informations sur le plugin dont c'est formulaire de configuration :
 * - path => le chemin effectif du formulaire
 * - meta => la table des metas a utiliser
 * - tout le reste : prefixe, categorie, nom, auteur, licence, version, etat, slogan,
 * logo (icon), schema (version_base), documentation (lien), description, options, fonctions, install,
 * config, noisette, traduire, menu (bouton), onglet, necessite, lib, utilise, procure, chemin,
**/
function formulaires_configurer_metas_infos($form) {
	$path = find_in_path($form.'.' . _EXTENSION_SQUELETTES, 'formulaires/');
	if (!$path) {
		spip_log("Configurer_Metas ne peut trouver de formulaire '$form' par un chemin attendu", 'associaspip');
		return ''; // cas traite en amont normalement.
	}
	if (!preg_match(_EXTRAIRE_PLUGIN, $path, $m)) { // Si ce n'est pas un plugin, ...
		spip_log("Configurer_Metas ne peut trouver de plugin utilisant '$form' ; on prendra la table standard des metas...", 'associaspip');
		return array('path' => $path, 'meta' => 'meta'); // ...dire qu'il faut prendre la table std des meta.
	}
	$get_infos = charger_fonction('get_infos', 'plugins'); // charger plugins_get_infos() ex plugin_get_infos() http://comments.gmane.org/gmane.comp.web.spip.user/156443
	$infos = $get_infos($m[2], FALSE, $m[1]); // $m=array(1=>$path, 2=>$plugin)
	if (!is_array($infos)) // pas de plugin.xml ?
		return _T('ecrire:erreur_plugin_nom_manquant') . ' ' . $m[2] . ' ' . $path;
	if (isset($infos['erreur'])) { // presence d'erreurs...?
		spip_log("Configurer_Metas rencontre les erreurs suivantes sur le plugin : ".implode(', '.$infos['erreur']), 'associaspip'); // plugins_infos_plugin() renvoie un tableau...
#		return $infos['erreur'][0]; // ...on sort avec la premiere erreur...
	}
	$infos['path'] = $path; // on rajoute le chemin a la liste des infos
	if (!isset($infos['meta'])) // pas de balise meta defini dans le plugin.xml !
		$infos['meta'] = ($infos['prefix'] . '_metas'); // ...mais si on fait appel a Configurer_Metas c'est pour utiliser une table des metas propre au plugin identifie par son prefixe...
	return $infos;
}

?>