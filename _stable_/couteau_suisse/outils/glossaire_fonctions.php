<?php

// Outils GLOSSAIRE - 26 mai 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/Plugin-glossaire

charger_generer_url();  # pour generer_url_mot()

// Compatibilite SPIP 1.91
if ($GLOBALS['spip_version_code']<1.92) { function _q($t) {return spip_abstract_quote($t);} }

// on calcule ici la globale $GLOBALS['glossaire_groupes_type']
$groupes = trim($GLOBALS['glossaire_groupes']);
if(!strlen($groupes)) $groupes = _q('Glossaire');
	else {
		$groupes = explode(':', $groupes);
		foreach($groupes as $i=>$g) $groupes[$i] = _q(trim($g));
		$groupes = join(" OR type=", $groupes);
	}
$GLOBALS['glossaire_groupes_type'] = 'type=' . $groupes;
unset($groupes);

// compatibilite SPIP 1.91
include_spip('inc/texte');
if(!function_exists('nettoyer_chapo')) {
	// Ne pas renvoyer le chapo si article virtuel
	function nettoyer_chapo($chapo){
		return (substr($chapo,0,1) == "=") ? '' : $chapo;
	}
}

// Cette fonction retire du texte les boites de definition
function glossaire_retire_glossaire($texte) {
	return preg_replace(',<span class="gl_d[td]">.*?</span>,', '', $texte);
}
$GLOBALS['cs_introduire'][] = 'glossaire_retire_glossaire';

function glossaire_echappe_balises_callback($matches) {
 return cs_code_echappement($matches[1], 'GLOSS');
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite|a
function cs_rempl_glossaire($texte) {
	$limit = defined('_GLOSSAIRE_LIMITE')?_GLOSSAIRE_LIMITE:-1;
	$r = spip_query("SELECT id_mot, titre, texte FROM spip_mots WHERE " . $GLOBALS['glossaire_groupes_type']);
	// parcours de tous les mots, sauf celui qui peut faire partie du contexte (par ex : /spip.php?mot5)
	while($mot = spip_fetch_array($r)) if ($mot['id_mot']<>$GLOBALS['id_mot'] && preg_match(",(\W)($mot[titre])(\W),i", $texte)) {
//		$table[$mot[id_mot]] = "<abbr title=\"$mot[texte]\">$mot[titre]</abbr>";
		// prudence : on protege TOUTES les balises contenant le mot en question
		$texte = preg_replace_callback(",(<[^>]*$mot[titre][^>]*>),Umsi", 'glossaire_echappe_balises_callback', $texte);
		$lien = generer_url_mot($mot['id_mot']);
/* JS */
		$definition = nl2br(trim($mot['texte']));
		$table1[$mot['id_mot']] = "<a name=\"mot$mot[id_mot]\" href=\"$lien\" class=\"cs_glossaire\"><span class=\"gl_mot\">";
if (defined('_GLOSSAIRE_JS')) {
		$table2[$mot['id_mot']] = "</span></a>";
} else {
		$table2[$mot['id_mot']] = "</span><span class=\"gl_dl\"><span class=\"gl_dt\">$mot[titre]</span><span class=\"gl_dd\">$definition</span></span></a>";
}
/*
		$table1[$mot['id_mot']] = "<a name=\"mot$mot[id_mot]\" href=\"$lien\" class=\"cs_glossaire\"><span class=\"gl_mot\">";
		$table2[$mot['id_mot']] = "</span><span class=\"gl_dl\"><span class=\"gl_dt\">$mot[titre]</span><span class=\"gl_dd\">"
			. nl2br(trim($mot['texte'])) . "</span></span></a>";
*/
		// a chaque mot reconnu, on pose une balise temporaire	
		$texte = preg_replace(",(\W)($mot[titre])(\W),i", "\\1@@GLOSS\\2#$mot[id_mot]@@\\3", $texte, $limit);
	}
	// remplacement final des balises posees ci-dessus
	$texte = preg_replace(",@@GLOSS([^#]+)#([0-9]+)@@,e", '"$table1[\\2]\\1$table2[\\2]"', $texte);
	return echappe_retour($texte, 'GLOSS');
}

function cs_glossaire($texte) {
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|a', 'cs_rempl_glossaire', $texte);
}

// autre exemple : www.vinove.com/glossary.php

?>