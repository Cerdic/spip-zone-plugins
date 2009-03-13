<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('base/create');
	include_spip('inc/plugin');
	include_spip('inc/indexation');
	include_spip('inc/formulaires_classes');
	include_spip('inc/formulaires_filtres');
	include_spip('inc/formulaires_balises');
	include_spip('lettres_fonctions');


	/**
	 * formulaires_ajouter_boutons
	 *
	 * Ajoute les boutons pour les formulaires dans l'espace privé
	 *
	 * @param array boutons_admin
	 * @return array boutons_admin le même tableau avec des entrées en plus
	 * @author Pierre Basson
	 **/
	function formulaires_ajouter_boutons($boutons_admin) {
		global $connect_statut;

		$boutons_admin['naviguer']->sousmenu['formulaires_tous'] = new Bouton('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', _T('formulairesprive:formulaires'));

		$verif = spip_query('SELECT APP.id_application 
							FROM spip_applications AS APP
							INNER JOIN spip_applicants AS A ON A.id_applicant=APP.id_applicant
							WHERE A.email!=""');
		if (spip_num_rows($verif) > 0)
			$boutons_admin['naviguer']->sousmenu['applications_tous'] = new Bouton('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', _T('formulairesprive:applicants_applications'));

		return $boutons_admin;
	}


	/**
	 * formulaires_header_prive
	 *
	 * @param string texte
	 * @return string texte
	 * @author Pierre Basson
	 **/
	function formulaires_header_prive($texte) {
		formulaires_verifier_base();
		return $texte;
	}


	/**
	 * formulaires_taches_generales_cron
	 *
	 * Ajout des tâches planifiées pour le plugin
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	function formulaires_taches_generales_cron($taches_generales) {
		$taches_generales['formulaires'] = 60 * 10; // toutes les 10 minutes
		return $taches_generales;
	}


	/**
	 * cron_formulaires
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	function cron_formulaires($t) {
		$requete_tous_les_formulaires_en_ligne = 'SELECT id_formulaire FROM spip_formulaires WHERE en_ligne="oui" AND limiter_temps="oui" LIMIT 1';
		$resultat_tous_les_formulaires_en_ligne = spip_query($requete_tous_les_formulaires_en_ligne);
		// la mise à jour sur un formulaire entraine toutes les autres
		list($id_formulaire) = spip_fetch_array($resultat_tous_les_formulaires_en_ligne, SPIP_NUM);
		$formulaire = new formulaire($id_formulaire);
		$formulaire->mettre_a_jour_rubriques();
		return true;
	}


	/**
	 * formulaires_accueil
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_accueil($flux) {
		global $spip_lang_left;

		switch($flux['args']['zone']) {

			case 'encours_accueil' :
				break;

		 	case 'gadget_0minirezo' :
				$flux['data'].= "<td>";
				$flux['data'].= icone_horizontale(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire("formulaires_edit","new=oui"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "creer.gif", false);
				$flux['data'].= "</td>";
				break;

			case 'gadget' :
				$n = spip_num_rows(spip_query("SELECT id_formulaire FROM spip_formulaires LIMIT 1"));
				if ($n) {
					$flux['data'].= "<center><table><tr>";
					$flux['data'].= "<td>";
					$flux['data'].= icone_horizontale(_T('formulairesprive:formulaires'), generer_url_ecrire("formulaires_tous"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "", false);
					$flux['data'].= "</td>";
					$flux['data'].= "</tr></table></center>\n";
				}
				break;

			case 'etat_base' :
				$res = spip_query("SELECT COUNT(*) AS cnt, statut FROM spip_formulaires GROUP BY statut");
				while($row = spip_fetch_array($res)) {
					$var  = 'nb_formulaires_'.$row['statut'];
					$$var = $row['cnt'];
				}
				if ($nb_formulaires_en_attente OR $nb_formulaires_publie OR $nb_formulaires_termine) {
					$flux['data'].= afficher_plus(generer_url_ecrire("formulaires_tous",""))."<b>"._T('formulairesprive:formulaires')."</b>";
					$flux['data'].= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
					$flux['data'].= "<li>"._T("formulairesprive:nb_formulaires").": ".($nb_formulaires_en_attente + $nb_formulaires_publie + $nb_formulaires_termine);
					if ($nb_formulaires_en_attente)	$flux['data'].= "<li>"._T("formulairesprive:nb_formulaires_en_attente").": ".$nb_formulaires_en_attente;
					if ($nb_formulaires_publie)		$flux['data'].= "<li>"._T("formulairesprive:nb_formulaires_publie").": ".$nb_formulaires_publie;
					if ($nb_formulaires_termine)	$flux['data'].= "<li>"._T("formulairesprive:nb_formulaires_termine").": ".$nb_formulaires_termine;
					$flux['data'].= "</ul>";
				}
				break;

		}
		return $flux;
	}


	/**
	 * formulaires_gadgets
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_gadgets($flux) {
		global $spip_lang_left, $couleur_foncee;

		switch($flux['args']['zone']) {

			case 'encours' :
				break;

			case 'raccourcis' :
				$flux['data'].= "<div style='width: 140px; float: $spip_lang_left;'>";
				$flux['data'].= icone_horizontale(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire("formulaires_edit","new=oui".$flux['args']['dans_rub']), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png',"creer.gif", false);
				$flux['data'].= "</div>";
				break;

		}
		return $flux;
	}


	/**
	 * formulaires_raccourcis_naviguer
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_raccourcis_naviguer($flux) {
		$id_rubrique = $flux['args']['id_rubrique'];
		$flux['data'].= icone_horizontale(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire("formulaires_edit","id_rubrique=$id_rubrique&new=oui"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "creer.gif", '', false);
		return $flux;
	}


	/**
	 * formulaires_contenu_naviguer
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_contenu_naviguer($flux) {
		global $spip_lang_right;
		$id_rubrique = $flux['args']['id_rubrique'];

		$flux['data'].= formulaires_afficher_formulaires(_T('formulairesprive:tous_formulaires_rubrique'), array("FROM" => 'spip_formulaires', "WHERE" => "id_rubrique='$id_rubrique'", 'ORDER BY' => "maj DESC"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png');

		$flux['data'].= "<div align='$spip_lang_right'>";
		$flux['data'].= icone(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire("formulaires_edit","id_rubrique=$id_rubrique&new=oui"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "creer.gif", '', 'non');
		$flux['data'].= "</div><p>";
		
		return $flux;
	}


	/**
	 * formulaires_brouteur_frame
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_brouteur_frame($flux) {
		$id_rubrique = $flux['args']['id_rubrique'];

		$result = spip_query("SELECT * FROM spip_formulaires WHERE id_rubrique='$id_rubrique' ORDER BY titre");
		if (spip_num_rows($result)>0) {
			$flux['data'].= "<div style='padding-top: 6px; padding-bottom: 3px;'><b class='verdana2'>"._T('formulairesprive:formulaires')."</b></div>";
			$flux['data'].= "<div class='plan-articles'>";
			while($row = spip_fetch_array($result)){
				$id_formulaire	= $row['id_formulaire'];
				$titre			= typo($row['titre']);
				$statut			= $row['statut'];
				$en_ligne		= $row['en_ligne'];
				switch ($en_ligne) {
					case 'non':
						$classe = 'prepa';
						break;
					case 'oui':
						switch ($statut) {
							case 'en_attente' :
								$classe = 'prop';
								break;
							case 'publie' :
								$classe = 'publie';
								break;
							case 'termine' :
								$classe = 'poubelle';
								break;
						}
						break;
				}
				$flux['data'].= '<a class="'.$classe.'" href="javascript:window.parent.location=\''.generer_url_ecrire('formulaires',"id_formulaire=$id_formulaire").'\'">'.$titre.'</a>';
			}
			$flux['data'].= "</div>";
		}
		return $flux;
	}


	/**
	 * formulaires_calculer_rubriques
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_calculer_rubriques($flux) {
		// Publier et dater les rubriques qui ont un formulaire publie
		$r = spip_query("SELECT rub.id_rubrique AS id,
		max(fille.maj) AS date
		FROM spip_rubriques AS rub, spip_formulaires AS fille
		WHERE rub.id_rubrique = fille.id_rubrique
		AND rub.date_tmp <= fille.maj AND fille.en_ligne='oui'
		GROUP BY rub.id_rubrique");
		while ($row = spip_fetch_array($r))
			spip_query("UPDATE spip_rubriques
			SET statut_tmp='publie', date_tmp='".$row['date']."'
			WHERE id_rubrique=".$row['id']);
		return $flux;
	}


	/**
	 * formulaires_propager_les_secteurs
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_propager_les_secteurs($flux) {
		// propager les secteurs aux formulaires
		$r = spip_query("SELECT fille.id_formulaire AS id, maman.id_secteur AS secteur
		FROM spip_formulaires AS fille, spip_rubriques AS maman
		WHERE fille.id_rubrique = maman.id_rubrique
		AND fille.id_secteur <> maman.id_secteur");
		while ($row = spip_fetch_array($r))
			spip_query("UPDATE spip_formulaires SET id_secteur=".$row['secteur']."
			WHERE id_formulaire=".$row['id']);
		return $flux;
	}


	/**
	 * formulaires_calculer_langues_rubriques
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_calculer_langues_rubriques($flux) {
		$s = spip_query("SELECT fils.id_formulaire AS id_formulaire, mere.lang AS lang
			FROM spip_formulaires AS fils, spip_rubriques AS mere
			WHERE fils.id_rubrique = mere.id_rubrique
			AND fils.langue_choisie != 'oui' AND (fils.lang='' OR mere.lang<>'')
			AND mere.lang<>fils.lang");
		while ($row = spip_fetch_array($s)) {
			$id_formulaire = $row['id_formulaire'];
			spip_query("UPDATE spip_formulaires SET lang=" . spip_abstract_quote($row['lang']) . ", langue_choisie='non' WHERE id_formulaire=$id_formulaire");
		}
		return $flux;
	}


	/**
	 * formulaires_tester_rubrique_vide
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_tester_rubrique_vide($flux) {
		global $connect_statut;
		if ($connect_statut == "0minirezo") {
			$id_rubrique = $flux['args']['id_rubrique'];
			$nb_formulaires = spip_num_rows(spip_query('SELECT id_formulaire FROM spip_formulaires WHERE id_rubrique="'.$id_rubrique.'"'));
			$flux['data'] = $flux['data'] + $nb_formulaires;
		}
		return $flux;
	}


	/**
	 * formulaires_recherche
	 *
	 * @param array flux
	 * @return array flux
	 * @author Pierre Basson
	 **/
	function formulaires_recherche($flux) {
		$args = $flux['args'];
		$data = $flux['data'];
		$activer_moteur = ($GLOBALS['meta']['activer_moteur'] == 'oui');

		$testnum		= $args['testnum'];
		$recherche		= $args['recherche'];
		$where			= $args['where'];
		$hash_recherche = $args['hash_recherche'];

		$query_formulaires['FROM'] = 'spip_formulaires';
		$query_formulaires['WHERE'] = ($testnum ? "(id_formulaire = $recherche)" :'') . $where;
		$query_formulaires['ORDER BY'] = "maj DESC";

		$where_applicants = split("[[:space:]]+", $recherche);
		if ($where_applicants) {
			foreach ($where_applicants as $k => $v) 
				$where_applicants[$k] = "'%" . substr(str_replace("%","\%", spip_abstract_quote($v)),1,-1) . "%'";
			$where_applicants = ($testnum ? "OR " : '') . ("(email LIKE " . join(" AND email LIKE ", $where_applicants) . ")");
		}

		$query_applicants['FROM'] = 'spip_applicants';
		$query_applicants['WHERE'] = ($testnum ? "(id_applicant = $recherche)" :'') . $where_applicants;
		$query_applicants['ORDER BY'] = "maj DESC";

		if ($activer_moteur) {	// texte integral
			$query_formulaires_int = requete_txt_integral('spip_formulaires', $hash_recherche);
		}

		$nbf = formulaires_afficher_formulaires(_T('formulairesprive:formulaires_trouves'), $query_formulaires);
		echo $nbf;
		$nba3 = formulaires_afficher_applicants(_T('formulairesprive:applicants_trouves'), $query_applicants);
		echo $nba3;


		if ($activer_moteur) {
			$nbf1 = formulaires_afficher_formulaires(_T('formulairesprive:formulaires_trouves_dans_texte'), $query_formulaires_int);
			echo $nbf1;

			$id_table_spip_reponses = id_index_table('spip_reponses');
			$nba1 = formulaires_afficher_applications(_T('formulairesprive:applications_trouvees_dans_reponses'), array('SELECT' => 'APP.*, SUM(REC.points) AS points', 'FROM' => 'spip_applications AS APP, spip_reponses AS REP, spip_index AS REC', 'WHERE' => 'REC.hash IN ('.$hash_recherche.') AND REC.id_table='.$id_table_spip_reponses.' AND REC.id_objet=REP.id_reponse AND APP.id_application=REP.id_application AND APP.statut="valide"', 'GROUP BY' => 'APP.id_application', 'ORDER BY' => 'points'));
			echo $nba1;

			$nba2 = formulaires_afficher_applications(_T('formulairesprive:applications_trouvees_dans_choix_question'), array('SELECT' => 'APP.*', 'FROM' => 'spip_applications AS APP, spip_reponses AS REP, spip_choix_question AS CHOIX', 'WHERE' => 'CHOIX.titre LIKE "%'.addslashes($recherche).'%" AND CHOIX.id_choix_question=REP.valeur AND REP.id_application=APP.id_application AND APP.statut="valide"', 'GROUP BY' => 'APP.id_application', 'ORDER BY' => 'APP.maj'));
			echo $nba2;
		}

		if ($data) $resultat = true;
		else
			if (!$nbf and !$nbf1 and !$nba1 and !$nba2 and !$nba3)
				$resultat = false;
			else
				$resultat = true;

		return array('args' => $args, 'data' => $resultat);
	}


	/**
	 * formulaires_verifier_base
	 *
	 * @return true
	 * @author Pierre Basson
	 **/
	function formulaires_verifier_base() {
		$info_plugin_formulaires = plugin_get_infos(_NOM_PLUGIN_FORMULAIRES);
		$version_plugin = $info_plugin_formulaires['version'];
		if (!isset($GLOBALS['meta']['spip_formulaires_version'])) {
			creer_base();
			mkdir(_DIR_FORMULAIRES);
			spip_query("ALTER TABLE spip_groupes_mots ADD formulaires VARCHAR(3) NOT NULL DEFAULT 'non';");
			$secret = formulaires_generer_nouveau_mdp(16);
			ecrire_meta('spip_formulaires_blowfish', $secret);
			ecrire_meta('spip_formulaires_version', $version_plugin);
			ecrire_meta('spip_formulaires_fond_formulaire_espace_applicant', 'espace_applicant');
			ecrire_meta('spip_formulaires_fond_formulaire_oubli_formulaire', 'oubli_formulaire');
			ecrire_metas();
		} else {
			$version_base = $GLOBALS['meta']['spip_formulaires_version'];
			if ($version_base < 0.2) {
				spip_query("ALTER TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','date','email_applicant') NOT NULL DEFAULT 'champ_texte'");
				ecrire_meta('spip_formulaires_version', $version_base = 0.2);
				ecrire_metas();
			}
			if ($version_base < 0.3) {
				ecrire_meta('spip_formulaires_fond_formulaire_espace_applicant', 'espace_applicant');
				ecrire_meta('spip_formulaires_fond_formulaire_oubli_formulaire', 'oubli_formulaire');
				spip_query("DROP TABLE spip_lettres_formulaires");
				spip_query("ALTER TABLE spip_formulaires ADD limiter_invitation ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER limiter_temps");
				spip_query("ALTER TABLE spip_formulaires DROP inscrire_applicant");
				spip_query("ALTER TABLE spip_applications ADD maj DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
				spip_query("ALTER TABLE spip_applicants DROP code");
				spip_query("ALTER TABLE spip_applicants ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL AFTER cookie");
				spip_query("ALTER TABLE spip_reponses ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL AFTER valeur");
				spip_query("ALTER TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','date','email_applicant','lettres') NOT NULL DEFAULT 'champ_texte'");
				spip_query("ALTER TABLE spip_questions CHANGE controle controle ENUM('non_vide','email','email_applicant','url','nombre') NOT NULL DEFAULT 'non_vide'");
				spip_query("UPDATE spip_questions SET controle='email_applicant' WHERE type='email_applicant'");
				spip_query("ALTER TABLE spip_choix_question ADD id_lettre BIGINT (21) DEFAULT '0' NOT NULL AFTER titre");
				ecrire_meta('spip_formulaires_version', $version_base = 0.3);
				ecrire_metas();
			}
			if ($version_base < 0.4) {
				spip_query("ALTER TABLE spip_applicants DROP idx");
				spip_query("ALTER TABLE spip_choix_question DROP idx");
				ecrire_meta('spip_formulaires_version', $version_base = 0.4);
				ecrire_metas();
			}
			if ($version_base < 0.5) {
				spip_query("ALTER TABLE spip_applicants DROP INDEX email");
				ecrire_meta('spip_formulaires_version', $version_base = 0.5);
				ecrire_metas();
			}
			if ($version_base < 0.6) {
				spip_query("ALTER TABLE spip_applicants CHANGE iv iv VARCHAR(32) NOT NULL;");
				$res1 = spip_query('SELECT * FROM spip_applicants');
				while ($arr = spip_fetch_array($res1)) {
					$verification = false;
					$i = 0;
					while (!$verification) {
						if ($i == 0)
					    	$iv = $arr['iv'];
						else
							$iv = formulaires_generer_vecteur_initialisation();
						$res2 = spip_query('SELECT id_applicant FROM spip_applicants WHERE iv="'.base64_encode($iv).'"');
						if (spip_num_rows($res2) == 0)
							$verification = true;
						$i++;
					}
					spip_query('UPDATE spip_applicants SET iv="'.base64_encode($iv).'" WHERE id_applicant="'.$arr['id_applicant'].'"');
				}
				ecrire_meta('spip_formulaires_version', $version_base = 0.6);
				ecrire_metas();
			}
			if ($version_base < 0.7) {
				spip_query("ALTER TABLE spip_applicants ADD UNIQUE (`iv`);");
				ecrire_meta('spip_formulaires_version', $version_base = 0.7);
				ecrire_metas();
			}
			if ($version_base < 0.8) {
				spip_query("ALTER TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','lettres') NOT NULL DEFAULT 'champ_texte'");
				spip_query("ALTER TABLE spip_questions CHANGE controle controle ENUM('non_vide','email','email_applicant','url','nombre','date') NOT NULL DEFAULT 'non_vide'");
				ecrire_meta('spip_formulaires_version', $version_base = 0.8);
				ecrire_metas();
			}
			if ($version_base < 0.9) {
				mkdir(_DIR_FORMULAIRES);
				creer_base();
				spip_query("ALTER TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','lettres','fichier') NOT NULL DEFAULT 'champ_texte'");
				ecrire_meta('spip_formulaires_version', $version_base = 0.9);
				ecrire_metas();
			}
			if ($version_base < 1.0) {
				spip_query("ALTER TABLE spip_applicants ADD nom VARCHAR(255) NOT NULL DEFAULT '' AFTER email");
				spip_query("ALTER TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','lettres','abonnements','fichier') NOT NULL DEFAULT 'champ_texte'");
				// id_lettre -> id_rubrique
				$blocs = spip_query('SELECT * FROM spip_blocs');
				while ($b = spip_fetch_array($blocs)) {
					$questions = spip_query('SELECT * FROM spip_questions WHERE type="lettres" AND id_bloc="'.$b['id_bloc'].'"');
					while ($q = spip_fetch_array($questions)) {
						$choix_questions = spip_query('SELECT * FROM spip_choix_question WHERE id_question="'.$q['id_question'].'"');
						while ($c = spip_fetch_array($choix_questions)) {
							$lettre = spip_query('SELECT id_rubrique FROM spip_lettres WHERE id_lettre="'.$c['id_lettre'].'"');
							if (spip_num_rows($lettre)) {
								list($id_rubrique) = spip_fetch_array($lettre, SPIP_NUM);
								spip_query('UPDATE spip_choix_question SET id_lettre="'.$id_rubrique.'" WHERE id_choix_question="'.$c['id_choix_question'].'"');
							} else {
								// la lettre a été supprimée
								$choix_question = new choix_question($b['id_formulaire'], $b['id_bloc'], $q['id_question'], $c['id_choix_question']);
								$choix_question->supprimer();
							}
						}
					}
				}
				spip_query("UPDATE spip_questions SET type='abonnements' WHERE type='lettres'");
				spip_query("ALTER TABLE spip_choix_question CHANGE id_lettre id_rubrique BIGINT(21) DEFAULT '0' NOT NULL");
				spip_query("ALTER TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','abonnements','fichier') NOT NULL DEFAULT 'champ_texte'");
				ecrire_meta('spip_formulaires_version', $version_base = 1.0);
				ecrire_metas();
			}
			if ($version_base < 1.1) {
				spip_query("ALTER TABLE spip_applicants CHANGE nom nom VARCHAR(255) NOT NULL DEFAULT ''");
				spip_query("ALTER TABLE spip_applicants DROP INDEX iv_2");
				spip_query("ALTER TABLE spip_applicants ADD maj DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER cookie");
				spip_query("ALTER TABLE spip_applicants ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL AFTER maj");
				ecrire_meta('spip_formulaires_version', $version_base = 1.1);
				ecrire_metas();
			}
			if ($version_base < 1.2) {
				spip_query("ALTER TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','abonnements','fichier','auteurs') NOT NULL DEFAULT 'champ_texte'");
				spip_query("ALTER TABLE spip_choix_question ADD id_auteur BIGINT (21) DEFAULT '0' NOT NULL AFTER id_rubrique");
				ecrire_meta('spip_formulaires_version', $version_base = 1.2);
				ecrire_metas();
			}
			if ($version_base < 1.3) {				
				// oubli de déclaration dans la base, on recréé si nécessaire
				$r = spip_query("SHOW FIELDS FROM spip_choix_question LIKE 'id_auteur';");
				$exits = spip_num_rows($r);
				
				if (!$exits) {
					spip_query("ALTER TABLE spip_choix_question ADD id_auteur BIGINT (21) DEFAULT '0' NOT NULL AFTER id_rubrique");
				}		
				ecrire_meta('spip_formulaires_version', $version_base = 1.3);
				ecrire_metas();		
			}
			if ($version_base < 1.4) {				
				spip_query("ALTER TABLE spip_formulaires ADD chapo TEXT NOT NULL AFTER descriptif");
				ecrire_meta('spip_formulaires_version', $version_base = 1.4);
				ecrire_metas();		
			}
		}

		if ($GLOBALS['meta']['spip_notifications_version'] >= 1.5) {
			$verification = spip_num_rows(spip_query('SELECT notification FROM spip_notifications WHERE notification="nouvelle_application"'));
			if (!$verification) {
				notifications_insertion_notification('nouvelle_application', 'formulairespublic');
			}
		}

		if (isset($GLOBALS['meta']['MotsPartout:tables_installees'])) {
			$tables_installees = unserialize($GLOBALS['meta']['MotsPartout:tables_installees']);
			if (!$tables_installees['formulaires']) {
				$tables_installees['formulaires'] = true;
				ecrire_meta('MotsPartout:tables_installees',serialize($tables_installees));
	  			ecrire_metas();
			}
		}

		if (isset($GLOBALS['meta']['INDEX_elements_objet'])) {
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			if (!isset($INDEX_elements_objet['spip_formulaires'])){
				$INDEX_elements_objet['spip_formulaires'] = array('titre'=>8,'descriptif'=>4,'chapo'=>2,'texte'=>1);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
				ecrire_metas();
			}
		}
		if (isset($GLOBALS['meta']['INDEX_elements_objet'])) {
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			if (!isset($INDEX_elements_objet['spip_applicants'])){
				$INDEX_elements_objet['spip_applicants'] = array('email'=>8,'nom'=>8);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
				ecrire_metas();
			}
		}
		if (isset($GLOBALS['meta']['INDEX_elements_objet'])) {
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			if (!isset($INDEX_elements_objet['spip_reponses'])){
				$INDEX_elements_objet['spip_reponses'] = array('valeur'=>1);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
				ecrire_metas();
			}
		}

		return true;
	}


	/**
	 * formulaires_generer_nouveau_mdp
	 *
	 * @param  int longueur
	 * @return  string nouveau mdp
	 * @author  Pierre Basson
	 **/
	function formulaires_generer_nouveau_mdp($longueur=8) {
		$chaine = "abBDEFcdefghijkmnPQRSTUVWXYpqrst23456789";
		srand((double)microtime()*1000000);
		$nouveau_mdp = '';
		for($i=0; $i<$longueur; $i++) {
			$nouveau_mdp.= $chaine[rand()%strlen($chaine)];
		}
		return $nouveau_mdp;
	}


	/**
	 * formulaires_identifier_applicant_avec_email_et_mdp
	 *
	 * @return int id_applicant
	 * @author Pierre Basson
	 **/
	function formulaires_identifier_applicant_avec_email_et_mdp($email, $mdp) {
		$verif = spip_query('SELECT id_applicant FROM spip_applicants WHERE email="'.addslashes($email).'" AND mdp="'.addslashes($mdp).'"');
		if (spip_num_rows($verif) == 0) {
			return 0;
		} else {
			list($id_applicant) = spip_fetch_array($verif, SPIP_NUM);
			return $id_applicant;
		}
	}
	
	
	/**
	 * formulaires_identifier_applicant
	 *
	 * @return int id_applicant
	 * @author Pierre Basson
	 **/
	function formulaires_identifier_applicant() {
   		$iv = base64_decode($_COOKIE['spip_formulaires_mcrypt_iv']);
		$id_applicant = formulaires_decrypter_avec_blowfish($_COOKIE['spip_formulaires_id_applicant'], $iv, $GLOBALS['meta']['spip_formulaires_blowfish']);
		return $id_applicant;
	}
	
	
	/**
	 * formulaires_generer_vecteur_initialisation
	 *
	 * @return string iv
	 * @author Pierre Basson
	 **/
	function formulaires_generer_vecteur_initialisation() {
	    srand((double) microtime() * 1000000);
	    return mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC), MCRYPT_RAND);
	}
	
	
	/**
	 * formulaires_crypter_avec_blowfish
	 *
	 * phpMyAdmin
	 * Encryption using blowfish algorithm (mcrypt)
	 *
	 * @param   string  original data
	 * @param   string  iv
	 * @param   string  the secret
	 *
	 * @return  string  the encrypted result
	 *
	 * @access  public
	 *
	 * @author  lem9
	 */
	function formulaires_crypter_avec_blowfish($data, $iv, $secret) {
 		return base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $secret, $data, MCRYPT_MODE_CBC, $iv));
	}


	/**
	 * formulaires_decrypter_avec_blowfish
	 *
	 * phpMyAdmin
	 * Decryption using blowfish algorithm (mcrypt)
	 *
	 * @param   string  encrypted data
	 * @param   string  iv
	 * @param   string  the secret
	 *
	 * @return  string  original data
	 *
	 * @access  public
	 *
	 * @author  lem9
	 */
	function formulaires_decrypter_avec_blowfish($encdata, $iv, $secret) {
	    return trim(mcrypt_decrypt(MCRYPT_BLOWFISH, $secret, base64_decode($encdata), MCRYPT_MODE_CBC, $iv));
	}


	/**
	 * calculer_url_formulaire
	 *
	 * permet d'intégrer des raccourcis [lien->formulaireXX] ou [->formulaireXX] dans les textes
	 *
	 * @param int id_formulaire
	 * @param string texte
	 * @param string ancre
	 * @return array lien
	 * @author Pierre Basson
	 **/
	function calculer_url_formulaire($id_formulaire, $texte, $ancre) {
		$lien = generer_url_formulaire($id_formulaire) . $ancre;
		if (!$texte) {
			$row = @spip_fetch_array(spip_query("SELECT titre FROM spip_formulaires WHERE id_formulaire=$id_formulaire"));
			$texte = $row['titre'];
		}
		return array($lien, 'spip_in', $texte);
	}


	/**
	 * generer_url_formulaire
	 *
	 * génère le lien vers le squelette d'un formulaire
	 *
	 * @param int id_formulaire
	 * @param boolean preview
	 * @return string url squelette produit
	 * @author Pierre Basson
	 **/
	function generer_url_formulaire($id_formulaire, $preview=false) {
		if ($preview)
			$var_mode = '&var_mode=preview';
		return generer_url_public('formulaire', 'id_formulaire='.$id_formulaire.$var_mode);
	}


	/**
	 * formulaires_icone_horizontale_nouvelle_fenetre
	 *
	 **/
	function formulaires_icone_horizontale_nouvelle_fenetre($texte, $lien, $fond = "", $fonction = "", $echo = true, $javascript='') {
		global $spip_display;

		$retour = '';


		if ($spip_display != 4) {
			//if (!$fonction) $fonction = "rien.gif";
	
			if ($spip_display != 1) {
				$retour .= "<a href='$lien' class='cellule-h' $javascript>";
				$retour .= "<table cellpadding='0' valign='middle'><tr>\n";
				$retour .= "<td><a href='$lien' class='cellule-h' $javascript><div class='cell-i'>" ;
				if ($fonction){
				  $retour .= http_img_pack($fonction, "", http_style_background($fond, "center center no-repeat"));
				}
				else {
					$retour .= http_img_pack($fond, "", "");
				}
				$retour .= "</div></a></td>\n" .
				  "<td class='cellule-h-lien'><a href='$lien' class='cellule-h' $javascript>$texte</a></td>\n";
				$retour .= "</tr></table>\n";
				$retour .= "</a>\n";
			}
			else {
				$retour .= "<a href='$lien' class='cellule-h-texte' $javascript><div>$texte</div></a>\n";
			}
			if ($fonction == "supprimer.gif")
				$retour = "<div class='danger'>$retour</div>";
		} else {
			$retour = "<li><a href='$lien' $javascript>$texte</a></li>";
		}

		if ($echo) echo $retour;
		return $retour;
	}


	/**
	 * formulaires_afficher_raccourci_creer_formulaire
	 *
	 * affiche un raccourci vers la création d'un nouveau formulaire
	 *
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_raccourci_creer_formulaire() {
		icone_horizontale(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire("formulaires_edit", "new=oui"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', 'creer.gif');
	}


	/**
	 * formulaires_afficher_raccourci_liste_formulaires
	 *
	 * affiche un raccourci vers la liste des formulaires
	 *
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_raccourci_liste_formulaires() {
		icone_horizontale(_T('formulairesprive:aller_liste_formulaires'), generer_url_ecrire("formulaires_tous"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', '');
	}


	/**
	 * formulaires_afficher_formulaires
	 *
	 * affiche la liste des formulaires
	 *
	 * @param string titre
	 * @param string requete
	 * @return string la liste des formulaires
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_formulaires($titre_table, $requete) {

		global $couleur_foncee, $options;

		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);

		if ($options == "avancees") {
			$largeurs = array('12', '', 100, 100, 50);
			$styles = array('arial1', 'arial2', 'arial1', 'arial1', 'arial1');
		} else {
			$largeurs = array('12', '', 100, 100);
			$styles = array('arial1', 'arial2', 'arial1', 'arial1');
		}

		return affiche_tranche_bandeau($requete, _DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', $couleur_foncee, 'white', $tmp_var, $titre_table, false, $largeurs, $styles, 'formulaires_afficher_formulaires_boucle', array());
	}


	/**
	 * formulaires_afficher_formulaires_boucle
	 *
	 * affiche une ligne
	 *
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_formulaires_boucle($row, &$tous_id, $voir_logo, $own) {
	
		global $connect_id_auteur, $dir_lang, $options, $spip_lang_right;
		
		$vals = '';

		$id_formulaire	= $row['id_formulaire'];
		$tous_id[]		= $id_formulaire;
		$titre			= $row['titre'];
		$limiter_temps	= $row['limiter_temps'];
		$date_debut		= $row['date_debut'];
		$date_fin		= $row['date_fin'];
		$en_ligne		= $row['en_ligne'];
		$statut			= $row['statut'];

		switch ($en_ligne) {
			case 'oui':
				switch ($statut) {
					case 'en_attente':
						$vals[] = http_img_pack('puce-orange.gif', 'puce-orange', ' border="0" style="margin: 1px;"');
						break;
					case 'publie':
						$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');
						break;
					case 'termine':
						$vals[] = http_img_pack('puce-poubelle.gif', 'puce-noire', ' border="0" style="margin: 1px;"');
						break;
				}
				break;
			case 'non':
				$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
				break;
		}

		$s = "<div>";
		$s .= "<a href='" . generer_url_ecrire("formulaires","id_formulaire=$id_formulaire") .
			"'$dir_lang style=\"display:block;\">";
		if ($voir_logo) {
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($id_formulaire, 'id_formulaire', 'on')) {
				list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
				$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
				if ($logo)
					$s .= "\n<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
			}
		}
		$s .= typo($titre);
		$s .= "</a>";
		$s .= "</div>";
	
		$vals[] = $s;

		if ($limiter_temps == 'oui') {
			$vals[] = affdate_jourcourt($date_debut);
			$vals[] = affdate_jourcourt($date_fin);
		} else {
			$vals[] = '&nbsp;';
			$vals[] = '&nbsp;';
		}

		if ($options == "avancees") {
			$vals[] = "<b>"._T('info_numero_abbreviation')."$id_formulaire</b>";
		}
	
		return $vals;
	}


	/**
	 * formulaires_afficher_applications
	 *
	 * affiche la liste des applications
	 *
	 * @param string titre
	 * @param string requete
	 * @return string la liste des invitations
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_applications($titre_table, $requete) {

		global $couleur_foncee, $options;

		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);

		$largeurs = array('12', '', 100, 50);
		$styles = array('arial1', 'arial2', 'arial1', 'arial1');

		return affiche_tranche_bandeau($requete, _DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', $couleur_foncee, 'white', $tmp_var, $titre_table, false, $largeurs, $styles, 'formulaires_afficher_applications_boucle', array());
	}


	/**
	 * formulaires_afficher_applications_boucle
	 *
	 * affiche une ligne
	 *
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_applications_boucle($row, &$tous_id, $voir_logo, $own) {
	
		global $connect_id_auteur, $dir_lang, $options, $spip_lang_right;
		
		$vals = '';

		$id_application	= $row['id_application'];
		$tous_id[]		= $id_application;
		$maj			= $row['maj'];
		$id_applicant	= $row['id_applicant'];
		$id_formulaire	= $row['id_formulaire'];

		$application = new application($id_applicant, $id_formulaire, $id_application);
		$est_vide = $application->est_vide();
		if ($est_vide)
			$vals[] = http_img_pack('puce-blanche.gif', 'puce-blanche', ' border="0" style="margin: 1px;"');
		else
			$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');

		$vals[] = '<a href="'.generer_url_ecrire('applications','id_application='.$id_application).'">'.$application->applicant->email.'</a>';

		$vals[] = affdate($maj);

		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_application</b>";
	
		return $vals;
	}


	/**
	 * formulaires_afficher_applicants
	 *
	 * affiche la liste des applicants
	 *
	 * @param string titre
	 * @param string requete
	 * @return string la liste des applicants
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_applicants($titre_table, $requete) {

		global $couleur_foncee, $options;

		$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);

		$largeurs = array('12', '', 100, 50);
		$styles = array('arial1', 'arial2', 'arial1', 'arial1');

		return affiche_tranche_bandeau($requete, _DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', $couleur_foncee, 'white', $tmp_var, $titre_table, false, $largeurs, $styles, 'formulaires_afficher_applicants_boucle', array());
	}


	/**
	 * formulaires_afficher_applicants_boucle
	 *
	 * affiche une ligne
	 *
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_applicants_boucle($row, &$tous_id, $voir_logo, $own) {
	
		global $connect_id_auteur, $dir_lang, $options, $spip_lang_right;
		
		$vals = '';

		$id_applicant = $row['id_applicant'];
		$tous_id[] = $id_applicant;
		$applicant = new applicant($id_applicant);

		$vals[] = http_img_pack('puce-verte.gif', 'puce-verte', ' border="0" style="margin: 1px;"');

		$vals[] = '<a href="'.generer_url_ecrire('applicants','id_applicant='.$id_applicant).'">'.$applicant->email.'</a>';

		if ($applicant->nom)
			$vals[] = $applicant->nom;
		else
			$vals[] = '&nbsp;';

		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_applicant</b>";
	
		return $vals;
	}


	/**
	 * formulaires_afficher_numero_formulaire
	 *
	 * @param int id_formulaire
	 * @param boolean prévisualisation
	 * @param boolean statistiques
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_numero_formulaire($id_formulaire, $previsu=false, $statistiques=false) {
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('formulairesprive:numero_formulaire')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>$id_formulaire</b></font>\n";
		if ($previsu) {
			formulaires_icone_horizontale_nouvelle_fenetre(_T('formulairesprive:previsualiser'), generer_url_formulaire($id_formulaire, true), $image, "racine-24.gif", true, 'target="_blank"');
		}
		echo "</div>\n";
		fin_boite_info();
	}


	/**
	 * formulaires_afficher_dates
	 *
	 * @param datetime date de debut
	 * @param datetime date de fin
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_dates($date_debut, $date_fin, $modif=false) {
		$titre_barre = _T('formulairesprive:periode_de_validite').'<br>'._T('formulairesprive:du').'&nbsp;'.majuscules(affdate($date_debut)).'&nbsp;'._T('formulairesprive:au').'&nbsp;'.majuscules(affdate($date_fin));
		debut_cadre_enfonce('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/periode.png', false, "", bouton_block_invisible('dates').$titre_barre);
		echo debut_block_invisible('dates');
		echo "<table border='0' width='100%' style='text-align: right'>";
		echo "<tr>";
		echo "	<td><span class='verdana1'><B>"._T('formulairesprive:changer_date_debut')."</B></span> &nbsp;</td>";
		echo "	<td>";
		echo afficher_jour(affdate($date_debut, 'jour'), "name='jour_debut' size='1' class='fondl'", true);
		echo afficher_mois(affdate($date_debut, 'mois'), "name='mois_debut' size='1' class='fondl'", true);
		echo afficher_annee(affdate($date_debut, 'annee'), "name='annee_debut' size='1' class='fondl'");
		echo "	</td>";
		echo "	<td>&nbsp;</td>";
		echo "</tr>";
		echo "<tr>";
		echo "	<td><span class='verdana1'><B>"._T('formulairesprive:changer_date_fin')."</B></span> &nbsp;</td>";
		echo "	<td>";
		echo afficher_jour(affdate($date_fin, 'jour'), "name='jour_fin' size='1' class='fondl'", true);
		echo afficher_mois(affdate($date_fin, 'mois'), "name='mois_fin' size='1' class='fondl'", true);
		echo afficher_annee(affdate($date_fin, 'annee'), "name='annee_fin' size='1' class='fondl'");
		echo "	</td>";
		echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_dates' VALUE='"._T('formulairesprive:changer')."' CLASS='fondo' STYLE='font-size:10px'></td>";
		echo "</tr>";
		echo "</table>";
		echo fin_block();
		fin_cadre_enfonce();
	}


	/**
	 * formulaires_afficher_auteurs
	 *
	 * @param int id_formulaire
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_auteurs($id_formulaire, $modif=false) {
		$titre_barre = _T('formulairesprive:auteurs');

		if ($modif)
			debut_cadre_enfonce('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/auteurs.png', false, "", bouton_block_invisible('auteurs').$titre_barre);
		else
			debut_cadre_enfonce('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/auteurs.png', false, "", $titre_barre);

		$tableau_auteurs_interdits = array();

		$auteurs_associes = 'SELECT A.id_auteur,
								A.email,
								A.nom
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_formulaires AS AF ON AF.id_auteur=A.id_auteur
							WHERE AF.id_formulaire="'.$id_formulaire.'"
							ORDER BY A.nom';
		$resultat_auteurs_associes = spip_query($auteurs_associes);
		if (@spip_num_rows($resultat_auteurs_associes) > 0) {
			echo "<div class='liste'>\n";
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			while ($arr = spip_fetch_array($resultat_auteurs_associes)) {
				$tableau_auteurs_interdits[] = $arr['id_auteur'];
				echo "<tr class='tr_liste'>\n";
				echo "<td width='25' class='arial11'>\n";
				echo "</td>\n";
				echo "<td class='arial2'>\n";
				echo "<A HREF='".generer_url_ecrire("auteur_infos","id_auteur=".$arr['id_auteur'], true)."'>\n";
				echo propre($arr['nom']);
				echo "</A>\n";
				echo "</td>\n";
				echo "<td class='arial2'>\n";
				echo $arr['email'];
				echo "</td>\n";
				if ($modif) {
					echo "<td class='arial1'>\n";
					echo "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=$id_formulaire&supprimer_auteur=".$arr['id_auteur'], true)."'>\n";
					echo _T('formulairesprive:retirer_auteur')."\n";
					echo "</A>\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</div>\n";
		}
		if ($modif) {
			$auteurs_interdits = implode(",", $tableau_auteurs_interdits);
			if (!empty($auteurs_interdits))
				$where_auteurs_interdits = ' WHERE A.id_auteur NOT IN ('.$auteurs_interdits.')';
			else
				$where_auteurs_interdits = '';
			$requete = 'SELECT A.id_auteur, 
							A.nom
						FROM spip_auteurs AS A
						'.$where_auteurs_interdits.'
						ORDER BY A.nom';
			$resultat_requete = spip_query($requete);
			if (@spip_num_rows($resultat_requete) > 0) {
				echo debut_block_invisible('auteurs');
				echo "<table border='0' width='100%' style='text-align: right'>";
				echo "<tr>";
				echo "	<td><span class='verdana1'><B>"._T('formulairesprive:ajouter_auteur')."</B></span> &nbsp;</td>";
				echo "	<td>";
				echo "		<select name='id_auteur' SIZE='1' STYLE='width: 180px;' CLASS='fondl'>";
				while ($arr = spip_fetch_array($resultat_requete)) {
					echo "				<option value='".$arr['id_auteur']."'>".propre($arr['nom'])."</option>";
				}
				echo "		</select><br/>";
				echo "	</td>";
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_auteur' VALUE='"._T('formulairesprive:choisir')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				echo fin_block();
			}
		}
		fin_cadre_enfonce();
	}
	


	/**
	 * formulaires_ordonner
	 *
	 * @param array tableau_sans_id_a_inserer
	 * @param int id_a_inserer
	 * @param int position
	 * @author Pierre Basson
	 **/
	function formulaires_ordonner($tableau_sans_id_a_inserer, $id_a_inserer, $position) {
		// on réordonne
		if ($id_a_inserer == 0) {
			foreach ($tableau_sans_id_a_inserer as $id)
			 	$tableau_final[] = $id;
		} else if ($position === 'dernier') {
			$tableau = array();
			foreach ($tableau_sans_id_a_inserer as $id)
			 	$tableau[] = $id;
			$tableau_final = array_merge($tableau, array($id_a_inserer));
		} else if ($position == 0) {
			$tableau = array();
			foreach ($tableau_sans_id_a_inserer as $id)
				$tableau[] = $id;
			$tableau_final = array_merge(array($id_a_inserer), $tableau);
		} else {
			$i = 0;
			$tableau_avant = array();
			$tableau_apres = array();
			$deuxieme_tableau = false;
			foreach ($tableau_sans_id_a_inserer as $id) {
				if ($position == $i)
					$deuxieme_tableau = true;
				if ($deuxieme_tableau)
					$tableau_apres[] = $id;
				else
					$tableau_avant[] = $id;
				$tableau[] = $id;
				$i++;
			}
			$tableau_final = array_merge($tableau_avant, array($id_a_inserer), $tableau_apres);
		}
		// on retourne le tableau final
		return $tableau_final;
	}


	function formulaires_formatage_csv($string) {
		$string = str_replace("\r", "\n", $string);
		$string = str_replace("\n\n", "\n", $string);
		$string = str_replace("\n\n", "\n", $string);
		$string = str_replace("\n", ", ", $string);
		$string = str_replace("- ", "", $string);
		return $string;
	}


	/**
	 * formulaires_remplacer_raccourci
	 *
	 * @param string string
	 * @param string email
	 * @return string string
	 * @author Pierre Basson
	 **/
	function formulaires_remplacer_raccourci($texte, $email) {
		if ($email) {
			list($mdp) = spip_fetch_array(spip_query('SELECT mdp FROM spip_applicants WHERE email="'.$email.'"'), SPIP_NUM);
			$texte = ereg_replace("%%MOT_DE_PASSE%%", $mdp, $texte);
		}
		return $texte;
	}


?>