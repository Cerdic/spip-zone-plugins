<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

## fichier appele par le .htaccess de IMG/ ; lequel doit contenir :

/*

RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
# Si spip est range a la racine du domaine ou pour un mutualise
RewriteRule .* /ecrire/?action=hash_404 [L]
# Si spip est range dans un sous dossier spip
#RewriteRule .* /spip/ecrire/?action=hash_404 [L]

*/

include_spip("hash_fonctions");
$doc = preg_replace(',^.*?IMG/,', '', $_SERVER['REQUEST_URI']);

if (($dest = hasher_adresser_document($doc)
AND file_exists('../'.$GLOBALS['meta']['dir_img'].$dest))
OR ($dest1 = hasher_adresser_document($doc, true)
AND file_exists('../'.$GLOBALS['meta']['dir_img'].$dest))
) {
	$url = 'http://'.$_SERVER['HTTP_HOST'].preg_replace(',^(.*?IMG/).*,', '\1', $_SERVER['REQUEST_URI']).$dest;
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: '.$url);
	echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>301 Moved Permanently</title>
</head><body>
<h1>Moved Permanently</h1>
<p>The requested URL '.htmlspecialchars($_SERVER['REQUEST_URI']).' has moved to <a href="'.$url.'">'.$url.'</a>.</p>
<hr>
'.$_SERVER['SERVER_SIGNATURE'].'
</body></html>
';
}
else {
	echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL '.htmlspecialchars($_SERVER['REQUEST_URI']).' was not found on this server.</p>
<hr>
'.$_SERVER['SERVER_SIGNATURE'].'
</body></html>
';
}

?>
