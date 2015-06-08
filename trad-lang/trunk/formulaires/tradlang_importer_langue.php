<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_tradlang_importer_langue_charger_dist($id_tradlang_module,$lang,$lang_orig) {
	$valeurs['_etapes'] = 2;
	$valeurs['lang_orig'] = $lang_orig;
	$valeurs['lang_cible'] = $lang;
	if(!$nom_mod = sql_getfetsel('nom_mod','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module))){
		$valeurs['message_erreur'] = _T('tradlang:erreur_import_module_inexistant',array('id'=>$id_tradlang_module));
		$valeurs['editable'] = false;
	}else{
		if(_request('_etape') == '2'){
			$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
			$fichier_module = $module.'_'.$lang.'.php';
			$fichier_module_po = $module.'_'.$lang.'.po';
			$dir_lang = sous_repertoire(_DIR_VAR, 'cache-tradlang');
			$dest = $dir_lang.$fichier_module;
			$dest_po = $dir_lang.$fichier_module_po;
			if(file_exists($dest) || file_exists($dest_po)){
				if(file_exists($dest)){
					$modifs = $undefined = array();
					$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
					include $dest;
					
					/**
					 * Recuperation des statuts dans le fichier
					 */
					$contenu=file_get_contents($dest);
					$contenu = str_replace("\t",'',$contenu);
					$tab=preg_split("/\r\n|\n\r|;\n|\n\/\/|\(\n|\n\);\n|\'\,\n|\n[\s\t]*(\')|\/\/[\s\t][0-9A-Z]\n[\s\t](\')/", $contenu,'-1',PREG_SPLIT_NO_EMPTY);
					$liste_trad=array();
					reset($tab);
					
					while (list(,$ligne) = each($tab)){
						$ligne = str_replace("\'",'',$ligne);
						if (strlen($ligne)>0) {
							if (preg_match("/\'(.*?)\'[\s\t]*=>[\s\t]*\'(.*?)\'[\s\t]*,{0,1}[\s\t]*(#.*)?/ms",$ligne,$matches)) {
								if (isset($matches[1]) and isset($matches[3]) and strlen(trim($matches[3]))>0 ) {
									list(,$comm)=explode("#",$matches[3]);
									if(in_array(trim($comm),array('NEW','MODIF','RELIRE')))
										$liste_trad[$matches[1]]=trim($comm);
								}
							}
						}
					}
					reset($liste_trad);
					$keys_liste_trad = array_keys($liste_trad);
					
					$str_lang = $GLOBALS[$memtrad];  // on a vu certains fichiers faire des betises et modifier idx_lang
					// verifie si c'est un fichier langue
					if (!is_array($str_lang))
						$erreurs['fichier_langue'] = _T('tradlang:erreur_upload_fichier_php_array',array('fichier'=>$file['name']));
					else{
						$langues_base = sql_allfetsel('id,str,statut','spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang));
						foreach($langues_base as $strings_id => $strings){
							$str_lang[$strings['id']] = preg_replace(',^(<(MODIF|NEW|RELIRE|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]);
							if((strlen($str_lang[$strings['id']]) > 0) && $strings['str'] != $str_lang[$strings['id']]){
								$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$str_lang[$strings['id']],'statut' => (in_array($strings['id'],$keys_liste_trad) ? $liste_trad[$strings['id']] : 'OK'));
							}
							elseif(strlen($str_lang[$strings['id']]) == 0){
								$undefined[] = $strings['id'];
							}
							elseif(in_array($strings['id'],$keys_liste_trad) && ($strings['statut'] != $liste_trad[$strings['id']])){
								$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$str_lang[$strings['id']],'statut' => $liste_trad[$strings['id']]);
							}
							elseif(!in_array($strings['id'],$keys_liste_trad) && $strings['statut'] != 'OK'){
								$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$str_lang[$strings['id']],'statut' => 'OK');
							}
							unset($langues_base[$strings_id]);
						}
					}
				}
				if(file_exists($dest_po)){
					lire_fichier($dest_po,$contenu_po);
					preg_match_all(',(\#\, php-format|\#\, fuzzy\, php-format).*msgstr.*\"\n,Uims', $contenu_po,$matches);
					$array_po = array();
					foreach($matches[0] as $match){
						$statut = "OK";
						preg_match(',\#\| msgid \"(.*)\"\n,Uims',$match,$matches);
						preg_match(',^msgstr \"(.*)(\"\n),Uims',$match,$matches_str);
						$str = $matches_str[1];
						if(preg_match(',\#\, fuzzy\, php-format,',$match,$matches_statut))
							$statut = "MODIF";
						if($str != '')
							$array_po[$matches[1]] = array('str'=>$str,'statut'=>$statut);
					}
					$modifs = array();
					$langues_base = sql_allfetsel('id,str,statut','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($lang));
					foreach($langues_base as $strings_id => $strings){
						$str_lang[$strings['id']] = tradlang_utf8(preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]));
						
						if(isset($array_po[$strings['id']]['str']) && strlen(trim($array_po[$strings['id']]['str'])) > 0){
							if(($strings['str'] != $array_po[$strings['id']]['str']) OR ($strings['statut'] != $array_po[$strings['id']]['statut'])){
								$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$array_po[$strings['id']]['str'],'statut'=>$array_po[$strings['id']]['statut']);
							}
						}
						unset($langues_base[$strings_id]);
					}
				}
			}
			$valeurs['_modifs'] = $modifs;
		}
	}
	if(count($undefined) > 0)
		$valeurs['message_erreur'] = singulier_ou_pluriel(count($undefined),'tradlang:item_non_defini_fichier','tradlang:item_non_defini_fichier_nb');
		
	$valeurs['id_tradlang_module'] = $id_tradlang_module;
	return $valeurs;
}

