<?php

// Outils GLOSSAIRE - 26 mai 2007
// Serieuse refonte et integration au Couteau Suisse : Patrice Vanneufville
// Doc : http://www.spip-contrib.net/?article2206

include_spip('inc/charsets');

// liste des accents (sans casse)
define('_GLOSSAIRE_ACCENTS', '#(19[2-9]|2[023][0-9]|21[0-46-9]|24[0-689]|25[0-4]|33[89]|35[23]|376)||a(?:acute|circ|elig|grave|ring|tilde|uml)|ccedil|e(?:acute|circ|grave|th|uml)|i(?:acute|circ|grave|uml)|ntilde|o(?:acute|circ|elig|grave|slash|tilde|uml)|s(?:caron|zlig)|thorn|u(?:acute|circ|grave|uml)|y(?:acute|uml)');

// on calcule ici la constante _GLOSSAIRE_QUERY, surchargeable dans config/mes_options.php
function glossaire_groupes() {
	$groupes = trim($GLOBALS['glossaire_groupes']);
	if(!strlen($groupes)) return _q('Glossaire');
		else {
			$groupes = explode(':', $groupes);
			foreach($groupes as $i=>$g) $groupes[$i] = _q(trim($g));
			return join(" OR type=", $groupes);
		}
}
@define('_GLOSSAIRE_QUERY', 'SELECT id_mot, titre, texte, descriptif FROM spip_mots WHERE type=' . glossaire_groupes() . ' ORDER BY id_mot ASC');
// TODO : QUERY pour SPIP 2.0

// surcharge possible de cette fonction glossaire_generer_url_dist par : glossaire_generer_url($id_mot, $titre) 
// si elle existe, elle sera utilisee pour generee l'url cliquable des mots trouves
//   exemple pour annuler le clic : function glossaire_generer_url($id_mot, $titre) { return 'javascript:;'; }
function glossaire_generer_url_dist($id_mot) {
	if(defined('_SPIP19300')) 
		return generer_url_entite($id_mot, 'mot'); // depuis SPIP 2.0
		else { charger_generer_url(); return generer_url_mot($id_mot); } // avant SPIP 2.0
}

// compatibilite pour SPIP 1.91
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

// remplace les accents unicode par l'equivalent charset/unicode/html
function glossaire_accents($regexpr) {
	if (strpos($regexpr, '&')===false) return $regexpr;
	return preg_replace_callback(",&#([0-9]+);,", 'glossaire_accents_callback', str_replace('& ','&amp; ',$regexpr));
}

// $matches est un caractere unicode sous forme &#XXX;
// ici on cherche toutes les formes de ce caractere, minuscule ou majuscule : unicode, charset et html
function glossaire_accents_callback($matches) {
	$u = unicode2charset($matches[0]);	// charset
	$u2 = init_mb_string()?mb_strtoupper($u):strtoupper($u);	// charset majuscule
	$u3 = htmlentities($u2, ENT_QUOTES, $GLOBALS['meta']['charset']);	// html majuscule
	$u4 = html2unicode($u3); // unicode majuscule
	$a = array_unique(array($u, $u2, htmlentities($u, ENT_QUOTES, $GLOBALS['meta']['charset']), $u3, $matches[0], $u4));
//	$a = array_unique(array($u, htmlentities($u, ENT_QUOTES, $GLOBALS['meta']['charset']), $matches[0]));
	return '(?:'.join('|', $a).')';
}
function glossaire_echappe_balises_callback($matches) {
 return cs_code_echappement($matches[0], 'GLOSS');
}
function glossaire_echappe_mot_callback($matches) {
 global $gloss_id;
 return "@@GLOSS".cs_code_echappement($matches[0], 'GLOSS')."#{$gloss_id}@@";
}

