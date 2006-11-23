<?php

function console_code_flash($width='300',$height='600'){
		$urlspiplog = urlencode(generer_url_ecrire('spiplog','logfile=spip',true));
		$urlsqllog = urlencode(generer_url_ecrire('spiplog','logfile=mysql',true));
		$flash = find_in_path('console.swf');
		return "
		<object type='application/x-shockwave-flash' 
		id='console'
		data='$flash?spiplog=$urlspiplog&sqllog=$urlsqllog' width='$width' height='$height' style='position:absolute;left:0;bottom:0;'>
			<param name='movie' value='$flash?spiplog=$urlspiplog&sqllog=$urlsqllog' />
			<param name='wmode' value='transparent' />
		</object>	";
}


function console_lit_log($logname){
	$files = preg_files(defined('_DIR_TMP')?_DIR_TMP:_DIR_SESSION ,"$logname\.log(\.[0-9])?");
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