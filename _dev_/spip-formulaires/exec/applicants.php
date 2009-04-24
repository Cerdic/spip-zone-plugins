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
	include_spip('formulaires_fonctions');


	function exec_applicants() {

		global $spip_lang_right;

		if (!autoriser('voir', 'formulaires')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_applicant = intval($_GET['id_applicant']);
		$applicant = new applicant($id_applicant);

		pipeline('exec_init',array('args'=>array('exec'=>'applicants','id_applicant'=>$applicant->id_applicant),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($application->applicant->email, "naviguer", "formulaires_tous");

		echo debut_gauche('', true);
		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('formulairesprive:applicant_numero').' :';
		echo '<p>'.$applicant->id_applicant.'</p>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'applicants','id_applicant'=>$applicant->id_applicant),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'applicants','id_applicant'=>$applicant->id_applicant),'data'=>''));

    	echo debut_droite('', true);

		echo '<div class="fiche_objet">';

		echo '<div class="bandeau_actions">';
		echo '<div style="float: right;">';
		echo icone_inline(_T('formulairesprive:modifier_applicant'), generer_url_ecrire("applicants_edit", "id_applicant=".$applicant->id_applicant), _DIR_PLUGIN_FORMULAIRES.'/prive/images/applications.png', "edit.gif", $GLOBALS['spip_lang_left']);
		echo '</div>';
		echo '</div>';

		echo '<h1>'.$applicant->email.'</h1>';

		if ($applicant->nom)
			echo '<span class="arial1 spip_medium"><b>'.typo($applicant->nom).'</b></span>';

		echo '<br class="nettoyeur" />';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'applicants','id_applicant'=>$applicant->id_applicant),'data'=>''));

		echo '</div><!-- fin fiche_objet -->';

		$res = sql_select('id_formulaire', 'spip_formulaires', 'limiter_invitation="oui"');
		if (sql_count($res)) {
			while ($arr = sql_fetch($res))
				$forms[] = $arr['id_formulaire'];
			echo afficher_objets('application', _T('formulairesprive:applications_cet_applicant'), array('FROM' => 'spip_applications', 'WHERE' => 'id_formulaire IN ('.implode(',', $forms).') AND id_applicant='.intval($id_applicant), 'ORDER BY' => 'maj DESC'));
		}

		echo fin_gauche();

		echo fin_page();

	}


?>