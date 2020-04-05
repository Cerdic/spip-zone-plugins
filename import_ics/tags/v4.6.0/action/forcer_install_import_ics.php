<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 *
 * utilisé par prive/objets/contenu
 * utilisation
 [(#AUTORISER{webmestre}|oui)
		[(#BOUTON_ACTION{<:almanach:forcer_install_import_ics:>,#URL_ACTION_AUTEUR{forcer_install_import_ics,#ID_ALMANACH,#URL_ECRIRE{admin_plugin}},noajax,<:almanach:confirmation_forcer_install_import_ics:>})]
 ]
 * Une fois les metas effacés,
 * rediriger vers la page des plugins
 * et relance l'installation automatiquement
 *
 *
 **/
//forcer l'installation
function action_forcer_install_import_ics(){
	
	ecrire_config('import_ics_forcer_deja','');
	
	//vérification de l'auteur en cours
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	//effacer les metas versions base de import_ics
	$nom_meta_base_version = 'import_ics_base_version';
	effacer_meta('import_ics_base_version');
	
	$meta_plug_installes = @unserialize($GLOBALS['meta']['plugin_installes']);
	if(in_array('import_ics_base_version',$meta_plug_installes)){
		unset($meta_plug_installes['import_ics_base_version']);
		ecrire_meta('plugin_installes', serialize($meta_plug_installes));
	};
	
	$meta_plugin = @unserialize($GLOBALS['meta']['plugin']);
	if(in_array('import_ics_base_version',$meta_plugin)){
		unset($meta_plugin['import_ics_base_version']);
		ecrire_meta('plugin', serialize($meta_plugin));
	};
	
	$meta_plugin_header = @unserialize($GLOBALS['meta']['plugin_header']);
	if(in_array('import_ics_base_version',$meta_plugin_header)){
		unset($meta_plugin_header['import_ics_base_version']);
		ecrire_meta('plugin_header', serialize($meta_plugin_header));
	};
	
	ecrire_config('import_ics_forcer_deja','oui');
		
	@spip_unlink(_DIR_TMP . "plugin_xml_cache.gz");
	@spip_unlink(_DIR_TMP . "meta_cache.php");
}