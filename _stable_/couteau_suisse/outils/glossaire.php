<?php

// Outils GLOSSAIRE - 26 mai 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/Plugin-glossaire

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite|a
function cs_rempl_glossaire($texte) {
	$r = spip_query("SELECT id_mot, titre, texte FROM spip_mots WHERE type='Glossaire'");
	while($mot = spip_fetch_array($r)) {
//		$table[$mot[id_mot]] = "<abbr title=\"$mot[texte]\">$mot[titre]</abbr>";
		$lien = generer_url_mot($mot[id_mot]);
		$table1[$mot[id_mot]] = "<a name=\"mot$mot[id_mot]\" href=\"$lien\" class=\"cs_glossaire\"><span class=\"mot\">";
		$table2[$mot[id_mot]] = "</span><span class=\"dl\"><span class=\"dt\">$mot[titre]</span><span class=\"dd\">"
			. (nl2br($mot['texte'])) . "</span></span></a>";
		$texte = preg_replace(",\b$mot[titre]\b,i", "@@GLOSS\\0#$mot[id_mot]@@", $texte);
	}
		$texte = preg_replace(",@@GLOSS([^#]+)#([0-9]+)@@,e", '"$table1[\\2]\\1$table2[\\2]"', $texte);

	return $texte;
}

function glossaire_post_propre($texte) {
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|a', 'cs_rempl_glossaire', $texte);
}

// autre exemple : www.vinove.com/glossary.php

?>