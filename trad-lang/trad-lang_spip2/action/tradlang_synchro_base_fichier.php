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
	if(is_array($config) && ($config['sauvegarde_locale'] != 'on')){
		return false;
	}
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\w+)$,", $arg, $r)) {
		spip_log("action_tradlang_synchro_base_fichier $arg pas compris");
	}
	else {
		include_spip('tradlang_fonctions');
		$dir_lang = tradlang_dir_lang();
		if(!$dir_lang)
			return false;
			
		$nom_mod = $r[1];
		$module = sql_fetsel('*','spip_tradlang_modules','nom_mod='.sql_quote($nom_mod));
		if(is_array($module)){
			$langues = sql_select("DISTINCT lang","spip_tradlang","module='$nom_mod'");
			while($langue=sql_fetch($langues)){
				$lg = $langue["lang"];
				$fichiers[$lg] = $nom_mod."_".$lg.".php";
				foreach($fichiers as $key => $fichier){
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
					}
					else{
						$ts_base = sql_getfetsel('ts','spip_tradlang','module='.sql_quote($nom_mod).' AND lang='.sql_quote($lg),'','ts DESC','0,1');
						
						include($chemin_fichier);
						$chs = $GLOBALS[$GLOBALS['idx_lang']];
						$ts_synchro = $chs["zz_timestamp_nepastraduire"];
						unset($GLOBALS[$GLOBALS['idx_lang']]);
						
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