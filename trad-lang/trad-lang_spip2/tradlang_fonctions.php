<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
			$module = $row["module"];
			$ret[$module] = $row;

			/**
			 * Récupération des différentes langues et calcul du nom des 
			 * fichiers de langue
			 */
			$res2 = sql_select("DISTINCT lang","spip_tradlang","module='$module'");
			while($row2=sql_fetch($res2)){
				$lg = $row2["lang"];
				$ret[$module]["langue_".$lg] = $row["lang_prefix"]."_".$lg.".php";
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

	$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($idmodule));

	$modules = tradlang_getmodules_base();
	$modok = $modules[$module];

	$getmodules_fics = charger_fonction('tradlang_getmodules_fics','inc');
	$modules2 = $getmodules_fics($dir_lang,$module);
	$modok2 = $modules2[$module];

	// union entre modok et modok2
	if(is_array($modok2)){
		foreach($modok2 as $cle=>$item){
			if (strncmp($cle, "langue_", 7) == 0){
				$sel = "";
				$lang = substr($cle,7);
				if (!array_key_exists($lang, $modok)){
					$module_final["langue_".$lang] = $item;
				}
			}
		}
	}
	// Le fichier n'existe pas
	if(!$module_final["langue_".$langue]){
		return false;
	}
	
	// lit le timestamp fichier
	$fic = $dir_lang."/".$module_final["langue_".$langue];
	include($fic);
	$chs = $GLOBALS[$GLOBALS['idx_lang']];
	$tsf = $chs["zz_timestamp_nepastraduire"];
	unset($GLOBALS[$GLOBALS['idx_lang']]);

	
	// lit le timestamp  base
	$tsb = sql_getfetsel("ts","spip_tradlang","module =".sql_quote($module)." AND lang=".sql_quote($langue),"","ts DESC","0,1");

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
		return false;
	}
	return $dir_lang;
}

/**
 * Filtre spip pour utiliser arsort sur un array
 * 
 * @param array $array
 */
function langues_sort($array){
	if(is_array($array)){
		arsort($array);
		return $array;
	}
	return false;
}

/**
 * <BOUCLE(TRADLANG_MODULES)>
 * On enlève les modules attic*
 */
function boucle_TRADLANG_MODULES_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	// Par defaut, selectionner uniquement les modules qui ne sont pas attic*
	if (!isset($boucle->modificateur['tout'])) {
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"attic%\"'"));
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"contrib\"'"));
	}

	if (!isset($boucle->modificateur['par']) 
		&& !isset($boucle->modificateur['tri'])) {
			$boucle->order[] = "'$id_table." ."priorite'";
			$boucle->order[] = "'$id_table." ."nom_mod'";
			//array_unshift();
			//array_unshift($boucle->order,"'$id_table." ."nom_mod'");
	}
	return calculer_boucle($id_boucle, $boucles);
}

/**
 * <BOUCLE(TRADLANG)>
 * On enlève les modules attic*
 */
function boucle_TRADLANG_dist($id_boucle, &$boucles) {
	spip_log($boucle->nom,'tradlang');
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	// Par defaut, selectionner uniquement les modules qui ne sont pas attic*
	if (!isset($boucle->modificateur['tout'])) {
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"attic%\"'"));
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"%attic\"'"));
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"contrib\"'"));
		array_unshift($boucle->where,array("'!='", "'$id_table." ."id'", "'\"zz_timestamp_nepastraduire\"'"));
	}
	if(($boucle->nom == 'calculer_langues_utilisees') && $boucle->id_boucle == 'tradlang'){
		array_unshift($boucle->where,array("'='", "'$id_table." ."id_tradlang'", "'0'"));
	}
	return calculer_boucle($id_boucle, $boucles);
}

/**
 * Critère permettant de sélection les langues complêtement traduites d'un module 
 * soit dans l'environnement soit passé en paramètre
 * {langue_complete} ou {langue_complete #ID_TRADLANG_MODULE}
 */
function critere_langue_complete_dist($id_boucle, &$boucles, $crit){
	$boucle = &$boucles[$id_boucle];
    $id_table = $boucle->id_table;
	if(isset($crit->param[0][0]))
		$id_module = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucles[$id_boucle]->id_parent);
	else
		$id_module = calculer_argument_precedent($id_boucle, 'id_tradlang_module', $boucles);

	$boucle->hash .= '
		$prepare_module = charger_fonction(\'prepare_module\', \'inc\');
		$module_having = $prepare_module('.$id_module.', "' . $boucle->sql_serveur . '");
	';

    if($id_table == 'tradlang'){
        array_unshift($boucle->where,array("'='", "'$id_table." ."statut'", "'\"OK\"'"));
        $boucles[$id_boucle]->group[] = "$id_table.lang";
        $boucles[$id_boucle]->having[] = "\n\t\t".'$module_having';
    }else
		return (array('zbug_critere_inconnu', array('table' => $crit->op.' ?')));
} 

function inc_prepare_module_dist($id_module,  $serveur='') {
	$lang_mere = sql_getfetsel('lang_mere','spip_tradlang_modules','id_tradlang_module='.intval($id_module));
	$count = sql_countsel('spip_tradlang','id_tradlang_module='.$id_module.' AND statut="OK" AND lang='.sql_quote($lang_mere));
	$having = "COUNT(*)=$count";
	return $having;
}
?>