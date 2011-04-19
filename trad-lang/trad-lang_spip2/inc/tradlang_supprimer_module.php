<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_tradlang_supprimer_module($nom_mod){
	$idmodule = sql_getfetsel('idmodule','spip_tradlang_modules','nom_mod='.sql_quote($nom_mod));
	if(intval($idmodule)){
		$traductions = sql_delete('spip_tradlang','module='.sql_quote($nom_mod));
		sql_delete('spip_tradlang_modules','nom_mod='.sql_quote($nom_mod));
		return $traductions;
	}else{
		return false;
	}
	
}
?>