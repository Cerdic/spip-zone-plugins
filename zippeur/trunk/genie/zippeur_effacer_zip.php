<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function genie_zippeur_effacer_zip_dist($t){
	// recherche dans la bdd 
	
	$info = sql_select("id_zip,nom",'spip_zippeur','date_zip +INTERVAL delai_suppression SECOND < NOW() AND delai_suppression > 0','0,'._ZIPPEUR_MAX_EFFACER_ZIP); 
	while ($ligne = sql_fetch($info) ){
		defined('_DIR_SITE') ? $chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.'cache-zip/'.$ligne['nom'].".zip" : $chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES.'cache-zip/'.$ligne['nom'].".zip";
		var_dump($chemin);
		function_exists ('effacer_repertoire') ? effacer_repertoire(zippeur_chemin_dossier_local().$ligne['nom']) else "SPIP < 3, impossible d'effacer ".zippeur_chemin_dossier_local().$ligne['nom'],"zippeur");
		if (supprimer_fichier($chemin) or !file_exists($chemin)){
			spip_log("Suppression de ".$chemin,"zippeur");
			sql_delete('spip_zippeur','id_zip='.$ligne['id_zip']);

		}
	}
	return 0;
}
?>