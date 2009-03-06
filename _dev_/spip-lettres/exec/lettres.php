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


	if (!defined("_ECRIRE_INC_VERSION")) return;
 	include_spip('inc/presentation');
	include_spip('inc/date');
	include_spip('inc/documents');
	include_spip('inc/headers');
	include_spip('inc/extra');
	include_spip('inc/filtres');
	include_spip('inc/filtres_images');
	include_spip('lettres_fonctions');
	include_spip('surcharges_fonctions');


	function exec_lettres() {
		global $dir_lang, $spip_lang_right, $champs_extra, $options, $spip_display;
		global $cherche_mot, $select_groupe;

		if (!autoriser('voir', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$lettre = new lettre($_GET['id_lettre']);
		
		pipeline('exec_init',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre, true);

		if (!empty($_GET['supprimer_auteur'])) {
			$lettre->supprimer_auteur($_GET['supprimer_auteur']);
			header('Location: '.$url);
			exit();
		}

		if (!empty($_GET['supprimer_article'])) {
			$lettre->supprimer_article($_GET['supprimer_article']);
			header('Location: '.$url);
			exit();
		}

		if (!empty($_POST['enregistrer_auteur'])) {
			$lettre->enregistrer_auteur($_POST['id_auteur']);
			header('Location: '.$url);
			exit();
		}

		if (!empty($_POST['enregistrer_article'])) {
			$lettre->enregistrer_article($_POST['id_article']);
			header('Location: '.$url);
			exit();
		}

		if (!empty($_POST['changer_date'])) {
			$lettre->enregistrer_date($_POST['annee'], $_POST['mois'], $_POST['jour'], $_POST['programmer_envoi']);
			header('Location: '.$url);
			exit();
		}

		if (!empty($_POST['renvoyer_lettre'])) {
			if ($_POST['tous'] == 1) {
				$url = generer_url_action('statut_lettre','id_lettre='.$lettre->id_lettre.'&changer_statut=1&statut=envoi_en_cours', true);
				header('Location: '.$url);
				exit();
			} else {
				$abonne = new abonne(0, $_POST['email_abonne']);
				if ($abonne->existe) {
					$resultat = $abonne->renvoyer_lettre($lettre->id_lettre);
					$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre.'&renvoi='.($resultat ? 'ok' : 'ko'), true);
					header('Location: '.$url);
					exit();
				} else {
					$abonne_inexistant = true;
				}
			}
		}


		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($lettre->titre, "naviguer", "lettres");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($lettre->id_rubrique);
		echo fin_grand_cadre(true);

		echo debut_gauche('', true);
		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('lettresprive:lettre_numero').' :';
		echo '<p>'.$lettre->id_lettre.'</p>';
		echo '</div>';

/*
		echo "<p id='note_envoi' style='color: red; display: none;' class='verdana1 spip_small'><b>"._T('lettresprive:aide_lettres_envoi_en_cours')."</b></p>";
		if ($lettre->statut == 'envoyee' and isset($_GET['envoi_termine']))
			echo "<p style='color: red' class='verdana1 spip_small'><b>"._T('lettresprive:envoi_termine')."</b></p>";
		if ($lettre->statut == 'envoi_en_cours')
			echo "<p style='color: red' class='verdana1 spip_small'><b>"._T('lettresprive:lettre_en_cours_d_envoi')."</b></p>";
		if (isset($_GET['renvoi']))
			echo "<p style='color: red' class='verdana1 spip_small'><b>"._T('lettresprive:renvoi_'.$_GET['renvoi'])."</b></p>";
		if (isset($_GET['test']))
			echo "<p style='color: red' class='verdana1 spip_small'><b>"._T('lettresprive:test_'.$_GET['test'])."</b></p>";
		echo "<br />";
*/

		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo _T('lettresprive:cette_lettre');
		echo '<ul>';
		if ($lettre->statut == 'brouillon') {
			echo '<li class="prepa selected">'._T('lettresprive:en_cours_de_redaction').'</li>';
			echo '<li class="prop"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=test', false, true).'">'._T('lettresprive:tester').'</a></li>';
			echo '<li class="publie"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=envoi_en_cours', false, true).'">'._T('lettresprive:envoyer').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=poubelle', false, true).'">'._T('lettresprive:a_la_poubelle').'</a></li>';
		}
		if ($lettre->statut == 'envoi_en_cours') {
			echo '<li class="prop selected">'._T('lettresprive:envoi_en_cours').'</li>';
			echo '<li class="refuse"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=envoyee', false, true).'">'._T('lettresprive:arreter_envoi').'</a></li>';
		}
		if ($lettre->statut == 'envoyee') {
			echo '<li class="publie selected">'._T('lettresprive:envoyee').'</li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=poubelle', false, true).'">'._T('lettresprive:a_la_poubelle').'</a></li>';
		}
		echo '</ul>';
		echo '</li>';
		echo '</ul>';

		if ($lettre->statut == 'envoyee') {
			echo '<ul class="instituer instituer_article">';
			echo '<li>';
			echo '<strong>'._T('lettresprive:envoi').'</strong>';
			echo '<ul>';
			echo '<li>'._T('lettresprive:debut').' : '.affdate($lettre->date_debut_envoi, 'h\hi d').' '.nom_mois($lettre->date_debut_envoi).'</li>';
			echo '<li>'._T('lettresprive:fin').' : '.affdate($lettre->date_fin_envoi, 'h\hi d').' '.nom_mois($lettre->date_fin_envoi).'</li>';
			if ($lettre->calculer_nb_envois('envoye'))
				echo '<li>'._T('lettresprive:nb_envois').' : '.$lettre->calculer_nb_envois('envoye').'/'.$lettre->calculer_nb_envois().'</li>';
			if ($lettre->calculer_nb_envois('annule'))
				echo '<li>'._T('lettresprive:nb_annules').' : '.$lettre->calculer_nb_envois('annule').'/'.$lettre->calculer_nb_envois().'</li>';
			if ($lettre->calculer_nb_envois('echec'))
				echo '<li>'._T('lettresprive:nb_echecs').' : '.$lettre->calculer_nb_envois('echec').'/'.$lettre->calculer_nb_envois().'</li>';
			echo '</ul>';
			echo '</li>';
			echo '<li>';
			echo '<strong>'._T('lettresprive:format').'</strong>';
			echo '<ul>';
			if ($lettre->calculer_pourcentage_format('mixte'))
				echo '<li>'._T('lettresprive:mixte').' : '.$lettre->calculer_pourcentage_format('mixte').'%</li>';
			if ($lettre->calculer_pourcentage_format('html'))
				echo '<li>'._T('lettresprive:html').' : '.$lettre->calculer_pourcentage_format('html').'%</li>';
			if ($lettre->calculer_pourcentage_format('texte'))
				echo '<li>'._T('lettresprive:texte').' : '.$lettre->calculer_pourcentage_format('texte').'%</li>';
			echo '</ul>';
			echo '</li>';
			if ($lettre->calculer_taux_ouverture()) {
				echo '<li>';
				echo '<strong>'._T('lettresprive:audience').'</strong>';
				echo '<ul>';
				echo '<li>'._T('lettresprive:taux_ouverture').' : '.$lettre->calculer_taux_ouverture().'%</li>';
				echo '</ul>';
				echo '</li>';
			}
			echo '</ul>';
		}
		if ($lettre->statut == 'brouillon') {
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_html'], 'id_lettre='.$lettre->id_lettre.'&var_mode=preview').'" class="cellule-h" target="_blank"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('lettresprive:previsualiser_html').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_html'], 'id_lettre='.$lettre->id_lettre.'&var_mode=preview').'" class="cellule-h" target="_blank">'._T('lettresprive:previsualiser_html').'</a></td></tr></table>';
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_texte'], 'id_lettre='.$lettre->id_lettre.'&var_mode=preview').'" class="cellule-h" target="_blank"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('lettresprive:previsualiser_texte').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_texte'], 'id_lettre='.$lettre->id_lettre.'&var_mode=preview').'" class="cellule-h" target="_blank">'._T('lettresprive:previsualiser_texte').'</a></td></tr></table>';
		} else {
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_lettre($lettre->id_lettre).'" class="cellule-h" target="_blank"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('lettresprive:voir_en_ligne').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_lettre($lettre->id_lettre).'" class="cellule-h" target="_blank">'._T('lettresprive:voir_en_ligne').'</a></td></tr></table>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';

		if ($lettre->statut == 'brouillon') {
			$iconifier = charger_fonction('iconifier', 'inc');
			echo $iconifier('id_lettre', $lettre->id_lettre, 'lettres');
		} else {
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($lettre->id_lettre, 'id_lettre', 'on')) {
				list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
				$res1 = image_reduire("<img src='$fid' alt='' />", 170, 170);
				if ($res1)
				    $res1 = "<div><a href='" .	$fid . "'>$res1</a></div>";
				else
				    $res1 = "<img src='$fid' width='$width' height='$height' alt=\"on\" />";
			}
			if ($logo = $chercher_logo($lettre->id_lettre, 'id_lettre', 'off')) {
				list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
				$res2 = image_reduire("<img src='$fid' alt='' />", 170, 170);
				if ($res2)
				    $res2 = "<div><a href='" .	$fid . "'>$res2</a></div>";
				else
				    $res2 = "<img src='$fid' width='$width' height='$height' alt=\"off\" />";
			}
			if ($res1) {
				echo debut_cadre_relief("image-24.gif", true);
		 		echo "<div class='verdana1' style='text-align: center;'>";
				echo "<b>"._T('lettresprive:logo_lettre')."</b>";
				echo $res1;
				echo '<br />';
				if ($res2) {
					echo "<b>"._T('logo_survol')."</b>";
					echo $res2;
				}
				echo "</div>";
				echo fin_cadre_relief(true);
			}
		}
