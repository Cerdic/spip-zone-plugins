<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */


/**
 * Insertion dans le pipeline declarer_tables_objets_surnoms (base/connect_sql.php)
 * La table spip_tradlang est une table ancienne, et n'a pas de S final ...
 * Pour éviter les problèmes liés à cela, on surnomme les objets
 * 
 * @param array $flux La liste des surnoms
 * @return array Le $flux complété
 */
function tradlang_declarer_tables_objets_surnoms($flux){
	$flux['tradlang'] = 'tradlang';
	return $flux;
}

/**
 * Insertion dans le pipeline revisions_liste_objets du plugin revisions (2.3)
 * Definir la liste des tables possibles
 * @param object $array
 * @return
 */
function tradlang_revisions_liste_objets($array){
	$array['tradlang'] = 'tradlang:chaines_langue';
	return $array;
}

/**
 * Insertion dans le pipeline post_edition
 * Si configuré comme tel on regénère les fichiers à chaque modification de chaine de langue
 * 
 * @param object $array
 * @return
 */
function tradlang_post_edition($flux){
	if($flux['args']['table'] == "spip_tradlang"){
		spip_log('post_edition');
		$config = @unserialize($GLOBALS['meta']['tradlang']);
		if (!is_array($config))
			return $flux;
		if(($config['sauvegarde_locale'] == 'on') && ($config['sauvegarde_post_edition'] == 'on')){
			include_spip('tradlang_fonctions');
			spip_log('on tente de réecrire le fichier?','test');
			if($dir_lang = tradlang_dir_lang()){
				$infos = sql_fetsel('*',$flux['args']['table'],'id_tradlang='.intval($flux['args']['id_objet']));
				$module = sql_fetsel('*','spip_tradlang_modules','module='.sql_quote($infos['module']));
				spip_log($infos,'test');
				$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
				$sauvegarder_module($module,$infos['lang'],$dir_lang);
				spip_log("On regénère le fichier à partir de la base",'test');
			}
		}
	}
	return $flux;
}
?>