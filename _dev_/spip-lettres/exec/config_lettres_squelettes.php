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


	function exec_config_lettres_squelettes() {

		if (!autoriser('configurer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

		if (!empty($_POST['valider'])) {
			if (!empty($_POST['spip_lettres_fond_formulaire_lettres'])) {
				$spip_lettres_fond_formulaire_lettres = addslashes($_POST['spip_lettres_fond_formulaire_lettres']);
				ecrire_meta('spip_lettres_fond_formulaire_lettres', $spip_lettres_fond_formulaire_lettres);
			}

			if (!empty($_POST['spip_lettres_fond_lettre_titre'])) {
				$spip_lettres_fond_lettre_titre = addslashes($_POST['spip_lettres_fond_lettre_titre']);
				ecrire_meta('spip_lettres_fond_lettre_titre', $spip_lettres_fond_lettre_titre);
			}

			if (!empty($_POST['spip_lettres_fond_lettre_html'])) {
				$spip_lettres_fond_lettre_html = addslashes($_POST['spip_lettres_fond_lettre_html']);
				ecrire_meta('spip_lettres_fond_lettre_html', $spip_lettres_fond_lettre_html);
			}

			if (!empty($_POST['spip_lettres_fond_lettre_texte'])) {
				$spip_lettres_fond_lettre_texte = addslashes($_POST['spip_lettres_fond_lettre_texte']);
				ecrire_meta('spip_lettres_fond_lettre_texte', $spip_lettres_fond_lettre_texte);
			}

			$spip_lettres_utiliser_articles = $_POST['spip_lettres_utiliser_articles'];
			ecrire_meta('spip_lettres_utiliser_articles', $spip_lettres_utiliser_articles);

			$spip_lettres_utiliser_ps = $_POST['spip_lettres_utiliser_ps'];
			ecrire_meta('spip_lettres_utiliser_ps', $spip_lettres_utiliser_ps);

			$spip_lettres_notifier_suppression_abonne = $_POST['spip_lettres_notifier_suppression_abonne'];
			ecrire_meta('spip_lettres_notifier_suppression_abonne', $spip_lettres_notifier_suppression_abonne);

			ecrire_metas();

			$url = generer_url_ecrire('config_lettres_squelettes');
			header('Location: '.$url);
			exit();
		}

		$spip_lettres_fond_formulaire_lettres		= $GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'];
		$spip_lettres_fond_lettre_titre				= $GLOBALS['meta']['spip_lettres_fond_lettre_titre'];
		$spip_lettres_fond_lettre_html				= $GLOBALS['meta']['spip_lettres_fond_lettre_html'];
		$spip_lettres_fond_lettre_texte				= $GLOBALS['meta']['spip_lettres_fond_lettre_texte'];
		$spip_lettres_utiliser_articles				= $GLOBALS['meta']['spip_lettres_utiliser_articles'];
		$spip_lettres_utiliser_ps					= $GLOBALS['meta']['spip_lettres_utiliser_ps'];
		$spip_lettres_notifier_suppression_abonne	= $GLOBALS['meta']['spip_lettres_notifier_suppression_abonne'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

		echo '<br /><br /><br />';
		echo gros_titre(_T('titre_configuration'),'',false);
		echo barre_onglets("configuration", "config_lettres_formulaire");
		echo "<br>";
		echo barre_onglets("lettres", "config_lettres_squelettes");

		echo debut_gauche('', true);
		echo debut_boite_info(true);
		echo _T('lettresprive:aide_config_lettres_squelettes');
		echo fin_boite_info(true);
		echo bloc_des_raccourcis(icone_horizontale(_T('lettresprive:aller_au_formulaire_abonnement'), generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres']), _DIR_PLUGIN_LETTRE_INFORMATION."/prive/images/formulaire.png", 'rien.gif', false));
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

   		echo debut_droite('', true);

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo '<h1>'._T('lettresprive:configuration_squelettes').'</h1>';
		echo '</div>';
		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('config_lettres_squelettes').'">';

	  	echo '<ul>';

	    echo '<li>';
		echo '<label for="spip_lettres_fond_formulaire_lettres">'._T('lettresprive:squelette_formulaire_abonnement').'</label>';
		echo '<input type="text" class="text" name="spip_lettres_fond_formulaire_lettres" id="spip_lettres_fond_formulaire_lettres" value="'.$spip_lettres_fond_formulaire_lettres.'" />';
		echo '</li>';

	    echo '<li>';
		echo '<label for="spip_lettres_fond_lettre_titre">'._T('lettresprive:squelette_titre_lettre').'</label>';
		echo '<input type="text" class="text" name="spip_lettres_fond_lettre_titre" id="spip_lettres_fond_lettre_titre" value="'.$spip_lettres_fond_lettre_titre.'" />';
		echo '</li>';

	    echo '<li>';
		echo '<label for="spip_lettres_fond_lettre_html">'._T('lettresprive:squelette_version_html_lettre').'</label>';
		echo '<input type="text" class="text" name="spip_lettres_fond_lettre_html" id="spip_lettres_fond_lettre_html" value="'.$spip_lettres_fond_lettre_html.'" />';
		echo '</li>';

	    echo '<li>';
		echo '<label for="spip_lettres_fond_lettre_texte">'._T('lettresprive:squelette_version_texte_lettre').'</label>';
		echo '<input type="text" class="text" name="spip_lettres_fond_lettre_texte" id="spip_lettres_fond_lettre_texte" value="'.$spip_lettres_fond_lettre_texte.'" />';
		echo '</li>';

	    echo '<li class="fieldset">';
		echo '<fieldset>';
		echo '<h3 class="legend">'._T('lettresprive:spip_lettres_utiliser_articles').'</h3>';
		echo '<ul>';
		echo '<li>';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_articles" value="oui" id="spip_lettres_utiliser_articles_oui" '.($spip_lettres_utiliser_articles == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_articles_oui">'._T('lettresprive:oui').'</label>';
		echo '</li>';
		echo '<li>';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_articles" value="non" id="spip_lettres_utiliser_articles_non" '.($spip_lettres_utiliser_articles == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_articles_non">'._T('lettresprive:non').'</label>';
		echo '</li>';
		echo '</ul>';
		echo '</fieldset>';
		echo '</li>';

	    echo '<li class="fieldset">';
		echo '<fieldset>';
		echo '<h3 class="legend">'._T('lettresprive:spip_lettres_utiliser_ps').'</h3>';
		echo '<ul>';
		echo '<li>';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_ps" value="oui" id="spip_lettres_utiliser_ps_oui" '.($spip_lettres_utiliser_ps == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_ps_oui">'._T('lettresprive:oui').'</label>';
		echo '</li>';
		echo '<li>';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_ps" value="non" id="spip_lettres_utiliser_ps_non" '.($spip_lettres_utiliser_ps == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_ps_non">'._T('lettresprive:non').'</label>';
		echo '</li>';
		echo '</ul>';
		echo '</fieldset>';
		echo '</li>';

	    echo '<li class="fieldset">';
		echo '<fieldset>';
		echo '<h3 class="legend">'._T('lettresprive:spip_lettres_notifier_suppression_abonne').'</h3>';
		echo '<ul>';
		echo '<li>';
		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne" value="oui" id="spip_lettres_notifier_suppression_abonne_oui" '.($spip_lettres_notifier_suppression_abonne == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_notifier_suppression_abonne_oui">'._T('lettresprive:oui').'</label>';
		echo '</li>';
		echo '<li>';
		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne" value="non" id="spip_lettres_notifier_suppression_abonne_non" '.($spip_lettres_notifier_suppression_abonne == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_notifier_suppression_abonne_non">'._T('lettresprive:non').'</label>';
		echo '</li>';
		echo '</ul>';
		echo '</fieldset>';
		echo '</li>';

		echo '</ul>';

/*
		
		echo '<br /><b>'._T('lettresprive:spip_lettres_notifier_suppression_abonne').'</b><br />';
		echo afficher_choix('spip_lettres_notifier_suppression_abonne', $spip_lettres_notifier_suppression_abonne,
			array('oui' => _T('lettresprive:oui'), 'non' => _T('lettresprive:non')), " &nbsp; ");
*/

	  	echo '<p class="boutons"><input type="submit" class="submit" name="valider" value="'._T('lettresprive:valider').'" /></p>';
		echo '</form>';
		echo '</div>';
		echo '</div>';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

		echo fin_gauche();

		echo fin_page();

	}


?>