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

function exec_suppr_activite() {
	$r = association_controle_id('activite', 'asso_activites', 'editer_inscriptions');
	if ($r AND (test_plugin_actif('AGENDA') OR test_plugin_actif('SIMPLECAL')) ) {
		list($id_activite, $activite) = $r;
		exec_suppr_activite_args($id_activite, $activite);
	}
}

function exec_suppr_activite_args($id_activite, $activite) {
	include_spip ('association_modules');
	echo association_navigation_onglets('titre_onglet_activite', 'activites');
	// INTRO : Rappel Infos Evenement & Participant
	$evenement = sql_fetsel('*', 'spip_evenements', 'id_evenement='.$activite['id_evenement']);
	$infos['evenement'] = $evenement['titre'];
	$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
	$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
	$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
	$infos['agenda:evenement_lieu'] = $evenement['lieu'];
	$infos[''] = typo('----'); // separateur
	$infos['nom'] = association_formater_idnom($activite['id_auteur'], $activite['nom'], '');
//		$infos['date'] = association_formater_date($activite['date_inscription']);
	$infos['date'] = association_formater_date($activite['date_paiement']);
	$infos['entete_quantite'] = association_formater_nombre($activite['quantite'], 1);
	$infos['entete_montant'] = association_formater_prix($activite['prix_unitaire'], 'fees');
	association_totauxinfos_intro('', 'activite', $id_activite, $infos );
	// datation et raccourcis
	echo association_navigation_raccourcis(array(
		array('activite_titre_inscriptions_activites', 'grille-24.png', array('inscrits_activite', "id=$activite[id_evenement]"), array('voir_inscriptions', 'association') ),
	) );
	debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
	echo association_form_suppression('activite', $id_activite);
	fin_page_association();
}

?>