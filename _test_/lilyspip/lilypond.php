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

	

// Pour compatibilite - ajout de Patrice  VANNEUFVILLE
if ($GLOBALS['spip_version_code']<1.92) define(_DIR_VAR, _DIR_IMG);
define('_DIR_Lilypond', sous_repertoire(_DIR_VAR, "cache-Lilypond"));	

function lilyspip_genere_midi($tex) {
	
	
	$server = $GLOBALS['meta']['lilyspip_server'];
	
	if (!@is_dir(_DIR_Lilypond))
		@mkdir (_DIR_Lilypond, 0777);	
	$fichiermidi = _DIR_Lilypond .md5(trim($tex)).".midi";
		
	if (!@file_exists($fichiermidi)) {
		// Aller chercher le fichier midi sur le serveur	
		spip_log($url = $server.'?'.'code='.rawurlencode($tex).'&format=midi');

		include_spip('inc/distant');
		if ($son = recuperer_page($url)) {
			if ($fs = @fopen($fichiermidi, 'w')) {
				@fwrite($fs, $son);
				@fclose($fs);
				}
			}
			else { 
// creation d'un fichier vide pour eviter d'aller rechercher le midi sur le serveur alors que la code ne le genere pas
			if ($fs = @fopen($fichiermidi, 'w')) {
				@fwrite($fs, "");
				@fclose($fs);
				}
			}
	}
	

	if ( @filesize($fichiermidi)) {
		return "<a href=\"$fichiermidi\" >";
		}
	else // pas de fichier
		return "";
}



function lilyspip_genere_image($tex) {

	
	$server = $GLOBALS['meta']['lilyspip_server'];

	// Regarder dans le repertoire local des images Lilypond

	if (!@is_dir(_DIR_Lilypond))
		@mkdir (_DIR_Lilypond, 0777);
	$fichier = _DIR_Lilypond .md5(trim($tex)).".png";
 
		

	if (!@file_exists($fichier)) {
		// Aller chercher l'image sur le serveur		
		spip_log($url = $server.'?'.'code='.rawurlencode($tex).'&format=png');

		include_spip('inc/distant');
		if ($image = recuperer_page($url)) {
			if ($f = @fopen($fichier, 'w')) {
				@fwrite($f, $image);
				@fclose($f);
				}
			
			}
		}
	
	// l'image correspond soit à la partition soit au log 

	$tex = entites_html($tex);
	// supprimer les retour à la ligne du code 
	$tex=str_replace(CHR(10),"",$tex); 
	$tex=str_replace(CHR(13),"",$tex);  

	if (@file_exists($fichier)) {
		list(,,,$size) = @getimagesize($fichier);
		$alt = "alt=\"$tex\" title=\"$tex\""; 
		return "<img src=\"$fichier\" style=\"vertical-align:middle;\" $size $alt />";
	}
	//return "<tt><span class='spip_code' dir='ltr'>".caracteres_alt($tex)."</span></tt>";

}



// echapper les caracteres que typo() veut marabouter
function caracteres_alt($texte) {
	foreach (array(';', '{', '}', ':',"?", '!', '"', '\'', '<', '>') as $bug)
		$texte = str_replace($bug, "&#".ord($bug).";", $texte);
	return $texte;
}






// Fonction appelee par propre() s'il repere un mode <lilypond>
function lilyspip_pre_propre($letexte) {
			
	preg_match_all("|<lilypond>(.*?)</lilypond>|s", $letexte, $regs, PREG_SET_ORDER);

	foreach ($regs as $lily) {
		$mid = lilyspip_genere_midi($lily[1]);
		if ($mid == "") $aendtag = ""; else $aendtag = "</a>";
		$img = "\n<div class=\"lilypond\" >".$mid.lilyspip_genere_image($lily[1]).$aendtag."</div>\n";
		
		$letexte = str_replace($lily[0], $img, $letexte);
	}		
		
	return echappe_html($letexte);
}

?>
