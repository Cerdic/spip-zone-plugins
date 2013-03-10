<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_tradlang_importer_langue_charger_dist($id_tradlang_module,$lang,$lang_orig) {
	$valeurs['_etapes'] = 2;
	$valeurs['lang_orig'] = $lang_orig;
	if(!$nom_mod = sql_getfetsel('nom_mod','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module))){
		$valeurs['message_erreur'] = _T('tradlang:erreur_import_module_inexistant',array('id'=>$id_tradlang_module));
		$valeurs['editable'] = false;
	}else{
		if(_request('_etape') == '2'){
			$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
			$fichier_module = $module.'_'.$lang.'.php';
			$fichier_module_po = $module.'_'.$lang.'.po';
			$dir_lang = sous_repertoire (_DIR_VAR, 'cache-tradlang');
			$dest = $dir_lang.$fichier_module;
			$dest_po = $dir_lang.$fichier_module_po;
			if(file_exists($dest) || file_exists($dest_po)){
				if(file_exists($dest)){
					$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
					include $dest;
					$str_lang = $GLOBALS[$memtrad];  // on a vu certains fichiers faire des betises et modifier idx_lang
		
					if (is_null($str_lang)) {
						spip_log("Erreur, fichier $module mal forme",'test');
					}
					// verifie si c'est un fichier langue
					if (!is_array($str_lang))
						$erreurs['fichier_langue'] = _T('tradlang:erreur_upload_fichier_php_array',array('fichier'=>$file['name']));
					else{
						$langues_base = sql_select('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($lang));
						$modifs = array();
						while($strings = sql_fetch($langues_base)){
							$str_lang[$strings['id']] = preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]);
							if($strings['str'] != $str_lang[$strings['id']]){
								$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$str_lang[$strings['id']]);
							}
						}
					}
					//spip_unlink($dest);
				}
			}
			$valeurs['_modifs'] = $modifs;
		}
	}
	return $valeurs;
}

function formulaires_tradlang_importer_langue_verifier_1_dist($id_tradlang_module,$lang) {
	if(_request('_etape')==1){
		$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		$fichiers_module[] = $module.'_'.$lang.'.php';
		$fichiers_module[] = $module.'_'.$lang.'.po';
		$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
		$files = array();
		include_spip('inc/joindre_document');
		if (is_array($post)){
			foreach ($post as $file) {
			  	//UPLOAD_ERR_NO_FILE
				if (!($file['error'] == 4)){
					if (!in_array($file['name'],$fichiers_module)){
						$erreurs['fichier_langue'] =  _T('tradlang:erreur_upload_fichier_php',array('fichier'=>$file['name'],'fichier_attendu'=>$fichier_module));  
					}
					
					if(!$erreurs['fichier_langue']){
						$dir_lang = sous_repertoire (_DIR_VAR, 'cache-tradlang');
						$dest = $dir_lang.$file['name'];
						@move_uploaded_file($file['tmp_name'],$dest);
						if(!file_exists($dest)){
							$erreurs['message_erreur'] = 'Fichier temporaire non créé';
						}
						$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
						include $dest;
						$str_lang = $GLOBALS[$memtrad];
	
						if (is_null($str_lang)) {
							spip_log("Erreur, fichier $module mal forme",'test');
						}
						// verifie si c'est un fichier langue
						if (!is_array($str_lang))
							$erreurs['fichier_langue'] = _T('tradlang:erreur_upload_fichier_php_array',array('fichier'=>$file['name']));
						else{
							$langues_base = sql_select('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($lang));
							$modifs = array();
							while($strings = sql_fetch($langues_base)){
								$str_lang[$strings['id']] = tradlang_utf8(preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]));
								if($strings['str'] != $str_lang[$strings['id']]){
									$modifs[$strings['id']] = array('orig'=>$strings['str'],'new'=>$str_lang[$strings['id']]);
									break;
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
	$fichier_module = $module.'_'.$lang.'.php';
	$dir_lang = sous_repertoire (_DIR_VAR, 'cache-tradlang');
	$dest = $dir_lang.$fichier_module;
	$modifs = array();
	if(file_exists($dest)){
		$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
		include $dest;
		$str_lang = $GLOBALS[$memtrad];  // on a vu certains fichiers faire des betises et modifier idx_lang

		if (is_null($str_lang)) {
			spip_log("Erreur, fichier $module mal forme",'test');
		}
		
		$langues_base = sql_select('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($lang));
		$modifs = array();
		while($strings = sql_fetch($langues_base)){
			if(_request($strings['id']) == 'oui'){
				$modifs[] = $strings['id'];
			}
		}
	}else{
		$erreurs['message_erreur'] = "Le fichier temporaire $dest n'a pas été créé";
	}
	if(!count($modifs)){
		$erreurs['message_erreur'] = _T('tradlang:erreur_upload_choisir_une');
	}
	return $erreurs;
}

function formulaires_tradlang_importer_langue_traiter_dist($id_tradlang_module,$lang) {
	include_spip('action/editer_tradlang');
	
	$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
	$fichier_module = $module.'_'.$lang.'.php';
	$dir_lang = sous_repertoire (_DIR_VAR, 'cache-tradlang');
	$dest = $dir_lang.$fichier_module;
	$count=0;
	if(file_exists($dest)){
		$memtrad = $GLOBALS['idx_lang'] = 'i18n_'.crc32($module).'_tmp';
		include $dest;
		$str_lang = $GLOBALS[$memtrad];  // on a vu certains fichiers faire des betises et modifier idx_lang

		if (is_null($str_lang)) {
			spip_log("Erreur, fichier $module mal forme",'tradlang');
		}
		
		$langues_base = sql_select('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($lang));
		$modifs = array();
		while($strings = sql_fetch($langues_base)){
			if(_request($strings['id']) == 'oui'){
				$set=null;
				spip_log('On en envoie 1','test');
				$set_new = tradlang_utf8(preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $str_lang[$strings['id']]));
				$set = array('str'=>$set_new,'statut'=>'OK');
				tradlang_set($strings['id_tradlang'],$set);
				$count++;
			}
		}
	}
	$res['editable'] = false;
	$res['message_ok'] = _T('tradlang:message_upload_nb_modifies',array('nb'=>$count));
	return $res;
}
?>