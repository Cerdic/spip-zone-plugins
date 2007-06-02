<?php

// Outils GLOSSAIRE - 26 mai 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/Plugin-glossaire

charger_generer_url();  # pour generer_url_mot()

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite|a
function cs_rempl_glossaire($texte) {
	$limit = defined('_GLOSSAIRE_LIMITE')?_GLOSSAIRE_LIMITE:-1;
	$r = spip_query("SELECT id_mot, titre, texte FROM spip_mots WHERE type='Glossaire'");
	// parcours de tous les mots, sauf celui qui peut faire partie du contexte (par ex : /spip.php?mot5)
	while($mot = spip_fetch_array($r)) if ($mot['id_mot']<>$GLOBALS['id_mot']) {
//		$table[$mot[id_mot]] = "<abbr title=\"$mot[texte]\">$mot[titre]</abbr>";
		$lien = generer_url_mot($mot['id_mot']);
		$table1[$mot['id_mot']] = "<a name=\"mot$mot[id_mot]\" href=\"$lien\" class=\"cs_glossaire\"><span class=\"gl_mot\">";
		$table2[$mot['id_mot']] = "</span><span class=\"gl_dl\"><span class=\"gl_dt\">$mot[titre]</span><span class=\"gl_dd\">"
			. nl2br(trim($mot['texte'])) . "</span></span></a>";
		// a chaque mot reconnu, on pose une balise temporaire	
		$texte = preg_replace(",\b$mot[titre]\b,i", "@@GLOSS\\0#$mot[id_mot]@@", $texte, $limit);
	}
	// remplacement final des balises posees ci-dessus
	return preg_replace(",@@GLOSS([^#]+)#([0-9]+)@@,e", '"$table1[\\2]\\1$table2[\\2]"', $texte);
}

function cs_glossaire($texte) {
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|a', 'cs_rempl_glossaire', $texte);
}

// autre exemple : www.vinove.com/glossary.php

?>