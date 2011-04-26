<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */
 
 
/**
 * Génère les appels js ou les css selon $type, correspondants à l'extension du fichier édité
 *
 * @param string $extension
 * @param string $type
 * @return string
 */
function skeleditor_dir($extension, $type) {
	if (!$extension)
		return "";

	switch($extension){
		case 'sh':
		case 'txt':
		case 'nfo':
		case 'log':
		case 'csv':
			$mode = null;
			break;
		case 'as':
		case 'js':
			$mode = array("javascript");
			// autoMatchParens: true
			break;
		case 'css':
			$mode = array("css");
			break;
		case 'xml':
		case 'svg':
		case 'rdf':
			$mode = array("xml");
			#continuousScanning: 500,
			break;
/* 		case 'sql':
			$parsers = array("../contrib/sql/js/parsesql.js");
			$css = array("css/sqlcolors.css");
			#textWrapping: false,
			break;
		case 'py':
			$parsers = array("../contrib/python/js/parsepython.js");
			$css = array("css/pythoncolors.css");
      #  lineNumbers: true,
      #  textWrapping: false,
      #  indentUnit: 4,
      #  parserConfig: {'pythonVersion': 2, 'strictErrors': true}
			break; */

		case 'php':
		case 'html':
		case 'htm':
		default:
			$mode = array("xml", "css", "javascript",
                     "clike","php");
			break;
	}
	if(!$type)
	return false;

	$dir = _DIR_PLUGIN_SKELEDITOR ."spip_210/";
	if ($type == "css") {
	$files .= "
	<link rel='stylesheet' href='".$dir."codemirror/lib/codemirror.css' type='text/css' />
	<link rel='stylesheet' href='".$dir."css/skeleditor.css' type='text/css' />";	
	}
	if ($type =="js")
	$files .= "<script src='".$dir."codemirror/lib/codemirror.js' type='text/javascript'></script>";
	foreach($mode as $cle=>$valeur) {
		
		$test = $dir."codemirror/mode/".$valeur."/".$valeur.".".$type;
 			if (find_in_path($test)) {
				if ($type == "css")
				$files .= "<link rel='stylesheet' href='".$test."' type='text/css' />";
				if ($type =="js")
				$files .= "<script src='".$test."' type='text/javascript'></script>";
 			}
	}
	
	return $files;
}

/**
 * Tester avec _request si on est dans l'edition de skeleditor et si oui, retourne l'extension du nom du fichier
 *
 * @return string
 */ 
function test_skeleditor_edition() {
$exec = _request('exec');
$filename = _request('f');
	if ($exec == 'skeleditor'
	AND $filename
	AND $infos = pathinfo($filename)
	AND $extension = $infos['extension']) 
	return $extension;
	else
	return false;
	
} 

/**
 * Produit les css dans le header_prive si nécessaire
 *
 * @param string $flux
 * @return string
 */ 
function skeleditor_insert_head_css($flux){
	$extension = test_skeleditor_edition();
		if($extension) {
		static $done = false;
		if (!$done) {
			$done = true;
			$type = "css";
			$css = skeleditor_dir($extension, $type);
			$flux .= $css; 
		}
	}
return $flux;
}
 
/**
 * Produit les js dans le header_prive si nécessaire
 *
 * @param string $flux
 * @return string
 */ 
function skeleditor_insert_head($flux){

	$extension = test_skeleditor_edition();
		if($extension) {
		$type = "js";
		$script = skeleditor_dir($extension, $type);
		$flux = skeleditor_insert_head_css($flux); // au cas ou il n'est pas implemente */
		$flux .= $script;
		}
	return $flux;
}

// pas de compresseur si var_inclure
if (_request('var_mode')=='inclure')
	define('_INTERDIRE_COMPACTE_HEAD',true);

function skeleditor_extraire_css($texte){
	$url_base = url_de_base();
	$url_page = substr(generer_url_public('A'), 0, -1);
	$dir = preg_quote($url_page,',').'|'.preg_quote(preg_replace(",^$url_base,",_DIR_RACINE,$url_page),',');

	$css = array();
	// trouver toutes les css pour les afficher dans le bouton
	// repris du compresseur
	foreach (extraire_balises($texte, 'link') as $s) {
		if (extraire_attribut($s, 'rel') === 'stylesheet'
		AND (!($type = extraire_attribut($s, 'type'))
			OR $type == 'text/css')
		AND !strlen(strip_tags($s))
		AND $src = preg_replace(",^$url_base,",_DIR_RACINE,extraire_attribut($s, 'href'))
		AND (
			// regarder si c'est du format spip.php?page=xxx
			preg_match(',^('.$dir.')(.*)$,', $src, $r)
			OR (
				// ou si c'est un fichier
				// enlever un timestamp eventuel derriere un nom de fichier statique
				$src2 = preg_replace(",[.]css[?].+$,",'.css',$src)
				// verifier qu'il n'y a pas de ../ ni / au debut (securite)
				AND !preg_match(',(^/|\.\.),', substr($src2,strlen(_DIR_RACINE)))
				// et si il est lisible
				AND @is_readable($src2)
			)
		)) {
			if ($r)
				$css[$s] = explode('&',
					str_replace('&amp;', '&', $r[2]), 2);
			else
				$css[$s] = $src;
		}
	}
	return $css;
}

function skeleditor_affichage_final($texte){
	if (isset($_COOKIE['spip_admin']) AND $GLOBALS['html']){
		if ((defined('_VAR_INCLURE') AND _VAR_INCLURE) OR $GLOBALS['var_inclure']){
			$retour = self();
			$url = generer_url_ecrire('skeleditor','retour='.$retour.'&f=');			
			$inserer = "<script type='text/javascript'>jQuery(function(){jQuery('.inclure_blocs h6:first-child').each(function(){
				jQuery(this).html(\"<a href='$url\"+jQuery(this).html()+\"'>\"+jQuery(this).html()+'<'+'/a>');
			})});</script></body>";
			$texte = preg_replace(",</body>,",$inserer,$texte);
			
			$css = skeleditor_extraire_css($texte);
			$lienplus = array();
			foreach($css as $src){
				// si c'est un skel, le trouver
				if (is_array($src))
					$src = find_in_path($src."."._EXTENSION_SQUELETTES);
				if ($src)
					$lienplus[] = "<a href='$url".urlencode($src)."'"
			.">".basename($src)."<\/a>";
			}
			if (count($lienplus)){
				$lienplus = implode('<br />',$lienplus);
				$lienplus = "<span class='spip-admin-boutons' id='inclure'>$lienplus<\/span>";
			};

		} else {
			$lienplus = "<a href='".parametre_url(self(),'var_mode','inclure')."' class='spip-admin-boutons' "
			."id='inclure'>"._T('skeleditor:squelettes')."<\/a>";
		}
		if ($lienplus)
			$inserer = "<script type='text/javascript'>/*<![CDATA[*/jQuery(function(){jQuery('#spip-admin').append(\"$lienplus\");});/*]]>*/</script></body>";
			$texte = preg_replace(",</body>,",$inserer,$texte);
	}
	return $texte;
}


?>