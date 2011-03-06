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
	include_spip('lettres_fonctions');
	include_spip('inc/presentation');


	function exec_abonnes() {

		if (!autoriser('voir', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_abonne = $_GET['id_abonne'];
		$abonne = new abonne($id_abonne);

		pipeline('exec_init', array('args' => array('exec' => 'abonnes', 'id_abonne' => $abonne->id_abonne), 'data' => ''));

		if (!empty($_POST['abonner'])) {
			$abonne->enregistrer_abonnement($_POST['id_parent']);
			$abonne->valider_abonnement($_POST['id_parent']);
			$url = generer_url_ecrire('abonnes','id_abonne='.$id_abonne, true);
			header('Location: '.$url);
			exit();
		}

		if (isset($_GET['desabonner'])) {
			$abonne->valider_desabonnement($_GET['desabonner']);
			$url = generer_url_ecrire('abonnes','id_abonne='.$id_abonne, true);
			header('Location: '.$url);
			exit();
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:abonnes'), "naviguer", "abonnes_tous");

		echo debut_gauche('', true);
		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('lettresprive:abonne_numero').' :';
		echo '<p>'.$abonne->id_abonne.'</p>';
		echo '</div>';

		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo '<strong>'._T('lettresprive:cet_abonne').'</strong>';
		echo '<ul>';
		switch ($abonne->calculer_statut()) {
			case 'a_valider':
				echo '<li class="prepa selected">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('lettresprive:a_valider').'</li>';
				echo '<li class="publie"><a href="'.generer_url_action('statut_abonne', 'id_abonne='.$abonne->id_abonne.'&statut=valider', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('lettresprive:valider_abonnements').'</a></li>';
				break;
			case 'valide':
				echo '<li class="publie selected">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('lettresprive:valide').'</li>';
				break;
			case 'vide':
				echo '<li class="poubelle selected">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('lettresprive:orphelin').'</li>';
				break;
		}
		echo '<li class="refuse"><a href="'.generer_url_action('statut_abonne', 'id_abonne='.$abonne->id_abonne.'&statut=poubelle', false, true).'">'.http_img_pack('puce-rouge.gif', 'puce-rouge', '')._T('lettresprive:a_la_poubelle').'</a></li>';
		echo '</ul>';
		echo '</li>';
		echo '</ul>';

		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo bloc_des_raccourcis(
				icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire("abonnes_tous"), _DIR_PLUGIN_LETTRES."prive/images/abonne.png", 'rien.gif', false).
				icone_horizontale(_T('lettresprive:ajouter_abonne'), generer_url_ecrire('abonnes_edit'), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', 'creer.gif', false)
			);

  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'abonnes', 'id_abonne' => $abonne->id_abonne), 'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'abonnes', 'id_abonne' => $abonne->id_abonne), 'data'=>''));

   		echo debut_droite('', true);
	
		echo '<div class="fiche_objet">';

		global $table_des_abonnes;
		echo '<div class="bandeau_actions">';
		echo '<div style="float: right;">';
		echo icone_inline($table_des_abonnes[$abonne->objet]['url_prive_titre'], generer_url_ecrire($table_des_abonnes[$abonne->objet]['url_prive'], $table_des_abonnes[$abonne->objet]['champ_id'].'='.$abonne->id_objet), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', "edit.gif", $GLOBALS['spip_lang_left']);
		echo '</div>';
		echo '</div>';

		echo '<h1>'.$abonne->email.'</h1>';
		
		echo '<br class="nettoyeur" />';

		$abonnements = sql_select('*', 'spip_abonnes_rubriques', 'id_abonne='.intval($abonne->id_abonne), '', 'date_abonnement DESC');
		if (sql_count($abonnements) > 0) {
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/rubrique-24.png', true, "", _T('lettresprive:boite_abonnements'));
			echo '<table cellpadding="2" cellspacing="0" width="100%" class="arial2" style="border: 1px solid #aaaaaa;">';
			while ($abo = sql_fetch($abonnements)) {
				$id_rubrique = $abo['id_rubrique'];
				$statut = $abo['statut'];
				if ($GLOBALS['meta']['spip_lettres_admin_abo_toutes_rubriques']=='oui') {
					$rubouthem = "spip_rubriques";
					$titre0 = _T('lettresprive:racine_du_site');
				} else {
					$rubouthem = "spip_themes";
					$titre0 = _T('lettres:tout_le_site');
				};
				if ($id_rubrique == 0)
					$titre = $titre0;
				else
					$titre = sql_getfetsel('titre', $rubouthem, 'id_rubrique='.intval($id_rubrique));
				echo "<tr style='background-color: #eeeeee;'>";
				echo '<td width="12">'.http_img_pack(_DIR_PLUGIN_LETTRES.'prive/images/rubrique-12.png', "rub", '').'</td>';
				echo '<td><a href="'.generer_url_ecrire("naviguer","id_rubrique=".$id_rubrique).'">'.typo($titre).'</a></td>';
				echo '<td width="60" class="arial1">'._T('lettresprive:'.$statut).'</td>';
				echo '<td width="100" class="arial1">'.affdate($abo['date_abonnement']).'</td>';
				echo '<td width="70" class="arial1">'."<a href='" . generer_url_ecrire('abonnes', "id_abonne=$id_abonne&desabonner=".$id_rubrique) . "'>"._T('lettresprive:desabonner').'</a></td>';
				echo '</tr>';
			}
			echo '</table>';
			echo fin_cadre_enfonce(true);
		}

		$test_racine = sql_countsel('spip_abonnes_rubriques', 'id_abonne='.intval($abonne->id_abonne).' AND id_rubrique=0');
		if (!$test_racine) {
			echo '<form method="post" action="'.generer_url_ecrire('abonnes', 'id_abonne='.$abonne->id_abonne).'">';
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/rubrique-24.png', true, "", _T('lettresprive:nouvel_abonnement'));
			echo "<div class='arial2'>";
			if (($GLOBALS['meta']['spip_lettres_admin_abo_toutes_rubriques']=='oui')
				or (lettres_nombre_themes()>1))
				echo _T('lettresprive:selectionnez_rubrique')."<br>";
			echo choisir_thematique();
			echo "<input type='submit' name='abonner' class='fondo'
					value='"._T('lettresprive:abonner')."' style='float:right; font-size:10px'>";
			echo "</div>";
			echo fin_cadre_enfonce(true);
			echo '</form>';
		}

		$fond = '<p>';
		if ($abonne->nom)
			$fond.= _T('lettresprive:nom')." : <strong>".$abonne->nom."</strong><br />";
		$fond.= _T('lettresprive:format')." : <strong>".$abonne->format."</strong><br />";
		$fond.= _T('lettresprive:code')." : <strong>".$abonne->code."</strong><br />";
		$fond.= _T('lettresprive:maj_le')." : <strong>".affdate($abonne->maj)."</strong><br />";
		$fond.= '</p>';
		$fond = pipeline('afficher_contenu_objet', array('args' => array('type' => 'abonne', 'id_objet' => $abonne->id_abonne, 'contexte' => array('id' => $abonne->id_abonne)), 'data' => $fond));
		echo '<div id="wysiwyg">'.$fond.'</div>';

		echo pipeline('affiche_milieu', array('args' => array('exec' => 'abonnes', 'id_abonne' => $abonne->id_abonne), 'data' => ''));

		echo '</div><!-- fin fiche_objet -->';

		echo debut_boite_info(true);
		echo _T('lettresprive:aide_abonnes');
		echo fin_boite_info(true);

		echo afficher_objets('lettre',
			_T('lettresprive:lettres_recues'),
			array('FROM' => 'spip_abonnes_lettres', 'WHERE' => 'id_abonne='.intval($abonne->id_abonne), 'ORDER BY' => 'maj DESC'), array('id_lettre' => $lettre->id_lettre));
		
		echo fin_gauche();

		echo fin_page();

	}


?>