<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// Envoyer une requete a un serveur d'orthographe
//
// http://doc.spip.org/@post_ortho
function post_ortho($texte, $lang) {
	//$url="http://tech-nova.fr/GSpellerServer/spell.php?lang=$lang";
	//$url="https://www.google.com/tbproxy/spell?lang=$lang";
	//$url="http://orangoo.com/labs/spellchecker/?lang=$lang";
	//$url="http://localhost/GSpellerServer/spell.php?lang=$lang";
	$url="http://localhost/spip/?page=spell&lang=$lang";
//error_log("ORTHO : post '$url' '$texte'");
	$xml='<?xml version="1.0" encoding="utf-8" ?>
<spellrequest textalreadyclipped="0" ignoredups="0" ignoredigits="1" ignoreallcaps="1"><text>'.$texte.'</text></spellrequest>';

	$r = recuperer_page($url, false, false, 1048576, $xml);

//error_log("ORTHO : post => '$r'");

	return $r;
}

//
// Gestion du dictionnaire local
//
// http://doc.spip.org/@suggerer_dico_ortho
function suggerer_dico_ortho(&$mots, $lang) {
	$result = spip_query("SELECT mot FROM spip_ortho_dico WHERE lang=" . spip_abstract_quote($lang) . " AND mot IN (".join(", ", array_map('spip_abstract_quote', $mots)).")");

	$mots = array_flip($mots);
	$bons = array();
	if (isset($mots[''])) unset($mots['']);
	while ($row = spip_fetch_array($result)) {
		$mot = $row['mot'];
		if (isset($mots[$mot])) {
			unset($mots[$mot]);
			$bons[] = $mot;
		}
	}

	if (count($mots)) $mots = array_flip($mots);
	else $mots = array();
	return $bons;
}

// http://doc.spip.org/@ajouter_dico_ortho
function ajouter_dico_ortho($mot, $lang) {
	global $connect_id_auteur;

	spip_query("INSERT IGNORE INTO spip_ortho_dico (lang, mot, id_auteur)  VALUES (" . spip_abstract_quote($lang) . ", " . spip_abstract_quote($mot) . ", $connect_id_auteur)");

}

// http://doc.spip.org/@supprimer_dico_ortho
function supprimer_dico_ortho($mot, $lang) {
	spip_query("DELETE FROM spip_ortho_dico WHERE lang=" . spip_abstract_quote($lang) . " AND mot=" . spip_abstract_quote($mot));

}

// http://doc.spip.org/@gerer_dico_ortho
function gerer_dico_ortho($lang) {
	global $ajout_ortho, $supp_ortho;
	if ($mot = strval($ajout_ortho)) {
		ajouter_dico_ortho($mot, $lang);
	}
	if ($mot = strval($supp_ortho)) {
		supprimer_dico_ortho($mot, $lang);
	}
}


//
// Gestion du cache de corrections
//
// http://doc.spip.org/@suggerer_cache_ortho
function suggerer_cache_ortho(&$mots, $lang) {
	global $duree_cache_ortho;

	$result = spip_query("SELECT mot, ok, suggest FROM spip_ortho_cache WHERE lang=" . spip_abstract_quote($lang) . " AND mot IN (".join(", ", array_map('spip_abstract_quote', $mots)).") AND maj > FROM_UNIXTIME(".(time() - $duree_cache_ortho).")");

	
	$mots = array_flip($mots);
	$suggest = array();
	if (isset($mots[''])) unset($mots['']);
	while ($row = spip_fetch_array($result)) {
		$mot = $row['mot'];
		if (isset($mots[$mot])) {
			unset($mots[$mot]);
			if (!$row['ok']) {
				if (strlen($row['suggest']))
					$suggest[$mot] = explode(",", $row['suggest']);
				else
					$suggest[$mot] = array();
			}
		}
	}
	if (count($mots)) $mots = array_flip($mots);
	else $mots = array();
	return $suggest;
}

