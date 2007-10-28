<?php

// Outils GLOSSAIRE - 26 mai 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/Plugin-glossaire

charger_generer_url();  # pour generer_url_mot()

// Compatibilite SPIP 1.91
if(defined('_SPIP19100') && !function_exists('_q')) { function _q($t) {return spip_abstract_quote($t);} }

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
	// compatibilite SPIP 1.92
	$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
	// parcours de tous les mots, sauf celui qui peut faire partie du contexte (par ex : /spip.php?mot5)
	while($mot = $fetch($r)) if ($mot['id_mot']<>$GLOBALS['id_mot']) {
		$m = $mot['titre'];
		$id = $mot['id_mot'];
		$u = charset2unicode($m);
		// liste de toutes les formes possible du mot clef en question.
		// normalement, y a pas besoin de : html_entity_decode($m)
		$les_mots = array_unique(array(
			htmlentities($m), $u, unicode_to_utf_8($u), unicode2charset($u), $m));
		foreach($les_mots as $i=>$v) $les_mots[$i] = preg_quote($v, ',');
		$les_mots = join('|', $les_mots);
		if(preg_match(",\W($les_mots)\W,i", $texte)) {
			// prudence : on protege TOUTES les balises contenant le mot en question
			$texte = preg_replace_callback(',(<[^>]*($les_mots)[^>]*>),Umsi', 'glossaire_echappe_balises_callback', $texte);
			$lien = generer_url_mot($id);
			$definition = nl2br(trim($mot['texte']));
			$table1[$id] = "<a name=\"mot$id\" href=\"$lien\" class=\"cs_glossaire\"><span class=\"gl_mot\">";
			$table2[$id] = defined('_GLOSSAIRE_JS')
				?'</span><span class="gl_js" title="'.htmlspecialchars($m).'"></span><span title="'.htmlspecialchars($definition).'"></span></a>'
				:"</span><span class=\"gl_dl\"><span class=\"gl_dt\">$m</span><span class=\"gl_dd\">$definition</span></span></a>";
			// a chaque mot reconnu, on pose une balise temporaire	
			$texte = preg_replace(",(\W)($les_mots)(\W),i", "\\1@@GLOSS\\2#$id@@\\3", $texte, $limit);
		}
	}
//print_r($table1);
	// remplacement final des balises posees ci-dessus
	$texte = preg_replace(",@@GLOSS(.*?)#([0-9]+)@@,e", '"$table1[\\2]\\1$table2[\\2]"', $texte);
	return echappe_retour($texte, 'GLOSS');
}

function cs_glossaire($texte) {
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|a', 'cs_rempl_glossaire', $texte);
}

// autre exemple : www.vinove.com/glossary.php

?>