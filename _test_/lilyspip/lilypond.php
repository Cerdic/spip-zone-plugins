<?php

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

// sacree compatibilite...
if ($GLOBALS['spip_version_code']<1.92) define(_DIR_VAR, _DIR_IMG);

define('_DIR_Lilypond', sous_repertoire(_DIR_VAR, "cache-lilypond"));	

function lilyspip_midi_lilypond($music) {
	// Regarder dans le repertoire local de Lilypond
	$fichier = _DIR_Lilypond.md5($music).".midi";
	
	if (!@file_exists($fichier) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul') {
		// Aller chercher le fichier midi sur le serveur
		$url = $GLOBALS['meta']['lilyspip_server'].'?format=midi&code='.rawurlencode($music);
		spip_log("Lilypond - appel midi de : $url");

		include_spip('inc/distant');
		$son = recuperer_page($url);
		ecrire_fichier($fichier, $son);
	}

	if (@file_exists($fichier) && @filesize($fichier))
		return "<a href=\"$fichier\" />";
		// pas de fichier midi ou taille nulle
		else return false;
}

function lilyspip_ly_lilypond($music) {
	// Regarder dans le repertoire local de Lilypond
	$fichier = _DIR_Lilypond.md5($music).".ly";
	
	if (!@file_exists($fichier) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul') {
		// Aller chercher le fichier midi sur le serveur
//		$url = $GLOBALS['meta']['lilyspip_server'].'?format=ly&code='.rawurlencode($music);
//		spip_log("Lilypond - appel ly de : $url");
//		include_spip('inc/distant');
//		$music = recuperer_page($url);
		ecrire_fichier($fichier, $music);
	}

	return $music;
}

function lilyspip_image_lilypond($music) {
	// Regarder dans le repertoire local de Lilypond
	$fichier = _DIR_Lilypond .md5($music).".png";

	if (!@file_exists($fichier) || $GLOBALS['var_mode'] == 'recalcul' || $GLOBALS['var_mode']=='calcul') {
		// Aller chercher l'image sur le serveur		
		$url = $GLOBALS['meta']['lilyspip_server'].'?format=png&code='.rawurlencode($music);
		spip_log("Lilypond - appel png de : $url");

		include_spip('inc/distant');
		if ($image = recuperer_page($url))
			ecrire_fichier($fichier, $image);
	}
	
	// Composer la reponse selon presence ou non de l'image
	$music = entites_html($music);
	if (@file_exists($fichier)) {
		list(,,,$size) = @getimagesize($fichier);
		$alt = "alt=\"$music\" title=\"$music\""; 
		return "<img class=\"no_image_filtrer\" src=\"$fichier\" style=\"vertical-align:middle;\" $size $alt />";
	}
	else // pas de fichier
		return "<tt><span class=\"spip_code\" dir=\"ltr\">"._T('lilyspip:erreur_code')."<br />\n".lilyspip_caracteres_alt($music)."</span></tt>";

}


// echapper les caracteres que typo() veut marabouter
function lilyspip_caracteres_alt($texte) {
	foreach (array(';', '{', '}', ':',"?", '!', '"', '\'', '<', '>') as $bug)
		$texte = str_replace($bug, "&#".ord($bug).";", $texte);
	return $texte;
}


// Fonction appelee par propre() s'il repere un mode <lilypond>
function lilyspip_pre_propre($letexte) {
	preg_match_all("|<lilypond>(.*?)</lilypond>|s", $letexte, $regs, PREG_SET_ORDER);
	foreach ($regs as $lily) {
		$code = trim($lily[1]);
		$lien = lilyspip_midi_lilypond($code);
		$lien .= lilyspip_image_lilypond($code).(strlen($lien)?"</a>":"");
		$img = "\n<p class=\"spip\" style=\"text-align: center;\">$lien</p>\n";
		$letexte = str_replace($lily[0], $img, $letexte);
		// on sauve aussi le .ly
		lilyspip_ly_lilypond($code);
	}
	return echappe_html($letexte);
}

?>
