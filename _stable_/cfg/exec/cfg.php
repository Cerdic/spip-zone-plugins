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
	include_spip('inc/filtres');

	$cfg = cfg_charger_classe('cfg','inc');
	$config = & new $cfg(
		($nom = sinon(_request('cfg'), '')),
		($cfg_id = sinon(_request('cfg_id'),''))
		);
	
	// si le fond cfg demande une redirection, 
	// (et provient de cette redirection), il est possible
	// qu'il y ait un message a afficher
	if ($config->form->param->rediriger 
		&& $messages = $GLOBALS['meta']['cfg_message_'.$GLOBALS['auteur_session']['id_auteur']]){
			include_spip('inc/meta');
			effacer_meta('cfg_message_'.$GLOBALS['auteur_session']['id_auteur']);
			if (defined('_COMPAT_CFG_192')) ecrire_metas();
			$config->form->messages = unserialize($messages);
	}

	$config->form->traiter();
	
	echo $config->sortie();

	return;
}


?>
