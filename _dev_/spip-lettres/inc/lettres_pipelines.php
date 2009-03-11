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


	function lettres_declarer_tables_objets_surnoms($surnoms) {
		$surnoms['lettre'] = 'lettres';
		$surnoms['abonne'] = 'abonnes';
		$surnoms['theme'] = 'themes';
		return $surnoms;
	}
	
	
	function lettres_rechercher_liste_des_champs($tables) {
		$tables['lettre']['titre']		= 8;
		$tables['lettre']['descriptif']	= 4;
		$tables['lettre']['texte']		= 2;
		$tables['lettre']['ps']			= 1;
		$tables['abonne']['email']		= 8;
		$tables['abonne']['nom']		= 4;
		$tables['theme']['titre']		= 8;
		return $tables;
	}


	function lettres_rechercher_liste_des_jointures($tables) {
		return $tables;
	}


	function lettres_tester_rubrique_vide($flux) {
		$flux['data']+= sql_countsel('spip_lettres', 'id_rubrique='.$flux['args']['id_rubrique']);
		return $flux;
	}


	function lettres_calculer_rubriques($flux) {
		$r = sql_select("rub.id_rubrique AS id, max(fille.date) AS date_h", "spip_rubriques AS rub, spip_lettres AS fille", "rub.id_rubrique = fille.id_rubrique AND rub.date_tmp <= fille.date AND fille.statut='envoyee' ", "rub.id_rubrique");
		while ($row = sql_fetch($r))
		  sql_updateq('spip_rubriques', array('statut_tmp'=>'publie', 'date_tmp'=>$row['date_h']), "id_rubrique=".$row['id']);
		return $flux;
	}


	function lettres_trig_propager_les_secteurs($flux) {
		$r = sql_select("fille.id_lettre AS id, maman.id_secteur AS secteur", "spip_lettres AS fille, spip_rubriques AS maman", "fille.id_rubrique = maman.id_rubrique AND fille.id_secteur <> maman.id_secteur");
		while ($row = sql_fetch($r))
			sql_update("spip_lettres", array("id_secteur" => $row['secteur']), "id_lettre=".$row['id']);
		return $flux;
	}


	function lettres_calculer_langues_rubriques($flux) {
		// lettres
		$s = sql_select("fils.id_lettre AS id_lettre, mere.lang AS lang", "spip_lettres AS fils, spip_rubriques AS mere", "fils.id_rubrique = mere.id_rubrique AND fils.langue_choisie != 'oui' AND (fils.lang='' OR mere.lang<>'') AND mere.lang<>fils.lang");
		while ($row = sql_fetch($s)) {
			$id_lettre = $row['id_lettre'];
			sql_updateq('spip_lettres', array("lang"=> $row['lang'], 'langue_choisie'=>'non'), "id_lettre=$id_lettre");
		}
		// themes
		$s = sql_select("fils.id_theme AS id_theme, mere.lang AS lang", "spip_themes AS fils, spip_rubriques AS mere", "fils.id_rubrique = mere.id_rubrique AND (fils.lang='' OR mere.lang<>'') AND mere.lang<>fils.lang");
		while ($row = sql_fetch($s)) {
			$id_theme = $row['id_theme'];
			sql_updateq('spip_themes', array("lang"=> $row['lang']), "id_theme=$id_theme");
		}
		return $flux;
	}


	function lettres_contenu_naviguer($flux) {
		global $spip_lang_right;
		if (autoriser('voir', 'lettres')) {
			$id_rubrique = $flux['args']['id_rubrique'];
			// lettres
			if ($id_rubrique) {
				$flux['data'].= afficher_objets('lettre', _T('lettresprive:toutes_lettres_rubrique'), array('FROM' => 'spip_lettres', 'WHERE' => 'id_rubrique='.intval($id_rubrique), 'ORDER BY' => 'maj DESC'));
				$flux['data'].= icone_inline(_T('lettresprive:creer_nouvelle_lettre'), generer_url_ecrire("lettres_edit", "id_rubrique=$id_rubrique"), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/lettre-24.png',"creer.gif", $spip_lang_right);
				$flux['data'].= '<br class="nettoyeur" />';
			}
			// abonnés
			$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($id_rubrique);
			$rubriques_virgules = implode(',', $rubriques);
			$abonnes = array();
			$res = sql_select('id_abonne', 'spip_abonnes_rubriques', 'id_rubrique IN ('.$rubriques_virgules.')');
			while ($arr = sql_fetch($res))
				$abonnes[] = $arr['id_abonne'];
			$abonnes_virgules = implode(',', $abonnes);
			$flux['data'].= afficher_objets('abonne', _T('lettresprive:tous_abonnes_rubrique'), array('FROM' => 'spip_abonnes', 'WHERE' => 'id_abonne IN ('.$abonnes_virgules.')', 'ORDER BY' => 'maj DESC'));
			$flux['data'].= icone_inline(_T('lettresprive:ajouter_abonne'), generer_url_ecrire("abonnes_edit", "id_rubrique=$id_rubrique"), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/abonne.png',"creer.gif", $spip_lang_right);
/*
			$flux['data'].= icone_inline(_T('lettresprive:import_abonnes'), generer_url_ecrire("naviguer_import","id_rubrique=$id_rubrique"), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/import.png', "rien.gif", $spip_lang_right);
			if (sql_count($res)) {
				$flux['data'].= icone_inline(_T('lettresprive:export_abonnes'), generer_url_ecrire("naviguer_export","id_rubrique=$id_rubrique"), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/export.png', "rien.gif", $spip_lang_right);
				$flux['data'].= icone_inline(_T('lettresprive:purge_abonnes'), generer_url_ecrire("naviguer_purge","id_rubrique=$id_rubrique"), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/purge.png', "rien.gif", $spip_lang_right);
			}
*/			$flux['data'].= '<br class="nettoyeur" />';
		}
		return $flux;
	}
	
	
	function lettres_editer_contenu_objet($flux){
		if ($flux['args']['type'] == 'groupe_mot'){
			// ajouter l'input sur les lettres
			$checked = in_array('lettres', $flux['args']['contexte']['tables_liees']);
			$checked = $checked ? ' checked="checked"' : '';
			$input = '<div class="choix"><input type="checkbox" class="checkbox" name="tables_liees&#91;&#93;" value="lettres" id="lettres"'.$checked.' /><label for="lettres">'._T('lettresprive:item_mots_cles_association_lettres').'</label></div>';
			$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->", $flux['data']);
		}
		return $flux;
	}


	function lettres_libelle_association_mots($libelles){
		$libelles['lettres'] = 'lettresprive:lettres';
		return $libelles;
	}


?>