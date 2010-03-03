<?php

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_TYPO_AVANCEE',(_DIR_PLUGINS.end($p)));


function traiter_typo_avancee($texte) {
	$lang = $GLOBALS["spip_lang"];
	if ($lang == "en") $lang = "en-GB";
	if (!$lang) $lang = "fr";


	include_spip('lib/php-typography/php-typography');
	include_spip("inc/charsets");
	
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
	$typo->set_max_dewidow_length(9);
	$typo->set_max_dewidow_pull(6);

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
	$texte = preg_replace("/^<span class=\"numbers\">([0-9]+)<\/span>\./", "\\1.", $texte);
	
	
	
	$texte = trim($texte);
	
	return $texte;
}


function typo_avancee_typo($texte) {


//	return $texte;
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
	return preg_replace("/(&#8203;|&#173;|­|​​)/", "", $texte);
}

?>