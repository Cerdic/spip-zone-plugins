<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Fichier d'export d'un module de langue en .csv
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'export d'une langue d'un module en .csv
 * 
 * @param string $module 
 * 		Le module à exporter (le champ "module" dans la base)
 * @param string $langue 
 * 		La langue à exporter
 * @param string $dir_lang 
 * 		Le répertoire où stocker les fichiers de langue
 * @return string $fichier 
 * 		Le fichier final
 */
function export_lang_csv_dist($module,$langue,$dir_lang){
	$chaines_csv=$tous=array();
	
	/**
	 * Le fichier final
	 * local/cache-lang/module_lang.csv
	 */
	$fichier = $dir_lang."/".$module."_".$langue.".csv";

	/**
	 * Les informations du module
	 */	
	$info_module = sql_fetsel('id_tradlang_module,lang_mere,nom_mod','spip_tradlang_modules','module='.sql_quote($module));

	/**
	 * Les chaines
	 */
	$res=sql_allfetsel("id,str,statut,comm","spip_tradlangs","id_tradlang_module=".intval($info_module['id_tradlang_module'])." AND lang=".sql_quote($langue)." AND statut != 'attic'","id");
	foreach($res as $row){
		$tous[$row['id']] = $row;
	}
	ksort($tous);
	
	foreach ($tous as $id => $row) {
		$str = $row['str'];

		$oldmd5 = md5($str);
		$str = str_replace("\r\n", "\n", unicode_to_utf_8(
			html_entity_decode(
				preg_replace('/&([lg]t;)/S', '&amp;\1', $str),
				ENT_NOQUOTES, 'utf-8')
		));
		$newmd5 = md5($str);

		if ($oldmd5 !== $newmd5) sql_updateq("spip_tradlangs",array('md5'=>$newmd5), "md5=".sql_quote($oldmd5)." AND id_tradlang_module=".intval($info_module['id_tradlang_module']));
		$str_original = sql_getfetsel('str','spip_tradlangs','id ='.sql_quote($id).' AND id_tradlang_module='.intval($info_module['id_tradlang_module']).' AND lang='.sql_quote($info_module['lang_mere']));

		$chaine = array('id' => $row['id'],'str_orig'=>$str_original,'str'=>$str,'statut'=>$row['statut'],'comm'=>$row['comm']);
		$chaines_csv[] = $chaine;
		unset($tous[$id]);
	}

	$fp = fopen($fichier, 'w');
	
	foreach ($chaines_csv as $fields) {
		fputcsv($fp, $fields);
	}

	fclose($fp);

	return $fichier;
}
?>