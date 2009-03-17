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


	include_spip('base/lettres');
	include_spip('inc/lettres_filtres');
	include_spip('inc/lettres_classes');
	include_spip('inc/lettres_pipelines');
	include_spip('public/lettres_balises');
	include_spip('public/lettres_boucles');
	include_spip('facteur_fonctions');


	function lettres_verifier_validite_email($email) {
		if (preg_match("/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i", $email))
			return false;
		return ereg("^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$", $email);
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


?>