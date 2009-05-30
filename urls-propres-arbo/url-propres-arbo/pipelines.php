<?php
function proprearbo_header_public($flux)
{	
	//Site public
	if(!_DIR_RESTREINT || !strstr($_SERVER['PHP_SELF'],_DIR_RESTREINT)){
		$flux = preg_replace('/https?:\/\/.*\/spip.php/i','spip.php',$flux);
		$flux = preg_replace('/https?:\/\/.*\/(.*)\/spip_admin.css/i','$1/spip_admin.css',$flux);
		$flux=explode('</title>',$flux);
		if($flux[1])	//Si </title> à été trouvé
		{	$flux[0].= '</title>'."\n".'<base href="'
				.$GLOBALS['meta']['adresse_site']
				.'/" />'
				.$flux[1];
		}		
		return $flux[0];
	}else{	//Partie privée
		return $flux;
	}
}
?>
