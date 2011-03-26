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
	$config = @unserialize($GLOBALS['meta']['tradlang']);
	if(is_array($config) && ($config['sauvegarde_locale'] == 'on')){
		return false;
	}
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\w+)$,", $arg, $r)) {
		spip_log("action_tradlang_synchro_base_fichier $arg pas compris");
	}
	else {
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
		spip_log($dir_lang,'test');
		$nom_mod = $r[1];
		$module = sql_fetsel('*','spip_tradlang_modules','nom_mod='.sql_quote($nom_mod));
		if(is_array($module)){
			$langues = sql_select("DISTINCT lang","spip_tradlang","module='$nom_mod'");
			while($langue=sql_fetch($langues)){
				$lg = $langue["lang"];
				$fichiers[$lg] = $nom_mod."_".$lg.".php";
				foreach($fichiers as $key => $fichier){
					spip_log("synchro $key => $fichier",'tradlang');
					$chemin_fichier = $dir_lang.'/'.$fichier;
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
						$sauvegarder_module($module,$lg,$dir_lang);
						spip_log("On regénère le fichier à partir de la base");
					}
					else{
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
							$sauvegarder_module($module,$lg,$dir_lang);
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