function formulaires_tradlang_importer_langue_verifier_1_dist($id_tradlang_module,$lang) {
	if(_request('_etape')==1){
		$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		$fichier_php = $module.'_'.$lang.'.php';
		$fichier_po = $module.'_'.$lang.'.po';
		$fichiers_module[] = $fichier_php;
		$fichiers_module[] = $fichier_po;
		$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
		$files = array();
		include_spip('inc/joindre_document');
		if (is_array($post)){
			foreach ($post as $file) {
				if (!($file['error'] == 4)){
					if (!in_array($file['name'],$fichiers_module)){
						$fichier_module = $fichier_php.', '.$fichier_po;
						$erreurs['fichier_langue'] =  _T('tradlang:erreur_upload_fichier_php',array('fichier'=>$file['name'],'fichier_attendu'=>$fichier_module));
					}
					
					if(!$erreurs['fichier_langue']){
						$dir_lang = sous_repertoire (_DIR_VAR, 'cache-tradlang');
						$dest = $dir_lang.$file['name'];
						@move_uploaded_file($file['tmp_name'],$dest);
						if(!file_exists($dest)){
							$erreurs['message_erreur'] = 'Fichier temporaire non créé';
						}
						/**
						 * Gestion du cas des fichiers php
						 */
						if($file['name'] == $fichier_php){
							$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
							include $dest;
							
							/**
							 * Recuperation des statuts dans le fichier
							 */
							$contenu=file_get_contents($dest);
							$contenu = str_replace("\t",'',$contenu);
							$tab=preg_split("/\r\n|\n\r|;\n|\n\/\/|\(\n|\n\);\n|\'\,\n|\n[\s\t]*(\')|\/\/[\s\t][0-9A-Z]\n[\s\t](\')/", $contenu,'-1',PREG_SPLIT_NO_EMPTY);
							$liste_trad=array();
							reset($tab);
							
							while (list(,$ligne) = each($tab)){
								$ligne = str_replace("\'",'',$ligne);
								if (strlen($ligne)>0) {
									if (preg_match("/\'(.*?)\'[\s\t]*=>[\s\t]*\'(.*?)\'[\s\t]*,{0,1}[\s\t]*(#.*)?/ms",$ligne,$matches)) {
										if (isset($matches[1]) and isset($matches[3]) and strlen(trim($matches[3]))>0 ) {
											list(,$comm)=explode("#",$matches[3]);
											if(in_array(trim($comm),array('NEW','MODIF','RELIRE')))
												$liste_trad[$matches[1]]=trim($comm);
										}
									}
								}
							}
							reset($liste_trad);
							$keys_liste_trad = array_keys($liste_trad);
							
							$str_lang = $GLOBALS[$memtrad];
							// verifie si c'est un fichier langue
							if (!is_array($str_lang))
								$erreurs['fichier_langue'] = _T('tradlang:erreur_upload_fichier_php_array',array('fichier'=>$file['name']));
							else{
								$langues_base = sql_allfetsel('id,str,statut','spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang));
								$modifs = array();
								foreach($langues_base as $strings_id => $strings){
									$str_lang[$strings['id']] = tradlang_utf8(preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]));
									if($strings['str'] != $str_lang[$strings['id']]){
										$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$str_lang[$strings['id']]);
										break;
									}
									if(in_array($strings['id'],$keys_liste_trad) && ($strings['statut'] != $liste_trad[$strings['id']])){
										$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$str_lang[$strings['id']]);
										break;
									}
								}
							}
						}
						/**
						 * Gestion du cas des fichiers .po
						 */
						else{
							lire_fichier($dest,$contenu_po);
							preg_match_all(',(\#\, php-format|\#\, fuzzy\, php-format).*msgstr.*\"\n,Uims', $contenu_po,$matches);
							$array_po = array();
							foreach($matches[0] as $match){
								$statut = "OK";
								preg_match(',\#\| msgid \"(.*)\"\n,Uims',$match,$matches);
								preg_match(',^msgstr \"(.*)(\"\n),Uims',$match,$matches_str);
								$str = $matches_str[1];
								if(preg_match(',\#\, fuzzy\, php-format,',$match,$matches_statut))
									$statut = "MODIF";
								if($str != '')
									$array_po[$matches[1]] = array('str'=>$str,'statut'=>$statut);
							}
							$langues_base = sql_allfetsel('id,str,statut','spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang));
							$modifs = array();
							foreach($langues_base as $strings_id => $strings){
								$str_lang[$strings['id']] = tradlang_utf8(preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]));
								if(isset($array_po[$strings['id']]['str']) && strlen(trim($array_po[$strings['id']]['str'])) > 0){
									if(($strings['str'] != $array_po[$strings['id']]['str']) OR ($strings['statut'] != $array_po[$strings['id']]['statut'])){
										$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$array_po[$strings['id']]['str'],'statut'=>$array_po[$strings['id']]['statut']);
										break;
									}
								}
							}
						}
					}
				}
			}
		}
		if (!count($post) && !$erreurs['fichier_langue'])
			$erreurs['fichier_langue'] = _T('medias:erreur_indiquez_un_fichier');
		
		if(!count($modifs) && !$erreurs['fichier_langue'])
			$erreurs['fichier_langue'] = _T('tradlang:erreur_upload_aucune_modif');
	}
	return $erreurs;
}

