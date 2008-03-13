<?php

// Outils GLOSSAIRE - 26 mai 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Doc : http://www.spip-contrib.net/?article2206

charger_generer_url();  # pour generer_url_mot()

// Compatibilite SPIP 1.91
if(defined('_SPIP19100') && !function_exists('_q')) { function _q($t) {return spip_abstract_quote($t);} }

// on calcule ici la globale $GLOBALS['glossaire_groupes_type']
function glossaire_groupes() {
	$groupes = trim($GLOBALS['glossaire_groupes']);
	if(!strlen($groupes)) return _q('Glossaire');
		else {
			$groupes = explode(':', $groupes);
			foreach($groupes as $i=>$g) $groupes[$i] = _q(trim($g));
			return join(" OR type=", $groupes);
		}
}
$GLOBALS['glossaire_groupes_type'] = 'type=' . glossaire_groupes();

// compatibilite SPIP 1.91
include_spip('inc/texte');
if(!function_exists('nettoyer_chapo')) {
	// Ne pas renvoyer le chapo si article virtuel
	function nettoyer_chapo($chapo){
		return (substr($chapo,0,1) == "=") ? '' : $chapo;
	}
}

// Cette fonction retire du texte les boites de definition
function cs_retire_glossaire($texte) {
	return preg_replace(',<span class="gl_d[td]">.*?</span>,', '', $texte);
}
$GLOBALS['cs_introduire'][] = 'cs_retire_glossaire';

function glossaire_echappe_balises_callback($matches) {
 return cs_code_echappement($matches[1], 'GLOSS');
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite|a
function cs_rempl_glossaire($texte) {
	static $accents;
	if(!isset($accents)) $accents = cs_glossaire_accents();
	$limit = defined('_GLOSSAIRE_LIMITE')?_GLOSSAIRE_LIMITE:-1;
	$r = spip_query("SELECT id_mot, titre, texte, descriptif FROM spip_mots WHERE " . $GLOBALS['glossaire_groupes_type']);
	// compatibilite SPIP 1.92
	$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
	// protection des liens SPIP
	if (strpos($texte, '[')!==false) 
		$texte = preg_replace_callback(',(\[([^][]*)->(>?)([^]]*)\]),msS', 'glossaire_echappe_balises_callback', $texte);
	
	// parcours de tous les mots, sauf celui qui peut faire partie du contexte (par ex : /spip.php?mot5)
	while($mot = $fetch($r)) if ($mot['id_mot']<>$GLOBALS['id_mot']) {
		// prendre en compte les formes du mot : architrave/architraves
		$a = explode('/', $titre = extraire_multi($mot['titre']));
		$id = $mot['id_mot'];
		$les_mots = array();
		foreach ($a as $m) {
			$u = charset2unicode($m = trim($m));
			// liste de toutes les formes possible du mot clef en question.
			// normalement, y a pas besoin de : html_entity_decode($m)
			$les_mots = array_merge($les_mots, array(
				htmlentities($m), $u, unicode_to_utf_8($u), unicode2charset($u), $m));
		}
		$les_mots = array_unique($les_mots);
		array_walk($les_mots, 'cs_preg_quote');
		$les_mots = join('|', $les_mots);
		if(preg_match(",\W($les_mots)\W,i", $texte)) {
			// prudence 1 : on protege TOUTES les balises contenant le mot en question
			$texte = preg_replace_callback(",(<[^>]*($les_mots)[^>]*>),Umsi", 'glossaire_echappe_balises_callback', $texte);
			// prudence 2 : on neutralise le mot si on trouve un accent html juste avant ou apres
			$texte = preg_replace_callback(",(&($accents);($les_mots)),i", 'glossaire_echappe_balises_callback', $texte);
			$texte = preg_replace_callback(",(($les_mots)&($accents);),i", 'glossaire_echappe_balises_callback', $texte);
			// on y va !
			$lien = generer_url_mot($id);
			$mem = $GLOBALS['toujours_paragrapher'];
			$GLOBALS['toujours_paragrapher'] = false;
			$definition = nl2br(trim(strlen($mot['descriptif'])?$mot['descriptif']:$mot['texte']));
			// on retire les notes avant propre()
			$definition = safehtml(propre(preg_replace(', *\[\[(.*?)\]\],msS', '', $definition)));
			$GLOBALS['toujours_paragrapher'] = $mem;
			$table1[$id] = "<a name=\"mot$id\" href=\"$lien\" class=\"cs_glossaire\"><span class=\"gl_mot\">";
			$table2[$id] = defined('_GLOSSAIRE_JS')
				?'</span><span class="gl_js" title="'.htmlspecialchars($titre).'"></span><span title="'.htmlspecialchars($definition).'"></span></a>'
				:"</span><span class=\"gl_dl\"><span class=\"gl_dt\">$titre</span><span class=\"gl_dd\">$definition</span></span></a>";
			// a chaque mot reconnu, on pose une balise temporaire	
			$texte = preg_replace(",(\W)($les_mots)(\W),i", "\\1@@GLOSS\\2#$id@@\\3", $texte, $limit);
		}
	}
	// remplacement final des balises posees ci-dessus
	$texte = preg_replace(",@@GLOSS(.*?)#([0-9]+)@@,e", '"$table1[\\2]\\1$table2[\\2]"', $texte);
	return echappe_retour($texte, 'GLOSS');
}

function cs_glossaire($texte) {
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|a', 'cs_rempl_glossaire', $texte);
}

// liste des accents (sans casse)
function cs_glossaire_accents() { return '#(19[2-9]|2[023][0-9]|21[0-46-9]|24[0-689]|25[0-4]|33[89]|35[23]|376)||a(acute|circ|elig|grave|ring|tilde|uml)|ccedil|e(acute|circ|grave|th|uml)|i(acute|circ|grave|uml)|ntilde|o(acute|circ|elig|grave|slash|tilde|uml)|s(caron|zlig)|thorn|u(acute|circ|grave|uml)|y(acute|uml)';
}

?>