<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */

/**
 * Sauvegarde d'une langue d'un module dans son fichier
 * 
 * @param array $module les information d'un module
 * @param object $langue la langue cible
 * @return 
 */
function inc_tradlang_sauvegarde_module_dist($module,$langue){
	spip_log('sauvegarde','tradlang');
	// Debut du fichier de langue
	$lang_prolog = "<"."?php\n// This is a SPIP language file  --  Ceci est un fichier langue de SPIP\nif (!defined('_ECRIRE_INC_VERSION')) return;\n\n";
	// Fin du fichier de langue
	$lang_epilog = "\n?".">\n";

	$fic_exp = _DIR_RACINE.$module["dir_lang"]."/".$module["nom_mod"]."_".$langue.".php";
	$tab = array();
	$conflit = array();  
	$tab = tradlang_lirelang($module, $langue);

	ksort($tab);
	reset($tab);
	$initiale = "";
	$texte = $lang_prolog;
	$texte .= "\$GLOBALS[\$GLOBALS['idx_lang']] = array(\n";

	while (list($code, $chaine) = each($tab)){
		if (!array_key_exists($code, $conflit)){
			if ($initiale != strtoupper($code[0])){
				$initiale = strtoupper($code[0]);
				$texte .= "\n// $initiale\n";
			}
			$texte .= "'".$code."' => '".texte_script($chaine)."',\n";
		}
	}

	// ecriture des chaines en conflit
	if (count($conflit)){
		ksort($conflit);
		reset($conflit);
		$texte .= "\n\n// PLUS_UTILISE\n";
		
		while (list($code, $chaine) = each($conflit))
			$texte .= "'".$code."' => '".texte_script($chaine)."',\n";
	}

	$texte = preg_replace("/\,\n$/", "\n\n);\n", $texte);
	$texte .= $lang_epilog;
	
	include_spip('inc/flock');
	ecrire_fichier($fic_exp,$texte);
	@chmod($fic_exp, 0666);
  
	return true;
}

/**
 * Récupération dans la base de donnée du contenu d'un module de langue dans 
 * une langue définie
 * 
 * @param array $module Les informations du module
 * @param object $langue La langue cible
 * @param object $type [optional]
 * @return 
 */
function tradlang_lirelang($module, $langue, $type=""){
	spip_log('lire_lang','tradlang');
	$ret = array();

	if ($type=="md5"){
		$res = sql_select("id,md5","spip_tradlang","module='$nom_mod' AND lang='$lang_orig' AND !ISNULL(md5)","","id ASC");
		while($row = sql_fetch($res))
		$ret[$row["id"]] = $row["md5"];
	}
	else{
		spip_log('type != md5','tradlang');
		$nom_mod = $module["nom_mod"];
		$res = sql_select("id,str,status","spip_tradlang","module = '$nom_mod' AND lang='$langue'","","id ASC");
		
		while($row = sql_fetch($res)){
			if ($row["status"] != "")
				$statut = "<".$row["status"].">";
			else
				$statut = "";
			$ret[$row["id"]] = $statut.$row["str"];
		}

		// initialise la chaine de tag timestamp sauvegarde
		$quer = "SELECT MAX(ts) as ts FROM spip_tradlang ".
			"WHERE module = '".$nom_mod."' AND lang='".$langue."'";
		$res = sql_query($quer);
		$row = sql_fetch($res);
		$ts = $row["ts"];
		spip_log($ts,'tradlang');

		$ret["zz_timestamp_nepastraduire"] = $ts;
	}

	return $ret;
}
?>