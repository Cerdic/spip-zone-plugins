<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Import d'un module de langue dans Trad-lang
 * 
 * @param object $module
 * @return 
 */
function inc_tradlang_importer_module($module,$dir_lang=false,$new_only=false){
	include_spip('inc/texte');
	include_spip('inc/lang_liste');
	$ret = '';

	/**
	 * On ne fournit pas de dir_lang donc on se base sur les fichiers du path
	 */
	if(!$dir_lang){
		list($select_modules, $tous_modules) = tradlang_select_liste_rep_lang();
		$nom_mod = $module['nom_mod'];
		$module_choisi = $tous_modules[$nom_mod];
		$langues_module = explode(',',$module_choisi['langues']);
	}
	
	if(!($res = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','nom_mod='.sql_quote($module['nom_mod'])))){
		/**
		 * Insertion du module dans la base
		 */
		$res = sql_insertq("spip_tradlang_modules",$module);
		$mode = 'new';	
	}else
		$mode = 'update';
	
	if ($new_only && ($mode=='update')){
		$ret .= propre(_T('tradlang:module_deja_importe',array('module'=>$module['module'])));
		return array($ret,false);
	}
	
	/**
	 * Insertion de chaque fichier de langue existant dans la base
	 */
	$liste_id_orig = array();
	array_unshift($langues_module, $module['lang_mere']);
	array_unique($langues_module);
	foreach($langues_module as $langue){
		$chs = null;
		$fichier = $module_choisi[$langue]['fichier'];
		$orig = 0;
		if ($langue == $module['lang_mere'])
			$orig = 1;

		$ret .= _T('tradlang:insertionlangue')." : ".$langue."<br />";
		$nom_fichier = _DIR_RACINE.$module['dir_lang']."/".$fichier;
		
		include($nom_fichier);
		$chs = $GLOBALS[$GLOBALS['idx_lang']];
		if (is_null($chs))
			return false;
		
		reset($chs);
		
		// nettoyer le contenu de ses <MODIF>
		$statut = array();
		foreach($chs as $id=>$v) {
			if (preg_match(',^<(MODIF|NEW|PLUS_UTILISE)>,US', $v, $r)) {
				$chs[$id] = preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $v);
				$statut[$id] = $r[1];
			}
			else
				$statut[$id] = '';
		}
		
		$res = sql_select("id, str, md5","spip_tradlangs","module=".sql_quote($nom_mod)." AND lang=".sql_quote($langue));
		if($mode == 'update'){
			if(sql_count($res)>0)
				spip_log("Fichier de langue $langue du module $nom_mod deja inclus dans la base\n","tradlang");
		}
		$existant = array();
		while ($t = sql_fetch($res))
			$existant[$t['id']] = $t['md5'];
			
		$ajoutees = $inchangees = $supprimees = $modifiees = $ignorees = 0;
		
		// Dans ce qui arrive, il y a 4 cas :
		foreach (array_unique(
					array_merge(array_keys($existant), array_keys($chs))
				) as $id) {
			if (isset($chs[$id]) AND !isset($existant[$id])){
				unset($md5);
				if ($orig)
					$md5 = md5($chs[$id]);
				else if (!isset($liste_id_orig[$id])) {
					spip_log("!-- Chaine $id inconnue dans la langue principale\n","tradlang");
					$ignorees++;
				}else
					$md5 = $liste_id_orig[$id];

				/**
				 * zz_timestamp_nepastraduire est ce qui nous permet de vérifier la synchro
				 * BDD / fichiers
				 */
				if (isset($md5) && ($id != 'zz_timestamp_nepastraduire')){
					sql_insertq('spip_tradlangs',array(
						'id' => $id,
						'id_tradlang_module' => $module['id_tradlang_module'],
						'module' => $module["module"],
						'str' => $chs[$id],
						'lang' => $langue,
						'orig' => $orig,
						'md5' => $md5,
						'statut' => $statut[$id]
					));
					$ajoutees++;
				}
			}
			else
			// * chaine existante
			if (isset($chs[$id]) AND isset($existant[$id])){
				spip_log('cas 2','tradlang');
				// * identique ? => NOOP
				$md5 = md5($chs[$id]);
				if ($md5 == $existant[$id])
					$inchangees++;
				// * modifiee ? => UPDATE
				else {
					// modifier la chaine
					$md5_new = $orig ? $md5 : $existant[$id];
					sql_updateq("spip_tradlangs",array(
						'str' => $str,
						'md5' => $md5_new,
						'statut' => '',
						), "module=".sql_quote($nom_mod)." AND lang=".sql_quote($langue)." AND id=".sql_quote($id));
					
					// signaler le statut MODIF de ses traductions
					if ($orig)
						sql_updateq("spip_tradlangs",array('statut'=>'MODIF'),"module=".sql_quote($nom_mod)." AND id=".sql_quote($id)." AND md5 !=".sql_quote($md5));
					$modifiees++;
				}
			}
			else
			// * chaine supprimee
			if (!isset($chs[$id]) AND isset($existant[$id])){
				spip_log('cas 3','tradlang');
				// mettre au grenier
				sql_updateq("spip_tradlangs",array(
					'id' => $id,
					'statut' => 'attic'),"id=".sql_quote($id)." AND module=".sql_quote($nom_mod));
				$supprimees++;
			}

			if ($orig AND isset($chs[$id])){
				spip_log('cas 4','tradlang');
				$liste_id_orig[$id]=md5($chs[$id]);
			}
		}
		
		/**
		 * Si ce n'est pas la langue mère que l'on importe :
		 * - On ajoute dans la base les chaines manquantes dans le fichier de la langue
		 * - On met le statut attic pour les chaines en trop dans les fichiers
		 */
		if($langue != $module['lang_mere']){
			$tradlang_verifier_langue_base = charger_fonction('tradlang_verifier_langue_base','inc');
			$tradlang_verifier_langue_base($nom_mod,$langue);
		}
		// si le fichier est inscriptible, on sauvegarde le
		// fichier depuis la base afin de tagguer le timestamp
		if ($fd = @fopen($nom_fichier, "a")){
			fclose($fd);
			$sauvegarde = charger_fonction('tradlang_sauvegarde_module','inc');
			$sauvegarde($module,$langue);
		}
		unset($GLOBALS[$GLOBALS['idx_lang']]);
		$ret .= _T('tradlang:insertionlangueok')."<br />";
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
		$tous_modules_base = sql_allfetsel('module','spip_tradlang_modules');
		foreach($tous_modules_base as $module => $nom_module){
			$tous_modules_en_base[] = $nom_module['module'];
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
						if(test_espace_prive()){
							if(strpos($chemin, _DIR_RACINE) !== false){
								$search = _DIR_RACINE;
								$chemin = str_replace($search,'',$chemin);
							}else{
								$chemin = _DIR_RESTREINT_ABS.$chemin;
							}
						}
						if(!array_key_exists($module[1],$tous_modules)){
							if($module[1] == $selected)
								$sel = "selected=\"selected\"";
							$ret .= "<option value=\"".$module[1]."\"$sel>".$module[1]."</option>\n";
							$tous_modules[$module[1]]['repertoire'] = dirname($chemin);
							$tous_modules[$module[1]]['langues'] = $module[2];
						}else
							$tous_modules[$module[1]]['langues'] .= ",".$module[2];
						$tous_modules[$module[1]][$module[2]] = array('fichier' => basename($chemin));
					}
				}
				else if(preg_match('/^([a-z]*_[a-z]*)_([a-z_]*)\.php$/i',$fichier,$module)){
					if(array_key_exists($module[2],$GLOBALS['codes_langues'])){
						if(test_espace_prive()){
							if(strpos($chemin, _DIR_RACINE) !== false){
								$search = _DIR_RACINE;
								$chemin = str_replace($search,'',$chemin);
							}else
								$chemin = _DIR_RESTREINT_ABS.$chemin;
						}
						if(!in_array($module[1],$tous_modules_en_base)){
							if(!array_key_exists($module[1],$tous_modules)){
								if($module[1] == $selected)
									$sel = "selected=\"selected\"";
								$ret .= "<option value=\"".$module[1]."\"$sel>".$module[1]."</option>\n";
								$tous_modules[$module[1]]['repertoire'] = dirname($chemin);
								$tous_modules[$module[1]]['langues'] = $module[2];
							}else
								$tous_modules[$module[1]]['langues'] .= ",".$module[2];	
							
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
		$fichier = _DIR_RACINE.$infos_modules['repertoire']."/".$infos_modules[$lang]['fichier'];
		if (!$fd = @fopen($fichier, "a"))
			$ficnok[] = $fichier;
		else
			fclose($fd);
	}
	return $ficnok;
}
?>