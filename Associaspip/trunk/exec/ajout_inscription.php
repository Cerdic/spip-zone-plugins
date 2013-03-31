<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_ajout_inscription() {
	if (!autoriser('editer_inscriptions', 'association') OR !(test_plugin_actif('AGENDA') OR test_plugin_actif('SIMPLECAL')) ) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('association_modules');
		$id_activite = association_passeparam_id('activite');
		if ($id_activite)
			$id_evenement = sql_getfetsel('id_evenement', 'spip_asso_activites', "id_activite=$id_activite");
		else
			$id_evenement = association_passeparam_id('id_evenement');
		echo association_navigation_onglets('titre_onglet_activite', 'activites');
		// INTRO : Rappel Infos Evenement
		$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement");
		$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
		$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
		$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
		$infos['agenda:evenement_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
		echo '<div class="vevent">'. association_totauxinfos_intro('<span class="summary">'.$evenement['titre'].'</span>', 'evenement', $id_evenement, $infos, 'evenement') .'</div>';
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			'activite_titre_inscriptions_activites' => array('grille-24.png', array('inscrits_activite', "id=$id_evenement"), array('voir_inscriptions', 'association') ),
		) );
		debut_cadre_association(($id_activite?'activites.gif':'panier_in.gif'), 'activite_titre_ajouter_inscriptions');
		echo recuperer_fond('prive/editer/ajouter_inscription', array (
			'id_activite' => $id_activite,
			'id_evenement' => $id_evenement,
		));
		fin_page_association();
	}
}

?>