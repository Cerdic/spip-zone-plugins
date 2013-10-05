<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de suppression de module
 * 
 * @param int $id_tradlang_module
 * 	Identifiant numérique du module à supprimer
 * @return bool
 */
function inc_tradlang_supprimer_module($id_tradlang_module){
	if(intval($id_tradlang_module) > 0){
		/**
		 * Le module existe-t-il ?
		 */
		$module = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		if($module){
			/**
			 * Suppression des versions des chaines de langue
			 */
			$tradlangs = sql_allfetsel('id_tradlang','spip_tradlangs','id_tradlang_module='.intval($module));
			$tradlangs_supprimer = array();
			foreach($tradlangs as $tradlang){
				$tradlangs_supprimer[] = $tradlang['id_tradlang'];
			}
			if(count($tradlangs_supprimer)){
				sql_delete('spip_versions','objet="tradlang" AND '.sql_in('id_objet',$tradlangs_supprimer));
				sql_delete('spip_versions_fragments','objet="tradlang" AND '.sql_in('id_objet',$tradlangs_supprimer));
			}
			/**
			 * Suppression des chaines de langue, du module et des bilans
			 */
			sql_delete('spip_tradlangs','id_tradlang_module='.intval($module));
			sql_delete('spip_tradlang_modules','id_tradlang_module='.intval($module));
			sql_delete('spip_tradlangs_bilans','id_tradlang_module='.intval($module));
			/**
			 * Suppression des versions du module
			 */
			sql_delete('spip_versions_fragments','objet="tradlang_module" AND id_objet='.intval($module));
			sql_delete('spip_versions','objet="tradlang_module" AND id_objet='.intval($module));
			return true;
		}else
			return false;
	}else
		return false;
	
}
?>