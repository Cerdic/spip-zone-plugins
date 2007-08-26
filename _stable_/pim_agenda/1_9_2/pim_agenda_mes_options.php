<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */

if (!defined('_DIR_PLUGIN_PIMAGENDA')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PIMAGENDA',(_DIR_PLUGINS.end($p)));
}
include_spip('base/pim_agenda');
function autoriser_pimagenda_modifier($faire, $quoi, $id, $qui, $opt) {
	if ($qui['statut'] == '0minirezo' && !$qui['restreint']) return true;
	$res = spip_query("SELECT id_auteur FROM spip_pim_agenda_auteurs WHERE id_agenda="._q($id)." AND id_auteur="._q($qui['id_auteur']));
	if (spip_fetch_array($res))
		return true;
	return false;
}

?>