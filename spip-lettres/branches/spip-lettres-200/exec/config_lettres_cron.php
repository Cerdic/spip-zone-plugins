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


	function exec_config_lettres_cron() {

		if (!autoriser('configurer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init', array('args' => array('exec' => 'config_lettres_cron'), 'data' => ''));

		if (!empty($_GET['supprimer_cron'])) {
			sql_delete('spip_rubriques_crontabs', 'id_rubrique='.intval($_GET['supprimer_cron']));
			$url = generer_url_ecrire('config_lettres_cron');
			header('Location: '.$url);
			exit();
		}

		if (!empty($_POST['valider'])) {
			if (!empty($_POST['titre']) and !empty($_POST['id_parent'])) {
				sql_replace('spip_rubriques_crontabs', array('id_rubrique' => intval($_POST['id_parent']), 'titre' => $_POST['titre']));
			}

			if (!empty($_POST['spip_lettres_envois_recurrents'])) {
				$spip_lettres_envois_recurrents = addslashes($_POST['spip_lettres_envois_recurrents']);
				ecrire_meta('spip_lettres_envois_recurrents', $spip_lettres_envois_recurrents);
			}

			ecrire_metas();

			$url = generer_url_ecrire('config_lettres_cron');
			header('Location: '.$url);
			exit();
		}

		$spip_lettres_envois_recurrents	= $GLOBALS['meta']['spip_lettres_envois_recurrents'];
		$spip_lettres_cron				= $GLOBALS['meta']['spip_lettres_cron'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

		echo '<br /><br /><br />';
		echo gros_titre(_T('titre_configuration'),'',false);
		echo barre_onglets("configuration", "config_lettres_formulaire_top");
		echo "<br>";
		echo barre_onglets("lettres", "config_lettres_cron");

		echo debut_gauche('', true);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'config_lettres_cron'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'config_lettres_cron'),'data'=>''));

   		echo debut_droite('', true);

		echo '<form method="post" action="'.generer_url_ecrire('config_lettres_cron').'">';
		echo debut_cadre_trait_couleur("", true, "", _T('lettresprive:config_cron'));

		echo '<table>';

	    echo '<tr>';
		echo '<td><label>'._T('lettresprive:spip_lettres_envois_recurrents').'</label></td>';
		echo '<td width="100">';
		echo '<input type="radio" class="radio" name="spip_lettres_envois_recurrents" value="oui" id="spip_lettres_envois_recurrents_oui" '.($spip_lettres_envois_recurrents == 'oui' ? 'checked="checked" ' : '').'/><label for="spip_lettres_envois_recurrents_oui">'._T('lettresprive:oui').'</label>';
		echo '&nbsp;';
		echo '<input type="radio" class="radio" name="spip_lettres_envois_recurrents" value="non" id="spip_lettres_envois_recurrents_non" '.($spip_lettres_envois_recurrents == 'non' ? 'checked="checked" ' : '').'/><label for="spip_lettres_envois_recurrents_non">'._T('lettresprive:non').'</label>';
		echo '</td>';
		echo '</tr>';

		echo '</table>';

		if ($spip_lettres_envois_recurrents == 'oui') {
			echo '<p>'._T('lettresprive:note_code_cron').'<br /><strong>spip.php?action=cron_lettres&code='.$spip_lettres_cron.'</strong></p>';
		}
		
		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('lettresprive:valider').'" /></p>';
		echo fin_cadre_trait_couleur(true);

		if ($spip_lettres_envois_recurrents == 'oui') {
			$cron = afficher_objets('cron', _T('lettresprive:taches_cron'), array('SELECT' => 'CT.*, RUB.titre AS titre_rub', 'FROM' => 'spip_rubriques_crontabs AS CT LEFT JOIN spip_rubriques AS RUB ON RUB.id_rubrique=CT.id_rubrique', 'ORDER BY' => 'CT.titre'));
			if ($cron) {
				echo $cron;
				echo '<br />';
			} else {
				echo debut_boite_info(true);
				echo _T('lettresprive:aucun_envoi_programme');
				echo fin_boite_info(true);
			}
			echo debut_cadre_trait_couleur("", true, "", _T('lettresprive:ajouter_rubrique'));
		    echo '<p>';
			echo '<label for="titre">'._T('lettresprive:titre').'</label>&nbsp;&nbsp;&nbsp;';
			echo '<input type="text" class="text" name="titre" id="titre" value="" /><br />';
			echo _T('lettresprive:cet_intitule_sera_titre_lettres_envoyees_par_cron');
			echo '</p>';
		    echo '<p>';
			echo '<label for="id_parent">'._T('lettresprive:choix_rubrique').'</label>';
			echo choisir_thematique();
			echo '</p>';
			echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('lettresprive:ajouter').'" /></p>';
			echo fin_cadre_trait_couleur(true);
		}
		
		echo '</form>';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'config_lettres_cron'),'data'=>''));

		echo fin_gauche();

		echo fin_page();

	}


?>