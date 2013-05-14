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

function exec_edit_activite() {
	sinon_interdire_acces(autoriser('editer_inscriptions', 'association') OR !(test_plugin_actif('AGENDA') OR test_plugin_actif('SIMPLECAL')) );
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_activite = association_passeparam_id('activite');
	$id_evenement = ($id_activite ? sql_getfetsel('id_evenement', 'spip_asso_activites', "id_activite=$id_activite") : association_recuperer_entier('id_evenement'));
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_activite', 'activites');
/// AFFICHAGES_LATERAUX : INTRO : info evenement
	$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement");
	if (test_plugin_actif('AGENDA')) {
		$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date'); // les champs sont de type "DateTime" mais cet champ qui vaut "oui"/"non" indique s'il faut prendre en compte ou pas les horaires et les intitules vont dans ce sens
		$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
		$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
		if ($evenement['lieu'])
			$infos['agenda:evenement_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
		if ($evenement['descriptif'])
			$infos['agenda:evenement_descriptif'] = '<span class="description">'.$evenement['descriptif'].'</span>';
	} elseif (test_plugin_actif('SIMPLECAL')) {
		$format = 'association_formater_date'; // les champs sont de type "DateTime" (donc a priori formater_heure) mais le "DatePicker" de l'interface ne permet de saisir que la date (donc finalement formater_date) et les intitules vont dans ce sens
		$infos['simplecal:info_date_debut'] = $format($evenement['date_debut'],'dtstart');
		$infos['simplecal:info_date_fin'] = $format($evenement['date_fin'],'dtend');
		if ($evenement['lieu'])
			$infos['simplecal:info_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
		if ($evenement['statut'])
			$infos['simplecal:statut'] = '<span class="status">'. _T('simplecal:info_statut_'.$evenement['statut']) .'</span>';
		if ($evenement['descriptif'])
			$infos['agenda:descriptif'] = '<span class="description">'.$evenement['descriptif'].'</span>';
	}
	echo '<div class="vevent">'. association_tablinfos_intro('<span class="summary">'.$evenement['titre'].'</span>', 'evenement', $id_evenement, $infos, 'evenement') .'</div>';
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('activite_titre_inscriptions_activites', 'grille-24.png', array('inscrits_activite', "id=$id_evenement"), array('voir_inscriptions', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association(($id_activite?'activites.gif':'panier_in.gif'), 'activite_titre_mise_a_jour_inscriptions');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_activite', array (
		'id_activite' => $id_activite,
		'id_evenement' => $id_evenement,
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>