// http://doc.spip.org/@ajouter_cache_ortho
function ajouter_cache_ortho($tous, $mauvais, $lang) {
	global $duree_cache_ortho;

	$values = array();
	$lang = spip_abstract_quote($lang);
	if (count($mauvais)) {
		foreach ($mauvais as $mot => $suggest) {
			$values[] = "($lang, " . spip_abstract_quote($mot) . ", 0, ".spip_abstract_quote(join(",", $suggest)).")";
		}
	}
	if (count($tous)) {
		foreach ($tous as $mot) {
			if (!isset($mauvais[$mot]))
				$values[] = "($lang, " . spip_abstract_quote($mot) . ", 1, '')";
		}
	}
	if (count($values)) {
		spip_query("DELETE FROM spip_ortho_cache WHERE maj < FROM_UNIXTIME(".(time() - $duree_cache_ortho).")");

		spip_query("INSERT IGNORE INTO spip_ortho_cache (lang, mot, ok, suggest) VALUES ".join(", ", $values));

	}
}


//
// Cette fonction doit etre appelee pour reecrire le texte en utf-8 "propre"
//
// http://doc.spip.org/@preparer_ortho
function preparer_ortho($texte, $lang) {
	include_spip('inc/charsets');

	$charset = $GLOBALS['meta']['charset'];

	if ($charset == 'utf-8')
		return unicode_to_utf_8(html2unicode($texte));
	else
		return unicode_to_utf_8(html2unicode(charset2unicode($texte, $charset, true)));
}

// http://doc.spip.org/@afficher_ortho
function afficher_ortho($texte) {
	$charset = $GLOBALS['meta']['charset'];
	if ($charset == 'utf-8') return $texte;

	if (!is_array($texte)) return charset2unicode($texte, 'utf-8');
	foreach ($texte as $key => $val) {
		$texte[$key] = afficher_ortho($val);
	}
	return $texte;
}

//
// Cette fonction envoie le texte prepare a un serveur d'orthographe
// et retourne un tableau de mots mal orthographies associes chacun a un tableau de mots suggeres
//
// http://doc.spip.org/@corriger_ortho
function corriger_ortho($texte, $lang, $charset = 'AUTO') {
	include_spip('inc/charsets');
	include_spip("inc/indexation");
	include_spip('inc/filtres');

	$texte = preg_replace(',<code>.*?</code>,is', '', $texte);
	$texte = preg_replace(',<cadre>.*?</cadre>,is', '', $texte);
	$texte = preg_replace(',\[([^][]*)->([^][]*)\],is', '\\1', $texte);
	$texte = supprimer_tags($texte);

	$texte = " ".$texte." ";
	
	// Virer les caracteres non-alphanumeriques
	if (test_pcre_unicode()) {
		$texte = preg_replace(',[^-\''.pcre_lettres_unicode().']+,us', ' ', $texte);
	}
	else {
		// Ici bidouilles si PCRE en mode UTF-8 ne fonctionne pas correctement ...
		// Caracteres non-alphanumeriques de la plage latin-1 + saloperies non-conformes
		$texte = preg_replace(',\xC2[\x80-\xBF],', ' ', $texte);
		// Poncutation etendue (unicode)
		$texte = preg_replace(",".plage_punct_unicode().",", ' ', $texte);
		// Caracteres ASCII non-alphanumeriques
		$texte = preg_replace(",[^-a-zA-Z0-9\x80-\xFF']+,", ' ', $texte);
	}
	$texte = preg_replace(', [-\']+,', ' ', $texte); // tirets de typo
	$texte = preg_replace(',\' ,', ' ', $texte); // apostrophes utilisees comme guillemets

	//echo htmlspecialchars($texte)."<br>";

	// Virer les mots contenant au moins un chiffre
	$texte = preg_replace(', ([^ ]*\d[^ ]* )+,', ' ', $texte);

	// Melanger les mots
	$mots = preg_split(', +,', $texte);
	sort($mots);
	$mots = array_unique($mots);

	// 1. Enlever les mots du dico local
	$bons = suggerer_dico_ortho($mots, $lang);

	// 2. Enlever les mots du cache local
	$result_cache = suggerer_cache_ortho($mots, $lang);

	// 3. Envoyer les mots restants a un serveur
	$xml = post_ortho($texte, $lang);
	if(preg_match('!<spellresult error=.0..*?>(.*)</spellresult>!s', $xml, $r)) {
		$body= $r[1];
	} else {
		return array(
			'erreur' => $xml,
			'bons' => $bons,
			'mauvais' => $mauvais
		);
	}

	preg_match_all('!<c o="(\d*)" l="(\d*)" s="(\d*)">(.*?)</c>!', $body, $r);
	$mauvais = array();
	for($i=0; $i<count($r[0]); $i++) {
		$mot=mon_substr($texte, $r[1][$i], $r[2][$i]);
		$mauvais[$mot]= explode("\t", $r[4][$i]);
	}
//error_log("MAUVAIS : ".var_export($mauvais, 1));
	if (!$erreur) ajouter_cache_ortho($mots, $mauvais, $lang);

	// Retour a l'envoyeur
	$mauvais = array_merge($result_cache, $mauvais);
	$result = array(
		'bons' => $bons,
		'mauvais' => $mauvais
	);

	if ($erreur) $result['erreur'] = $erreur;
	return $result;
}

