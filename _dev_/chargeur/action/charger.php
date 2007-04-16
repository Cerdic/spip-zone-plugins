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
 *   ?action=charger&zip=[nom zip sans .zip]
 *
 *	* charger un plugin, le décompresser et l'activer
 *   ?action=charger&plugin=[prefixe plugin]
 *  Si le nom du zip differe du plugin, rajouter: &zip=[nom zip sans .zip]
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
	include_spip('inc/chargeur');
	$url_retour = chargeur_request('url_retour', 'ecrire/');
	if ($actplug = chargeur_request('activer')) {
		chargeur_activer_plugin($actplug);
		redirige_par_entete($url_retour);
	}
	$plugin = chargeur_request('plugin');
	$zip = chargeur_request('zip', $plugin);
	$depot =  chargeur_request('depot', 'http://files.spip.org/spip-zone/');
	$remove = chargeur_request('remove_path', 'spip' . ($plugin ? '/plugins' : ''));
	$dest = chargeur_request('dest_path', _DIR_RACINE . ($plugin ? 'plugins/' : ''));

	$status = chargeur_charger_zip($depot, $zip, $remove, $dest, $plugin);

	redirige_par_entete($url_retour);
}

function chargeur_request($var, $def = null)
{
	($ret = _request($var)) || ($ret = $def);
	return $ret;
}

?>
