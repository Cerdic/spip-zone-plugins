<?php
// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function titres_typographies_declarer_tables_interfaces($interfaces){
	if (isset($interfaces['table_des_traitements']['TITRE']['forums'])){
		$interfaces['table_des_traitements']['TITRE']['forums']= "trim(PtoBR(liens_nofollow(safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS)."))))";
	}
	return $interfaces;
}
?>
