<?php

/*
   Ce tweak decoration permet aux redacteurs d'un site spip de d'appliquer les styles souligné, barré, au dessus aux textes SPIP
   Attention : seules les balises en minuscules sont reconnues.
*/

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function decoration_rempl($texte) {
	if (strpos($texte, '<')===false) return $texte;
	// debut de balises
	$texte = str_replace("<sc>", "<span style=\"font-variant: small-caps\">", $texte);
	$texte = str_replace("<souligne>", "<span style=\"text-decoration: underline;\">", $texte);
	$texte = str_replace("<barre>", "<span style=\"text-decoration: line-through;\">", $texte);
	$texte = str_replace("<dessus>", "<span style=\"text-decoration: overline;\">", $texte);
	$texte = str_replace("<clignote>", "<span style=\"text-decoration: blink;\">", $texte);
	$texte = str_replace("<surfluo>", "<span style=\"background-color: #ffff00; padding:0px 2px;\">", $texte);
	$texte = str_replace("<surgris>", "<span style=\"background-color: #EAEAEC; padding:0px 2px;\">", $texte);
	// compatibilite
	$texte = str_replace("<fluo>", "<span style=\"background-color: #ffff00; padding:0px 2px;\">", $texte);
	// fin de balises
	$texte = str_replace(array("</sc>", "</souligne>", "</barre>", "</dessus>", "</clignote>", "</surfluo>", "</surgris>", "</fluo>"), "</span>", $texte);
	return $texte;  
}

// fonction pipeline
function decoration_pre_typo($texte) {
	if (strpos($texte, '<')===false) return $texte;
	return tweak_echappe_balises('', 'decoration_rempl', $texte);
}

// cette fonction est appelee automatiquement a chaque affichage de la page privee de Tweak SPIP
// le resultat est une chaine apportant des informations sur les nouveau raccourcis ajoutes par le tweak
// si cette fonction n'existe pas, le plugin cherche alors  _T('tweak:mon_tweak:aide');
function decoration_raccourcis() {
	$liste = '<strong>sc</strong>, <strong>souligne</strong>, <strong>barre</strong>, <strong>dessus</strong>, <strong>clignote</strong>, <strong>surfluo</strong>, <strong>surgris</strong>';
	return _T('tweak:decoration:aide', array('liste' => $liste));
}

?>