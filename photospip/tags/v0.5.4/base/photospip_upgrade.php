<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function photospip_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/abstract_sql');
		if ($current_version==0.0){
			include_spip('base/create');
			creer_base();
			$config_palette = lire_config('palette',array());
			$config_palette['palette_ecrire'] = 'on';
			ecrire_meta("palette",serialize($config_palette));
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			spip_query("ALTER TABLE spip_documents_inters  ADD `filtre` text AFTER `version` ");
			ecrire_meta($nom_meta_base_version,$current_version="0.2","non");
		}
		if (version_compare($current_version,'0.3','<')){
			spip_query("ALTER TABLE spip_documents_inters  ADD `param` text AFTER `filtre` ");
			ecrire_meta($nom_meta_base_version,$current_version="0.3","non");
		}
		if (version_compare($current_version,'0.4','<')){
			$config_palette = lire_config('palette',array());
			$config_palette['palette_ecrire'] = 'on';
			ecrire_meta("palette",serialize($config_palette));
			ecrire_meta($nom_meta_base_version,$current_version="0.4","non");
		}
		if (version_compare($current_version,'0.5','<')){
			include_spip('base/create');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version="0.5","non");
		}
	}
}
	
function photospip_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	spip_query("DROP TABLE spip_documents_inters");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>