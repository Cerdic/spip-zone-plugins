<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : inc/spipbb_init                               #
#  Authors : Scoty, 2007 et als                            #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/plugin'); // pour version du plugin
spipbb_log('included',3,__FILE__);

# lire version plugin.xml
#
if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
	$get_infos = charger_fonction('get_infos','plugins');
	$infos = $get_infos(_DIR_PLUGIN_SPIPBB);
}
else {
	$infos = plugin_get_infos(_DIR_PLUGIN_SPIPBB);
}
$GLOBALS['spipbb_plug_version'] = $infos['version'];

# recup des metas
// c: 18/12/7 normalement ce n'est pas utile !! car deja initialise ailleurs !
// $GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']); # init juste apres dans inc/spipbb_util spipbb_is_configured

#
# inclure fonctions communes (exec/ .. action/ )...
# controle config. ... Charge conversion 192
#
include_spip('inc/spipbb_util');

#
# Controle de la version du plugin vs SPIP
#
if (defined('SPIPBB_SPIP19200')) {
	// minipres + message
	include_spip('inc/minipres');
	echo minipres(_T('spipbb:plugin_mauvaise_version'));
	die(_T('spipbb:plugin_mauvaise_version'));
}
else
{

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
}
?>