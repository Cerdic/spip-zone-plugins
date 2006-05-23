<?php

function console_lit_log($logname){
	$files = preg_files(_DIR_SESSIONS,"$logname\.log(\.[0-9])?");
	krsort($files);

	$log = "";
	foreach($files as $nom){
		if (lire_fichier($nom,$contenu))
			$log.=$contenu;
	}
	$contenu = explode("<br />",nl2br($contenu));
	
	$out = "";
	$maxlines = 40;
	while ($contenu && $maxlines--){
		$out .= array_pop($contenu)."\n";
	}
	return $out;
}


?>