/*
		if ($lettre->statut == 'envoi_en_cours') {
			echo '<br />';
			echo debut_cadre_relief(_DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/statistiques.png', true, "", _T('lettresprive:statistiques'));
			echo "<div class='verdana1'>";
			echo "<b>"._T('lettresprive:envoi')."</b><br />";
			echo "<ul style='margin: 0px; padding-$spip_lang_left: 0px; margin-bottom: 5px;'>";
			echo "<li>"._T('lettresprive:debut')." : ".affdate($lettre->date_debut_envoi, 'h\hi d').' '.nom_mois($lettre->date_debut_envoi).'</li>';
			$nb_envois = $lettre->calculer_nb_envois();
			if ($nb_envois) {
				echo "<li>"._T('lettresprive:nb_envois').' : '.$lettre->calculer_nb_envois('envoye').'/'.$nb_envois.'<br />';
				$pourcentage = intval($lettre->calculer_nb_envois('envoye') / $nb_envois * 100);
				echo http_img_pack("jauge-vert.gif", ' ', 'height="10" width="'.$pourcentage.'"');
				echo http_img_pack("jauge-rouge.gif", ' ', 'height="10" width="'.(100 - $pourcentage).'"');
			}
			echo '</li>';
			echo "</ul>";
			echo "</div>";
			// 2 </div> suppélementaires
			echo "</div>";
			echo "</div>";
			echo fin_cadre_relief();
		}
*/
		echo bloc_des_raccourcis(
				icone_horizontale(_T('lettresprive:aller_liste_lettres'), generer_url_ecrire("lettres_tous"), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/lettre-24.png', 'rien.gif', false).
				icone_horizontale(_T('lettresprive:creer_nouvelle_lettre'), generer_url_ecrire("lettres_edit", 'id_rubrique='.$lettre->id_rubrique), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/lettre-24.png', 'creer.gif', false).
				icone_horizontale(_T('lettresprive:copier_cetter_lettre'), generer_url_action("copie_lettre", 'copie_lettre='.$lettre->id_lettre), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/copie.png', 'rien.gif', false)
			);

		if ($lettre->statut == 'envoyee')
			echo afficher_objets('clic', _T('lettresprive:clics'), array('SELECT' => 'COUNT(AC.id_clic) AS total, C.url AS url', 'FROM' => 'spip_clics AS C LEFT JOIN spip_abonnes_clics AS AC ON AC.id_clic=C.id_clic', 'WHERE' => 'C.id_lettre='.intval($lettre->id_lettre), 'GROUP BY' => 'C.url', 'ORDER BY' => 'total DESC, C.id_clic ASC'));

		echo afficher_objets('lettres_mini', _T('info_meme_rubrique'), array('FROM' => 'spip_lettres', 'WHERE' => 'id_rubrique='.intval($lettre->id_rubrique).' AND id_lettre!='.intval($lettre->id_lettre), 'ORDER BY' => 'date DESC'));

  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'lettres_tous'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'lettres_tous'),'data'=>''));

   		echo debut_droite('', true);



		$contexte = array('id' => $lettre->id_lettre);
		$fond = recuperer_fond("prive/contenu/lettre", $contexte);
		$fond = pipeline('afficher_contenu_objet', array('args' => array('type' => 'lettre', 'id_objet' => $lettre->id_lettre, 'contexte' => $contexte), 'data' => $fond));
		$onglet_contenu = "<div id='wysiwyg'>$fond</div>";

		$editer_mots = charger_fonction('editer_mots', 'inc');
		$onglet_proprietes = $editer_mots('lettre', $lettre->id_lettre, $cherche_mot, $select_groupe, true, false, 'lettres');
