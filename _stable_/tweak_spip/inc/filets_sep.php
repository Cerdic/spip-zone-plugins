<?php
/*
*	+----------------------------------+
*	Nom du Tweak : Filets de Separation
*	Idee originale : FredoMkb
*	Serieuse refonte : Patrice Vanneufville
*	+-------------------------------------+
*	Toutes les infos sur : http://www.spip-contrib.net/?article1564
*/

// Fonction pour generer des filets de separation selon les balises presentes dans le texte fourni.
// Cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function filets_sep_rempl($texte) {
	
	// On memorise les modeles d'expression rationnelle a utiliser pour chercher les balises.
	$base_nombre = '\d+';
	$base_fichier = '[\w+\.-]+\.(jpg|png|gif)';
	$base_total = $base_nombre.'|'.$base_fichier;
	$modele_nombre = "#[\n\r]\s*__({$base_nombre})__\s*[\n\r]#iU";
	$modele_fichier = "#[\n\r]\s*__({$base_fichier})__\s*[\n\r]#iU";
	$modele_total = "#[\n\r]\s*__({$base_total})__\s*[\n\r]#iU";
	
	// On verifie si des balises filets existent dans le texte fourni.
	$test= preg_match($modele_total, $texte);

	if ($test) {
		// On remplace les balises filets numeriques dans le texte par le code Html correspondant.
		$texte = preg_replace($modele_nombre,'<html><p class="filet_sep_$1"><!-- --></p></html>',$texte); 

		// On remplace les balises filets numeriques dans le texte par le code Html correspondant.
		$t=preg_split($modele_fichier, $texte, -1, PREG_SPLIT_DELIM_CAPTURE);
		$texte = $t[0];
		for ($i=1; $i<count($t); $i+=3) {
			$f=find_in_path('img/filets/'.$t[$i]);
			if (file_exists($f)) list(,$haut) = @getimagesize($f);
			if ($haut) $haut='height:'.$haut.'px; ';
			$texte .= '<html><p class="filet_sep_image" style="'.$haut.'background-image: url('.$f.');"><!-- --></p></html>'.$t[$i+2];
		}
	};

	return $texte;
}

function filets_sep($texte) {
	return tweak_exclure_balises('', 'filets_sep_rempl', $texte);
}
?>
