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
		$vars = formulaires_configurer_metas_recense($infos['path'], PREG_PATTERN_ORDER);
		$meta = $infos['meta'];
		foreach (array_unique($vars[2]) as $k) {
			$v = _request($k);
			ecrire_meta($k, is_array($v) ? serialize($v) : $v, 'oui', $meta);
		}
		return !isset($infos['prefix']) ? array()
		: array('redirect' => generer_url_ecrire($infos['prefix']));
	}
}

// version amelioree de la RegExp de cfg_formulaire.
define('_EXTRAIRE_SAISIES',
	'#<(select|textarea|input)[^>]*\sname=["\'](\w+)(\[\w*\])?["\'](?: class=["\']([^\'"]*)["\'])?( multiple=)?[^>]*?>#ims');

define('_EXTRAIRE_INCLURE','#INCLU[DR]E{fond=([^,} ]+)[^}]*}#s');

// determiner la liste des noms des saisies d'un formulaire
// (a refaire avec SAX)
function formulaires_configurer_metas_recense($form, $opt='') {
	if (!$opt) $opt = PREG_SET_ORDER;
	$f = file_get_contents($form);
	if (preg_match_all(_EXTRAIRE_INCLURE, $f, $r, PREG_SET_ORDER)) {
		foreach($r as $m) {
			if ($i = find_in_path($m[1] . '.html')) 
			  $f = str_replace($m[0], file_get_contents($i), $f);
		}
	}
	if ($f AND preg_match_all(_EXTRAIRE_SAISIES, $f, $r, $opt))
		return $r;
	else
		return array();
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
		return array('path' => $path, 'meta' => 'meta');
	$plugin = $m[2];
	$get_infos = charger_fonction('get_infos','plugins');
	$infos = $get_infos($plugin, FALSE, $m[1]);
	if (!is_array($infos))
	  return _T('erreur_plugin_nom_manquant') . ' ' . $plugin . ' ' . $path;
	if (isset($infos['erreur'])) return $infos['erreur'][0];
	$prefix = $infos['prefix'];
	$infos['path'] = $path;
	if (!isset($infos['meta']))
		$infos['meta'] = ($prefix . '_metas');
	return $infos;
}

?>