<?php


	/**
	 * SPIP-Formulaires
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


	function exec_config_formulaires() {

		if (!autoriser('configurer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'config_formulaires'),'data'=>''));

		if (!empty($_POST['valider'])) {
			if (!empty($_POST['spip_formulaires_utiliser_descriptif'])) {
				$spip_formulaires_utiliser_descriptif = addslashes($_POST['spip_formulaires_utiliser_descriptif']);
				ecrire_meta('spip_formulaires_utiliser_descriptif', $spip_formulaires_utiliser_descriptif);
			}

			if (!empty($_POST['spip_formulaires_utiliser_chapo'])) {
				$spip_formulaires_utiliser_chapo = addslashes($_POST['spip_formulaires_utiliser_chapo']);
				ecrire_meta('spip_formulaires_utiliser_chapo', $spip_formulaires_utiliser_chapo);
			}

			if (!empty($_POST['spip_formulaires_utiliser_ps'])) {
				$spip_formulaires_utiliser_ps = addslashes($_POST['spip_formulaires_utiliser_ps']);
				ecrire_meta('spip_formulaires_utiliser_ps', $spip_formulaires_utiliser_ps);
			}

			ecrire_metas();

			$url = generer_url_ecrire('config_formulaires');
			header('Location: '.$url);
			exit();
		}

		$spip_formulaires_utiliser_descriptif		= $GLOBALS['meta']['spip_formulaires_utiliser_descriptif'];
		$spip_formulaires_utiliser_chapo			= $GLOBALS['meta']['spip_formulaires_utiliser_chapo'];
		$spip_formulaires_utiliser_ps				= $GLOBALS['meta']['spip_formulaires_utiliser_ps'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

		echo '<br /><br /><br />';
		echo gros_titre(_T('titre_configuration'),'',false);
		echo barre_onglets("configuration", "config_formulaires");

		echo debut_gauche('', true);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'config_formulaires'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'config_formulaires'),'data'=>''));

   		echo debut_droite('', true);

		echo '<form method="post" action="'.generer_url_ecrire('config_formulaires').'">';

		echo debut_cadre_trait_couleur("", true, "", _T('formulairesprive:options'));

		echo '<table>';

	    echo '<tr>';
		echo '<td><label>'._T('formulairesprive:spip_formulaires_utiliser_descriptif').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_formulaires_utiliser_descriptif" value="oui" id="spip_formulaires_utiliser_descriptif_oui" '.($spip_formulaires_utiliser_descriptif == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_formulaires_utiliser_descriptif_oui">'._T('formulairesprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_formulaires_utiliser_descriptif" value="non" id="spip_formulaires_utiliser_descriptif_non" '.($spip_formulaires_utiliser_descriptif == 'non' ? 'checked="checked" ' : '').'/><label for="spip_formulaires_utiliser_descriptif_non">'._T('formulairesprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T('formulairesprive:spip_formulaires_utiliser_chapo').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_formulaires_utiliser_chapo" value="oui" id="spip_formulaires_utiliser_chapo_oui" '.($spip_formulaires_utiliser_chapo == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_formulaires_utiliser_chapo_oui">'._T('formulairesprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_formulaires_utiliser_chapo" value="non" id="spip_formulaires_utiliser_chapo_non" '.($spip_formulaires_utiliser_chapo == 'non' ? 'checked="checked" ' : '').'/><label for="spip_formulaires_utiliser_chapo_non">'._T('formulairesprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

	    echo '<tr>';
		echo '<td><label>'._T('formulairesprive:spip_formulaires_utiliser_ps').'</label></td>';
		echo '<td>';
		echo '<input type="radio" class="radio" name="spip_formulaires_utiliser_ps" value="oui" id="spip_formulaires_utiliser_ps_oui" '.($spip_formulaires_utiliser_ps == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_formulaires_utiliser_ps_oui">'._T('formulairesprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_formulaires_utiliser_ps" value="non" id="spip_formulaires_utiliser_ps_non" '.($spip_formulaires_utiliser_ps == 'non' ? 'checked="checked" ' : '').'/><label for="spip_formulaires_utiliser_ps_non">'._T('formulairesprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

		echo '</table>';
		
		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('formulairesprive:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);

		echo '</form>';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'config_formulaires'),'data'=>''));

		echo fin_gauche();

		echo fin_page();

	}


?>