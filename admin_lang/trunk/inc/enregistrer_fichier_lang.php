<?
// ---------------------------------------------
//  Plugin admin_lang
//	 
//  spip addition to manage language files
//  alm@elastick.net
//  simeray@tektonika.com
//  dani@rezo.net
// ---------------------------------------------

if (!defined('_ECRIRE_INC_VERSION')) return;

function enregister_fichier_lang($dir, $langue, $module, $items, $comment='')
{
	// ---------------------------------------------
	//		WRITE RESULTING FILE
	// ---------------------------------------------
	$f = fopen($dir . '/' . $module . '_' . $langue . '.php',"w");
	if (!$f) return false;
	// lock it
	flock ($f, LOCK_EX); 
			
	// ---------------------------------------------
	//		write header
	// ---------------------------------------------
	wf($f, "<" . "?php");
	wf($f, "");
	wf($f, "// This is a SPIP language file  --  Ceci est un fichier langue de SPIP");
	wf($f, '// Langue: ' . $langue);
	wf($f, '// Module: ' . $module);
	wf($f, '// Date: ' . date('d-m-Y H:i:s'));
	wf($f, '// Items: ' . count($items));
	wf($f, $comment);
	wf($f, "");
	wf($f, "\$GLOBALS[\$GLOBALS['idx_lang']] = array(");
	
	// ---------------------------------------------
	//		write body
	// ---------------------------------------------
	$prev_letter = '';
	foreach($items as $k => $v) {
		if ($k[0] != $prev_letter) {
			wf($f, "");
			wf($f, "");
			wf($f, "// " . strtoupper($k[0]));
			$prev_letter = $k[0];
		}
		wf($f, "'$k' => " . _q($v) . ",");
	}
	
	// ---------------------------------------------
	//		write footer
	// ---------------------------------------------
	wf($f, "");
	wf($f, "");
	wf($f, ");");
	wf($f, "");
	wf($f, "?>");
	
	flock ($f, LOCK_UN);
	return fclose ($f);
}

// ---------------------------------------------
//	Write File, line by line
// ---------------------------------------------
function wf($myFile, $any_string, $end_line="\n") {
	global $display_debug_full;
	
	if ($display_debug_full) {
	print "-> write string : $any_string<br />";
	}
	
	if (substr($any_string, -2) != "?>") {$any_string = $any_string . $end_line;} // append end_line except ending php file
	if ($myFile != "") {fputs($myFile, $any_string);}
	else {print "!! Error - File to write is undefined<br />\r";}
}
?>