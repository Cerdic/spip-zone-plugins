<?php

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_TYPO_AVANCEE',(_DIR_PLUGINS.end($p)));

	include_spip("php-typography/php-typography"); 
	include_spip("inc/charsets");

// ATTENTION
// J'ai patché php-typography.php selon remarque de Renato
// => ligne 2095
// => il faut commencer la boucle avec $segmentLength=1;
// sinon les patterns d'un caractere ne fonctionnent pas (et l'italien en a beaucoup)


function traiter_typo_avancee($texte) {
	$lang = $GLOBALS["spip_lang"];
	if ($lang == "en") $lang = "en-GB";
	if (!$lang) $lang = "fr";
	
	// Ne pas hyphener dans la premiere passe
	$typo = new phpTypography();
	$typo->set_defaults();

	$typo->set_min_before_hyphenation(3);
	$typo->set_min_after_hyphenation(4);
	$typo->set_hyphenate_headings(false);
	$typo->set_hyphenation_language($lang);
	// Pas de cesures si le texte est court (moins d'une longue ligne de texte)
	if (spip_strlen($texte) < 80) $typo->set_hyphenation(false);

	$typo->set_dewidow(true);
	$typo->set_max_dewidow_length(12);
	$typo->set_max_dewidow_pull(8);

	// Ne pas activer l'accentuation automatique (faut pas deconner non plus)
	$typo->set_smart_diacritics(false);


	// Ne pas activer les tirets
	// car ils perturbent certains raccourcis de SPIP (tirets a la ligne)
	$typo->set_smart_dashes(false);
	
	// Ne pas regrouper les espaces: sinon les tirets a la ligne ne fonctionnent plus
	$typo->set_space_collapse(false);

	
	// Forcer les smarquotes dans la bonne langue
	$typo->set_smart_quotes_language($lang);
	// et ne pas l'appliquer (ça déconne)
	$typo->set_smart_quotes(false);

	// Pas de gestion des fractons
	$typo->set_smart_fractions(false);
	
	// Et hop, le traitement!
	$texte = $typo->process($texte);
	
	
	
	// Et maintenant, redonner la compatibilité avec les traitements de SPIP
	
	// Gaffe: le traitement des mots en majuscules et des chiffres transforme les echappements de SPIP.
	$texte = preg_replace("/@@<span class=\"caps\">SPIP_ECHAPPE_LIEN_<span class=\"numbers\">([0-9]+)<\/span><\/span>@@/", "@@SPIP_ECHAPPE_LIEN_\\1@@", $texte);
	$texte = preg_replace("/@@@<span class=\"caps\">SPIP_DIFF<span class=\"numbers\">([0-9]+)<\/span><\/span>@@@/", "@@@SPIP_DIFF\\1@@@", $texte);
	
	// Remettre les retours a la ligne simples
	$zerohyphen = $typo->chr["zeroWidthSpace"];
	$nbrsp = $typo->chr["noBreakSpace"];
	$texte = preg_replace("/[\n\r]_(".$zerohyphen."|\ |".$nbrsp.")*/", "\n_ ", $texte);
	
	
	// Ajout: apres une esperluete, mettre un blanc insecable
	$texte = preg_replace("/<span class=\"amp\">&amp;<\/span> +/","<span class=\"amp\">&amp;</span>&nbsp;", $texte); 
	
	
	// Attention aux titres numerotes
	$texte = preg_replace("/^<span class=\"numbers\">([0-9]+)<\/span>\.([[:space:]]|".chr(194)."|".chr(160).")+/", "\\1. ", $texte);
	
	
	$texte = trim($texte);
	
	return $texte;
}


function uniord($c) {
    $h = ord($c{0});
    if ($h <= 0x7F) {
        return $h;
    } else if ($h < 0xC2) {
        return false;
    } else if ($h <= 0xDF) {
        return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
    } else if ($h <= 0xEF) {
        return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                                 | (ord($c{2}) & 0x3F);
    } else if ($h <= 0xF4) {
        return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                                 | (ord($c{2}) & 0x3F) << 6
                                 | (ord($c{3}) & 0x3F);
    } else {
        return false;
    }
}


function unichr ($codes) {
	if (is_scalar($codes)) $codes= func_get_args();
	$str= '';
	foreach ($codes as $code) $str.= html_entity_decode('&#'.$code.';',ENT_NOQUOTES,'UTF-8');
	return $str;
}


function typo_avancee_typo($texte) {
	// Ne pas appliquer dans l'espace prive
	if (_DIR_RACINE == "../") return $texte;
	
	// Traiter paragraphe par paragraphe
	include_spip("inc/texte");
	$texte_par = traiter_retours_chariots($texte);

	$paragraphes = explode("\n\n", $texte_par);
	$retour = "";
	foreach($paragraphes as $paragraphe) {
	
	
		$retour[]= traiter_typo_avancee(trim($paragraphe));
	}
	$texte = join($retour,"\n\n");
	
	return $texte;
}

function supprimer_cesure($texte) {
	// pas terrible, pas terrible...
	return preg_replace("/(".unichr(8203)."|".unichr(173)."|­|​​)/", "", $texte);
}

?>