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
		if (autoriser('voir', 'lettres')) {
			$id_rubrique = $flux['args']['id_rubrique'];
			$flux['data'].= afficher_objets('lettre', _T('lettresprive:toutes_lettres_rubrique'), array('FROM' => 'spip_lettres', 'WHERE' => 'id_rubrique='.intval($id_rubrique), 'ORDER BY' => 'maj DESC'));
			$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($id_rubrique);
			$rubriques_virgules = implode(',', $rubriques);
			$flux['data'].= afficher_objets('abonne', _T('lettresprive:tous_abonnes_rubrique'), array('FROM' => 'spip_abonnes AS A, spip_abonnes_rubriques AS AR', 'WHERE' => 'A.id_abonne=AR.id_abonne AND AR.id_rubrique IN ('.$rubriques_virgules.')', 'ORDER BY' => 'AR.date_abonnement DESC', 'GROUP BY' => 'A.id_abonne', 'LIMIT' => '100'));
			$flux['data'].= "<div align='right'>";
			$flux['data'].= icone(_T('lettresprive:creer_nouvelle_lettre'), generer_url_ecrire("lettres_edit", "id_rubrique=$id_rubrique"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "creer.gif", '', 'non');
			$flux['data'].= "</div><p>";
#			$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($id_rubrique);
#			$rubriques_virgules = implode(',', $rubriques);
#			$flux['data'].= lettres_afficher_abonnes(_T('lettresprive:tous_abonnes_rubrique'), array("FROM" => 'spip_abonnes AS A, spip_abonnes_rubriques AS AR', "WHERE" => "A.id_abonne=AR.id_abonne AND AR.id_rubrique IN ($rubriques_virgules)", 'ORDER BY' => "AR.date_abonnement DESC", 'GROUP BY' => 'A.id_abonne', 'LIMIT' => '100'), $id_rubrique);
#			$flux['data'].= "<div align='$spip_lang_right'>";
#			$flux['data'].= icone(_T('lettresprive:ajouter_abonne'), generer_url_ecrire('abonnes_edit', 'id_rubrique='.$id_rubrique), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "creer.gif", '', 'non');
#			$flux['data'].= "</div><p>";
		}
		return $flux;
	}
	
	
	function lettres_affiche_milieu($flux) { 
		switch($flux['args']['exec']) {
			case 'naviguer':
#				if (autoriser('configurer', 'lettres')) $flux['data'].= lettres_afficher_cron($flux['args']['id_rubrique']);
				break;
		}
		return $flux;
	}


?>