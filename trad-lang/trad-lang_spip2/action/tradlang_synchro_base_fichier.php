<?php
/**
 * Action permettant de synchroniser la base avec les fichiers de langue
 * 
 * Ne devrait jamais être utilisé à moins que l'import se soit mal passé
 * 
 * @return 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_tradlang_synchro_base_fichier_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\w+)$,", $arg, $r)) {
		spip_log("action_tradlang_synchro_base_fichier $arg pas compris");
	}
	else {
		$nom_mod = $r[1];
		$module = sql_fetsel('*','spip_tradlang_modules','nom_mod='.sql_quote($nom_mod));
		if(is_array($module)){
			$langues = sql_select("DISTINCT lang","spip_tradlang","module='$nom_mod'");
			while($langue=sql_fetch($langues)){
				$lg = $langue["lang"];
				$fichiers[$lg] = $nom_mod."_".$lg.".php";
				foreach($fichiers as $key => $fichier){
					spip_log("synchro $key => $fichier",'tradlang');
					$chemin_fichier = _DIR_RACINE.$module['dir_lang'].'/'.$fichier;
					/**
					 * On récupère la date de modification du fichier sur le disque
					 */
					if(!file_exists($chemin_fichier)){
						/**
						 * Cas simple :
						 * - Le fichier est un fichier généré par tradlang car $ts_synchro
						 * - La base est plus récente que la dernière écriture par tradlang
						 * - La dernière synchro de tradlang correspond à la date de modif du fichier
						 */
						$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
						$sauvegarder_module($module,$lg);
						spip_log("On regénère le fichier à partir de la base");
					}
					/**
					 * On ne vérifie les modifications importantes que sur le fichier de lang mère
					 * (celui sur lequel travaille le développeur)
					 */
					else if($key == $module['lang_mere']){
						spip_log("$key est la lang_mere",'tradlang');
						include($chemin_fichier);
						$chs = $GLOBALS[$GLOBALS['idx_lang']];
						$ts_synchro = $chs["zz_timestamp_nepastraduire"];
						spip_log("TIMESTAMP synchro : $ts_synchro",'tradlang');
						// Comparer le contenu de la base avec le contenu du fichier
						$contenu_base = sql_select('id,str','spip_tradlang','module='.sql_quote($nom_mod).' AND lang='.sql_quote($lg),'','id ASC');
						while($contenu_element = sql_fetch($contenu_base)){
							$array_contenu[$contenu_element['id']] = $contenu_element['str'];  	
						}
						unset($array_contenu['zz_timestamp_nepastraduire']);
						unset($chs['zz_timestamp_nepastraduire']);
						spip_log(count($chs),'tradlang');
						spip_log(count($array_contenu),'tradlang');
						if($diff = array_diff_assoc($chs,$array_contenu)){
							spip_log("Count diff",'tradlang');
							spip_log(count($diff),'tradlang');
							spip_log("Array diff",'tradlang');
							spip_log($diff,'tradlang');
							$import = charger_fonction('tradlang_importer_module','inc');
							$import($module);
							spip_log("Mise à jour de la base avec le contenu du module $nom_mod",'tradlang');
						}
						else{
							$ts_base = sql_getfetsel('ts','spip_tradlang','module='.sql_quote($nom_mod).' AND lang='.sql_quote($lg),'','ts DESC','0,1');
							spip_log("TIMESTAMP base : $ts_base",'tradlang');
							
							/**
							 * Cas où la base est plus récente que le ts incorporé dans le fichier
							 **/
							if($ts_synchro < $ts_base){
								/**
								 * Cas simple :
								 * - Le fichier est un fichier généré par tradlang car $ts_synchro
								 * - La base est plus récente que la dernière écriture par tradlang
								 * - La dernière synchro de tradlang correspond à la date de modif du fichier
								 */
								$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
								$sauvegarder_module($module,$lg);
								spip_log("On regénère le fichier à partir de la base $lg",'tradlang');
							}
						}
						unset($GLOBALS[$GLOBALS['idx_lang']]);
						unset($chs);
					}else{
						$ts_base = sql_getfetsel('ts','spip_tradlang','module='.sql_quote($nom_mod).' AND lang='.sql_quote($lg),'','ts DESC','0,1');
						spip_log("else TIMESTAMP base : $ts_base",'tradlang');
						
						include($chemin_fichier);
						$chs = $GLOBALS[$GLOBALS['idx_lang']];
						$ts_synchro = $chs["zz_timestamp_nepastraduire"];
						unset($GLOBALS[$GLOBALS['idx_lang']]);
						spip_log("TIMESTAMP synchro : $ts_synchro",'tradlang');
						
						/**
						 * Cas où la base est plus récente que le ts incorporé dans le fichier
						 **/
						if(!$ts_synchro OR ($ts_synchro < $ts_base)){
							/**
							 * Cas simple :
							 * - Le fichier est un fichier généré par tradlang car $ts_synchro
							 * - La base est plus récente que la dernière écriture par tradlang
							 * - La dernière synchro de tradlang correspond à la date de modif du fichier
							 */
							$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
							$sauvegarder_module($module,$lg);
							spip_log("On regénère le fichier à partir de la base",'tradlang');
						}
					}
				}
				
			}
			/**
			 * Invalidation du cache
			 */
			include_spip('inc/invalideur');
			suivre_invalideur("id='id_tradlang/$id_tradlang'");
		}else{
			spip_log("action_tradlang_synchro_base_fichier : Module $nom_mod inexistant");
		}
	}
	
	return;
}
?>