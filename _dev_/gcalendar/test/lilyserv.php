<?php
//
// Serveur d'images Lilypond developpe pour SPIP par
// Richard Christophe � partir du serveur Tex de Philippe Riviere <fil@rezo.net> et Benjamin Sonntag <benjamin@sonntag.fr>
// Distribue sous licence GNU/GPL
// � 2006 - v0.1
// ChangeLog : ajout d'une box
//


// Necessite l'installation de lilypond et ImageMagick


// Cache du serveur
$cache_dir = "CACHE/lilyspip";
if (!is_dir($cache_dir))
	mkdir ($cache_dir);




function lilypond_enhance($tex) {
	// Correction pour forcer la ligne de base
	$tex = "\paper{indent=0\mm line-width=120\mm oddFooterMarkup=##f oddHeaderMarkup=##f
  bookTitleMarkup = ##f
  scoreTitleMarkup = ##f
}".$tex;
	return $tex;
}

function lilypond_($texte) {
	

	global $cache_dir;

	$fichier = "$cache_dir/".md5(trim($texte)).'.png';

	if (!file_exists($fichier) OR (filemtime($fichier)<filemtime("lilyserv.php"))) {

		$texte = escapeshellarg(lilypond_enhance($texte));
		$cmd = "/usr/local/bin/lilypond -safe -- png --output=$fichier $texte";
		exec($cmd);
		$cmd2 = "/usr/local/bin/convert trim $fichier $fichier";
	}

	return $fichier;
}

// Retourner l'image demandee
header("Content-Type: image/png");
readfile(lilypond_(urldecode($_SERVER['QUERY_STRING'])));

?>
