<?php
$dominio_cookie=$HTTP_SERVER_VARS['SERVER_NAME'];

if (substr($dominio_cookie,0,4)=="www.") {
	$dominio_cookie=substr($dominio_cookie,4);
}

// Funcion para purgar la cache y que todo el sitio funcione con el nuevo esqueleto 
// si se produce un cambio
// de 3615MARLENE (http://marlene.c3ew.com/article.php?id_article=18)
function purge($dir, $age='ignore', $regexp = '') { 
	$handle = @opendir($dir);
	if (!$handle) return;

	while (($fichier = @readdir($handle)) != '') {
 	// Eviter ".", "..", ".htaccess", etc.
		if ($fichier[0] == '.') continue;
		if ($regexp AND !ereg($regexp, $fichier)) continue;
		$chemin = "$dir/$fichier";
		if (is_file($chemin))
			@unlink($chemin);
		else if (is_dir($chemin))
			if ($fichier != 'CVS')
				purge($chemin);
	}
	@closedir($handle);
}

if (isset($esqueleto)) {
$path= "esqueletos";
$dir = @opendir($path);
	while ($esqueleto_carp = @readdir($dir)) {
		if (!is_file($esqueleto_carp) and ($esqueleto_carp!=".") and ($esqueleto_carp!="..") and  ($esqueleto_carp==$esqueleto) ){
		// Guarda en una cookie el esqueleto seleccionado durante un agno...
 		purge(CACHE, 0);     // vaciar la cache
		$dossier_squelettes = "esqueletos/".$esqueleto;
		setcookie("esqueleto_selec","$esqueleto",time()+365*24*3600, "/", "$dominio_cookie"); // pone la nueva cookie
		$esqueleto = "";
		break;
		}

	}

@closedir($dir);
}

// ...o, si el de la cookie existe, lo utiliza

elseif(isset($_COOKIE['esqueleto_selec'])) {
$path= "esqueletos";
$dir = @opendir($path);
	while ($esqueleto_carp = @readdir($dir)) {
		if (!is_file($esqueleto_carp) and $esqueleto_carp!="." and $esqueleto_carp!=".." and  ($esqueleto_carp==isset($_COOKIE['esqueleto_selec'])) ){
					$dossier_squelettes="esqueletos/".$_COOKIE['esqueleto_selec'];
					break;
		}
	}

@closedir($dir);
}

else { // ...si no existe esqueleto a cambiar, o cookie, utiliza el "predeterminada"
	purge(CACHE, 0);     // vaciar la cache
$dossier_squelettes = "esqueletos/predeterminada";
}
?>