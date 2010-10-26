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

		$lettre = new lettre(_request('id_lettre'));
		
		pipeline('exec_init',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre, true);

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($lettre->titre, "naviguer", "lettres_tous");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($lettre->id_rubrique);
		echo fin_grand_cadre(true);

		if ($lettre->statut == 'envoi_en_cours') {
			include_spip('inc/delivrer');
			$delivrer = lettres_delivrer_surveille_ajax($lettre->id_lettre,generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre.'&message=envoi_termine', true));
			// plus rien a faire : hop on la passe en envoyee
			if (!$delivrer)
				$lettre->enregistrer_statut('envoyee');
		}

		echo debut_gauche('', true);
		echo debut_boite_info(true);
		echo lettre_boite_info($lettre);
		echo fin_boite_info(true);

		$flag_editable = autoriser('modifier','lettre',$lettre->id_lettre);
		$iconifier = charger_fonction('iconifier', 'inc');
		echo $iconifier('id_lettre', $lettre->id_lettre, 'lettres', false, $flag_editable);


		if ($lettre->statut == 'envoyee')
			echo afficher_objets('clic', _T('lettresprive:clics'), array('SELECT' => 'COUNT(AC.id_clic) AS total, C.url AS url', 'FROM' => 'spip_clics AS C LEFT JOIN spip_abonnes_clics AS AC ON AC.id_clic=C.id_clic', 'WHERE' => 'C.id_lettre='.intval($lettre->id_lettre), 'GROUP BY' => 'C.url', 'ORDER BY' => 'total DESC, C.id_clic ASC'));

		echo afficher_objets('lettres_mini', _T('info_meme_rubrique'), array('FROM' => 'spip_lettres', 'WHERE' => 'id_rubrique='.intval($lettre->id_rubrique).' AND id_lettre!='.intval($lettre->id_lettre).' AND statut!=\'poub\'', 'ORDER BY' => 'maj DESC'));

		echo bloc_des_raccourcis(
				icone_horizontale(_T('lettresprive:creer_nouvelle_lettre'), generer_url_ecrire("lettres_edit"), _DIR_PLUGIN_LETTRES."prive/images/lettre-24.png", 'creer.gif', false)
				. ((intval($lettre->id_lettre) AND $lettre->statut !== 'envoi_en_cours')?
				  icone_horizontale(_T('lettresprive:copier'), generer_action_auteur("dupliquer_lettre", $lettre->id_lettre,self()), _DIR_PLUGIN_LETTRES."prive/images/lettre-dupliquer-24.png", 'creer.gif', false)
				  :"")
				. icone_horizontale(_T('lettresprive:aller_liste_lettres'), generer_url_ecrire("lettres_tous"), _DIR_PLUGIN_LETTRES.'prive/images/lettre-24.png', 'rien.gif', false)
				. icone_horizontale(_T('lettresprive:ajouter_abonne'), generer_url_ecrire('abonnes_edit',"id_rubrique=".$lettre->id_rubrique), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', 'creer.gif', false)
			);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'lettres','id_lettre'=>$lettre->id_lettre),'data'=>''));

		echo debut_droite('', true);

		$articles = '';
		if ($GLOBALS['meta']['spip_lettres_utiliser_articles'] == 'oui') {
			$auth = autoriser('joindrearticle','lettre',$lettre->id_lettre);
			$action = generer_action_auteur("joindrearticle_lettre",$lettre->id_lettre,self());
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
					if ($auth) {
						$articles.= "<td class='arial1' width='100'>\n";
						$articles.= "<a href='".parametre_url($action,'id_article',-$arr['id_article'])."'>\n";
						$articles.= _T('lettresprive:retirer_article').' '.http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'")."\n";
						$articles.= "</a>\n";
						$articles.= "</td>\n";
					}
					$articles.= "</tr>\n";
				}
				$articles.= "</table>\n";
				$articles.= "</div>\n";
			}
			if ($auth) {
				$articles.= '<form method="post" action="'.$action.'">'
				 . '<div>'.form_hidden($action).'</div>';
				$articles.= _T('lettresprive:ajouter_article');
				$articles.= '&nbsp;<input type="text" name="id_article" size="5" />&nbsp;';
				$articles.= '<input type="submit" name="enregistrer_article" value="'._T('lettresprive:ajouter').'" class="fondo">';
				$articles.= '</form>';
			}
			if (strlen($articles)) {
				$articles =
				  debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/articles.gif', true, "", _T('lettresprive:articles'))
					. $articles
					. fin_cadre_enfonce(true);
			}
		}

		$editer_mots = charger_fonction('editer_mots', 'inc');
		$editer_auteurs = charger_fonction('editer_auteurs', 'inc');
		$dater = charger_fonction('dater', 'inc');

		if ($lettre->statut == 'envoyee') {
			$action = generer_action_auteur("renvoyer_lettre",$lettre->id_lettre,self());

			$renvoi = '<form method="post" action="'.$action.'">';
			$renvoi.= "<div>".form_hidden($action)."</div>";
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
		$onglet_proprietes.= $editer_mots('lettre', $lettre->id_lettre, $cherche_mot, $select_groupe, $flag_editable, '', 'lettres');
		$onglet_proprietes.= $editer_auteurs('lettre', $lettre->id_lettre, ($lettre->statut == 'brouillon'), '', 'lettres');
		$onglet_proprietes.= $articles;

		$contexte = array('id' => $lettre->id_lettre);
		$fond = recuperer_fond("prive/contenu/lettre", $contexte);
		$fond = pipeline('afficher_contenu_objet', array('args' => array('type' => 'lettre', 'id_objet' => $lettre->id_lettre, 'contexte' => $contexte), 'data' => $fond));
		$onglet_contenu = "<div id='wysiwyg'>$fond</div>";

		$documenter_objet = charger_fonction('documenter_objet','inc');
		$onglet_documents = $documenter_objet($lettre->id_lettre,'lettre','lettres',$flag_editable);
	
		echo $delivrer;

		if ($m = _request('message')
			AND in_array($m,array('test_ok','test_ko','renvoi_ok','renvoi_ko','envoi_termine'))) {
			$ok = in_array($m,array('test_ok','renvoi_ok','envoi_termine'))?'success':'error';
			$balise_img = chercher_filtre('balise_img');
			echo "<div class='$ok'>";
			echo '<div style="float: right;"><a href="'.parametre_url(self(), 'message','')
			  .'">'.$balise_img(find_in_path('img_pack/frame-close.png'),"x").'</a></div>';
			echo _T('lettresprive:'.$m);
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
			echo afficher_objets('abonne',
				_T('lettresprive:tous_abonnes_rubrique'),
				array('FROM' => 'spip_abonnes_rubriques', 'WHERE' => sql_in('id_rubrique',$rubriques), 'ORDER BY' => 'date_abonnement DESC'), array('id_rubrique' => $lettre->id_rubrique));
		} else {
			echo afficher_objets('abonne',
				_T('lettresprive:les_abonnes_suivants_ont_recu_cette_lettre'),
				array('FROM' => 'spip_abonnes_lettres', 'WHERE' => 'id_lettre='.intval($lettre->id_lettre), 'ORDER BY' => 'maj DESC'), array('id_lettre' => $lettre->id_lettre));
		}

		echo fin_gauche();

		echo fin_page();

	}

	function lettre_boite_info(&$lettre){
		$res = "";
		$res.='<div class="infos">';
		$res.='<div class="numero">';
		$res.=_T('lettresprive:lettre_numero').' :';
		$res.='<p>'.$lettre->id_lettre.'</p>';
		$res.='</div>';

		$res.='<ul class="instituer instituer_article">';
		$res.='<li>';
		$res.='<strong>'._T('lettresprive:cette_lettre').'</strong>';
		$res.='<ul>';
		$href = generer_action_auteur('instituer_lettre',$lettre->id_lettre,self());

		if ($lettre->statut == 'brouillon') {
			$res.='<li class="prepa selected">'.puce_statut('prepa')._T('lettresprive:en_cours_de_redaction').'</li>';
			$res.='<li class="publie"><a href="'
			  .parametre_url($href,'statut_nouv','envoi_en_cours').'">'
				.puce_statut('publie')._T('lettresprive:envoyer').'</a></li>';
			$res.='<li class="poubelle"><a href="'
			  .parametre_url($href,'statut_nouv','poubelle').'">'
			  .puce_statut('poubelle')._T('lettresprive:a_la_poubelle').'</a></li>';
		}
		if ($lettre->statut == 'envoi_en_cours') {
			$res.='<li class="prop selected">'.puce_statut('prop')._T('lettresprive:envoi_en_cours').'</li>';
			$res.='<li class="refuse"><a href="'
			  .parametre_url($href,'statut_nouv','envoyee').'">'
				.puce_statut('refuse')._T('lettresprive:arreter_envoi').'</a></li>';
		}
		if ($lettre->statut == 'envoyee') {
			$res.='<li class="publie selected">'.puce_statut('publie')._T('lettresprive:envoyee').'</li>';
			$res.='<li class="poubelle"><a href="'
			  .parametre_url($href,'statut_nouv','poubelle').'">'
			  .puce_statut('poubelle')._T('lettresprive:a_la_poubelle').'</a></li>';
		}
		if ($lettre->statut == 'poub') {
			$res.='<li class="poubelle selected">'.puce_statut('poubelle')._T('lettresprive:a_la_poubelle').'</li>';
		}
		$res.='</ul>';
		$res.='</li>';
		$res.='</ul>';

		if ($lettre->statut == 'envoyee') {
			$res.='<ul class="instituer instituer_article">';
			$res.='<li>';
			$res.='<strong>'._T('lettresprive:envoi').'</strong>';
			$res.='<ul>';
			$res.='<li>'._T('lettresprive:debut').' : '.affdate($lettre->date_debut_envoi, 'h\hi d').' '.nom_mois($lettre->date_debut_envoi).'</li>';
			$res.='<li>'._T('lettresprive:fin').' : '.affdate($lettre->date_fin_envoi, 'h\hi d').' '.nom_mois($lettre->date_fin_envoi).'</li>';
			if ($lettre->calculer_nb_envois('envoye'))
				$res.='<li>'._T('lettresprive:nb_envois').' : '.$lettre->calculer_nb_envois('envoye').'/'.$lettre->calculer_nb_envois().'</li>';
			if ($lettre->calculer_nb_envois('annule'))
				$res.='<li>'._T('lettresprive:nb_annules').' : '.$lettre->calculer_nb_envois('annule').'/'.$lettre->calculer_nb_envois().'</li>';
			if ($lettre->calculer_nb_envois('echec'))
				$res.='<li>'._T('lettresprive:nb_echecs').' : '.$lettre->calculer_nb_envois('echec').'/'.$lettre->calculer_nb_envois().'</li>';
			$res.='</ul>';
			$res.='</li>';
			$res.='<li>';
			$res.='<strong>'._T('lettresprive:format').'</strong>';
			$res.='<ul>';
			if ($lettre->calculer_pourcentage_format('mixte'))
				$res.='<li>'._T('lettresprive:mixte').' : '.$lettre->calculer_pourcentage_format('mixte').'%</li>';
			if ($lettre->calculer_pourcentage_format('html'))
				$res.='<li>'._T('lettresprive:html').' : '.$lettre->calculer_pourcentage_format('html').'%</li>';
			if ($lettre->calculer_pourcentage_format('texte'))
				$res.='<li>'._T('lettresprive:texte').' : '.$lettre->calculer_pourcentage_format('texte').'%</li>';
			$res.='</ul>';
			$res.='</li>';
			if ($lettre->calculer_taux_ouverture()) {
				$res.='<li>';
				$res.='<strong>'._T('lettresprive:audience').'</strong>';
				$res.='<ul>';
				$res.='<li>'._T('lettresprive:taux_ouverture').' : '.$lettre->calculer_taux_ouverture().'%</li>';
				$res.='</ul>';
				$res.='</li>';
			}
			$res.='</ul>';
		}
		if (autoriser('previsualiser','lettre',$lettre->id_lettre)) {
			$res.=icone_horizontale(_T('lettresprive:previsualiser_html'), generer_url_public('lettre_preview', 'format=html&id_lettre='.$lettre->id_lettre.'&var_mode=preview'), "racine-24.gif", '', false,' target="_blank"');
			$res.=icone_horizontale(_T('lettresprive:previsualiser_texte'), generer_url_public('lettre_preview', 'format=texte&id_lettre='.$lettre->id_lettre.'&var_mode=preview'), "racine-24.gif", '', false,' target="_blank"');
		}
		if (autoriser('tester','lettre',$lettre->id_lettre)) {
			$res.=icone_horizontale(_T('lettresprive:tester'), generer_action_auteur('tester_lettre', $lettre->id_lettre, self()), _DIR_PLUGIN_LETTRES."prive/images/lettre-tester-24.png", '', false);
		}

		if ($lettre->statut == 'envoyee') {
			$res.=icone_horizontale(_T('lettresprive:voir_en_ligne'), generer_url_lettre($lettre->id_lettre), "racine-24.gif", '', false,' target="_blank"');
		}
		$res.='</div>';
		return $res;
	}

?>