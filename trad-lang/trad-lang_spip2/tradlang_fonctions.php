<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Fichier des fonctions spécifiques du plugin
 * 
 * @package SPIP\Tradlang\Fonctions
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne les modules disponibles en base sous la forme d'un array complet
 * 
 * @return array $ret
 * 		Un array complet des modules en base
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
			$res2 = sql_select("DISTINCT lang","spip_tradlangs","module='$module'");
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
 * @param array $id_tradlang_module 
 * 		L'identifiant numérique du module
 * @param string $lang 
 * 		Le code de langue à vérifier
 * @return 
 */
function tradlang_testesynchro($id_tradlang_module, $lang){
	$dir_lang = tradlang_dir_lang();

	$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));

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
	if(!$module_final["langue_".$lang]){
		return false;
	}
	
	// lit le timestamp fichier
	$fic = $dir_lang."/".$module_final["langue_".$lang];
	include($fic);
	$chs = $GLOBALS[$GLOBALS['idx_lang']];
	$tsf = $chs["zz_timestamp_nepastraduire"];
	unset($GLOBALS[$GLOBALS['idx_lang']]);

	
	// lit le timestamp  base
	$tsb = sql_getfetsel("maj","spip_tradlangs","module =".sql_quote($module)." AND lang=".sql_quote($lang),"","maj DESC","0,1");

	return ($tsb == $tsf);
}

function tradlang_to_langue($id,$lang){
	$str_lang = sql_getfetsel('str','spip_tradlangs','id='.sql_quote($id).' AND lang='.sql_quote($lang));
	return $str_lang;
}

function tradlang_dir_lang(){
	global $dossier_squelettes;
	if(!$dossier_squelettes && !is_dir(_DIR_RACINE.'squelettes'))
		return false;
	else
		$squelettes = $dossier_squelettes ? $dossier_squelettes : 'squelettes';
	
	if(!is_dir($dir_lang=_DIR_RACINE.$squelettes.'/lang'))
		return false;
	
	return $dir_lang;
}

/**
 * Filtre spip pour utiliser arsort sur un array
 * 
 * @param array $array
 */
function langues_sort($array,$defaut=null){
	if(is_array($array)){
		arsort($array);
		if(isset($defaut)){
			$langue_defaut = array($defaut => $array[$defaut]);
			unset($array[$defaut]);
			$array = array_merge($langue_defaut,$array);
		}
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

	/**
	 * Par defaut, selectionner uniquement les modules qui ne sont pas attic*
	 */ 
	if (!isset($boucle->modificateur['tout'])
	&& !isset($boucle->modificateur['criteres']['module'])
	&& !isset($boucle->modificateur['criteres']['id_tradlang_module'])) {
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"attic%\"'"));
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"contrib\"'"));
	}
	
	/**
	 * Par défaut on tri par priorité et nom_mod
	 */
	if (!isset($boucle->modificateur['par']) 
		&& !isset($boucle->modificateur['tri'])) {
			$boucle->order[] = "'$id_table." ."priorite'";
			$boucle->order[] = "'$id_table." ."nom_mod'";
	}
	return calculer_boucle($id_boucle, $boucles);
}

/**
 * <BOUCLE(TRADLANG)>
 * On enlève les modules attic*
 */
function boucle_TRADLANGS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	// Par defaut, selectionner uniquement les modules qui ne sont pas attic*
	if (!isset($boucle->modificateur['tout'])
		&& !isset($boucle->modificateur['criteres']['module'])
		&& !isset($boucle->modificateur['criteres']['id_tradlang'])
		&& !isset($boucle->modificateur['criteres']['id_tradlang_module'])
	) {
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"attic%\"'"));
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"%attic\"'"));
		array_unshift($boucle->where,array("'NOT LIKE'", "'$id_table." ."module'", "'\"contrib\"'"));
	}
	if(isset($boucle->nom) && ($boucle->nom == 'calculer_langues_utilisees') && $boucle->id_boucle == 'tradlang'){
		array_unshift($boucle->where,array("'='", "'$id_table." ."id_tradlang'", "'0'"));
	}
	return calculer_boucle($id_boucle, $boucles);
}

/**
 * Le critère des langues préférées {langues_preferees}...
 * 
 * {langues_preferees} : Sur une table avec le champ lang retourne uniquement les résultats dans les 
 * langues préférées des utilisateurs, si aucune langue préférée, ne retourne rien
 * {!langues_preferees} : Sur une table avec le champ lang retourne uniquement les résultats dans les 
 * langues non préférées des utilisateurs, si aucune langue préférée, renverra tout
 * 
 * Le critère est sessionné ... Le résultat des boucles l'utilisant est donc modifié en fonction de la session utilisateur
 */
