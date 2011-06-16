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
	include_spip('lettres_fonctions');

	function exec_naviguer_purge() {
		$id_rubrique	= $_REQUEST['id_rubrique'];
		$purger			= $_REQUEST['purger'];
		$id_parent		= $_REQUEST['id_parent'];

		if (!autoriser('purger', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'naviguer_purge'),'data'=>''));

		if (!empty($purger)) {
			$abonnes = sql_select('id_abonne', 'spip_abonnes_rubriques', 'id_rubrique='.intval($id_parent));
			$nb_abonnements_supprimes = 0;
			while ($arr = sql_fetch($abonnes)) {
				$abonne = new abonne($arr['id_abonne']);
				$abonne->valider_desabonnement($id_parent);
				$abonne->supprimer_si_zero_abonnement();
				$nb_abonnements_supprimes++;
			}
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:purge_abonnes'), "naviguer", "abonnes_tous");

		echo '<br /><br /><br />';
		echo gros_titre(_T('lettresprive:purge_abonnes'),'',false);

		echo debut_gauche('', true);

		echo debut_boite_alerte(true);
		echo _T('lettresprive:aide_naviguer_purge');
		echo fin_boite_alerte(true);

		$raccourcis = icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire('abonnes_tous'), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', 'rien.gif', false);
		if ($id_rubrique)
			$raccourcis.= icone_horizontale(_T('lettresprive:retour_rubrique'), generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique), _DIR_PLUGIN_LETTRES.'prive/images/rubrique-24.png', 'rien.gif', false);
		echo bloc_des_raccourcis($raccourcis);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'naviguer_purge'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'naviguer_purge'),'data'=>''));

   		echo debut_droite('', true);

		echo "<form method='post' action='".generer_url_ecrire('naviguer_purge')."' method='get'>";

		if (!empty($purger)) {
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/purge.png', true, "", _T('lettresprive:resultat'));
			echo "<p><strong>"._T('lettresprive:nb_abonnements_supprimes')."</strong> ".$nb_abonnements_supprimes."</p>";
			echo '<div align="right">';
			echo '<input type="submit" name="retour" class="fondo" value="'._T('lettresprive:retour').'" />';
			echo '</div>';
			echo fin_cadre_enfonce(true);
		} else {
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/purge.png', true, "", _T('lettresprive:selectionnez_rubrique'));
			echo choisir_thematique($id_rubrique);

			echo '<input type="hidden" name="id_rubrique" value="'.$id_rubrique.'" />';
			echo '<div align="right">';
			echo '<input type="submit" name="purger" class="fondo" value="'._T('lettresprive:purger').'" />';
			echo '</div>';
			echo fin_cadre_enfonce(true);
		}

		echo '</form>';

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'naviguer_purge'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>