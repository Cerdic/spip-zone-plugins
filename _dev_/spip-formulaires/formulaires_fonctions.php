<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('base/create');
	include_spip('inc/plugin');
	include_spip('inc/indexation');
	include_spip('inc/formulaires_classes');
	include_spip('inc/formulaires_filtres');
	include_spip('inc/formulaires_pipelines');
	include_spip('public/formulaires_boucles');
	include_spip('public/formulaires_balises');


	function formulaires_generer_nouveau_mdp($longueur=8) {
		$chaine = "abBDEFcdefghijkmnPQRSTUVWXYpqrst23456789";
		srand((double)microtime()*1000000);
		$nouveau_mdp = '';
		for($i=0; $i<$longueur; $i++) {
			$nouveau_mdp.= $chaine[rand()%strlen($chaine)];
		}
		return $nouveau_mdp;
	}


	function formulaires_identifier_applicant_avec_email_et_mdp($email, $mdp) {
		$verif = sql_select('id_applicant', 'spip_applicants', 'email="'.addslashes($email).'" AND mdp="'.addslashes($mdp).'"');
		if (sql_count($verif) == 0) {
			return 0;
		} else {
			$t = sql_fetch($verif);
			return $t['id_applicant'];
		}
	}
	
	
	function formulaires_identifier_applicant() {
   		$iv = base64_decode($_COOKIE['spip_formulaires_mcrypt_iv']);
		$id_applicant = formulaires_decrypter_avec_blowfish($_COOKIE['spip_formulaires_id_applicant'], $iv, $GLOBALS['meta']['spip_formulaires_blowfish']);
		return $id_applicant;
	}
	
	
	function formulaires_generer_vecteur_initialisation() {
	    srand((double) microtime() * 1000000);
	    return mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC), MCRYPT_RAND);
	}
	
	
	function formulaires_crypter_avec_blowfish($data, $iv, $secret) {
 		return base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $secret, $data, MCRYPT_MODE_CBC, $iv));
	}


	function formulaires_decrypter_avec_blowfish($encdata, $iv, $secret) {
	    return trim(mcrypt_decrypt(MCRYPT_BLOWFISH, $secret, base64_decode($encdata), MCRYPT_MODE_CBC, $iv));
	}


	function calculer_url_formulaire($id_formulaire, $texte, $ancre) {
		$lien = generer_url_formulaire($id_formulaire) . $ancre;
		if (!$texte) {
			$texte = sql_getfetsel('titre', 'spip_formulaires', 'id_formulaire='.intval($id_formulaire));
		}
		return array($lien, 'spip_in', $texte);
	}


	function generer_url_formulaire($id_formulaire) {
		return generer_url_public('formulaire', 'id_formulaire='.$id_formulaire);
	}


	function formulaires_afficher_auteurs($id_formulaire) {
		$auteurs = '<form method="post" action="'.generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire).'">';
		$bouton = bouton_block_depliable(_T('formulairesprive:bloc_auteurs'), false, 'auteurs');
		$auteurs.= debut_cadre_enfonce(_DIR_PLUGIN_FORMULAIRES.'/prive/images/auteurs.png', true, "", $bouton);
		$tableau_auteurs_interdits = array();
		$resultat_auteurs_associes = sql_select('A.id_auteur, A.email, A.nom', 'spip_auteurs AS A INNER JOIN spip_auteurs_formulaires AS AF ON AF.id_auteur=A.id_auteur', 'AF.id_formulaire='.intval($id_formulaire), '', 'A.nom');
		if (sql_count($resultat_auteurs_associes) > 0) {
			$auteurs.= "<div class='liste'>\n";
			$auteurs.= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			while ($arr = sql_fetch($resultat_auteurs_associes)) {
				$tableau_auteurs_interdits[] = $arr['id_auteur'];
				$auteurs.= "<tr class='tr_liste'>\n";
				$auteurs.= "<td width='12' class='arial11'>\n";
				$auteurs.= "</td>\n";
				$auteurs.= "<td class='arial2'>\n";
				$auteurs.= "<a href='".generer_url_ecrire("auteur_infos","id_auteur=".$arr['id_auteur'], true)."'>\n";
				$auteurs.= typo($arr['nom']);
				$auteurs.= "</a>\n";
				$auteurs.= "</td>\n";
				$auteurs.= "<td class='arial2'>\n";
				$auteurs.= $arr['email'];
				$auteurs.= "</td>\n";
				$auteurs.= "<td class='arial1'>\n";
				$auteurs.= "<a href='".generer_url_ecrire("formulaires","id_formulaire=$id_formulaire&supprimer_auteur=".$arr['id_auteur'], true)."'>\n";
				$auteurs.= _T('formulairesprive:retirer_auteur')."\n";
				$auteurs.= "</a>\n";
				$auteurs.= "</td>\n";
				$auteurs.= "</tr>\n";
			}
			$auteurs.= "</table>\n";
			$auteurs.= "</div>\n";
		}
		$auteurs.= debut_block_depliable(false, 'auteurs');
		$auteurs_interdits = implode(",", $tableau_auteurs_interdits);
		if (!empty($auteurs_interdits))
			$where_auteurs_interdits = 'id_auteur NOT IN ('.$auteurs_interdits.')';
		else
			$where_auteurs_interdits = '';
		$resultat_requete = sql_select('id_auteur, email', 'spip_auteurs', $where_auteurs_interdits, '', 'nom');
		if (sql_count($resultat_requete) > 0) {
			$auteurs.= _T('formulairesprive:ajouter_auteur');
			$auteurs.= "&nbsp;&nbsp;&nbsp;&nbsp;";
			$auteurs.= '<select name="id_auteur" class="fondl">';
			while ($arr = sql_fetch($resultat_requete)) {
				$auteurs.= "<option value='".$arr['id_auteur']."'>".typo($arr['email'])."</option>";
			}
			$auteurs.= "</select>";
			$auteurs.= '<div align="right">';
			$auteurs.= '<input type="submit" name="ajouter_auteur" class="fondo" value="'._T('formulairesprive:valider').'" />';
			$auteurs.= '</div>';
		}
		$auteurs.= fin_block();
		$auteurs.= fin_cadre_enfonce(true);
		$auteurs.= '</form>';
		return $auteurs;
	}
	


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


	function formulaires_remplacer_raccourci($texte, $email) {
		if ($email) {
			$mdp = sql_getfetsel('mdp', 'spip_applicants', 'email="'.addslashes($email).'"');
			$texte = ereg_replace("%%MOT_DE_PASSE%%", $mdp, $texte);
		}
		return $texte;
	}


?>