function glossaire_safe($texte) {
	// on retire les notes avant propre()
	return safehtml(propre(preg_replace(', *\[\[(.*?)\]\],msS', '', nl2br(trim($texte)))));
}

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite|a
function cs_rempl_glossaire($texte) {
	global $gloss_id;
	// mise en static de la table des mots pour eviter d'interrroger la base a chaque fois
	// attention aux besoins de memoire...
	static $glossaire_array;
	if(!isset($glossaire_array)) {
		$glossaire_array = array();
		// compatibilite SPIP 1.92
		$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
		$query = spip_query(_GLOSSAIRE_QUERY);
		while($glossaire_array[] = $fetch($query));
	}
	$limit = defined('_GLOSSAIRE_LIMITE')?_GLOSSAIRE_LIMITE:-1;
	// protection des liens SPIP
	if (strpos($texte, '[')!==false) 
		$texte = preg_replace_callback(',\[[^][]*->>?[^]]*\],msS', 'glossaire_echappe_balises_callback', $texte);
	// parcours de tous les mots, sauf celui qui peut faire partie du contexte (par ex : /spip.php?mot5)
	$mot_contexte=$GLOBALS['contexte']['id_mot']?$GLOBALS['contexte']['id_mot']:_request('id_mot');
	foreach ($glossaire_array as $mot) if (($gloss_id = $mot['id_mot']) <> $mot_contexte) {
		// prendre en compte les formes du mot : architrave/architraves
		$a = explode('/', $titre = extraire_multi($mot['titre']));
		$les_mots = array();
		foreach ($a as $m) $les_mots[] = charset2unicode($m = trim($m));
		$les_mots = array_unique($les_mots);
		array_walk($les_mots, 'cs_preg_quote');
		$les_mots = glossaire_accents(join('|', $les_mots));
		if(preg_match(",\W(?:$les_mots)\W,i", $texte)) {
			// prudence 1 : on protege TOUTES les balises HTML comprenant le mot
			if (strpos($texte, '<')!==false) {
				$texte = preg_replace_callback(",<[^>]*(?:$les_mots)[^>]*>,Ui", 'glossaire_echappe_balises_callback', $texte);
			}
			// prudence 2 : en iso-8859-1, (\W) comprend les accents, mais pas en utf-8... Donc on passe en unicode
			if($GLOBALS['meta']['charset'] != 'iso-8859-1') $texte = charset2unicode($texte);
			// prudence 3 : on neutralise le mot si on trouve un accent (HTML ou unicode) juste avant ou apres
			if (strpos($texte, '&')!==false) {
				$texte = preg_replace_callback(',&(?:'._GLOSSAIRE_ACCENTS.");(?:$les_mots),i", 'glossaire_echappe_balises_callback', $texte);
				$texte = preg_replace_callback(",(?:$les_mots)&(?:"._GLOSSAIRE_ACCENTS.');,i', 'glossaire_echappe_balises_callback', $texte);
			}
			// on y va !
			$lien = function_exists('glossaire_generer_url')
				?$glossaire_generer_url($gloss_id, $titre)
				:glossaire_generer_url_dist($gloss_id);
			$mem = $GLOBALS['toujours_paragrapher'];
			$GLOBALS['toujours_paragrapher'] = false;
			// $definition =strlen($mot['descriptif'])?$mot['descriptif']:$mot['texte'];
			$table1[$gloss_id] = "href='$lien' name='mot$gloss_id"; // name est complete plus tard pour eviter les doublons
			$table2[$gloss_id] = recuperer_fond(
				defined('_GLOSSAIRE_JS')?'fonds/glossaire_js':'fonds/glossaire_css', 
				array('id_mot' => $gloss_id, 'titre' => $titre, 
					'texte' => glossaire_safe($mot['texte']), 
					'descriptif' => glossaire_safe($mot['descriptif'])));
			$GLOBALS['toujours_paragrapher'] = $mem;
			// a chaque mot reconnu, on pose une balise temporaire cryptee
			$texte = preg_replace_callback(",(?<=\W)(?:$les_mots)(?=\W),i", "glossaire_echappe_mot_callback", $texte, $limit);
		}
	}
	// remplacement final des balises posees ci-dessus
	$GLOBALS['i']=0;
	return preg_replace(",@@GLOSS(.*?)#([0-9]+)@@,e", 
		'"<a $table1[\\2]_".$GLOBALS["i"]++."\' class=\'cs_glossaire\'><span class=\'gl_mot\'>\\1</span>$table2[\\2]</a>"', echappe_retour($texte, 'GLOSS'));
}

function cs_glossaire($texte) {
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite|a', 'cs_rempl_glossaire', $texte);
}

?>