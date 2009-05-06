<?php


	/**
	 * SPIP-Plans
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
	include_spip('plans_fonctions');


	function exec_points_edit() {

		if (!autoriser('editer', 'plans')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_plan = intval($_REQUEST['id_plan']);
		$id_point = intval($_GET['id_point']);
		$point = new point($id_plan, $id_point);
		
		pipeline('exec_init', array('args' => array('exec' => 'points_edit', 'id_point' => $point->id_point), 'data' => ''));

		if (!empty($_POST['enregistrer'])) {
			$point->titre		= $_POST['titre'];
			$point->descriptif	= $_POST['descriptif'];
			$point->lien		= $_POST['lien'];

			$point->abscisse	= $_POST['abscisse'];
			$point->ordonnee	= $_POST['ordonnee'];

			if (!empty($_POST['image_x'])) {
				$point->abscisse	= $_POST['image_x'];
				$point->ordonnee	= $_POST['image_y'];
			}

			$point->enregistrer();

			$point->enregistrer_z_index($_POST['z_index']);

			if ($_FILES['point_normal'])
				$point->ajouter_logo($_FILES['point_normal'], 'on');
			if ($_FILES['point_survol'])
				$point->ajouter_logo($_FILES['point_survol'], 'off');

			$url = generer_url_ecrire('plans', 'id_plan='.$point->id_plan, true);
			header('Location: ' . $url);
			exit();
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($plan->titre, "naviguer", "plans_tous");

		echo debut_gauche("",true);

		echo pipeline('affiche_gauche', array('args' => array('exec' => 'points_edit', 'id_point' => $point->id_point), 'data' => ''));
		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'points_edit', 'id_point' => $point->id_point), 'data' => ''));
		echo debut_droite("",true);


		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('plans', 'id_plan='.$id_plan), _DIR_PLUGIN_PLAN.'/prive/images/plan-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('plans:edition');
		echo '<h1>'.$point->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('points_edit', 'id_point='.$point->id_point).'" enctype="multipart/form-data">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('plans:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$point->titre.'" '.($point->id_plan == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="lien">'._T('plans:lien').'</label>';
		echo '<input type="text" class="text" name="lien" id="lien" value="'.$point->lien.'" />';
		echo '</li>';

	    echo '<li class="editer_descriptif">';
		echo '<label for="descriptif">'._T('plans:descriptif').'</label>';
		echo '<textarea name="descriptif" id="descriptif" rows="2" cols="40">'.$point->descriptif.'</textarea>';
		echo '</li>';

	    echo '<li>';
		echo '<label for="point_normal">'._T('plans:point_normal').'</label>';
		echo '<input type="file" class="file" id="point_normal" name="point_normal" />';
		echo '<p class="explication">'._T('plans:laissez_vide_si_pas_de_changement').'</p>';
		echo '</li>';

	    echo '<li>';
		echo '<label for="point_survol">'._T('plans:point_survol').'</label>';
		echo '<input type="file" class="file" id="point_survol" name="point_survol" />';
		echo '<p class="explication">'._T('plans:laissez_vide_si_pas_de_changement').'</p>';
		echo '</li>';

		echo '<li class="obligatoire">';
		echo '<label for="z_index">'._T('plans:z_index').'</label>';
		echo '<select name="z_index" id="z_index">';		
		$i = 0;
		echo '<option value="'.$i++.'" ';
		if ($point->z_index == 0) echo 'selected';
		echo '>'._T('plans:tout_en_dessous').'</option>';
		$resultat_autres_points = sql_select('*', 'spip_points', 'id_plan='.intval($point->id_plan).' AND id_point!='.intval($point->id_point), '', 'z_index');
		while ($arr = sql_fetch($resultat_autres_points)) {
			echo '<option value="'.$i.'" ';
			if ($point->z_index == $i) echo 'selected';
			echo '>'._T('sondagesprive:au_dessus_de').'&nbsp;'.$arr['titre'].'</option>';
			$i++;
		}
		echo '</select>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="abscisse">'._T('plans:abscisse').'</label>';
		echo '<input type="text" class="text" name="abscisse" id="abscisse" value="'.$point->abscisse.'" />';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="ordonnee">'._T('plans:ordonnee').'</label>';
		echo '<input type="text" class="text" name="ordonnee" id="ordonnee" value="'.$point->ordonnee.'" />';
		echo '</li>';

		echo '</ul>';

		$logo_f = charger_fonction('chercher_logo', 'inc');
		$image = $logo_f($point->id_plan, 'id_plan', 'on');

	  	echo '<div style="padding: 10px;">';
		echo '<label>'._T('plans:cliquez_pour_coordonnees').'</label>';
		echo '<input type="image" class="image" name="image" src="'.$image[0].'" />';
	  	echo '</div>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('plans:enregistrer').'" /></p>';

		echo '<input type="hidden" name="id_plan" value="'.$point->id_plan.'" />';
		echo '<input type="hidden" name="enregistrer" value="1" />';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}

?>