<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2013 - Distribué sous licence GNU/GPL
 *
 * Script d'installation
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function mediaspip_player_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			/**
			 * Si on avait une configuration de feu html5_player, on la renomme
			 */
			if(isset($GLOBALS['meta']['html5_player'])){
				ecrire_meta('mediaspip_player',$GLOBALS['meta']['html5_player'],'non');
			}
			/**
			 * On vide les caches js et on invalide le cache global
			 */
			include_spip('inc/invalideur');
			$rep_js = _DIR_VAR.'cache-js/';
			purger_repertoire($rep_js);
			suivre_invalideur("1");
			
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.1','<')){
			/**
			 * Si on avait une configuration de feu html5_player, on la renomme
			 */
			if(isset($GLOBALS['meta']['html5_player'])){
				ecrire_meta('mediaspip_player',$GLOBALS['meta']['html5_player'],'non');
			}
			/**
			 * On vide les caches js et on invalide le cache global
			 */
			include_spip('inc/invalideur');
			$rep_js = _DIR_VAR.'cache-js/';
			purger_repertoire($rep_js);
			suivre_invalideur("1");
			
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

function mediaspip_player_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>