function critere_langues_preferees_dist($idb,&$boucles,$crit){
	$boucle = &$boucles[$idb];
    $id_table = $boucle->id_table;
	$not = ($crit->not ? '' : 'NOT');
	$primary = 'lang';
	$c = "sql_in('".$id_table.'.'.$primary."', prepare_langues_preferees()".($not=='NOT' ? "" : ",'NOT'").")";
	$boucle->where[] = $c;
	/**
	 * Cette boucle est sessionnée car on utilise $GLOBALS['visiteur_session']
	 */
	$boucles[$idb]->descr['session'] = true;
}

/**
 * Fonction de préparation de la sélection des langues préférées
 * 
 * Les langues préférées d'un utilisateur sont stockées dans un array serialisé dans la table
 * spip_auteurs (c'est le champ "langues_preferees")
 * 
 * Si un auteur est identifié, on regarde en base si langue_preferees est rempli sinon on retourne toutes les
 * langues de la liste de ecrire/inc/lang_liste.php
 * 
 * @param return array 
 * 		L'array des langues préférées ou toutes les langues possibles
 */
function prepare_langues_preferees() {
	include_spip('inc/lang_liste');
	if(isset($GLOBALS['visiteur_session']['id_auteur']) && $GLOBALS['visiteur_session']['id_auteur'] >= 1){
		$langues_preferees = sql_getfetsel('langues_preferees','spip_auteurs','id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
		if($langues_preferees && count(unserialize($langues_preferees)) > 0)
			$langues_array = unserialize($langues_preferees);
		else
			$langues_array = array_keys($GLOBALS['codes_langues']);
	}else
		$langues_array = array_keys($GLOBALS['codes_langues']);
	return $langues_array;
}

/**
 * Critère sélectionnant les langues complêtement traduites d'un module 
 * soit dans l'environnement soit passé en paramètre
 * 
 * Ce critère doit être utiliser dans une boucle (TRADLANGS)
 * 
 * Utilisations : 
 * {langue_complete} ou {langue_complete #ID_TRADLANG_MODULE}
 */
function critere_langue_complete_dist($id_boucle, &$boucles, $crit){
	$boucle = &$boucles[$id_boucle];
    $id_table = $boucle->id_table;
	if($id_table == 'tradlangs'){
		if(isset($crit->param[0][0]))
			$id_module = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucles[$id_boucle]->id_parent);
		else
			$id_module = calculer_argument_precedent($id_boucle, 'id_tradlang_module', $boucles);
	
		$boucle->hash .= '
			$prepare_module = charger_fonction(\'prepare_module_langue_complete\', \'inc\');
			$module_having = $prepare_module('.$id_module.', "' . $boucle->sql_serveur . '");
		';

        array_unshift($boucle->where,array("'='", "'$id_table." ."statut'", "'\"OK\"'"));
        $boucles[$id_boucle]->group[] = "$id_table.lang";
        $boucles[$id_boucle]->having[] = "\n\t\t".'$module_having';
    }else
		return (array('zbug_critere_inconnu', array('table' => $crit->op.' ?')));
} 

/**
 * Fonction de préparation du critère {langue_complete}
 * 
 * @param int $id_module
 * 		L'identifiant numérique du module
 * @param string $serveur
 * @return string $having
 * 		La clause having à utiliser par le critère
 */
function inc_prepare_module_langue_complete_dist($id_module,  $serveur='') {
	/**
	 * On récupère les informations du module nécessaires "module" et "lang_mere"
	 */
	$module = sql_fetsel('module,lang_mere','spip_tradlang_modules','id_tradlang_module='.intval($id_module));
	/**
	 * On compte le nombre d'éléments de la langue mère ce qui correspond au nombre total d'éléments à traduire pour un module
	 */
	$count = sql_countsel('spip_tradlangs','id_tradlang_module='.$id_module.' AND statut="OK" AND lang='.sql_quote($module['lang_mere']).' AND module='.sql_quote($module['module']));
	/**
	 * On crée ainsi la clause $having qui sera utilisée dans la boucle
	 */
	$having = "COUNT(*)=$count";
	return $having;
}

/**
 * Convertie une chaîne de caractère en UTF-8
 * 
 * @param string $str
 * 		La chaîne à convertir
 * @return string $str
 * 		La chaîne convertie en UTF-8
 */
function tradlang_utf8($str){
	$str = unicode_to_utf_8(
		html_entity_decode(
			preg_replace('/&([lg]t;)/S', '&amp;\1', $str),
			ENT_NOQUOTES, 'utf-8')
	);
	return $str;
}

/**
 * Fonction qui vérifie que le code de langue spécifié en paramètre est utilisable
 * 
 * Les codes de langue acceptés sont ceux du fichier ecrire/inc/lang.php (s'il n'est pas surchargé)
 * 
 * @param string $lang
 * 		Le code de lang à tester
 * @return bool
 * 		true si ok, false sinon
 */
function langue_possible($lang){
	include_spip('inc/lang');
	$langues = $GLOBALS['codes_langues'];
	if(key_exists($lang,$langues))
		return true;
	
	return false;
}
?>