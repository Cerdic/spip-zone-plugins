<?php


/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *  
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *  
 **/

include_spip('inc/lettres_filtres');
include_spip('classes/lettre');
include_spip('inc/lettres_pipelines');
include_spip('public/lettres_balises');
include_spip('public/lettres_boucles');
include_spip('facteur_fonctions');
	
function lettres_verifier_validite_email($email) {
	include_spip('inc/filtres');
	return email_valide($email);
}

function lettres_tester_parmi_desabonnes($email) {
	$test = sql_countsel('spip_desabonnes', 'email='.sql_quote($email));
	return $test;
}


function calculer_url_lettre($id_lettre, $texte, $ancre) {
	$lien = generer_url_lettre($id_lettre).$ancre;
	if (!$texte) {
		$texte = sql_getfetsel('titre', 'spip_lettres', 'id_lettre='.intval($id_lettre));
	}
	return array($lien, 'spip_in', $texte);
}


function generer_url_lettre($id_lettre, $format='', $preview=false) {
	$var_mode='';
	$chaine_format='';
	
	if ($preview)
		$var_mode = '&var_mode=preview';
	if (!empty($format))
		$chaine_format = '&format='.$format;
	return generer_url_public('lettre', 'id_lettre='.$id_lettre.$chaine_format.$var_mode);
}


function lettres_recuperer_toutes_les_rubriques_parentes($id_rubrique) {
	$rubriques = array();
	$rubriques[] = $id_rubrique;
	while (1) {
		$id_parent = lettres_recuperer_la_rubrique_parente($id_rubrique);
		$rubriques[] = $id_parent;
		$id_rubrique = $id_parent;
		if (!$id_parent)
			break;
	}
	return $rubriques;
}


function lettres_recuperer_la_rubrique_parente($id_rubrique) {
	$id_parent=0;
	if ($id_rubrique)
		$id_parent = sql_getfetsel('id_parent', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique));
	return intval($id_parent);
}

// Traiter les raccourcis au moment de l'envoi, pour plus grande personnalisation du mail
// %%CHAMP%%
// %%CHAMP|filtre_si_existant ou texte_sinon%%
// %%[texte avant(#CHAMP|filtre_si_existant ou texte_sinon) texte après]%%
function lettres_remplacer_raccourci($raccourci, $valeur, $texte) {
	$texte = str_replace('&nbsp;!', '!', $texte);
	$texte = str_replace(' !', '!', $texte);
	
	$$raccourci = strtoupper($raccourci);
	
	// i: insensible à la casse, s : capturer les fins de lignes. U : ungreedy
	$motif_simple = "`%%$raccourci%%`i";
	$motif_complexe = "`%%$raccourci\|([^%]+)%%`i";
	$motif_calcul = "`%%\[([^\]]*)\(#$raccourci(\|[^)]+)?\)(.*)\]%%`isU";

	if (preg_match_all($motif_calcul, $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $r) {
			$avant = $r[1];
			$pipe = $r[2];

			$apres = $r[3];
			$cherche = $r[0];
			
			// spip_log ("CALCUL motif ($motif_calcul) a trouve ($cherche) pour raccourci ($raccourci) avec valeur=($valeur), avant=($avant), pipe=($pipe), apres=($apres)", "lettres_raccourcis");

			if ($pipe) {
				$filtre = trim($sinon=substr($pipe,1));
				if (function_exists($filtre))
					$remplace = $filtre($valeur);
				else
					$remplace = ($valeur ? $valeur : $sinon); // homogénéïté avec motif_complexe, surtout utile pour faire un sinon alors qu'il n'y a pas d'arguments pour les filtres
			}
			else
				$remplace = $valeur;

			if ($remplace)
				$texte = str_replace($cherche, $avant.$remplace.$apres, $texte);
			else $texte = str_replace($cherche, "", $texte);
		}
	}

	if (preg_match_all($motif_complexe, $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $r) {
			$sinon = $r[1];
			$cherche = $r[0];
			if (!empty($valeur))
				$remplace = $valeur;
			else
				$remplace = $sinon;
			$texte = str_replace($cherche, $remplace, $texte);
		}
	}
	if (preg_match_all($motif_simple, $texte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $r) {
			$cherche = $r[0];
			$remplace = $valeur;
			$texte = str_replace ($cherche, $remplace, $texte);
		}
	}
	return $texte;
}


function lettres_rubrique_autorisee($id_rubrique) {
	return sql_countsel('spip_themes', 'id_rubrique='.intval($id_rubrique));
}


function redirection_clic($id_clic) {
	$verification_clic = sql_select('url', 'spip_clics', 'id_clic='.intval($id_clic));
	if (sql_count($verification_clic) == 1) {
		$url = sql_fetch($verification_clic);
		$redirection = $url['url'];
	} else {
		$redirection = $GLOBALS['meta']['adresse_site'];
	}
	return $redirection;
}


//
// thèmes et thème par défaut, pour le privé et les squelettes
//
function lettres_un_seul_theme() {
	return sql_countsel ("spip_themes") == 1;
}

function lettres_nombre_themes() {
	return sql_countsel ("spip_themes");
}

// id de la rubrique ou -1 si il n'y a pas de thème par défaut
function lettres_rubrique_theme_par_defaut () {
	if (lettres_un_seul_theme())
		return sql_getfetsel ("id_rubrique", "spip_themes");
	else return $GLOBALS['meta']['spip_lettres_abonnement_par_defaut'];	
};

// titre du thème par défaut
function lettres_titre_theme_par_defaut () {
	if ($GLOBALS['meta']['spip_lettres_abonnement_par_defaut'] > 0)
		return sql_getfetsel ("TH.titre", "spip_themes AS TH LEFT JOIN spip_rubriques AS RUB ON RUB.id_rubrique=TH.id_rubrique", "RUB.id_rubrique=".$GLOBALS['meta']['spip_lettres_abonnement_par_defaut']);
	else if ($GLOBALS['meta']['spip_lettres_abonnement_par_defaut'] == -1)
		return _T('lettresprive:aucun_theme_selectionne');
	else
		return _T('lettres:tout_le_site');
};

function styler_pournavigateur ($html) {
	return str_replace ('pouremail', 'invisiblepournavigateur', $html);
};

// pour le format texte les liens html sont transformés de manière à avoir à la fois le texte
// et le lien clicable à la suite, entre parenthèse.
// Si le texte du lien est déjà une url ou y ressemble fort, on ne met que l'url
// Si c'est une url relative, on ajoute l'adresse du site avant
function prepare_format_texte_lien($matches) {
	if ((strpos(ltrim($matches[2]), 'http:')===0) 
		or (strpos(ltrim($matches[2]), 'www.')===0))
		return $matches[1];
	if (strpos(ltrim($matches[1]),'/')===0)
		 $matches[1] =  $GLOBALS['meta']['adresse_site'].$matches[1];
	return $matches[2]." [ ".$matches[1]." ]";
};


function prepare_format_texte ($html) {
	$pat = "!<a[^>]+href\s*=\s*['\"]([^'\"]*)['\"][^>]*>([^<]*)<\/a>!i";
	return textebrut (preg_replace_callback ($pat, 'prepare_format_texte_lien', $html));
};

?>
