<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');

	function exec_scenari_dist(){

		// si pas autorise : message d'erreur
		if (!autoriser('voir', 'scenari')) {
			include_spip('inc/minipres');
			print minipres();
			exit;
		}

		// pipeline d'initialisation
		pipeline('exec_init', array('args'=>array('exec'=>'scenari'),'data'=>''));

		// entetes
		$commencer_page = charger_fonction('commencer_page', 'inc');

		// titre, partie, sous_partie (pour le menu)
		print $commencer_page(_T('scenari:scenari'), "editer", "editer");

		//Efface récursivement un scenari
		if(isset($_GET['dropid'])&&strlen(trim($_GET['dropid']))){
			$torm=_DIR_IMG.'scenari/'.trim($_GET['dropid']);
			if (is_dir($torm)) rrmdir($torm);
		}

		// titre
		print gros_titre(_T('scenari:titre2'),'', false);

		// colonne gauche
		print debut_gauche('', true);
		print pipeline('affiche_gauche', array('args'=>array('exec'=>'scenari'),'data'=>''));

		// colonne droite
		print creer_colonne_droite('', true);
		print pipeline('affiche_droite', array('args'=>array('exec'=>'scenari'),'data'=>''));
		include("scenari_form.php");

		// centre
		print debut_droite('', true);
		// contenu
		include("scenari_list.php");

		// fin contenu
		print pipeline('affiche_milieu', array('args'=>array('exec'=>'scenari'),'data'=>''));
		echo fin_gauche(), fin_page();

	}

?>
