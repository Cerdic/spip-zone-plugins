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
	include_spip('inc/config');
	include_spip('inc/meta');
	include_spip('lettres_fonctions');


	function exec_config_lettres_formulaire() {

		if (!autoriser('configurer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'config_lettres_formulaire'),'data'=>''));

		if (!empty($_POST['titre'])) {
			sql_replace('spip_themes', array('id_rubrique' => intval($_POST['id_parent']), 'titre' => $_POST['titre']));
			include_spip('inc/rubriques');
			calculer_langues_rubriques();
			$url = generer_url_ecrire('config_lettres_formulaire');
			header('Location: '.$url);
			exit();
		};

		// rubrique par défaut pour un nouvel abonné	
		if ($_REQUEST['pardefaut']=='oui') {
			if (isset($_POST['supprimer'])) // pas d'abo par défaut
				ecrire_meta('spip_lettres_abonnement_par_defaut', -1); 
			else
				ecrire_meta('spip_lettres_abonnement_par_defaut', 
							$_POST['id_parent']);
			ecrire_metas();
			$url = generer_url_ecrire('config_lettres_formulaire');
			header('Location: '.$url);
			exit();
		}			

		if (!empty($_GET['supprimer_theme'])) {
			sql_delete('spip_themes', 'id_theme='.intval($_GET['supprimer_theme']));
			$url = generer_url_ecrire('config_lettres_formulaire');
			header('Location: '.$url);
			exit();
		}			

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");


		echo '<br /><br /><br />';
		echo gros_titre(_T('titre_configuration'),'',false);
		echo barre_onglets("configuration", "config_lettres_formulaire_top");
		echo "<br>";
		echo barre_onglets("lettres", "config_lettres_formulaire");

		echo debut_gauche('', true);
		echo debut_boite_info(true);
		echo _T('lettresprive:aide_config_lettres_formulaire');
		echo fin_boite_info(true);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'config_lettres_formulaire'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'config_lettres_formulaire'),'data'=>''));

   		echo debut_droite('', true);

		$themes = afficher_objets('theme', _T('lettresprive:themes_disponibles'), array('SELECT' => 'T.*, RUB.titre AS titre_rub', 'FROM' => 'spip_themes AS T LEFT JOIN spip_rubriques AS RUB ON RUB.id_rubrique=T.id_rubrique', 'ORDER BY' => 'T.titre'));
		if ($themes) {
			echo $themes;
			echo '<br />';
		} else {
			echo debut_boite_info(true);
			echo _T('lettresprive:aucun_theme_selectionne');
			echo fin_boite_info(true);
		}

		echo '<form method="post" action="'.generer_url_ecrire('config_lettres_formulaire').'">';
		echo debut_cadre_trait_couleur("", true, "", _T('lettresprive:ajouter_theme'));
		
	    echo '<p>';
		// Ajout d'un message d'erreur (avec un style inline car ne n'est pas un CVT)
		if (empty($_REQUEST['pardefaut']) and !empty($_POST['id_parent']) and empty($_POST['titre']))
			echo '<span style="color:red;">'._T('info_obligatoire').'</span><br/>';
		echo '<label for="titre">';
		echo _T('lettresprive:titre').'</label>&nbsp;&nbsp;&nbsp;';

		echo '<input type="text" class="text" name="titre" id="titre" value="" />';
		echo '</p>';
	    echo '<p>';
		echo '<label for="id_parent">'._T('lettresprive:choix_rubrique').'</label>';
		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		echo $selecteur_rubrique(0, 'rubrique', false);
		echo '</p>';

		echo '<p style="text-align: right;"><input class="fondo" name="ajouter" type="submit" value="'._T('lettresprive:ajouter').'" /></p>';
		echo fin_cadre_trait_couleur(true);
		echo '</form>';
		
		// rubrique par défaut pour un nouvel abonné créé via la partie privée	
		$nbt= lettres_nombre_themes();
		if ($nbt==1) {
			ecrire_meta('spip_lettres_abonnement_par_defaut', 
						lettres_rubrique_theme_par_defaut());
			ecrire_metas();
			echo '<p>'._T('lettresprive:theme_par_defaut_actuel')
				.lettres_titre_theme_par_defaut();
		}
		else if ($nbt>1) {
			echo '<form method="post" action="'.generer_url_ecrire('config_lettres_formulaire',"pardefaut=oui").'">';
			echo debut_cadre_trait_couleur("", true, "", _T('lettresprive:theme_par_defaut'));
						
			echo '<p>'._T('lettresprive:theme_par_defaut_actuel')
						.lettres_titre_theme_par_defaut();;
			if ($GLOBALS['meta']['spip_lettres_abonnement_par_defaut']>=0)
				echo '<input class="fondo" style="float: right;" name="supprimer" type="submit" value="'._T('lettresprive:supprimer').'" />';
			echo '</p><p>';
			echo '<label for="id_parent">'._T('lettresprive:theme_par_defaut_modifier').'</label>';
			echo $selecteur_rubrique(0, 'rubrique', false);
			echo '</p>';
			echo '<p style="text-align: right;"><input class="fondo" name="enregistrer" type="submit" value="'._T('lettresprive:enregistrer').'" /></p>';
			echo fin_cadre_trait_couleur(true);
			echo '</form>';
		};
		
		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'config_lettres_formulaire'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();
	}
?>