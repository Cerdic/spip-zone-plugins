<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */

/**
 * Retourne les modules disponibles en base sous la forme d'un array complet
 * 
 * @return Array
 */
function tradlang_getmodules_base(){
	$ret = array();

	/**
	 * Sélection de tous les modules de langue
	 */
	$res = sql_select("*","spip_tradlang_modules");
	if ($res){
		while($row=sql_fetch($res)){
			$nom_mod = $row["nom_mod"];
			$ret[$nom_mod] = $row;

			/**
			 * Récupération des différentes langues et calcul du nom des 
			 * fichiers de langue
			 */
			$res2 = sql_select("DISTINCT lang","spip_tradlang","module='$nom_mod'");
			while($row2=sql_fetch($res2)){
				$lg = $row2["lang"];
				$ret[$nom_mod]["langue_".$lg] = $row["lang_prefix"]."_".$lg.".php";
			}
		}
	}
	return $ret;
}

/**
 * Teste la synchro du fichier de la base avec le fichier de langue en se basant
 * sur une ligne ajoutée lors de l'import si possible
 * 
 * @param array $module Les informations du module
 * @param string $langue Le code de langue
 * @return 
 */
function tradlang_testesynchro($idmodule, $langue){
	$dir_lang = tradlang_dir_lang();
	spip_log($dir_lang);
	spip_log("testesynchro $idmodule - $langue",'tradlang');
	$module = sql_fetsel('*','spip_tradlang_modules','id_tradlang_module='.intval($idmodule));
	$nom_module = $module["module"];
	$nom_mod = $module["nom_mod"];
	//$dir_lang = $module["dir_lang"];
	
	$modules = tradlang_getmodules_base();
	$modok = $modules[$nom_mod];
	$getmodules_fics = charger_fonction('tradlang_getmodules_fics','inc');
	
	$modules2 = $getmodules_fics($dir_lang);
	$modok2 = $modules2[$nom_mod];

	// union entre modok et modok2
	if(is_array($modok2)){
		foreach($modok2 as $cle=>$item){
			if (strncmp($cle, "langue_", 7) == 0){
				$sel = "";
				$lang = substr($cle,7);
				if (!array_key_exists($lang, $modok)){
					$module["langue_".$lang] = $item;
				}
			}
		}
	}
	// Le fichier n'existe pas
	if(!$module["langue_".$langue]){
		return false;
	}
	
	// lit le timestamp fichier
	$fic = $dir_lang."/".$module["langue_".$langue];
	include($fic);
	$chs = $GLOBALS[$GLOBALS['idx_lang']];
	$tsf = $chs["zz_timestamp_nepastraduire"];
	spip_log($tsf,'tradlang');
	unset($GLOBALS[$GLOBALS['idx_lang']]);

	// lit le timestamp  base
	$res = sql_select("*","spip_tradlang","module =".sql_quote($nom_mod)." AND lang=".sql_quote($langue),"","ts DESC","0,1");
	$row = sql_fetch($res);
	$tsb = $row["ts"];
	spip_log($tsb);
	return ($tsb == $tsf);
}

function tradlang_to_langue($id,$lang){
	$str_lang = sql_getfetsel('str','spip_tradlang','id='.sql_quote($id).' AND lang='.sql_quote($lang));
	return $str_lang;
}

function tradlang_dir_lang(){
	global $dossier_squelettes;
	if(!$dossier_squelettes && !is_dir(_DIR_RACINE.'squelettes')){
		return false;
	}
	else{
		$squelettes = $dossier_squelettes ? $dossier_squelettes : _DIR_RACINE.'squelettes/';
	}
	if(!is_dir($dir_lang=$squelettes.'lang')){
		spip_log("pas de squelette $squelettes",'test');
		return false;
	}
	return $dir_lang;
}
?>