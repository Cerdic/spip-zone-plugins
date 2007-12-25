<?php
/*
|
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

spipbb_log('included',3,__FILE__);

# lire version plugin.xml
#
if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');
$infos=plugin_get_infos(_DIR_PLUGIN_SPIPBB);
$GLOBALS['spipbb_plug_version'] = $infos['version'];

# recup des metas
// c: 18/12/7 normalement ce n'est pas utile !! car deja initialise ailleurs !
$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);

#
# inclure fonctions communes (exec/ .. action/ )...
# controle config. ... Charge conversion 192 
#
include_spip('inc/spipbb_util');

#
# Si Install/Maj ? Redir immediate, sauf si on y est !
#
// c: 18/12/7 pas beau mais pas beau du tout un redirect planque dans un include !!!
if(!spipbb_is_configured() AND _request('exec')!="spipbb_configuration") {
	include_spip('inc/headers');
	redirige_par_entete(generer_url_ecrire("spipbb_configuration",'',true));
}

#
# inclure fonctions communes a tous exec 
#
include_spip('inc/spipbb_presentation');
include_spip('inc/spipbb_menus_gauche');



# Def. repertoire icones back
	if (!defined("_DIR_IMG_SPIPBB")) {
		define('_DIR_IMG_SPIPBB', _DIR_PLUGIN_SPIPBB.'/img_pack/');
	}



?>
