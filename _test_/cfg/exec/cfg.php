<?php
/*
 * Plugin cfg : ecrire/?exec=cfg&cfg=xxxx
 *
 * Auteur : bertrand@toggg.com
 * (c) 2007 - Distribue sous licence LGPL
 *
 */
// la fonction appelee par le core, une simple "factory" de la classe cfg


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
		if (version_compare($GLOBALS['spip_version_code'],'1.93','<')) ecrire_metas();
		$config->message = $message;
	}

	$config->traiter();
	
	echo $config->sortie();

	return;
}


?>
