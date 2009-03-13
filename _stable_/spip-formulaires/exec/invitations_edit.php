<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


 	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/headers');
	include_spip('formulaires_fonctions');


	/**
	 * exec_invitations_edit
	 *
	 * @author Pierre Basson
	 **/
	function exec_invitations_edit() {
	 	
		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		$id_formulaire	= intval($_GET['id_formulaire']);
		$formulaire		= new formulaire($id_formulaire);
		
		if (!empty($_POST['enregistrer'])) {
			$email = addslashes($_POST['email']);
			
			if (ereg(_REGEXP_EMAIL, $email)) {
				$invitation = new invitation($id_formulaire, $email);

				$rubriques = _request('rubriques');
				if (empty($rubriques)) $rubriques = array();

				$abonne = new abonne(0, $email);

				if ($abonne->existe) {

					$abonnements = $abonne->recuperer_abonnements();
					$abonnements_disponibles = $formulaire->recuperer_abonnements_disponibles();
					$abonnements = array_intersect($abonnements_disponibles, $abonnements);

					$desabonnements = array_diff($abonnements, $rubriques);
					if (!empty($desabonnements)) { // on désinscrit s'il y a des différences
						foreach ($desabonnements as $id_rubrique) {
							$abonne->valider_desabonnement($id_rubrique);
						}
					}
					$abonnements = array_diff($rubriques, $abonnements);
					if (!empty($abonnements)) {
						foreach ($abonnements as $id_rubrique) {
							$abonne->enregistrer_abonnement($id_rubrique);
							$abonne->valider_abonnement($id_rubrique);
						}
					}

					$abonne->supprimer_si_zero_abonnement();

				} else {

					if (!empty($rubriques)) {
						$abonne->enregistrer();
						foreach ($rubriques as $id_rubrique) {
							$abonne->enregistrer_abonnement($id_rubrique);
							$abonne->valider_abonnement($id_rubrique);
						}
					}

				}

				$url = generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire, true);
				header('Location: ' . $url);
				exit();
			} else {
				$erreur = true;
			}
		}

		$new		= true;
		$email		= '';
		$onfocus	= " onfocus=\"if(!antifocus){formulaire.value='';antifocus=true;}\"";

		pipeline('exec_init',array('args'=>array('exec'=>'invitations_edit','id_formulaire'=>$id_formulaire),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

	 	debut_gauche();

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		icone(_T('icone_retour'), generer_url_ecrire("formulaires", "id_formulaire=".$id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/invitation.png', "rien.gif");

		$formulaire = new formulaire($id_formulaire);
		
		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		gros_titre(_T('formulairesprive:creer_invitation').' "'.propre($formulaire->titre).'"');
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("invitations_edit", "id_formulaire=".$id_formulaire, 'formulaire');

		echo "<P><B>"._T('formulairesprive:email_de_l_invite')."</B>";
		echo "<BR><INPUT TYPE='text' NAME='email' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE='' SIZE='40'>";

		$abonnements_disponibles = $formulaire->recuperer_abonnements_disponibles();
		$themes = spip_query('SELECT * FROM spip_themes WHERE id_rubrique IN ('.implode(',', $abonnements_disponibles).') ORDER BY titre');
		if (spip_num_rows($themes) > 0) {
			echo "<P><B>"._T('formulairesprive:abonner_a')."</B><br />";
			while ($arr = spip_fetch_array($themes)) {
				echo '<input type="checkbox" name="rubriques[]" value="'.$arr['id_rubrique'].'" /> '.$arr['titre'].'<br/>';
			}
			echo "</P>\n";
		}

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('formulairesprive:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
		echo fin_gauche();

		echo fin_page();
	 	
	}
	
	
?>