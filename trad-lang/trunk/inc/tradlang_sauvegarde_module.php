<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Sauvegarde d'une langue d'un module dans son fichier
 * 
 * @param string $module 
 * 		Le nom d'un module d'un module (par défaut local/cache-lang/$module)
 * @param string $langue 
 * 		La langue cible à exporter
 * @param string $dir_lang 
 * 		Le répertoire de stockage des fichiers de langue
 * @param bool $tout 
 * 		Si true, exporte toutes les chaines même non traduites (utilisé uniquement pour les modules SPIP)
 * @return string $fichier
 * 		Le chemin complet du fichier de langue
 */
function inc_tradlang_sauvegarde_module_dist($module,$langue,$dir_lang=false,$type=false,$tout=true){
	include_spip('inc/flock');
	include_spip('inc/filtres'); # Pour url_absolue
	if(!$dir_lang){
		$dir_lang = _DIR_VAR.'cache-lang/'.($tout ? '':'partiels/').$module;
		if(!is_dir(_DIR_VAR.'cache-lang/'))
			sous_repertoire(_DIR_VAR,'cache-lang');
		if(!$tout && !is_dir(_DIR_VAR.'cache-lang/partiels/'))
			sous_repertoire(_DIR_VAR.'cache-lang/','partiels');
	}
	if(!is_dir($dir_lang)){
		sous_repertoire($dir_lang);
		if(!is_dir($dir_lang))
			return false;
	}
	$tradlang_module = sql_fetsel('id_tradlang_module,type_export','spip_tradlang_modules','module='.sql_quote($module));

	/**
	 * L'URL du site de traduction
	 */
	$url_trad = parametre_url(url_absolue(generer_url_entite($tradlang_module['id_tradlang_module'],'tradlang_module')),'lang_cible',$langue);

	if(!$type)
		$type = $tradlang_module['type_export'];

	if(!$f = charger_fonction($type,'export_lang'))
		$f = charger_fonction('spip','export_lang');

	$fichier = $f($module,$langue,$dir_lang,$tout);

	return $fichier;
}
?>