function mon_substr($s, $o, $l) {
	if(init_mb_string()) {
		return mb_substr($s, $o, $l);
	}

	$ss=strlen($s);

	$delta=0;
	for($i=0, $j=0; $j<$o && $i<$ss; $i++) {
		if(ord($s{$i})>=192) {
			$delta++;
		} else {
			$j++;
			$delta=0;
		}
	}
	if($i==$ss) return $s;
	$debut=$i-$delta;

	for($j=0; $j<$l && $i<$ss; $i++) {
		if(ord($s{$i})<192) {
			$j++;
		}
	}
	if($i==$ss) return substr($s, $debut);
	return substr($s, $debut, $i-$debut);
}

//
// Fonctions d'affichage HTML
//

// http://doc.spip.org/@panneau_ortho
function panneau_ortho($ortho_result) {
	global $id_suggest;

	$id_suggest = array();
	$i = 1;

	$mauvais = $ortho_result['mauvais'];
	$bons = $ortho_result['bons'];
	if (!count($mauvais) && !count($bons)) return;
	ksort($mauvais);

	echo "<script type='text/javascript'><!--
	var curr_suggest = null;
	// http://doc.spip.org/@suggest
	function suggest(id) {
		var menu_box;
		if (curr_suggest)
			document.getElementById('suggest' + curr_suggest).className = 'suggest-inactif';
		if (1 || id!=curr_suggest) {
			document.getElementById('suggest' + id).className = 'suggest-actif';
			curr_suggest = id;
		}
		else curr_suggest = null;
		menu_box = document.getElementById('select_ortho');
		if (menu_box.length > id) menu_box.selectedIndex = id;
	}";
	echo "//--></script>";

	echo "<form class='form-ortho verdana2' action='' method='get'>\n";
	echo "<select name='select_ortho' id='select_ortho' onChange='suggest(this.selectedIndex);'>\n";
	echo "<option value='0'>... "._T('ortho_mots_a_corriger')." ...</option>\n";
	foreach ($mauvais as $mot => $suggest) {
		$id = $id_suggest[$mot] = "$i";
		$i++;
		$mot_html = afficher_ortho($mot);
		echo "<option value='$id'>$mot_html</option>\n";
	}
	foreach ($bons as $mot) {
		$id = $id_suggest[$mot] = "$i";
		$i++;
	}
	echo "</select>\n";
	echo "</form>\n";
	// Mots mal orthographies :
	// liste des suggestions plus lien pour ajouter au dico
	foreach ($mauvais as $mot => $suggest) {
		$id = $id_suggest[$mot];
		$mot_html = afficher_ortho($mot);
		echo "<div class='suggest-inactif' id='suggest$id'>";
		echo "<span class='ortho'>$mot_html</span>\n";
		echo "<div class='detail'>\n";
		if (is_array($suggest) && count($suggest)) {
			echo "<ul>\n";
			$i = 0;
			foreach ($suggest as $sug) {
				if (++$i > 12) {
					echo "<li><i>(...)</i></li>\n";
					break;
				}
				echo "<li>".typo(afficher_ortho($sug))."</li>\n";
			}
			echo "</ul>\n";
		}
		else {
			echo "<i>"._T('ortho_aucune_suggestion')."</i>";
		}
		echo "<br />";
		$lien = parametre_url(self(), 'supp_ortho', '');
		$lien = parametre_url($lien, 'ajout_ortho', $mot);
		icone_horizontale(_T('ortho_ajouter_ce_mot'), $lien, "ortho-24.gif", "creer.gif");
		echo "</div>\n";
		echo "</div>\n\n";
	}
	// Mots trouves dans le dico :
	// message plus lien pour retirer du dico
	foreach ($bons as $mot) {
		$id = $id_suggest[$mot];
		$mot_html = afficher_ortho($mot);
		echo "<div class='suggest-inactif' id='suggest$id'>";
		echo "<span class='ortho-dico'>$mot_html</span>";
		echo "<div class='detail'>\n";
		echo "<i>"._T('ortho_ce_mot_connu')."</i>";
		echo "<br />";
		$lien = parametre_url(self(), 'ajout_ortho', '');
		$lien = parametre_url($lien, 'supp_ortho', $mot);
		icone_horizontale(_T('ortho_supprimer_ce_mot'), $lien, "ortho-24.gif", "supprimer.gif");
		echo "</div>\n";
		echo "</div>\n";
	}
}


