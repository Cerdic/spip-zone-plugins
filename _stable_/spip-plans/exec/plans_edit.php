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


	function exec_plans_edit() {

		if (!autoriser('editer', 'plans')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_plan = intval($_GET['id_plan']);
		$plan = new plan($id_plan);
		
		pipeline('exec_init', array('args' => array('exec' => 'plans_edit', 'id_plan' => $plan->id_plan), 'data' => ''));

		if (!empty($_POST['enregistrer'])) {
			$plan->titre		= $_POST['titre'];
			$plan->descriptif	= $_POST['descriptif'];

			$plan->enregistrer();

			if ($_FILES['plan_normal'])
				$plan->ajouter_logo($_FILES['plan_normal'], 'on');
			if ($_FILES['plan_survol'])
				$plan->ajouter_logo($_FILES['plan_survol'], 'off');

			$url = generer_url_ecrire('plans', 'id_plan='.$plan->id_plan, true);
			header('Location: ' . $url);
			exit();
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($plan->titre, "naviguer", "plans_tous");

		echo debut_gauche("",true);

		echo pipeline('affiche_gauche', array('args' => array('exec' => 'plans_edit', 'id_plan' => $plan->id_plan), 'data' => ''));
		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'plans_edit', 'id_plan' => $plan->id_plan), 'data' => ''));
		echo debut_droite("",true);


		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		if ($plan->existe) {
			echo icone_inline(_T('icone_retour'), generer_url_ecrire('plans', 'id_plan='.$plan->id_plan), _DIR_PLUGIN_PLAN.'/prive/images/plan-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		} else {
			echo icone_inline(_T('icone_retour'), generer_url_ecrire('plans_tous'), _DIR_PLUGIN_PLAN.'/prive/images/plan-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		}
		echo _T('plans:edition');
		echo '<h1>'.$plan->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('plans_edit', ($plan->id_plan ? 'id_plan='.$plan->id_plan : '')).'" enctype="multipart/form-data">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('plans:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$plan->titre.'" '.($plan->id_plan == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li class="editer_descriptif">';
		echo '<label for="descriptif">'._T('plans:descriptif').'</label>';
		echo '<textarea name="descriptif" id="descriptif" rows="2" cols="40">'.$plan->descriptif.'</textarea>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="plan_normal">'._T('plans:plan_normal').'</label>';
		echo '<input type="file" class="file" id="plan_normal" name="plan_normal" />';
		echo '<span class="explication">'._T('plans:laissez_vide_si_pas_de_changement').'</span>';
		echo '</li>';

	    echo '<li>';
		echo '<label for="plan_survol">'._T('plans:plan_survol').'</label>';
		echo '<input type="file" class="file" id="plan_survol" name="plan_survol" />';
		echo '<span class="explication">'._T('plans:laissez_vide_si_pas_de_changement').'</span>';
		echo '</li>';

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('plans:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}

?>