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


	function exec_abonnes_edit() {

		if (!autoriser('editer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$id_abonne = $_GET['id_abonne'];
		
		pipeline('exec_init', array('args' => array('exec' => 'abonnes_edit', 'id_abonne' => $id_abonne), 'data' => ''));

		if (!empty($_POST['enregistrer'])) {
			if (lettres_verifier_validite_email($_POST['email'])) {
				// retrouver l'abonne deja existant par son email si c'est une creation
				$abonne = new abonne($id_abonne, $_POST['email']);
				$abonne->email	= $_POST['email'];
				if ($_POST['nom'])
					$abonne->nom	= $_POST['nom'];
				$abonne->format	= $_POST['format'];
				$abonne->enregistrer();

				// on associe un abonnement si l'url le demande (lien raccourci)
				// ou si il y a un abo par défaut.
				if (isset($_POST['id_rubrique']) and (intval($_POST['id_rubrique'])>0))	
					$abo_init = intval($_POST['id_rubrique']);
				else 
					$abo_init = lettres_rubrique_theme_par_defaut();
				// si 0 : abo à la racine
				// si -1 : pas d'abo par défaut
				if ($abo_init > -1)  {
					$abonne->enregistrer_abonnement($abo_init);
					$abonne->valider_abonnement($abo_init);
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
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:abonnes'), "naviguer", "abonnes_tous");

		echo debut_gauche("",true);
		echo pipeline('affiche_gauche', array('args' => array('exec' => 'abonnes_edit', 'id_abonne' => $abonne->id_abonne), 'data' => ''));
		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'abonnes_edit', 'id_abonne' => $abonne->id_abonne), 'data' => ''));
		echo debut_droite("",true);

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		if ($abonne->existe)
			echo icone_inline(_T('icone_retour'), generer_url_ecrire('abonnes', 'id_abonne='.$abonne->id_abonne), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', "rien.gif", $GLOBALS['spip_lang_left']);
		else
			echo icone_inline(_T('icone_retour'), generer_url_ecrire('abonnes_tous'), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('lettresprive:modifier_abonne');
		echo '<h1>'.sinon($abonne->email, _T('lettresprive:nouvel_abonne')).'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';

		if ($erreur)
			echo '<p class="reponse_formulaire reponse_formulaire_erreur">'._T('lettresprive:email_non_valide').'</p>';

		echo '<form method="post" action="'.generer_url_ecrire('abonnes_edit', ($abonne->id_abonne ? 'id_abonne='.$abonne->id_abonne : '')).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="email">'._T('lettresprive:email').'</label>';
		echo '<input type="text" class="text" name="email" id="email" value="'.$abonne->email.'" '.($abonne->id_abonne ? '' : 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ').'/>';
		echo '</li>';

	    echo '<li>';
		echo '<label for="nom">'._T('lettresprive:nom').'</label>';
		echo '<input type="text" class="text" name="nom" id="nom" value="'.$abonne->nom.'" />';
		echo '</li>';

	    echo '<li>';
		echo '<label for="format">'._T('lettresprive:format').'</label>';
		echo '<select name="format" id="format">';		
		echo '<option value="mixte"'.(($abonne->format == 'mixte') ? ' selected="selected"' : '' ).'>'._T('lettresprive:mixte').'</option>';
		echo '<option value="html"'.(($abonne->format == 'html') ? ' selected="selected"' : '' ).'>'._T('lettresprive:html').'</option>';
		echo '<option value="texte"'.(($abonne->format == 'texte') ? ' selected="selected"' : '' ).'>'._T('lettresprive:texte').'</option>';
		echo '</select>';
		echo '</li>';

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('lettresprive:enregistrer').'" /></p>';

		if (isset($_GET['id_rubrique']))
			echo '<input type="hidden" name="id_rubrique" value="'.$_GET['id_rubrique'].'" />';

/*
TODO
		if ($champs_extra) {
			echo extra_saisie($abonne->extra, 'abonnes');
		}
*/
		echo '</div>';
		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}
	
	
	
?>