function formulaires_tradlang_importer_langue_verifier_2_dist($id_tradlang_module,$lang) {
	$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
	$fichier_php = $module.'_'.$lang.'.php';
	$fichier_po = $module.'_'.$lang.'.po';
	$dir_lang = sous_repertoire (_DIR_VAR, 'cache-tradlang');
	$dest = $dir_lang.$fichier_php;
	$destpo = $dir_lang.$fichier_po;
	$modifs = array();
	if(file_exists($dest)){
		$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
		include $dest;
		$str_lang = $GLOBALS[$memtrad];  // on a vu certains fichiers faire des betises et modifier idx_lang

		$langues_base = sql_allfetsel('*','spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang));
		$modifs = array();
		foreach($langues_base as $strings_id => $strings){
			if(_request($strings['id']) == 'oui')
				$modifs[] = $strings['id'];
			unset($langues_base[$strings_id]);
		}
	}else if(file_exists($destpo)){
		$langues_base = sql_allfetsel('id','spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang));
		$modifs = array();
		foreach($langues_base as $strings_id => $strings){
			if(_request($strings['id']) == 'oui')
				$modifs[] = $strings['id'];
			unset($langues_base[$strings_id]);
		}
	}
	else
		$erreurs['message_erreur'] = "Le fichier temporaire $dest n'a pas été créé";
	if(!count($modifs))
		$erreurs['message_erreur'] = _T('tradlang:erreur_upload_choisir_une');
	return $erreurs;
}

function formulaires_tradlang_importer_langue_traiter_dist($id_tradlang_module,$lang) {
	include_spip('action/editer_tradlang');
	
	$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
	$fichier_php = $module.'_'.$lang.'.php';
	$fichier_po = $module.'_'.$lang.'.po';
	$dir_lang = sous_repertoire (_DIR_VAR, 'cache-tradlang');
	$count=0;
	if(file_exists($dest = $dir_lang.$fichier_php)){
		$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
		include $dest;
		
		/**
		 * Recuperation des statuts dans le fichier
		 */
		$contenu=file_get_contents($dest);
		$contenu = str_replace("\t",'',$contenu);
		$tab=preg_split("/\r\n|\n\r|;\n|\n\/\/|\(\n|\n\);\n|\'\,\n|\n[\s\t]*(\')|\/\/[\s\t][0-9A-Z]\n[\s\t](\')/", $contenu,'-1',PREG_SPLIT_NO_EMPTY);
		$liste_trad=array();
		reset($tab);
		
		while (list(,$ligne) = each($tab)){
			$ligne = str_replace("\'",'',$ligne);
			if (strlen($ligne)>0) {
				if (preg_match("/\'(.*?)\'[\s\t]*=>[\s\t]*\'(.*?)\'[\s\t]*,{0,1}[\s\t]*(#.*)?/ms",$ligne,$matches)) {
					if (isset($matches[1]) and isset($matches[3]) and strlen(trim($matches[3]))>0 ) {
						list(,$comm)=explode("#",$matches[3]);
						if(in_array(trim($comm),array('NEW','MODIF','RELIRE')))
							$liste_trad[$matches[1]]=trim($comm);
					}
				}
			}
		}
		reset($liste_trad);
		$keys_liste_trad = array_keys($liste_trad);
		
		$str_lang = $GLOBALS[$memtrad];  // on a vu certains fichiers faire des betises et modifier idx_lang

		if (is_null($str_lang)) {
			spip_log("Erreur, fichier $module mal forme",'tradlang');
		}
		
		$langues_base = sql_allfetsel('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($lang));
		$modifs = array();
		foreach($langues_base as $strings_id => $strings){
			if(_request($strings['id']) == 'oui'){
				$set_new = tradlang_utf8(preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]));
				$set = array('str'=>$set_new,'statut'=> (in_array($strings['id'],$keys_liste_trad) ? $liste_trad[$strings['id']] : 'OK'));
				tradlang_set($strings['id_tradlang'],$set);
				$count++;
				unset($langues_base[$strings_id]);
			}
		}
		spip_unlink($dest);
	}else if(file_exists($dest = $dir_lang.$fichier_po)){
		lire_fichier($dest,$contenu_po);
		preg_match_all(',(\#\, php-format|\#\, fuzzy\, php-format).*msgstr.*\"\n,Uims', $contenu_po,$matches);
		$array_po = array();
		foreach($matches[0] as $match){
			$statut = "OK";
			preg_match(',\#\| msgid \"(.*)\"\n,Uims',$match,$matches);
			preg_match(',^msgstr \"(.*)(\"\n),Uims',$match,$matches_str);
			$str = $matches_str[1];
			if(preg_match(',\#\, fuzzy\, php-format,',$match,$matches_statut)){
				$statut = "MODIF";
			}
			if($str != '')
				$array_po[$matches[1]] = array('str'=>$str,'statut'=>$statut);
		}

		$langues_base = sql_allfetsel('id_tradlang,id,str,statut','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($lang));
		$modifs_po = array();
		foreach($langues_base as $strings_id => $strings){
			if(_request($strings['id']) == 'oui'){
				$set=$instit=null;
				if(isset($array_po[$strings['id']]['str']) && strlen(trim($array_po[$strings['id']]['str'])) > 0){
					$set = array('str'=>$array_po[$strings['id']]['str']);
					tradlang_set($strings['id_tradlang'],$set);
					$instit = array('statut'=>$array_po[$strings['id']]['statut']);
					instituer_tradlang($strings['id_tradlang'],$instit);
					$count++;
					unset($langues_base[$strings_id]);
				}
			}
		}
		spip_unlink($dest);
	}
	$res['editable'] = false;
	if($count > 1)
		$res['message_ok'] = _T('tradlang:message_upload_nb_modifies',array('nb'=>$count));
	else
		$res['message_ok'] = _T('tradlang:message_upload_nb_modifies',array('nb'=>$count));
	return $res;
}
?>