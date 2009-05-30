<?php


	/**
	 * SPIP-Sondages
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
	include_spip('sondages_fonctions');


	function exec_choix_edit() {

		if (!autoriser('editer', 'sondages')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_sondage = $_REQUEST['id_sondage'];
		$id_choix	= $_REQUEST['id_choix'];
		$choix = new choix($id_choix, $id_sondage);

		pipeline('exec_init',array('args' => array('exec' => 'choix_edit', 'id_choix' => $choix->id_choix), 'data' => ''));

		if (!empty($_POST['enregistrer'])) {
			$choix->titre = $_POST['titre'];
			$choix->enregistrer();
			$choix->enregistrer_position($_POST['position']);

			$url = generer_url_ecrire('sondages', 'id_sondage='.$id_sondage, true);
			header('Location: ' . $url);
			exit();
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($choix->titre, "naviguer", "sondages_tous");

		echo debut_gauche("",true);
		echo pipeline('affiche_gauche', array('args' => array('exec' => 'choix_edit', 'id_choix' => $choix->id_choix), 'data' => ''));
		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'choix_edit', 'id_choix' => $choix->id_choix), 'data' => ''));
		echo debut_droite("",true);

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('sondages', 'id_sondage='.$choix->id_sondage), _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('sondagesprive:editer_choix');
		echo '<h1>'.$choix->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('choix_edit', 'id_sondage='.$_REQUEST['id_sondage'].($choix->id_choix ? '&id_choix='.$choix->id_choix : '')).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('sondagesprive:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$choix->titre.'" '.($choix->id_choix == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

		echo '<li class="obligatoire">';
		echo '<label for="position">'._T('sondagesprive:position').'</label>';
		echo '<select name="position" id="position">';		
		$i = 0;
		echo '<option value="'.$i++.'" ';
		if ($choix->ordre == 0) echo 'selected';
		echo '>'._T('sondagesprive:en_premier').'</option>';
		$resultat_autres_choix = sql_select('*', 'spip_choix', 'id_sondage='.intval($choix->id_sondage).' AND id_choix!='.intval($choix->id_choix), '', 'ordre');
		while ($arr = sql_fetch($resultat_autres_choix)) {
			echo '<option value="'.$i.'" ';
			if ($choix->ordre == $i) echo 'selected';
			echo '>'._T('sondagesprive:apres').'&nbsp;'.$arr['titre'].'</option>';
			$i++;
		}
		echo '</select>';
		echo '</li>';

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('sondagesprive:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}
	
	
?>