// http://doc.spip.org/@souligner_match_ortho
function souligner_match_ortho(&$texte, $cherche, $remplace) {
	// Eviter les &mdash;, etc.
	if ($cherche{0} == '&' AND $cherche{strlen($cherche) - 1} == ';') return;

	if ($cherche{0} == '>') 
		$texte = str_replace($cherche, $remplace, $texte);
	else {
		// Ne pas remplacer a l'interieur des tags HTML
		$table = explode($cherche, $texte);
		unset($avant);
		$texte = '';
		foreach ($table as $s) {
			if (!isset($avant)) {
				$avant = $s;
				continue;
			}
			$ok = true;
			$texte .= $avant;
			// Detecter si le match a eu lieu dans un tag HTML
			if (is_int($deb_tag = strrpos($texte, '<'))) {
				if (strrpos($texte, '>') <= $deb_tag)
					$ok = false;
			}
			if ($ok) $texte .= $remplace;
			else $texte .= $cherche;
			$avant = $s;
		}
		$texte .= $avant;
	}
}

// http://doc.spip.org/@souligner_ortho
function souligner_ortho($texte, $lang, $ortho_result) {
	global $id_suggest;
	$vu = array();

	$mauvais = $ortho_result['mauvais'];
	$bons = $ortho_result['bons'];

	// Neutraliser l'apostrophe unicode pour surligner correctement les fautes
	$texte = " ".str_replace("\xE2\x80\x99", "'", $texte)." ";
	// Chercher et remplacer les mots un par un
	$delim = '[^-\''.pcre_lettres_unicode().']';
	foreach ($mauvais as $mot => $suggest) {
		$pattern = ",$delim".$mot."$delim,us";
		// Recuperer les occurences du mot dans le texte
		if (preg_match_all($pattern, $texte, $regs, PREG_SET_ORDER)) {
			$id = $id_suggest[$mot];
			$mot_html = afficher_ortho($mot);
			foreach ($regs as $r) {
				if ($vu[$cherche = $r[0]]) continue;
				$vu[$cherche] = 1;
				$html = "<a class='ortho' onclick=\"suggest($id);return false;\" href=''>$mot_html</a>";
				$remplace = str_replace($mot, $html, $cherche);
				souligner_match_ortho($texte, $cherche, $remplace);
			}
		}
	}
	foreach ($bons as $mot) {
		$pattern = ",$delim".$mot."$delim,us";
		// Recuperer les occurences du mot dans le texte
		if (preg_match_all($pattern, $texte, $regs, PREG_SET_ORDER)) {
			$id = $id_suggest[$mot];
			$mot_html = afficher_ortho($mot);
			foreach ($regs as $r) {
				if ($vu[$cherche = $r[0]]) continue;
				$vu[$cherche] = 1;
				$html = "<a class='ortho-dico' onclick=\"suggest($id);return false;\" href=''>$mot_html</a>";
				$remplace = str_replace($mot, $html, $cherche);
				souligner_match_ortho($texte, $cherche, $remplace);
			}
		}
	}
	
	$texte = preg_replace(',(^ | $),', '', $texte);
	return $texte;
}

// http://doc.spip.org/@init_ortho
function init_ortho() {
	global $duree_cache_ortho, $duree_cache_miroirs_ortho;
 
 	$duree_cache_ortho = 7 * 24 * 3600;
	$duree_cache_miroirs_ortho = 24 * 3600;
}

init_ortho();

?>
