<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


 	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('formulaires_fonctions');
	include_spip('inc/headers');


	/**
	 * exec_blocs_edit
	 *
	 * Page d'édition d'un bloc
	 *
	 * @author Pierre Basson
	 **/
	function exec_blocs_edit() {
	 	
		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		$id_formulaire	= intval($_GET['id_formulaire']);
		$id_bloc		= intval($_GET['id_bloc']);
		
		if (!empty($_POST['enregistrer'])) {
			$bloc = new bloc($id_formulaire, $id_bloc);

			$bloc->titre 		= addslashes($_POST['titre']);
			$bloc->descriptif	= addslashes($_POST['descriptif']);
			$bloc->texte		= addslashes($_POST['texte']);

			$bloc->enregistrer();
			$bloc->changer_ordre($_POST['position']);
			
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$bloc->formulaire->id_formulaire, true);
			header('Location: ' . $url);
			exit();
		}

		if ($id_formulaire AND $id_bloc) {
			$bloc = new bloc($id_formulaire, $id_bloc);
		} else {
			$new		= true;
			$bloc		= new bloc($id_formulaire);
			$onfocus	= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

		pipeline('exec_init',array('args'=>array('exec'=>'blocs_edit','id_formulaire'=>$bloc->formulaire->id_formulaire,'id_bloc'=>$bloc->id_bloc),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

	 	debut_gauche();

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		icone(_T('icone_retour'), generer_url_ecrire("formulaires", "id_formulaire=".$bloc->formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/bloc.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('formulairesprive:editer_bloc');
		gros_titre($bloc->titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("blocs_edit", "id_formulaire=".$bloc->formulaire->id_formulaire."&id_bloc=".$bloc->id_bloc, 'formulaire');

		echo "<P><B>"._T('formulairesprive:titre')."</B>";
		echo "<BR><INPUT TYPE='text' NAME='titre' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"".$bloc->titre."\" SIZE='40' $onfocus>";

		echo "<P>"._T('formulairesprive:position')."<br />";
		echo "<select name='position' CLASS='fondl'>";		
		$i = 0;
		echo '<option value="'.$i++.'" ';
		if ($bloc->ordre == 0) echo 'selected';
		echo '>'._T('formulairesprive:en_premier').'</option>';
		$blocs = $bloc->recuperer_autres_blocs();
		foreach ($blocs as $indice) {
			$autre_bloc = new bloc($bloc->id_formulaire, $indice);
			echo '<option value="'.$i.'" ';
			if ($bloc->ordre == $i) echo 'selected';
			echo '>'._T('formulairesprive:apres').'&nbsp;'.$autre_bloc->titre.'</option>';
			$i++;
		}
		echo "</select></P>\n";

		echo "<P><B>"._T('formulairesprive:descriptif')."</B>";
		echo "<TEXTAREA NAME='descriptif' CLASS='forml' ROWS='3' COLS='40' wrap=soft>";
		echo $bloc->descriptif;
		echo "</TEXTAREA></P>\n";

		echo "<p><B>"._T('formulairesprive:texte')."</B>";
		echo "<br>"._T('texte_enrichir_mise_a_jour');
		echo aide("raccourcis");
		echo afficher_barre('document.formulaire.texte');
		echo "<TEXTAREA id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='20' COLS='40' wrap=soft>";
		echo $bloc->texte;
		echo "</TEXTAREA></p>\n";

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('formulairesprive:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
		echo fin_gauche();

		echo fin_page();
	 	
	}
	
	
?>