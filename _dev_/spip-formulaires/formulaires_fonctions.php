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
	include_spip('inc/formulaires_pipelines');
	include_spip('public/formulaires_boucles');
	include_spip('public/formulaires_balises');

# TODO	include_spip('lettres_fonctions');


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


	function generer_url_formulaire($id_formulaire, $preview=false) {
		if ($preview)
			$var_mode = '&var_mode=preview';
		return generer_url_public('formulaire', 'id_formulaire='.$id_formulaire.$var_mode);
	}


	function formulaires_afficher_dates($date_debut, $date_fin, $modif=false) {
/* TODO
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
*/	}


	function formulaires_afficher_auteurs($id_formulaire) {
/* TODO
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
*/	}
	


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