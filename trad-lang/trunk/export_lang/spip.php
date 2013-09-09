<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Fichier des fonctions spécifiques du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'export d'une langue d'un module SPIP en php
 * 
 * @param string $module
 * 		Le module à exporter
 * @param string $langue
 * 		La langue à exporter
 * @param string $dir_lang
 * 		Le répertoire où stocker les fichiers de langue
 * @param bool $tout
 * 		Si true, exporte toutes les chaines même non traduites
 * @return string $fichier
 * 		Le fichier final
 */
function export_lang_spip_dist($module,$langue,$dir_lang,$tout=false){
	/**
	 * Le fichier final
	 * local/cache-lang/module_lang.php
	 */
	$fichier = $dir_lang."/".$module."_".$langue.".php";

	$tab = "\t";
	$where = "module=".sql_quote($module)." AND lang=".sql_quote($langue);
	if(!$tout)
		$where .= " AND statut IN ('OK','MODIF')";
	$res=sql_select("id,str,comm,statut","spip_tradlangs",$where,"id");
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
		$str = unicode_to_utf_8(
			html_entity_decode(
				preg_replace('/&([lg]t;)/S', '&amp;\1', $str),
				ENT_NOQUOTES, 'utf-8')
		);
		$newmd5 = md5($str);
		if ($oldmd5 !== $newmd5) sql_updateq("spip_tradlangs",array('md5'=>$newmd5), "md5=".sql_quote($oldmd5)." AND module=".sql_quote($module));

		$x[]="$tab".var_export($row['id'],1).' => ' .var_export($str,1).','.$row['comm'] ;
	}

	/**
	 * historiquement les fichiers de lang de spip_loader ne peuvent pas etre securises
	 */
	$secure = ($module == 'tradloader')
		? ''
		: "if (!defined('_ECRIRE_INC_VERSION')) return;\n\n";

	$fd = fopen($fichier, 'w');

	/**
	 * On supprime la virgule du dernier item
	 */
	$x[count($x)-1] = preg_replace('/,([^,]*)$/', '\1', $x[count($x)-1]);

	$contenu = join("\n",$x);
	
	/**
	 * On écrit le fichier
	 */
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
	@chmod($fichier, 0666);
	
	return $fichier;
}
?>