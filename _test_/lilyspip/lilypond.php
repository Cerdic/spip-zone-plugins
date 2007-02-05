<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


/***************************************************************************\
 *  Plugin Lilyspip                         				   *
 *                                                                         *
 *  Auteur : Christophe RICHARD						   *
 *  Adaptation de la gestion des formules mathematiques	de SPIP		   * 
 *									   *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


//
// Gestion du raccourci <lilypond>...</lilypond> en client-serveur
//

define('_DIR_Lilypond', _DIR_IMG . "cache-Lilypond/");	



function midi_lilypond($code) {
	
	
	$server = $GLOBALS['meta']['lilyspip_server'];
	
	if (!@is_dir(_DIR_Lilypond))
		@mkdir (_DIR_Lilypond, 0777);	
	$fichiermidi = _DIR_Lilypond .md5(trim($code)).".midi";
		
	if (!@file_exists($fichiermidi)) {
		// Aller chercher le fichier midi sur le serveur	
		spip_log($url = $server.'?'.'code='.rawurlencode($code).'&format=midi');

		include_spip('inc/distant');
		if ($son = recuperer_page($url)) {
			if ($fs = @fopen($fichiermidi, 'w')) {
				@fwrite($fs, $son);
				@fclose($fs);
				}
			}
			else { 
// creation d'un fichier vide pour éviter d'aller rechercher le midi sur le serveur alors que la code ne le genere pas
			if ($fs = @fopen($fichiermidi, 'w')) {
				@fwrite($fs, "");
				@fclose($fs);
				}
			}
	}
	

	if (@file_exists($fichiermidi) && !fichier_vide($fichiermidi)) {
		return "<a href=\"$fichiermidi\"  />";
		}
	else // pas de fichier
		return "";
}



function image_lilypond($code) {

	
	$server = $GLOBALS['meta']['lilyspip_server'];

	// Regarder dans le repertoire local des images Lilypond

	if (!@is_dir(_DIR_Lilypond))
		@mkdir (_DIR_Lilypond, 0777);
	$fichier = _DIR_Lilypond .md5(trim($code)).".png";
		

	if (!@file_exists($fichier)) {
		// Aller chercher l'image sur le serveur		
		spip_log($url = $server.'?'.'code='.rawurlencode($code).'&format=png');

		include_spip('inc/distant');
		if ($image = recuperer_page($url)) {
			if ($f = @fopen($fichier, 'w')) {
				@fwrite($f, $image);
				@fclose($f);
				}
			}
		}
	
	// Composer la reponse selon presence ou non de l'image
	$tex = entites_html($code);
	if (@file_exists($fichier)) {
		list(,,,$size) = @getimagesize($fichier);
		$alt = "alt=\"$code\" title=\"$code\""; 
		return "<img src=\"$fichier\" style=\"vertical-align:middle;\" $size $alt />";
	}
	else // pas de fichier
		return "<tt><span class='spip_code' dir='ltr'>".caracteres_alt($code)."</span></tt>";

}



// echapper les caracteres que typo() veut marabouter
function caracteres_alt($texte) {
	foreach (array(';', '{', '}', ':',"?", '!', '"', '\'', '<', '>') as $bug)
		$texte = str_replace($bug, "&#".ord($bug).";", $texte);
	return $texte;
}



// tester si le fichier MIDI est vide pour ne pas afficher le lien
function fichier_vide($file) {

$octet=filesize($file);
if ($octet==0) return true;
else return false;
}



// Fonction appelee par propre() s'il repere un mode <lilypond>
function lilyspip_pre_propre($letexte) {
	global $flag_ecrire;
			
	preg_match_all("|<lilypond>(.*?)</lilypond>|s", $letexte, $regs, PREG_SET_ORDER);

	foreach ($regs as $lily) {
		$mid = midi_lilypond($lily[1]);
		if ($mid == "") $aendtag = ""; else $aendtag = "</a>";
		$img = "\n<p class=\"spip\" style=\"text-align: center;\">".$mid.image_lilypond($lily[1]).$aendtag."</p>\n";
		
		$letexte = str_replace($lily[0], $img, $letexte);
	}		
		
	return echappe_html($letexte);
}

?>
