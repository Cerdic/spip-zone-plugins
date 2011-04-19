<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_tradlang_ajouter_code_langue($module,$lang){
	/**
	 * Insertion des chaines de la langue mère avec le statut NEW
	 */
	$chaines_mere = sql_select('*','spip_tradlang',"module=".sql_quote($module['module'])." AND lang=".sql_quote($module['lang_mere']));
	while($chaine = sql_fetch($chaines_mere)){
		spip_log($chaine);
		$res = sql_insertq('spip_tradlang',array(
				'id' => $chaine['id'],
				'module' => $module["nom_mod"],
				'str' => $chaine['str'],
				'lang' => $lang,
				'status' => 'NEW',
				'md5' => md5($chaine['str']),
				'orig' => 0
			));
	}
	
	/**
	 * On invalide
	 */
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	
	/**
	 * On génère le fichier correspondant
	 */
	$config = @unserialize($GLOBALS['meta']['tradlang']);
	if (!is_array($config))
		return;
	if(($config['sauvegarde_locale'] == 'on') && ($config['sauvegarde_post_edition'] == 'on')){
		include_spip('tradlang_fonctions');
		if($dir_lang = tradlang_dir_lang()){
			$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
			$sauvegarder_module($module,$lang,$dir_lang);
		}
	}
}
?>