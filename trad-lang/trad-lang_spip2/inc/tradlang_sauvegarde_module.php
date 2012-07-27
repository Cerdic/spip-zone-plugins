<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Sauvegarde d'une langue d'un module dans son fichier
 * 
 * @param string $module le nom d'un module d'un module (par défaut local/cache-lang/$module)
 * @param string $langue la langue cible
 * @param string $dir_lang le répertoire de stockage des fichiers de langue
 * @return string $fic_exp le chemin complet du fichier de langue
 */
function inc_tradlang_sauvegarde_module_dist($module,$langue,$dir_lang=false){
	include_spip('inc/flock');
	include_spip('inc/filtres'); # Pour url_absolue

	if(!$dir_lang){
		$dir_lang = _DIR_VAR.'cache-lang/'.$module;
		if(!is_dir(_DIR_VAR.'cache-lang/')){
			sous_repertoire(_DIR_VAR,'cache-lang');
		}
	}
	if(!is_dir($dir_lang)){
		sous_repertoire($dir_lang);
		if(!is_dir($dir_lang)){
			return false;	
		}
	}
	
	$id_tradlang_module = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','module='.sql_quote($module));
	
	/**
	 * L'URL du site de traduction
	 */
	$url_trad = parametre_url(url_absolue(generer_url_entite($id_tradlang_module,'tradlang_module')),'lang_cible',$langue);
	
	/**
	 * Le fichier final
	 * local/cache-lang/module_lang.php
	 */
	$fic_exp = $dir_lang."/".$module."_".$langue.".php";

	$tab = "\t";

	$res=sql_select("id,str,comm,statut","spip_tradlangs","module=".sql_quote($module)." AND lang=".sql_quote($langue),"id");
	$x=array();
	$prev="";
	$tous = $lorigine; // on part de l'origine comme ca on a tout meme si c'est pas dans la base de donnees (import de salvatore/lecteur.php)
	while ($row=sql_fetch($res)) {
		$tous[$row['id']] = $row;
	}
	ksort($tous);
	foreach ($tous as $row) {
		if ($prev!=strtoupper($row['id'][0])) $x[] = "\n$tab// ".strtoupper($row['id'][0]);
		$prev=strtoupper($row['id'][0]);
		if (strlen($row['statut']) && ($row['statut'] != 'OK'))
			$row['comm'] .= ' '.$row['statut'];
		if (trim($row['comm'])) $row['comm']=" # ".trim($row['comm']); // on rajoute les commentaires ?

		$str = $row['str'];

		$oldmd5 = md5($str);
		//$str = unicode_to_utf_8(html_entity_decode($str, ENT_NOQUOTES, 'utf-8'));
		$str = unicode_to_utf_8(
			html_entity_decode(
				preg_replace('/&([lg]t;)/S', '&amp;\1', $str),
				ENT_NOQUOTES, 'utf-8')
		);
		$newmd5 = md5($str);
		if ($oldmd5 !== $newmd5) sql_updateq("spip_tradlangs",array('md5'=>$newmd5), "md5=".sql_quote($oldmd5)." AND module=".sql_quote($module));

		$x[]="$tab".var_export($row['id'],1).' => ' .var_export($str,1).','.$row['comm'] ;
	}

	// historiquement les fichiers de lang de spip_loader ne peuvent pas etre securises
	$secure = ($module == 'tradloader')
		? ''
		: "if (!defined('_ECRIRE_INC_VERSION')) return;\n\n";

	$fd = fopen($fic_exp, 'w');

	# supprimer la virgule du dernier item
	$x[count($x)-1] = preg_replace('/,([^,]*)$/', '\1', $x[count($x)-1]);

	$contenu = join("\n",$x);
	
	# ecrire le fichier
	fwrite($fd,
	'<'.'?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de '.$url_trad.'
// ** ne pas modifier le fichier **
'
."\n".$secure.'$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
'
. str_replace("\r\n", "\n", $contenu)
.'
);

?'.'>'
	);
	fclose($fd);
	@chmod($fic_exp, 0666);
  
	return $fic_exp;
}
?>