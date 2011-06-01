<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.55 - 05/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Initialiser actijour (maj meta)
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;


# lire version plugin.xml
#
if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');
$infos=plugin_get_infos(_DIR_PLUGIN_ACTIJOUR);
$GLOBALS['actijour_plug_version'] = $infos['version'];

#
# requis pour tous exec
# 
include_spip("inc/func_acj");

#
# recup des metas
#
$GLOBALS['actijour'] = @unserialize($GLOBALS['meta']['actijour']);

#
# initialise actijour
#
if( !isset($GLOBALS['meta']['actijour']) 
	OR 
	version_compare(substr($GLOBALS['actijour']['version'],0,5),$GLOBALS['actijour_plug_version'],'<') )
	{
	initialise_metas_actijour($GLOBALS['actijour']['version']);
	}

?>
