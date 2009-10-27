<?php

## fichier appele par le .htaccess de IMG/ ; lequel doit contenir :

/*

RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* /plugins/hash_documents/hash_404.php [L]

*/


@require 'hash_fonctions.php';

$doc = preg_replace(',^.*?IMG/,', '', $_SERVER['REQUEST_URI']);
if (($dest = hasher_adresser_document($doc)
AND file_exists('../../IMG/'.$dest))
OR ($dest = hasher_adresser_document($doc, true)
AND file_exists('../../IMG/'.$dest))
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


