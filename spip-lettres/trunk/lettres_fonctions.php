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
		if ($id_rubrique)
			$id_parent = sql_getfetsel('id_parent', 'spip_rubriques', 'id_rubrique='.intval($id_rubrique));
		return intval($id_parent);
	}


	function lettres_remplacer_raccourci($raccourci, $valeur, $texte) {
		$texte = str_replace('&nbsp;!', '!', $texte);
		$texte = str_replace(' !', '!', $texte);
		$motif_complexe = '`%%'.strtoupper($raccourci).'\|([^%]+)%%`';
		$motif_simple = '`%%'.strtoupper($raccourci).'%%`';
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
				$texte = str_replace($cherche, $remplace, $texte);
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
	
	
	if(!function_exists('str_split')) {
		function str_split($text, $split = 1) {
			$array = array();
			for ($i = 0; $i < strlen($text);) {
				$array[] = substr($text, $i, $split);
				$i+= $split;
			}
			return $array;
		}
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
	function prepare_format_texte_lien($matches) {
		if ((strpos(ltrim($matches[2]), 'http:')===0) 
			or (strpos(ltrim($matches[2]), 'www.')===0))
			return $matches[1];
		else return $matches[2]." [ ".$matches[1]." ]";
	};
	function prepare_format_texte ($html) {
		$pat = "!<a[^>]+href\s*=\s*['\"]([^'\"]*)['\"][^>]*>([^<]*)<\/a>!i";
		return textebrut (preg_replace_callback ($pat, 'prepare_format_texte_lien', $html));
	};
	
	/**
 * Filtre copié depuis spip-listes http://zone.spip.org/trac/spip-zone/changeset/49179
 * author: paladin@...
 * Corrige le bug de spip2 (corrigé dans spip3) qui fait que liens [ xxx->n] calculés dans le privé 
 * (comme les lettres le sont au  moment de leur envoi) pointent vers l'adresse privée au lieu de publique
 * J(JLuc)'y ajoute une fonction corrige_liens_publics, à utiliser de préférence dans SPIP2 pour corriger les liens,
 * et qui est redéfinie dans la version pour SPIP 3, de manière à ce que les squelettes ne corrigent plus,
 * sans devoir être immédiatement corrigés, eux, lors du portage du site sous spip3.
 *  
 * Commentaire d'origine :
 *
 * Un filtre pour transformer les URLs relatives
 * à l'espace privé en URLs pour espace public.
 * A appliquer au conteneur, dans le patron,
 * du style : [(#TEXTE|liens_publics)]
 * @version CP-20110629
 * @example [(#TEXTE|liens_publics)]
 * @see http://www.spip.net/fr_article3377.html
 * @param string $texte
 * @return string
 */
 // pour compatibilité des squelettes : ne fait plus rien dans spip3, retirer des squelettes
function corrige_liens_publics ($texte) {
	return $texte;
}
function liens_publics ($texte)
{
	$url_site = $GLOBALS['meta']['adresse_site'];
	
	$replace = array(
			'articles' => 'article',
			'naviguer' => 'rubrique',
			'breves' => 'breve',
			'mots_edit' => 'mot',
			'sites_tous' => 'site',
	);
	
	foreach ($replace as $key => $value)
	{
			if (preg_match_all(',(<a[[:space:]]+[^<>]*href=["\']?' . $url_site . ')'
											   . '/ecrire/\?exec=(' . $key . ')'
											   . '([^<>]*>),imsS', 
											$texte,
											$liens,
											PREG_SET_ORDER))
			{
					foreach ($liens as $lien)
					{
							$to = $lien[1] . '/?page=' . $value . $lien[3];
							$texte = str_ireplace($lien[0], $to, $texte);
					}
			}
	}
	return ($texte);
}
?>