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

			$spip_lettres_utiliser_descriptif = $_POST['spip_lettres_utiliser_descriptif'];
			ecrire_meta('spip_lettres_utiliser_descriptif', $spip_lettres_utiliser_descriptif);

			$spip_lettres_utiliser_chapo = $_POST['spip_lettres_utiliser_chapo'];
			ecrire_meta('spip_lettres_utiliser_chapo', $spip_lettres_utiliser_chapo);

			$spip_lettres_utiliser_ps = $_POST['spip_lettres_utiliser_ps'];
			ecrire_meta('spip_lettres_utiliser_ps', $spip_lettres_utiliser_ps);

			$spip_lettres_utiliser_articles = $_POST['spip_lettres_utiliser_articles'];
			ecrire_meta('spip_lettres_utiliser_articles', $spip_lettres_utiliser_articles);

			$spip_lettres_notifier_suppression_abonne = $_POST['spip_lettres_notifier_suppression_abonne'];
			ecrire_meta('spip_lettres_notifier_suppression_abonne', $spip_lettres_notifier_suppression_abonne);

			$spip_lettres_cliquer_anonyme = $_POST['spip_lettres_cliquer_anonyme'];
			ecrire_meta('spip_lettres_cliquer_anonyme', $spip_lettres_cliquer_anonyme);
			
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
		$spip_lettres_utiliser_descriptif			= $GLOBALS['meta']['spip_lettres_utiliser_descriptif'];
		$spip_lettres_utiliser_chapo				= $GLOBALS['meta']['spip_lettres_utiliser_chapo'];
		$spip_lettres_utiliser_ps					= $GLOBALS['meta']['spip_lettres_utiliser_ps'];
		$spip_lettres_notifier_suppression_abonne	= $GLOBALS['meta']['spip_lettres_notifier_suppression_abonne'];
		$spip_lettres_cliquer_anonyme				= $GLOBALS['meta']['spip_lettres_cliquer_anonyme'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

		echo '<br /><br /><br />';
		echo gros_titre(_T('titre_configuration'),'',false);
		echo barre_onglets("configuration", "config_lettres_formulaire_top");
		echo "<br>";
		echo barre_onglets("lettres", "config_lettres_squelettes");

		echo debut_gauche('', true);
		echo bloc_des_raccourcis(icone_horizontale(_T('lettresprive:aller_au_formulaire_abonnement'), generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres']), _DIR_PLUGIN_LETTRES."prive/images/formulaire.png", 'rien.gif', false));
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

   		echo debut_droite('', true);

		echo '<form method="post" action="'.generer_url_ecrire('config_lettres_squelettes').'">';
		echo debut_cadre_trait_couleur("", true, "", _T('lettresprive:configuration_squelettes'));

		echo '<p>'._T('lettresprive:aide_config_lettres_squelettes').'</p>';

		echo '<table>';

	    echo '<tr>';
		echo '<td width="250"><label for="spip_lettres_fond_formulaire_lettres">'._T('lettresprive:squelette_formulaire_abonnement').'</label></td>';
		echo '<td><input type="text" class="text" name="spip_lettres_fond_formulaire_lettres" id="spip_lettres_fond_formulaire_lettres" value="'.$spip_lettres_fond_formulaire_lettres.'" /></td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label for="spip_lettres_fond_lettre_titre">'._T('lettresprive:squelette_titre_lettre').'</label></td>';
		echo '<td><input type="text" class="text" name="spip_lettres_fond_lettre_titre" id="spip_lettres_fond_lettre_titre" value="'.$spip_lettres_fond_lettre_titre.'" /></td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label for="spip_lettres_fond_lettre_html">'._T('lettresprive:squelette_version_html_lettre').'</label></td>';
		echo '<td><input type="text" class="text" name="spip_lettres_fond_lettre_html" id="spip_lettres_fond_lettre_html" value="'.$spip_lettres_fond_lettre_html.'" /></td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label for="spip_lettres_fond_lettre_texte">'._T('lettresprive:squelette_version_texte_lettre').'</label></td>';
		echo '<td><input type="text" class="text" name="spip_lettres_fond_lettre_texte" id="spip_lettres_fond_lettre_texte" value="'.$spip_lettres_fond_lettre_texte.'" /></td>';
		echo '</tr>';

		echo '</table>';
		
		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('lettresprive:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);

		echo debut_cadre_trait_couleur("", true, "", _T('lettresprive:options'));

		echo '<table>';

	    echo '<tr>';
		echo '<td><label>'._T('lettresprive:spip_lettres_utiliser_descriptif').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_descriptif" value="oui" id="spip_lettres_utiliser_descriptif_oui" '.($spip_lettres_utiliser_descriptif == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_descriptif_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_descriptif" value="non" id="spip_lettres_utiliser_descriptif_non" '.($spip_lettres_utiliser_descriptif == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_descriptif_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T('lettresprive:spip_lettres_utiliser_chapo').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_chapo" value="oui" id="spip_lettres_utiliser_chapo_oui" '.($spip_lettres_utiliser_chapo == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_chapo_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_chapo" value="non" id="spip_lettres_utiliser_chapo_non" '.($spip_lettres_utiliser_chapo == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_chapo_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T('lettresprive:spip_lettres_utiliser_ps').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_ps" value="oui" id="spip_lettres_utiliser_ps_oui" '.($spip_lettres_utiliser_ps == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_ps_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_ps" value="non" id="spip_lettres_utiliser_ps_non" '.($spip_lettres_utiliser_ps == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_ps_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T('lettresprive:spip_lettres_utiliser_articles').'</label></td>';
		echo '<td width="100">';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_articles" value="oui" id="spip_lettres_utiliser_articles_oui" '.($spip_lettres_utiliser_articles == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_articles_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_utiliser_articles" value="non" id="spip_lettres_utiliser_articles_non" '.($spip_lettres_utiliser_articles == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_utiliser_articles_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T('lettresprive:spip_lettres_notifier_suppression_abonne').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne" value="oui" id="spip_lettres_notifier_suppression_abonne_oui" '.($spip_lettres_notifier_suppression_abonne == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_notifier_suppression_abonne_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne" value="non" id="spip_lettres_notifier_suppression_abonne_non" '.($spip_lettres_notifier_suppression_abonne == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_notifier_suppression_abonne_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T("Statistiques anonymes sur les clics de liens").'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_cliquer_anonyme" value="oui" id="spip_lettres_cliquer_anonyme_oui" '.($spip_lettres_cliquer_anonyme == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_cliquer_anonyme_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_cliquer_anonyme" value="non" id="spip_lettres_cliquer_anonyme_non" '.($spip_lettres_cliquer_anonyme == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_cliquer_anonyme_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

		echo '</table>';
		
		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('lettresprive:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);

		echo '</form>';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

		echo fin_gauche();

		echo fin_page();

	}


?>