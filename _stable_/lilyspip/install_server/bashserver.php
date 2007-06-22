<?php
//
// Serveur d'images Lilypond developpe pour SPIP par
// Richard Christophe � partir du serveur Tex de Philippe Riviere <fil@rezo.net> et Benjamin Sonntag <benjamin@sonntag.fr>
// Distribue sous licence GNU/GPL
// � 2007 - v0.1
// 
//

// Le format peut prendre trois valeurs 'png' pour l'image, 'midi' pour le fichier MIDI 
// et 'test' pour afficher la version du serveur dans les parametres du plugin


// Necessite l'installation de lilypond et ImageMagick
$convert_bin = "/usr/bin/convert";
$script="/home/script/lilypond.sh";
$lilypond_version = "2.10.15";

// Cache du serveur
$cache_dir = "CACHE/lilyspip";


if (get_magic_quotes_gpc()) {
    $code = stripslashes($_GET['code']); 
} 
else {
 $code = ($_GET['code']);
} 
$format = $_GET['format']; 


function lilypond_enhance($texte,$code_format) {
	global $lilypond_version;
	
	if ($code_format=="test"){//Pour afficher la version dans les parametres du Plugin
	$textem = "\version \"$lilypond_version\"
	\header {}
	\paper {
		ragged-right = ##t
		indent = 60.0\mm
		paper-width = 100.0\mm
		paper-height = 60.0\mm
		}
		{\\relative c''{\\time 4/4  c4 c g g a a g g}}";
	}
	
	else {// Correction pour supprimer le bas de page et centrer l'image
	
	$textem = "\version \"$lilypond_version\"
	\header {
		tagline= \"\"
	}
	\paper {
		ragged-right = ##t
		indent = 0.0\mm
	}
	$texte";
	}

	return $textem;
}

function lilypond_($texte, $code_format) {
	
	global $cache_dir;
	global $convert_bin;
	global $script;
	
	$fichier = "$cache_dir/".md5(trim($texte)); //contiendra le log en cas d echec
	$fichier_source = $fichier.'.ly';
	$fichier_image = $fichier.'.png';
	$fichier_son = $fichier.'.midi';
	$fichier_ps = $fichier.'.ps';
		
	$texte = lilypond_enhance($texte, $code_format);
			
	if ($f = @fopen($fichier_source, 'w')) {
		@fwrite($f, $texte);
		@fclose($f);
	}
	

	if (!file_exists($fichier_image) OR (filemtime($fichier_image)<filemtime("bashserver.php"))) {
		$cmd = "$script $fichier $fichier_source";
		system($cmd);


		if (@file_exists($fichier_image)){
			$cmd2 = $convert_bin." -trim ".$fichier_image." ".$fichier_image;
			exec($cmd2);
		}
		else { // insertion du fichier log dans l image
		$cmd3 = $convert_bin." -size 800x150 xc:white -pointsize 10 -gravity northwest -annotate 0 @".$fichier." ".$fichier_image;
		exec($cmd3);
		}
	}

	//efface fichiers ly log et ps du CACHE
	if (@file_exists($fichier_ps) && chmod($fichier_ps,0777)) unlink($fichier_ps);
	if (@file_exists($fichier_source) && chmod($fichier_source,0777)) unlink($fichier_source);	
	if (@file_exists($fichier) && chmod($fichier,0777)) unlink($fichier);


	if ($code_format=="test" || $code_format=="png") 
		return $fichier_image;
		
	else if ($code_format == "midi" && file_exists($fichier_son))
		return $fichier_son;
}



// Retourner l'image ou le son demande
if (($format=="test" || $format=="png")) $type_header = "image/png";		
else if ($format=="midi") $type_header = "audio/x-midi";

header("Content-Type: ".$type_header);
readfile(lilypond_(rawurldecode($code),$format));

?>
