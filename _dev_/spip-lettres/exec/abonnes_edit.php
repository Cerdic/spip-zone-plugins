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
	include_spip('inc/extra');


	function exec_abonnes_edit() {
		global $id_abonne, $email, $nom, $champs_extra;

		if (!autoriser('editer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		pipeline('exec_init',array('args'=>array('exec'=>'abonnes_edit','id_abonne'=>$id_abonne),'data'=>''));

		if (!empty($_POST['enregistrer'])) {
			if (lettres_verifier_validite_email($email)) {
				$abonne = new abonne($id_abonne, $email);
				$abonne->email	= $email;
				$abonne->nom	= $nom;

				$abonne->enregistrer();

				if (isset($_POST['id_rubrique'])) {
					$abonne->enregistrer_abonnement($_POST['id_rubrique']);
					$abonne->valider_abonnement($_POST['id_rubrique']);
				}

				$url = generer_url_ecrire('abonnes', 'id_abonne='.$abonne->id_abonne, true);
				header('Location: ' . $url);
				exit();
			} else {
				$erreur = true;
			}
		}

		$abonne = new abonne($id_abonne);
		
		if (!$abonne->existe) {
			$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		} else if ($abonne->objet != 'abonnes') {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:abonnes'), "naviguer", "abonnes_tous");

	 	debut_gauche();

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'abonnes_edit','id_abonne'=>$abonne->id_abonne),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'abonnes_edit','id_abonne'=>$abonne->id_abonne),'data'=>''));

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		if (!$abonne->existe)
			icone(_T('icone_retour'), generer_url_ecrire("abonnes_tous"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png');
		else
			icone(_T('icone_retour'), generer_url_ecrire("abonnes", 'id_abonne='.$abonne->id_abonne), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png');

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('lettresprive:modifier_abonne');
		if ($abonne->existe)
			gros_titre($abonne->email);
		else
			gros_titre(_T('lettresprive:nouvel_abonne'));
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("abonnes_edit", 'id_abonne='.$abonne->id_abonne, 'formulaire');

		if (isset($_GET['id_rubrique']))
			echo '<input type="hidden" name="id_rubrique" value="'.$_GET['id_rubrique'].'" />';

		echo '<b>'._T('lettresprive:email').'</b>';
		if ($erreur)
			echo ' <b>'._T('lettresprive:email_non_valide').'</b>';
		echo '<br /><input type="text" name="email" style="font-weight: bold; font-size: 13px;" class="formo" value="'.$abonne->email.'" size="40" '.$onfocus.' />';

		echo '<b>'._T('lettresprive:nom').'</b>';
		echo '<br /><input type="text" name="nom" class="formo" value="'.$abonne->nom.'" size="40" '.$onfocus.' />';

		if ($champs_extra) {
			echo extra_saisie($abonne->extra, 'abonnes');
		}

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('lettresprive:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
		echo fin_gauche();

		echo fin_page();
	 	
	}
	
	
	
?>