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

function formulaires_configurer_metas_charger_dist($form) {
	$f = charger_fonction('charger', "formulaires/$form", TRUE);
	if ($f)
		return $f($form);
	else {
		$infos = formulaires_configurer_metas_infos($form);
		if (!is_array($infos))
			return $infos;
		if (isset($infos['meta']) AND isset($GLOBALS[$infos['meta']]))
		    return $GLOBALS[$infos['meta']];
		spip_log("configurer_meta, charger: table des meta pour $form inconnue ".$infos['meta'],'associaspip');
		return array();
	}
}

function formulaires_configurer_metas_verifier_dist($form) {
	$f = charger_fonction('verifier', "formulaires/$form", TRUE);
	return $f ? $f($form) : array();
}

function formulaires_configurer_metas_traiter_dist($form) {
	$f = charger_fonction('traiter', "formulaires/$form", TRUE);
	if ($f)
		return $f($form);
	else {
		$infos = formulaires_configurer_metas_infos($form);
		if (!is_array($infos))
			return $infos; // fait ci-dessus en fait
		include_spip('balise/formulaire_');
		$vars = formulaires_configurer_metas_recense($form, formulaire__charger('configurer_metas', $args,TRUE) );
		foreach ($vars as $k) {
			$v = _request($k);
			ecrire_meta($k, is_array($v) ? serialize($v) : $v, 'oui', $infos['meta']);
		}
		return !isset($infos['prefix']) ? array()
		: array('redirect' => generer_url_ecrire($infos['prefix']));
	}
}

// determiner la liste des noms des saisies d'un formulaire
// (a refaire avec SAX)
function formulaires_configurer_metas_recense($form, $args=array()) {
	if ($f = find_in_path($form.'.html', 'formulaires/') ) { // c'est un formulaire CVT...
#		spip_log("Associaspip va recenser les metas dans : $f", 'associaspip');
		$liste_metas = array();
		if ($charger_valeurs = charger_fonction("charger","formulaires/$form",TRUE) )
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
				AND !in_array($n, array('formulaire_action','formulaire_action_args')) // on ne prend pas ces champs rajoutes par SpIP pour la securisation et d'autres automatismes
				AND !in_array(extraire_attribut($b,'type'), array('submit','reset')) // on ne prend pas les saisies d'action (pas plus qu'on n'a pris en en compte les "button"s
				AND !extraire_attribut($b, 'disabled') // on ne prend pas les champs desactives : ils ne sont normalement pas soumis
			) {
				$liste_metas[] = $n;
			}
		}
//		spip_log("Associaspip liste dans '$form' les metas suivants : ". implode(', ', array_keys($liste_metas)), 'associaspip');
//		return array_keys(array_unique($liste_metas));
		spip_log("Associaspip trouve dans '$form' les metas suivants : ". implode(', ', array_unique($liste_metas)), 'associaspip');
		return array_unique($liste_metas);
	} else {
		spip_log("Associaspip ne peut recenser les metas de : $f", 'associaspip');
		return array();
	}

}

// Repertoires potentiels des plugins, ce serait bien d'avoir ça ailleurs
// ca n'est pas lie a cette balise
// Attention a l'ordre:
// si l'un des 3 est un sous-rep d'un autre, le mettre avant.

define('_EXTRAIRE_PLUGIN', '@(' .  _DIR_PLUGINS_AUTO . '|' . _DIR_PLUGINS . '|' . _DIR_EXTENSIONS .')(.+)/formulaires/[^/]+$@');

// Recuperer la description XML du plugin et normaliser
// Si ce n'est pas un plugin, dire qu'il faut prendre la table std des meta.
function formulaires_configurer_metas_infos($form) {

	$path = find_in_path($form.'.' . _EXTENSION_SQUELETTES, 'formulaires/');
	if (!$path)
		return ''; // cas traite en amont normalement.
	if (!preg_match(_EXTRAIRE_PLUGIN, $path, $m))
		return array('path' => $path, 'meta' => 'meta'); // structure de $m...
	$get_infos = charger_fonction('get_infos', 'plugins');
	$infos = $get_infos($m[2], FALSE, $m[1]);
	if (!is_array($infos))
		return _T('erreur_plugin_nom_manquant') . ' ' . $m[2] . ' ' . $path;
	if (isset($infos['erreur']))
		return $infos['erreur'][0];
	$infos['path'] = $path;
	if (!isset($infos['meta']))
		$infos['meta'] = ($infos['prefix'] . '_metas');
	return $infos;
}

?>