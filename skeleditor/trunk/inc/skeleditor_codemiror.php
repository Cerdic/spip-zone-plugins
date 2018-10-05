<?php

/**
 * G�n�re les appels js ou les css selon $type, correspondants � l'extension du fichier �dit�
 *
 * @param string $extension
 * @return array
 */
function skeleditor_inline_inc($extension){
	if (!$extension)
		return array("","");

	$css = $js = "";
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
		case 'scss':
		case 'less':
			$mode = array("css");
			break;
		case 'xml':
		case 'yaml':
		case 'svg':
		case 'rdf':
			$mode = array("xml");
			#continuousScanning: 500,
			break;
		/*
		case 'sql':
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
			break;
		*/
		case 'php':
		case 'html':
			// $mode = array('htmlmixed','xml');
			// break;
		case 'htm':
		default:
			$mode = array("xml","htmlmixed", "css", "javascript", "clike","php");
			break;
	}
	
	$dir = _DIR_PLUGIN_SKELEDITOR;
	$css .= "<link rel='stylesheet' href='".$dir."codemirror/lib/codemirror.css' type='text/css' />\n"
	  // . "<link rel='stylesheet' href='".$dir."codemirror/theme/default.css' type='text/css' />\n"
		. "<link rel='stylesheet' href='".$dir."codemirror/theme/solarized.css' type='text/css' />\n"
		. "<link rel='stylesheet' href='".$dir."codemirror/addon/display/fullscreen.css' type='text/css' />\n"
		. "<link rel='stylesheet' href='".$dir."codemirror/addon/fold/foldgutter.css' type='text/css' />\n"
		. "<link rel='stylesheet' href='".$dir."css/skeleditor.css' type='text/css' />\n";

	$js .= "<script src='".$dir."codemirror/lib/codemirror.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."javascript/emmet.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/display/fullscreen.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/edit/matchbrackets.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/edit/closebrackets.js' type='text/javascript'></script>\n";

	$js .= "<script src='".$dir."codemirror/addon/selection/active-line.js' type='text/javascript'></script>\n";

	$js .= "<script src='".$dir."codemirror/addon/fold/foldcode.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/fold/foldgutter.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/fold/brace-fold.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/fold/xml-fold.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/fold/indent-fold.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/fold/comment-fold.js' type='text/javascript'></script>\n";

	$js .= "<script src='".$dir."codemirror/addon/edit/matchtags.js' type='text/javascript'></script>\n";
	$js .= "<script src='".$dir."codemirror/addon/edit/closetag.js' type='text/javascript'></script>\n";

	foreach($mode as $m) {
		$test = $dir."codemirror/mode/$m/$m";
		if (file_exists($f=$test.".css"))
			$css .= "<link rel='stylesheet' href='$f' type='text/css' />\n";
		if (file_exists($f=$test.".js"))
			$js .= "<script src='$f' type='text/javascript'></script>\n";
	}

	return array($css,$js);
}

/**
 * D�termine le mime_type pour le mode de codemirror � afficher, selon l'extension du nom du fichier edit�
 *
 * @param string $extension
 * @return string
 */
function skeleditor_codemirror_determine_mode($extension) {
	$mode = "";
  $modes = array(
		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'application/x-httpd-php',
		'css' => 'text/css',
		'scss' => 'text/x-scss',
		'js' => 'javascript', //codemirror2 ne doit pas avoir de mode d�finit pour les js
		'json' => 'application/json',
		'xml' => 'application/xml',
	);
	if (array_key_exists($extension, $modes)) {
		$mode = $modes[$extension];
	}
	return $mode;
}

/**
 * G�n�re le script d'appel de codemirror
 *
 * @param string $filename
 * @param bool $editable
 * @return string
 */
function skeleditor_codemirror($filename,$editable=true){
	if (!$filename)
		return "";

	$infos = pathinfo($filename);
	list($css,$js) = skeleditor_inline_inc($infos['extension']);

	// readOnly: jQuery("#code").attr("readonly"),
	$mode = skeleditor_codemirror_determine_mode($infos['extension']);
	$script =
    '<script type="text/javascript">var cm_mode="'.$mode.'";</script>'
		. $css
		. $js
		. "<script src='".find_in_path("javascript/codemirror_init.js")."' type='text/javascript'></script>\n";

	// compresser le tout si possible !
	if (include_spip('compresseur_fonctions')
		AND function_exists('compacte_head'))
		$script = compacte_head($script);

	return $script;
}
