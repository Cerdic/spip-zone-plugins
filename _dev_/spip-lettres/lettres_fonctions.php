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
	include_spip('inc/filtres');
	include_spip('inc/lettres_filtres');
	include_spip('inc/lettres_classes');
	include_spip('inc/lettres_pipelines');
	include_spip('public/lettres_balises');
	include_spip('public/lettres_boucles');
	include_spip('inc/notifications_classes');


	function lettres_afficher_cron($id_rubrique) {
		global $spip_lang_right;
		global $envois_recurrents;
		$cron = '';
		if ($envois_recurrents and $id_rubrique) {
			$cron.= '<form action="'.generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique).'" method="post">';
			if ($_POST['cron_hidden']) {
				@sql_delete('spip_rubriques_crontabs', 'id_rubrique='.intval($id_rubrique));
				if ($_POST['cron'] == 1)
					@sql_replace('spip_rubriques_crontabs', 
								array(
									'id_rubrique' => intval($id_rubrique)
									)
								);
			}
			$test = sql_countsel('spip_rubriques_crontabs', 'id_rubrique='.intval($id_rubrique));
			if (!$test)
				$cron.= debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRES.'/img_pack/cron.png', true, "", bouton_block_invisible('cron')._T('lettresprive:envois_recurrents'));
			else
				$cron.= debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRES.'/img_pack/cron.png', true, "", _T('lettresprive:envois_recurrents'));
			if (!$test)
				$cron.= debut_block_invisible('cron');
			$cron.= '<input type="checkbox" name="cron" id="cron_lettres" value="1"'.($test ? ' checked="checked"' : '').' /> ';
			$cron.= '<input type="hidden" name="cron_hidden" value="1" /> ';
			$cron.= '<label for="cron_lettres">'._T('lettresprive:activer_les_envois_recurrents_sur_cette_rubrique'). '</label>';
			$cron.= '<div align="'.$spip_lang_right.'"><input type="submit" class="fondl" value="'._T('lettresprive:valider').'" /></div>';
			if (!$test)
				$cron.= fin_block();
			$cron.= fin_cadre_enfonce(true);
			$cron.= '</form>';
		}
		return $cron;
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