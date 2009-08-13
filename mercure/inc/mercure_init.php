<?php
/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com

+--------------------------------------------+
| Initialiser mercure (maj meta)            |
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;


# lire version plugin.xml
#
if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');
$infos = plugin_get_infos(_DIR_PLUGIN_MERCURE);
$GLOBALS['mercure_plug_version'] = $infos['version'];

#
# requis pour tous exec
# 
include_spip("inc/func_mercure");

#
# recup des metas
#
$GLOBALS['mercure'] = @unserialize($GLOBALS['meta']['mercure']);

#
# initialise mercure
#
if( !isset($GLOBALS['meta']['mercure']) 
	OR 
	version_compare(substr($GLOBALS['mercure']['version'],0,5),$GLOBALS['mercure_plug_version'],'<') )
  {
	 initialise_metas_mercure($GLOBALS['mercure']['version']);
	}

?>
