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
	include_spip('lettres_fonctions');


	function exec_lettres() {

		if (!autoriser('voir', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$lettre = new lettre($_GET['id_lettre']);
		
		pipeline('exec_init',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre, true);

		if (!empty($_GET['supprimer_article'])) {
			$lettre->supprimer_article($_GET['supprimer_article']);
			header('Location: '.$url);
			exit();
		}

		if (!empty($_POST['enregistrer_article'])) {
			$lettre->enregistrer_article($_POST['id_article']);
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
					$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre.'&message=renvoi_'.($resultat ? 'ok' : 'ko'), true);
					header('Location: '.$url);
					exit();
				} else {
					$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre.'&message=abonne_inexistant', true);
					header('Location: '.$url);
					exit();
				}
			}
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($lettre->titre, "naviguer", "lettres_tous");

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

		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo '<strong>'._T('lettresprive:cette_lettre').'</strong>';
		echo '<ul>';
		if ($lettre->statut == 'brouillon') {
			echo '<li class="prepa selected">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('lettresprive:en_cours_de_redaction').'</li>';
			echo '<li class="prop"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=test', false, true).'">'.http_img_pack('puce-orange.gif', 'puce-orange', '')._T('lettresprive:tester').'</a></li>';
			echo '<li class="prop"><a href="'.generer_url_action('copie_lettre', 'copie_lettre='.$lettre->id_lettre, false, true).'">'.http_img_pack('puce-orange.gif', 'puce-orange', '')._T('lettresprive:copier').'</a></li>';
			echo '<li class="publie"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=envoi_en_cours', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('lettresprive:envoyer').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('lettresprive:a_la_poubelle').'</a></li>';
		}
		if ($lettre->statut == 'envoi_en_cours') {
			echo '<li class="prop selected">'.http_img_pack('puce-orange.gif', 'puce-orange', '')._T('lettresprive:envoi_en_cours').'</li>';
			echo '<li class="refuse"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=envoyee', false, true).'">'.http_img_pack('puce-rouge.gif', 'puce-rouge', '')._T('lettresprive:arreter_envoi').'</a></li>';
		}
		if ($lettre->statut == 'envoyee') {
			echo '<li class="publie selected">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('lettresprive:envoyee').'</li>';
			echo '<li class="prop"><a href="'.generer_url_action('copie_lettre', 'copie_lettre='.$lettre->id_lettre, false, true).'">'.http_img_pack('puce-orange.gif', 'puce-orange', '')._T('lettresprive:copier').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_lettre', 'id_lettre='.$lettre->id_lettre.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('lettresprive:a_la_poubelle').'</a></li>';
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
		if (autoriser('previsualiser') and $lettre->statut == 'brouillon') {
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_html'], 'id_lettre='.$lettre->id_lettre.'&lang='.$lettre->lang.'&var_mode=preview').'" class="cellule-h" target="_blank"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('lettresprive:previsualiser_html').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_html'], 'id_lettre='.$lettre->id_lettre.'&var_mode=preview').'" class="cellule-h" target="_blank">'._T('lettresprive:previsualiser_html').'</a></td></tr></table>';
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_texte'], 'id_lettre='.$lettre->id_lettre.'&lang='.$lettre->lang.'&var_mode=preview').'" class="cellule-h" target="_blank"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('lettresprive:previsualiser_texte').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_public($GLOBALS['meta']['spip_lettres_fond_lettre_texte'], 'id_lettre='.$lettre->id_lettre.'&var_mode=preview').'" class="cellule-h" target="_blank">'._T('lettresprive:previsualiser_texte').'</a></td></tr></table>';
		}
		if ($lettre->statut == 'envoyee') {
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

		if ($lettre->statut == 'envoyee')
			echo afficher_objets('clic', _T('lettresprive:clics'), array('SELECT' => 'COUNT(AC.id_clic) AS total, C.url AS url', 'FROM' => 'spip_clics AS C LEFT JOIN spip_abonnes_clics AS AC ON AC.id_clic=C.id_clic', 'WHERE' => 'C.id_lettre='.intval($lettre->id_lettre), 'GROUP BY' => 'C.url', 'ORDER BY' => 'total DESC, C.id_clic ASC'));

		echo afficher_objets('lettres_mini', _T('info_meme_rubrique'), array('FROM' => 'spip_lettres', 'WHERE' => 'id_rubrique='.intval($lettre->id_rubrique).' AND id_lettre!='.intval($lettre->id_lettre), 'ORDER BY' => 'maj DESC'));

		echo bloc_des_raccourcis(
				icone_horizontale(_T('lettresprive:creer_nouvelle_lettre'), generer_url_ecrire("lettres_edit"), _DIR_PLUGIN_LETTRES."prive/images/lettre-24.png", 'creer.gif', false).
				icone_horizontale(_T('lettresprive:aller_liste_lettres'), generer_url_ecrire("lettres_tous"), _DIR_PLUGIN_LETTRES.'prive/images/lettre-24.png', 'rien.gif', false).
				icone_horizontale(_T('lettresprive:ajouter_abonne'), generer_url_ecrire('abonnes_edit'), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', 'creer.gif', false)
			);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

   		echo debut_droite('', true);

		$articles = '';
		if ($GLOBALS['meta']['spip_lettres_utiliser_articles'] == 'oui') {
			if ($lettre->statut == 'brouillon') {
				$articles.= '<form method="post" action="'.generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre).'">';
				$articles.= debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/articles.gif', true, "", _T('lettresprive:articles'));
				$affiche = true;
			} else {
				if (sql_countsel('spip_articles_lettres', 'id_lettre='.intval($lettre->id_lettre)))
					$affiche = true;
				else
					$affiche = false;
				if ($affiche) {
					$articles.= '<form method="post" action="'.generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre).'">';
					$articles.= debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/articles.gif', true, "", _T('lettresprive:articles'));
				}
			}
			if ($affiche) {
				$tableau_articles_interdits = array();
				$resultat_articles_associes = sql_select('A.id_article, A.titre, A.statut, A.id_rubrique', 'spip_articles AS A INNER JOIN spip_articles_lettres AS AL ON AL.id_article=A.id_article', 'AL.id_lettre='.intval($lettre->id_lettre), '', 'A.titre');
				if (@sql_count($resultat_articles_associes) > 0) {
					$articles.= "<div class='liste'>\n";
					$articles.= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
					while ($arr = sql_fetch($resultat_articles_associes)) {
						$articles.= "<tr class='tr_liste'>\n";
						$articles.= "<td class='arial1' width='30'>\n";
						$articles.= _T('info_numero_abbreviation').$arr['id_article'];
						$articles.= "</td>\n";
						$articles.= "<td class='arial2'>\n";
						$articles.= "<a href='".generer_url_ecrire("articles","id_article=".$arr['id_article'])."'>\n";
						$articles.= typo($arr['titre']);
						$articles.= "</a>\n";
						$articles.= "</td>\n";
						if ($lettre->statut == 'brouillon') {
							$articles.= "<td class='arial1' width='100'>\n";
							$articles.= "<a href='".generer_url_ecrire("lettres","id_lettre=".$lettre->id_lettre."&supprimer_article=".$arr['id_article'], true)."'>\n";
							$articles.= _T('lettresprive:retirer_article').' '.http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'")."\n";
							$articles.= "</a>\n";
							$articles.= "</td>\n";
						}
						$articles.= "</tr>\n";
					}
					$articles.= "</table>\n";
					$articles.= "</div>\n";
				}
				if ($lettre->statut == 'brouillon') {
					$articles.= _T('lettresprive:ajouter_article');
					$articles.= '&nbsp;<input type="text" name="id_article" size="5" />&nbsp;';
					$articles.= '<input type="submit" name="enregistrer_article" value="'._T('lettresprive:ajouter').'" class="fondo">';
				}
			}
			if ($affiche) {
				$articles.= fin_cadre_enfonce(true);
				$articles.= '</form>';
			}
		}

		$editer_mots = charger_fonction('editer_mots', 'inc');
		$editer_auteurs = charger_fonction('editer_auteurs', 'inc');
		$dater = charger_fonction('dater', 'inc');

		if ($lettre->statut == 'envoyee') {
			$renvoi = '<form method="post" action="'.generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre).'">';
			$renvoi.= debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/renvoi.png', true, "", _T('lettresprive:renvoyer_lettre'));
			$renvoi.= '<p><label><input type="checkbox" name="tous" value="1" /> '._T('lettresprive:renvoyer_a_tous').'</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <label>'._T('lettresprive:ou_abonne').' <input type="text" name="email_abonne" value="" /></label></p>';
			$renvoi.= '<div align="right">';
			$renvoi.= '<input type="submit" name="renvoyer_lettre" class="fondo" value="'._T('lettresprive:renvoyer').'" />';
			$renvoi.= '</div>';
			$renvoi.= fin_cadre_enfonce(true);
			$renvoi.= '</form>';
		}

		$onglet_proprietes = $dater($lettre->id_lettre, true, $lettre->statut, 'lettre', 'lettres', $lettre->date);
		$onglet_proprietes.= $renvoi;
		$onglet_proprietes.= $editer_mots('lettre', $lettre->id_lettre, $cherche_mot, $select_groupe, ($lettre->statut == 'brouillon'), '', 'lettres');
		$onglet_proprietes.= $editer_auteurs('lettre', $lettre->id_lettre, ($lettre->statut == 'brouillon'), '', 'lettres');
		$onglet_proprietes.= $articles;

		$contexte = array('id' => $lettre->id_lettre);
		$fond = recuperer_fond("prive/contenu/lettre", $contexte);
		$fond = pipeline('afficher_contenu_objet', array('args' => array('type' => 'lettre', 'id_objet' => $lettre->id_lettre, 'contexte' => $contexte), 'data' => $fond));
		$onglet_contenu = "<div id='wysiwyg'>$fond</div>";

		if ($lettre->statut == 'brouillon')
			$onglet_documents = lettres_documents('lettre', intval($lettre->id_lettre));
	
		if ($lettre->statut == 'envoi_en_cours') {
			echo '<div id="progression_envoi_lettre"></div>';

			echo '<script type="text/javascript">'."\n";
			echo 'function progression() {'."\n";
			echo '  $.post("'.generer_url_action('progression_envoi_lettre', 'id_lettre='.$lettre->id_lettre, true, true).'", function(xml) {'."\n";
			echo '    fin = $("fin", xml).text();';
			echo '    if (fin == 1) {'."\n";
			echo '      window.location.href="'.generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre.'&message=envoi_termine', true).'";'."\n";
			echo '    } else {'."\n";
			echo '      $("#progression_envoi_lettre").load("'.generer_url_ecrire('progression_envoi_lettre', 'id_lettre='.$lettre->id_lettre, true).'");'."\n";
			echo '      progression();'."\n";
			echo '    }'."\n";
			echo '	});'."\n";
			echo '}'."\n";
			echo '$("#progression_envoi_lettre").load("'.generer_url_ecrire('progression_envoi_lettre', 'id_lettre='.$lettre->id_lettre, true).'");'."\n";
			echo 'progression();'."\n";
			echo '</script>'."\n";
		}

		if (isset($_GET['message'])) {
			echo '<div style="padding: 10px; border: 1px solid red; margin-bottom: 15px; background: #fff; color: red; font-weight: bold; text-align: center;">';
			echo '<div style="float: right;"><a href="'.generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre).'">'.http_img_pack('croix-rouge.gif', "x", "").'</a></div>';
			echo _T('lettresprive:'.$_GET['message']);
			echo '</div>';
		}

		echo '<div class="fiche_objet">';

		if ($lettre->statut == 'brouillon') {
			echo '<div class="bandeau_actions">';
			echo '<div style="float: right;">';
			echo icone_inline(_T('lettresprive:modifier_lettre'), generer_url_ecrire("lettres_edit", "id_lettre=".$lettre->id_lettre), _DIR_PLUGIN_LETTRES.'prive/images/lettre-24.png', "edit.gif", $GLOBALS['spip_lang_left']);
			echo '</div>';
			echo '</div>';
		}
		echo '<h1>'.$lettre->titre.'</h1>';
		
		echo '<br class="nettoyeur" />';

	  	echo afficher_onglets_pages(
			  	array(
				  	'props' => _T('onglet_proprietes'),
				  	'voir' => _T('onglet_contenu'),
				  	'docs' => _T('onglet_documents')
				),
			  	array(
				    'props' => $onglet_proprietes,
				    'voir' => $onglet_contenu,
				    'docs' => $onglet_documents
				)
			);

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		echo '</div><!-- fin fiche_objet -->';


		if ($lettre->statut == 'brouillon') {
			$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($lettre->id_rubrique);
			$rubriques_virgules = implode(',', $rubriques);
			$abonnes = array();
			$res = sql_select('id_abonne', 'spip_abonnes_rubriques', 'id_rubrique IN ('.$rubriques_virgules.')');
			while ($arr = sql_fetch($res))
				$abonnes[] = $arr['id_abonne'];
			$abonnes_virgules = implode(',', $abonnes);
			if (count($abonnes))
				echo afficher_objets('abonne', _T('lettresprive:tous_abonnes_rubrique'), array('FROM' => 'spip_abonnes', 'WHERE' => 'id_abonne IN ('.$abonnes_virgules.')', 'ORDER BY' => 'maj DESC'), array('id_rubrique' => $lettre->id_rubrique));
		} else {
			$abonnes = array();
			$res = sql_select('id_abonne', 'spip_abonnes_lettres', 'id_lettre='.$lettre->id_lettre);
			while ($arr = sql_fetch($res))
				$abonnes[] = $arr['id_abonne'];
			$abonnes_virgules = implode(',', $abonnes);
			if (count($abonnes))
				echo afficher_objets('abonne', _T('lettresprive:les_abonnes_suivants_ont_recu_cette_lettre'), array('FROM' => 'spip_abonnes', 'WHERE' => 'id_abonne IN ('.$abonnes_virgules.')', 'ORDER BY' => 'maj DESC'), array('id_lettre' => $lettre->id_lettre));
		}

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

		return "<div id='portfolio'>" . $documenter($id, $type, 'portfolio') . "</div><br />"
		. "<div id='documents'>" . $documenter($id, $type, 'documents') . "</div>"
		. $res;
	}


?>