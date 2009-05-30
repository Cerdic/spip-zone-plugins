<?php
function taille_typo_affichage_final($texte){

	$taille_typo = 1;
	
	// Demande-t-on une taille ?
	if (isset($_GET['taille_typo'])) {
		include_spip('inc/cookie');
		spip_setcookie('spip_taille_typo', $_COOKIE['spip_taille_typo'] = $_GET['taille_typo'], NULL, '/');
		$taille_typo = $_GET['taille_typo'];
	}
	// Porte-t-on un cookie de squelette ?
	if (isset($_COOKIE['spip_taille_typo'])){
		include_spip('inc/cookie');
		$taille_typo = $_COOKIE['spip_taille_typo'];
	}
	// Insertion du code pour le cookie
	$code.='<style type="text/css"><!-- html {font-size:'.$taille_typo.'em;} --></style>';
	
	// On rajoute le code du cookie avant la balise <html>
	$texte=eregi_replace("</head>","$code</head>",$texte);

	return($texte);
}
?>