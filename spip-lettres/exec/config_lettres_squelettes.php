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

			$spip_lettres_cliquer_anonyme = $_POST['spip_lettres_cliquer_anonyme'];
			ecrire_meta('spip_lettres_cliquer_anonyme', $spip_lettres_cliquer_anonyme);

			$spip_lettres_admin_abo_toutes_rubriques = $_POST['spip_lettres_admin_abo_toutes_rubriques'];
			ecrire_meta('spip_lettres_admin_abo_toutes_rubriques', $spip_lettres_admin_abo_toutes_rubriques);
			
			$spip_lettres_log_utiliser_email = $_POST['spip_lettres_log_utiliser_email'];
			ecrire_meta('spip_lettres_log_utiliser_email', $spip_lettres_log_utiliser_email);
			
			$spip_lettres_signe_par_auteurs = $_POST['spip_lettres_signe_par_auteurs'];
			ecrire_meta('spip_lettres_signe_par_auteurs', $spip_lettres_signe_par_auteurs);

			// notification du désabonnement d'un abonné
			$spip_lettres_notifier_suppression_abonne = $_POST['spip_lettres_notifier_suppression_abonne'];
			$spip_lettres_notifier_suppression_abonne_cible  = $_POST['spip_lettres_notifier_suppression_abonne_cible'];
			$spip_lettres_notifier_auteur_id = $_POST['spip_lettres_notifier_auteur_id'];
	
			// les valeurs possibles sont : 'non', 'webmaster' ou un auteur_id			
			if ('non'==$spip_lettres_notifier_suppression_abonne)
				$meta_spip_lettres_notifier_desabonnement = 'non';
			else if ('webmaster' == $spip_lettres_notifier_suppression_abonne_cible)
				$meta_spip_lettres_notifier_desabonnement = 'webmaster';
			else if ('auteur' == $spip_lettres_notifier_suppression_abonne_cible)
				$meta_spip_lettres_notifier_desabonnement = $spip_lettres_notifier_auteur_id;

			ecrire_meta('spip_lettres_notifier_suppression_abonne', $meta_spip_lettres_notifier_desabonnement);

			ecrire_metas();

			$url = generer_url_ecrire('config_lettres_squelettes');
			header('Location: '.$url);
			exit();
		}

		$spip_lettres_fond_formulaire_lettres			= $GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'];
		$spip_lettres_fond_lettre_titre					= $GLOBALS['meta']['spip_lettres_fond_lettre_titre'];
		$spip_lettres_fond_lettre_html					= $GLOBALS['meta']['spip_lettres_fond_lettre_html'];
		$spip_lettres_fond_lettre_texte					= $GLOBALS['meta']['spip_lettres_fond_lettre_texte'];
		$spip_lettres_utiliser_articles					= $GLOBALS['meta']['spip_lettres_utiliser_articles'];
		$spip_lettres_utiliser_descriptif				= $GLOBALS['meta']['spip_lettres_utiliser_descriptif'];
		$spip_lettres_utiliser_chapo					= $GLOBALS['meta']['spip_lettres_utiliser_chapo'];
		$spip_lettres_utiliser_ps						= $GLOBALS['meta']['spip_lettres_utiliser_ps'];	
		$spip_lettres_cliquer_anonyme					= $GLOBALS['meta']['spip_lettres_cliquer_anonyme'];
		$spip_lettres_admin_abo_toutes_rubriques		= $GLOBALS['meta']['spip_lettres_admin_abo_toutes_rubriques'];
		$spip_lettres_log_utiliser_email				= $GLOBALS['meta']['spip_lettres_log_utiliser_email'];
		$spip_lettres_signe_par_auteurs					= $GLOBALS['meta']['spip_lettres_signe_par_auteurs'];

		$spip_lettres_notifier_suppression_abonne = '';
		$spip_lettres_notifier_suppression_abonne_cible = '';
		$spip_lettres_notifier_auteur_id = '';
		// configuration de l'envoi de notitication de désabonnement
		if ('non' == $GLOBALS['meta']['spip_lettres_notifier_suppression_abonne'])
		{
			$spip_lettres_notifier_suppression_abonne = 'non';
			$spip_lettres_notifier_suppression_abonne_cible  = 'webmaster';
			$spip_lettres_notifier_auteur_id = '0';	
		}
			
		else if ('webmaster' == $GLOBALS['meta']['spip_lettres_notifier_suppression_abonne'])
		{
			$spip_lettres_notifier_suppression_abonne = 'oui';
			$spip_lettres_notifier_suppression_abonne_cible  = 'webmaster';
			$spip_lettres_notifier_auteur_id = '0';	
		}
		else if (is_numeric($GLOBALS['meta']['spip_lettres_notifier_suppression_abonne']))
		{
			$spip_lettres_notifier_suppression_abonne = 'oui';
			$spip_lettres_notifier_suppression_abonne_cible  = 'auteur';
			$spip_lettres_notifier_auteur_id = $GLOBALS['meta']['spip_lettres_notifier_suppression_abonne'];	
		}

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
		echo '<td><label>'._T("lettresprive:cliquer_anonyme").'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_cliquer_anonyme" value="oui" id="spip_lettres_cliquer_anonyme_oui" '.($spip_lettres_cliquer_anonyme == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_cliquer_anonyme_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_cliquer_anonyme" value="non" id="spip_lettres_cliquer_anonyme_non" '.($spip_lettres_cliquer_anonyme == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_cliquer_anonyme_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T("lettresprive:admin_abo_toutes_rubriques").'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_admin_abo_toutes_rubriques" value="oui" id="spip_lettres_admin_abo_toutes_rubriques_oui" '.($spip_lettres_admin_abo_toutes_rubriques == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_admin_abo_toutes_rubriques_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_admin_abo_toutes_rubriques" value="non" id="spip_lettres_admin_abo_toutes_rubriques_non" '.($spip_lettres_admin_abo_toutes_rubriques == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_admin_abo_toutes_rubriques_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T("lettresprive:log_utiliser_email").'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_log_utiliser_email" value="oui" id="spip_lettres_log_utiliser_email_oui" '.($spip_lettres_log_utiliser_email == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_log_utiliser_email_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_log_utiliser_email" value="non" id="spip_lettres_log_utiliser_email_non" '.($spip_lettres_log_utiliser_email == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_log_utiliser_email_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T("lettresprive:signe_par_auteurs").'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_signe_par_auteurs" value="oui" id="spip_lettres_signe_par_auteurs_oui" '.($spip_lettres_signe_par_auteurs == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_signe_par_auteurs_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_signe_par_auteurs" value="non" id="spip_lettres_signe_par_auteurs_non" '.($spip_lettres_signe_par_auteurs == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_signe_par_auteurs_non">'._T('lettresprive:non').'</label>';

		echo '</td>';
		echo '</tr>';

		echo '</table>';
		
		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('lettresprive:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);
		
		echo debut_cadre_trait_couleur("", true, "", _T('lettresprive:notifications'));

		echo '<table>';

	    echo '<tr>';
		echo '<td><label>'._T('lettresprive:notifier_desabonnement_par_mail').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne" value="oui" id="spip_lettres_notifier_suppression_abonne_oui" '.($spip_lettres_notifier_suppression_abonne == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_notifier_suppression_abonne_oui" class="spip_lettres_notifier_suppression_abonne">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne" value="non" id="spip_lettres_notifier_suppression_abonne_non" '.($spip_lettres_notifier_suppression_abonne == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_notifier_suppression_abonne_non" class="spip_lettres_notifier_suppression_abonne">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr class="spip_lettres_notifier_suppression_abonne_cible"'.($spip_lettres_notifier_suppression_abonne == 'non' ? ' style="display:none" ' : '').'>';
		echo '<td colspan="2">';
		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne_cible" value="webmaster" id="spip_lettres_notifier_suppression_abonne_cible_webmaster" '.($spip_lettres_notifier_suppression_abonne_cible == 'webmaster' ? 'checked="checked" ' : '').'/>';
		echo '&nbsp;';
		echo '<label for="spip_lettres_notifier_suppression_abonne_cible_webmaster">'._T('lettresprive:envoyer_notification_desabonnement_webmaster').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr class="spip_lettres_notifier_suppression_abonne_cible"'.($spip_lettres_notifier_suppression_abonne == 'non' ? ' style="display:none" ' : '').'>';
		echo '<td colspan="2">';

		echo '<input type="radio" class="radio" name="spip_lettres_notifier_suppression_abonne_cible" value="auteur" id="spip_lettres_notifier_suppression_abonne_cible_auteur" '.($spip_lettres_notifier_suppression_abonne_cible == 'auteur' ? 'checked="checked" ' : '').'/>';
		echo '&nbsp;';

		echo '<label for="spip_lettres_notifier_suppression_abonne_cible_auteur">'. ucfirst(_T('lettresprive:envoyer_notification_desabonnement_auteur')).'</label>';
		echo '</tr>';

		$auteurs = sql_allfetsel("id_auteur, nom", "spip_auteurs", "statut='0minirezo'", "", "nom");
		$options = '<option value="0">'._T('lettresprive:selectionner_auteur').'</option>';
		foreach($auteurs as $ligne)
		{
			$options .= '<option value="'.$ligne['id_auteur'].'"'.(intval($spip_lettres_notifier_auteur_id) ==$ligne['id_auteur'] ? ' selected="selected"' : '' ).'>'.$ligne['nom'].'</option>';
		}
	
		echo '<tr class="spip_lettres_notifier_suppression_abonne_cible_auteur"'.($spip_lettres_notifier_suppression_abonne_cible != 'auteur' ? ' style="display:none"' : '').'>';
		echo '<td colspan="2" style="text-align:center">';
		echo '<select name="spip_lettres_notifier_auteur_id">';
		echo $options;
		echo '</select>';
		echo '</td>';
		echo '</tr>';
		
		echo '</table>';
		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('lettresprive:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);		
		
		echo '</form>';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'config_lettres_squelettes'),'data'=>''));

		echo fin_gauche();

		echo '<script type="text/javascript">
		$(document).ready(function($) {
			  $("input[name=spip_lettres_notifier_suppression_abonne]").click(function (){
					if ("oui"==$("input[name=spip_lettres_notifier_suppression_abonne]:checked").val())
						$(".spip_lettres_notifier_suppression_abonne_cible").show("normal");
					else
						$(".spip_lettres_notifier_suppression_abonne_cible").hide("normal");
			  });
			  $("input[name=spip_lettres_notifier_suppression_abonne_cible]").click(function (){
					if ("auteur"==$("input[name=spip_lettres_notifier_suppression_abonne_cible]:checked").val())
						$(".spip_lettres_notifier_suppression_abonne_cible_auteur").show("normal");
					else
						$(".spip_lettres_notifier_suppression_abonne_cible_auteur").hide("normal");
			  });
		});
		</script>';

		echo fin_page();

	}


?>