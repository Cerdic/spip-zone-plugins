<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 2.0 - 06/2009 - SPIP 2.x
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| D. Chiche . pour la maj 2.0
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Initialiser actijour (maj meta)
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;


# lire version plugin.xml
# la fonction plugin_get_infos n'exsite plus à partir de spip 2.1, on teste donc la version de spip pour savoir quelle fonction utiliser 

if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
   $get_infos = charger_fonction('get_infos','plugins');
   $infos = $get_infos(_DIR_PLUGIN_ACTIJOUR);
}
else { }
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