#	  . $dater($id_article, $flag_editable, $statut_article, 'article', 'articles', $date, $date_redac)
#	  . $editer_auteurs('article', $id_article, $flag_editable, $cherche_auteur, $ids)

		$onglet_documents = lettres_documents('lettre', $lettre->id_lettre);

		echo '<div class="fiche_objet">';

		if ($lettre->statut == 'brouillon') {
			echo '<div class="bandeau_actions">';
			echo '<div style="float: right;">';
			echo icone_inline(_T('lettresprive:modifier_lettre'), generer_url_ecrire("lettres_edit", "id_lettre=".$lettre->id_lettre), _DIR_PLUGIN_LETTRE_INFORMATION.'/prive/images/lettre-24.png', "edit.gif", $GLOBALS['spip_lang_left']);
			echo '</div>';
			echo '</div>';
		}
		echo '<h1>'.$lettre->titre.'</h1>';

	  	echo afficher_onglets_pages(
			  	array(
				  	'props' => _T('onglet_proprietes'),
				  	'voir' => _T('onglet_contenu'),
				  	'docs' => _T('onglet_documents'),
				),
			  	array(
				    'props' => $onglet_proprietes,
				    'voir' => $onglet_contenu,
				    'docs' => $onglet_documents
				)
			);

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		echo '</div><!-- fin fiche_objet -->';

