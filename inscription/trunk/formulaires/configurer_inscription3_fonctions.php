<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Inclure cextras_pipelines pour gérer les champs extras
 * Notamment la fonction extras_champs_utilisables()
 */
if(defined('_DIR_PLUGIN_CEXTRAS'))
	include_spip('cextras_pipelines');

?>