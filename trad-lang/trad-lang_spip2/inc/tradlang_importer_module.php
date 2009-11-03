<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */

/**
 * 
 * Import d'un module de langue dans Trad-lang
 * 
 * @param object $module
 * @return 
 */
function inc_tradlang_importer_module($module){
	include_spip('inc/texte');
	include_spip('inc/lang_liste');
	$ret = '';

	list($select_modules, $tous_modules) = tradlang_select_liste_rep_lang();
	
	/**
	 * Insertion du module dans la base
	 */
	$res = sql_insertq("spip_tradlang_modules",$module);
	
	if (!$res){
		$ret .= propre(_T('tradlang:module_deja_importe',array('module'=>$module['nom_module'])));
		return array($ret,false);
	}

	$nom_mod = $module['nom_mod'];
	$module_choisi = $tous_modules[$nom_mod];
	$langues_module = explode(',',$module_choisi['langues']);
	
	/**
	 * Insertion de chaque fichier de langue existant dans la base
	 */
	foreach($langues_module as $langue){
		if($langue){
			$fichier = $module_choisi[$langue]['fichier'];
			
			$orig = 0;
			if ($langue == $module['lang_mere'])
				$orig = 1;
				
			$ret .= _T('tradlang:insertionlangue')." : ".$langue."...";
			$nom_fichier = $module['dir_lang']."/".$fichier;
			include($nom_fichier);
			$chs = $GLOBALS[$GLOBALS['idx_lang']];
			reset($chs);
			while(list($id, $str) = each($chs)){
				$res = sql_insertq('spip_tradlang',array(
					'id' => $id,
					'module' => $module["nom_mod"],
					'str' => $str,
					'lang' => $langue,
					'orig' => $orig
				));
				if ($res === false) {	
					$ret .= mysql_error();
					return array($ret,false);
				}
			}
			$ret .= _T('tradlang:insertionlangueok')."<br />";
		
			unset($GLOBALS[$GLOBALS['idx_lang']]);
		
			// si le fichier est inscriptible, on sauvegarde le
			// fichier depuis la base afin de tagguer le timestamp
			if ($fd = @fopen($nom_fichier, "a")){
				fclose($fd);
				$sauvegarde = charger_fonction('tradlang_sauvegarde_module','inc');
				$sauvegarde($module,$langue);
			}
		}
	}
	return array($ret,true);
}

/**
 * 
 * Liste les modules qui sont potentiellement traduisibles
 * 
 * @param string $name [optional] le name et id du select
 * @param string $selected [optional] l'option à sélectionner par défaut
 * @return Array un Array avec en première clé 'select_string' qui est un 
 * input select utilisable et en seconde un array complet des informations des
 * fichiers de langue
 */
function tradlang_select_liste_rep_lang($name="repertoirelangue",$selected='',$new_only='false'){
	$ret = '';
	$tous_modules_en_base = array();
	if($new_only){
		$tous_modules_en_base = array();
		$tous_modules_base = sql_allfetsel('nom_module','spip_tradlang_modules');
		foreach($tous_modules_base as $module => $nom_module){
			$tous_modules_en_base[] = $nom_module['nom_module'];
		}
	}
	$tous_modules = array();
	$fichiers_lang = find_all_in_path('lang/','[a-z]?\_[a-z_]{2,7}\.php$');
	ksort($fichiers_lang);
	if(count($fichiers_lang) > 0){
		$ret .= "<select name=\"$name\" id=\"$name\" class=\"text\">\n";
		foreach($fichiers_lang as $fichier => $chemin){
			$sel = '';
			if(preg_match('/^([a-z]*)_([a-z_]*)\.php$/i',$fichier,$module)){
				if(array_key_exists($module[2],$GLOBALS['codes_langues'])){
					if(!in_array($module[1],$tous_modules_en_base)){
						if(!array_key_exists($module[1],$tous_modules)){
							if($module[1] == $selected){
								$sel = "selected=\"selected\"";
							}
							$ret .= "<option value=\"".$module[1]."\"$sel>".$module[1]."</option>\n";
							$tous_modules[$module[1]]['repertoire'] = dirname($chemin);
							$tous_modules[$module[1]]['langues'] = $module[2];
						}else{
							$tous_modules[$module[1]]['langues'] .= ",".$module[2];
						}
						$tous_modules[$module[1]][$module[2]] = array('fichier' => basename($chemin));
					}
				}
				else if(preg_match('/^([a-z]*_[a-z]*)_([a-z_]*)\.php$/i',$fichier,$module)){
					if(array_key_exists($module[2],$GLOBALS['codes_langues'])){
						if(!in_array($module[1],$tous_modules_en_base)){
							if(!array_key_exists($module[1],$tous_modules)){
								if($module[1] == $selected){
									$sel = "selected=\"selected\"";
								}
								$ret .= "<option value=\"".$module[1]."\"$sel>".$module[1]."</option>\n";
								$tous_modules[$module[1]]['repertoire'] = dirname($chemin);
								$tous_modules[$module[1]]['langues'] = $module[2];
							}else{
								$tous_modules[$module[1]]['langues'] .= ",".$module[2];	
							}
							$tous_modules[$module[1]][$module[2]] = array('fichier' => basename($chemin));
						}
					}
				}
			}
		}
		$ret .= "</select>\n";
	}
	return array($ret,$tous_modules);
}

/**
 * 
 * Un select listant les langues disponibles pour un module
 * 
 * @param string $module Le module de langue
 * @param string $name [optional] Le name et l'id du champs select
 * @param string $selected [optional] La langue à sélectionner par défaut
 * @return 
 */
function tradlang_select_langues_module($module, $name="langue_mere",$selected="",$option_vide=false){
	$ret = '';
	list($select,$tous_modules) = tradlang_select_liste_rep_lang();
	$langues = $tous_modules[$module]['langues'];
	if(is_array($tous_modules[$module]) && (count(explode(',',$langues)) > 0)){
		$langues = explode(',',$langues);
		$ret .= "<select class=\"text\" name=\"$name\" id=\"$name\">\n";
		$ret .= $option_vide ? "<option value=\"\">--</option>" : ""; 
		foreach($langues as $langue){
			$ret .= "<option value=\"$langue\">".traduire_nom_langue($langue)."</option>\n";
		}
		$ret .= "</select>\n";
	}
	return $ret;
}

function tradlang_verifier_acces_fichiers($module){
	list($select,$tous_modules) = tradlang_select_liste_rep_lang();
	$infos_modules = $tous_modules[$module];
	
	$langues = explode(',',$infos_modules['langues']);
	// test si fichier inscriptible
	foreach($langues as $lang){
		$fichier = $infos_modules['repertoire']."/".$infos_modules[$lang]['fichier'];
		if (!$fd = @fopen($fichier, "a"))
			$ficnok[] = $fichier;
		else
			fclose($fd);
	}
	return $ficnok;
}
?>