/*
		if (strlen($lettre->descriptif) > 1) {
			echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
			echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
			echo image_reduire(propre($lettre->descriptif), 375, 0);
			echo "</font>";
			echo "</div>";
		}

		if ($lettre->statut == 'brouillon') {
			$editer_mot = charger_fonction('editer_mot', 'inc');
			echo $editer_mot('lettre', $lettre->id_lettre, $cherche_mot, $select_groupe, true);
		} else {
			echo '<br />';
		}
		
		echo generer_url_post_ecrire("lettres", "id_lettre=".$lettre->id_lettre, 'formulaire1');

		if ($lettre->statut == 'brouillon') {
			if ($lettre->programmer_envoi)
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/date.png', false, "", bouton_block_invisible('datepub')._T('lettresprive:envoi_programme').'&nbsp;&nbsp;('.majuscules(affdate($lettre->date)).')');
			else
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/date.png', false, "", bouton_block_invisible('datepub')._T('lettresprive:date').'&nbsp;&nbsp;('.majuscules(affdate($lettre->date)).')');
			echo debut_block_invisible('datepub');
			echo "<table border='0' width='100%' style='text-align: right'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('lettresprive:changer_date')."</B></span> &nbsp;</td>";
			echo "	<td colspan='2'>";
			echo afficher_jour(affdate($lettre->date, 'jour'), "name='jour' size='1' class='fondl'", true);
			echo afficher_mois(affdate($lettre->date, 'mois'), "name='mois' size='1' class='fondl'", true);
			echo afficher_annee(affdate($lettre->date, 'annee'), "name='annee' size='1' class='fondl'");
			echo "	</td>";
			echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_date' VALUE='"._T('lettresprive:changer')."' CLASS='fondo' STYLE='font-size:10px'></td>";
			echo "</tr>";
			echo "</table>";
			echo fin_block();
			fin_cadre_enfonce();
		} else {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/date.png', false, "", _T('lettresprive:date').'&nbsp;&nbsp;('.majuscules(affdate($lettre->date)).')');
			fin_cadre_enfonce();
		}

		$titre_barre = _T('lettresprive:auteurs');
		if ($lettre->statut == 'brouillon')
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/auteur.png', false, "", bouton_block_invisible('auteurs').$titre_barre);
		else
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/auteur.png', false, "", $titre_barre);
		$tableau_auteurs_interdits = array();
		$auteurs_associes = 'SELECT A.id_auteur,
								A.email,
								A.nom
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur
							WHERE AL.id_lettre="'.$lettre->id_lettre.'"
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
				echo "<A HREF='".generer_url_ecrire("auteur_infos","id_auteur=".$arr['id_auteur'], '&')."'>\n";
				echo typo($arr['nom']);
				echo "</A>\n";
				echo "</td>\n";
				echo "<td class='arial2'>\n";
				echo $arr['email'];
				echo "</td>\n";
				if ($lettre->statut == 'brouillon') {
					echo "<td class='arial1'>\n";
					echo "<A HREF='".generer_url_ecrire("lettres","id_lettre=".$lettre->id_lettre."&supprimer_auteur=".$arr['id_auteur'], true)."'>\n";
					echo _T('lettresprive:retirer_auteur').' '.http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'")."\n";
					echo "</A>\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</div>\n";
		}
		if ($lettre->statut == 'brouillon') {
			$auteurs_interdits = implode(",", $tableau_auteurs_interdits);
			if (!empty($auteurs_interdits))
				$where_auteurs_interdits = ' AND A.id_auteur NOT IN ('.$auteurs_interdits.')';
			else
				$where_auteurs_interdits = '';
			$requete = 'SELECT A.id_auteur, 
							A.nom,
							A.email
						FROM spip_auteurs AS A
						WHERE A.email!="" 
						'.$where_auteurs_interdits.'
						ORDER BY A.nom';
			$resultat_requete = spip_query($requete);
			if (@spip_num_rows($resultat_requete) > 0) {
				echo debut_block_invisible('auteurs');
				echo "<table border='0' width='100%'>";
				echo "<tr>";
				echo "	<td><span class='verdana1'><B>"._T('lettresprive:ajouter_auteur')."</B></span> &nbsp;</td>";
				echo "	<td>";
				echo "		<select name='id_auteur' SIZE='1' CLASS='fondl' style='width: 250px;'>";
				while ($arr = spip_fetch_array($resultat_requete)) {
					echo "				<option value='".$arr['id_auteur']."'>".propre($arr['nom'])." - ".$arr['email']."</option>";
				}
				echo "		</select>";
				echo "	</td>";
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='enregistrer_auteur' VALUE='"._T('lettresprive:choisir')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				echo fin_block();
			}
		}
		fin_cadre_enfonce();

		if ($GLOBALS['meta']['spip_lettres_utiliser_articles'] == 'oui') {
			$titre_barre = _T('lettresprive:articles');
			if ($lettre->statut == 'brouillon') {
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/articles.gif', false, "", bouton_block_invisible('arts').$titre_barre);
				$affiche = true;
			} else {
				if (spip_num_rows(spip_query('SELECT * FROM spip_articles_lettres WHERE id_lettre='.$lettre->id_lettre)) > 0)
					$affiche = true;
				else
					$affiche = false;
				if ($affiche)
					debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/articles.gif', false, "", $titre_barre);
			}
			if ($affiche) {
				$tableau_articles_interdits = array();
				$articles_associes = 'SELECT A.id_article,
										A.titre,
										A.statut,
										A.id_rubrique
									FROM spip_articles AS A
									INNER JOIN spip_articles_lettres AS AL ON AL.id_article=A.id_article
									WHERE AL.id_lettre="'.$lettre->id_lettre.'"
									ORDER BY A.titre';
				$resultat_articles_associes = spip_query($articles_associes);
				if (@spip_num_rows($resultat_articles_associes) > 0) {
					echo "<div class='liste'>\n";
					echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
					while ($arr = spip_fetch_array($resultat_articles_associes)) {
						$tableau_articles_interdits[] = $arr['id_article'];
						echo "<tr class='tr_liste'>\n";
						echo "<td width='25' class='arial11'>\n";
						echo puce_statut_article($arr['id_article'], $arr['statut'], $arr['id_rubrique'], 'article');
						echo "</td>\n";
						echo "<td class='arial2'>\n";
						echo "<A HREF='".generer_url_ecrire("articles","id_article=".$arr['id_article'])."'>\n";
						echo typo($arr['titre']);
						echo "</A>\n";
						echo "</td>\n";
						echo "<td class='arial1' width='50'>\n";
						echo _T('info_numero_abbreviation').$arr['id_article'];
						echo "</td>\n";
						if ($lettre->statut == 'brouillon') {
							echo "<td class='arial1' width='100'>\n";
							echo "<A HREF='".generer_url_ecrire("lettres","id_lettre=".$lettre->id_lettre."&supprimer_article=".$arr['id_article'], true)."'>\n";
							echo _T('lettresprive:retirer_article').' '.http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'")."\n";
							echo "</A>\n";
							echo "</td>\n";
						}
						echo "</tr>\n";
					}
					echo "</table>\n";
					echo "</div>\n";
				}
				if ($lettre->statut == 'brouillon') {
					$articles_interdits = implode(",", $tableau_articles_interdits);
					if (!empty($articles_interdits))
						$where_articles_interdits = ' WHERE A.id_article NOT IN ('.$articles_interdits.')';
					else
						$where_articles_interdits = '';
					$requete = 'SELECT A.id_article, 
									A.titre AS titre,
									A.id_rubrique,
									R.titre AS titre_rub 
								FROM spip_articles AS A
								INNER JOIN spip_rubriques AS R ON R.id_rubrique=A.id_rubrique
								'.$where_articles_interdits.'
								ORDER BY R.titre, A.titre';
					$resultat_requete = spip_query($requete);
					if (@spip_num_rows($resultat_requete) > 0) {
						echo debut_block_invisible('arts');
						echo "<table border='0' width='100%'>";
						echo "<tr>";
						echo "	<td width='120'><span class='verdana1'><B>"._T('lettresprive:ajouter_article')."</B></span> &nbsp;</td>";
						echo '	<td width="100"><input type="text" name="id_article" class="fondo" size="5" /></td>';
						echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='enregistrer_article' VALUE='"._T('lettresprive:choisir')."' CLASS='fondo' STYLE='font-size:10px'></td>";
						echo "</tr>";
						echo "</table>";
						echo fin_block();
					}
				}
			}
			if ($affiche)
				fin_cadre_enfonce();
		}

		if ($lettre->statut == 'envoyee') {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/renvoi.png', false, "", _T('lettresprive:renvoyer_lettre'));
			echo "<table border='0' width='100%'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('lettresprive:choix_abonne')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo "<input type='checkbox' name='tous' value='1' /> "._T('lettresprive:renvoyer_a_tous')."<br />";
			echo _T('lettresprive:ou_son_email')."<input type='text' name='email_abonne' value='".$_POST['email_abonne']."' />";
			if ($abonne_inexistant)
			 	echo '<br /><strong>'._T('lettresprive:abonne_inexistant').'</strong>';
			echo "	</td>";
			echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='renvoyer_lettre' VALUE='"._T('lettresprive:renvoyer')."' CLASS='fondo' STYLE='font-size:10px'></td>";
			echo "</tr>";
			echo "</table>";
			fin_cadre_enfonce();
		}
		
		echo '</form>';

		echo '<form action="'.generer_url_action('statut_lettre','id_lettre='.$lettre->id_lettre).'" method="post">';


		echo "<div $dir_lang style='padding: 10px;'>";
		echo image_reduire(propre($lettre->texte), 475, 0);
		echo "<br clear='both' />";
		echo "</div>";

		if ($GLOBALS['meta']['spip_lettres_utiliser_ps'] == 'oui') {
			if ($lettre->ps) {
				echo debut_cadre_enfonce('',true);
				echo "<div $dir_lang style='font-size: small;' class='verdana1'>";
				echo justifier("<b>"._T('info_ps')."</b> ".image_reduire(propre($lettre->ps), 475, 0));
				echo "</div>";
				echo fin_cadre_enfonce(true);
			}
		}
		
		if ($champs_extra and $lettre->extra) {
			echo extra_affichage($lettre->extra, "lettres");
		}

		echo '</form>';


		fin_cadre_relief();

		echo '<br/>';

		if ($lettre->statut == 'brouillon') {
			$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($lettre->id_rubrique);
			$rubriques_virgules = implode(',', $rubriques);
			$nb_abonnes = spip_num_rows(spip_query('SELECT A.id_abonne FROM spip_abonnes AS A INNER JOIN spip_abonnes_rubriques AS RUB ON RUB.id_abonne=A.id_abonne WHERE RUB.statut="valide" AND RUB.id_rubrique IN ('.$rubriques_virgules.') GROUP BY A.id_abonne'));
			echo lettres_afficher_abonnes(_T('lettresprive:les_abonnes_suivants_recevront_cette_lettre').' ('._T('lettresprive:total').' : '.$nb_abonnes.')', array("FROM" => 'spip_abonnes AS A, spip_abonnes_rubriques AS AR', "WHERE" => "A.id_abonne=AR.id_abonne AND AR.id_rubrique IN ($rubriques_virgules)", 'ORDER BY' => "AR.date_abonnement DESC", 'GROUP BY' => 'A.id_abonne', 'LIMIT' => '100'), $lettre->id_rubrique);
		} else {
			echo lettres_afficher_abonnes(_T('lettresprive:les_abonnes_suivants_ont_recu_cette_lettre').' ('._T('lettresprive:total').' : '.$lettre->calculer_nb_envois().')', array("FROM" => 'spip_abonnes AS A, spip_abonnes_lettres AS AL', "WHERE" => "A.id_abonne=AL.id_abonne AND AL.id_lettre=".$lettre->id_lettre, 'ORDER BY' => "A.maj DESC", 'GROUP BY' => 'A.id_abonne', 'LIMIT' => '100'), $lettre->id_rubrique);
		}

		if ($lettre->statut == 'envoi_en_cours') {
			echo '<script language="javascript" type="text/javascript">'."\n"; 
			echo 'document.location.href="'.generer_url_action('statut_lettre','id_lettre='.$lettre->id_lettre.'&changer_statut=1&statut=envoi_en_cours', true).'";'."\n"; 
			echo '</script>'."\n"; 
		}
*/

		echo fin_gauche();

		echo fin_page();

	}


	function lettres_documents($type, $id) {
		global $spip_lang_left, $spip_lang_right;

		// Joindre ?
		if  ($GLOBALS['meta']["documents_$type"]=='non'
		OR !autoriser('joindre', $type, $id))
			$res = '';
		else {
			$joindre = charger_fonction('joindre', 'inc');

			$res = $joindre(array(
				'cadre' => 'relief',
				'icone' => 'image-24.gif',
				'fonction' => 'creer.gif',
				'titre' => _T('titre_joindre_document'),
				'script' => 'lettres',
				'args' => "id_lettre=$id",
				'id' => $id,
				'intitule' => _T('info_telecharger_ordinateur'),
				'mode' => 'document',
				'type' => 'lettre',
				'ancre' => '',
				'id_document' => 0,
				'iframe_script' => generer_url_ecrire("documenter","id_lettre=$id&type=$type",true)
			));

			// eviter le formulaire upload qui se promene sur la page
			// a cause des position:relative incompris de MSIE
			if ($GLOBALS['browser_name']!='MSIE') {
				$res = "\n<table style='float: $spip_lang_right' width='50%' cellpadding='0' cellspacing='0' border='0'>\n<tr><td style='text-align: $spip_lang_left;'>\n$res</td></tr></table>";
			}

			$res .= http_script('',"async_upload.js")
			  . http_script('$("form.form_upload").async_upload(async_upload_portfolio_documents);');
		}

		$documenter = charger_fonction('documenter', 'inc');

		$flag_editable = autoriser('modifier', 'lettres', $id);

		return "<div id='portfolio'>" . $documenter($id, $type, 'portfolio') . "</div><br />"
		. "<div id='documents'>" . $documenter($id, $type, 'documents') . "</div>"
		. $res;
	}


?>