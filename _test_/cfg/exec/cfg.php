<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * la fonction appelee par le core, une simple "factory" de la classe cfg
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_cfg_dist($class = null)
{
	if (!$class || !class_exists($class)) 
		$class = 'cfg';
		
	$cfg = cfg_charger_classe($class);

	$config = & new $cfg(
		($nom = _request('cfg'))? $nom : 'cfg',
		($vue = _request('vue'))? $vue : $nom,
		($cfg_id = _request('cfg_id'))? $cfg_id : ''
		);

	if ($message = $GLOBALS['meta']['cfg_message_'.$GLOBALS['auteur_session']['id_auteur']]) {
		include_spip('inc/meta');
		effacer_meta('cfg_message_'.$GLOBALS['auteur_session']['id_auteur']);
		if (defined('_COMPAT_CFG_192')) ecrire_metas();
		$config->message = $message;
	}

	$config->traiter();
	
	echo $config->sortie();

	return;
}


?>
