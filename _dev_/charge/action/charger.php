<?php
/*
 * Plugin charge : action charger decompresser
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 * Charger un zip, le decompresser et si plugin, l'activer.
 * Appels apres authentification: [url spip public]/...
 *	* charger un zip et le décompresser:
 *   ?action=charger&zip=[nom paquet sans .zip]
 *
 *	* charger un plugin, le décompresser et l'activer
 *   ?action=charger&plugin=[prefixe plugin]
 *  Si le nom du paquet differe du plugin, rajouter: &zip=[nom paquet sans .zip]
 *
 * Options supplementaires:
 *  &url_retour=[url retour], défaut ecrire/
 *  &depot=[url du depot des zip] defaut http://files.spip.org/spip-zone/
 *
 * On peut aussi simplement activer un plugin deja present par:
 *   ?action=charger&activer=[prefixe plugin]
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_charger()
{
	include_spip('inc/loader');
	$url_retour = charge_request('url_retour', 'ecrire/');
	if ($actplug = charge_request('activer_plugin')) {
		kit_activer_plugin($actplug);
		redirige_par_entete($url_retour);
	}
	$plugin = charge_request('plugin');
	$paquet = charge_request('paquet', $plugin);
	$depot =  charge_request('depot', 'http://files.spip.org/spip-zone/');
	$remove = charge_request('remove_path', 'spip');
	$dest = charge_request('dest_path', _DIR_RACINE . ($plugin ? 'plugins/' : ''));

	$status = kit_charger_zip($depot, $paquet, $remove, $dest, $plugin);

	redirige_par_entete($url_retour);
}

function charge_request($var, $def = null)
{
	is_null($ret = _request($var)) && ($ret = $def);
	return $ret;
}

?>
