<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_tradlang_supprimer_module($id_tradlang_module){
	if(intval($id_tradlang_module)){
		$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		if($module){
			$traductions = sql_delete('spip_tradlangs','module='.sql_quote($module));
			sql_delete('spip_tradlang_modules','module='.sql_quote($module));
			return $traductions;
		}else{
			return false;
		}
	}else{
		return false;
	}
	
}
?>