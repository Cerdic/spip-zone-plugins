<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 Emmanuel Saint-James
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/
if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Version HTML de la synthese des Comptes de Resultat
function exec_compte_resultat() {
	sinon_interdire_acces(autoriser('voir_compta', 'association'));
	include_spip('association_modules');
/// INITIALISATIONS
	include_spip('inc/association_comptabilite');
	$ids = association_passeparam_compta();
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_comptes', 'comptes');
/// AFFICHAGES_LATERAUX : INTRO : rappel de l'exercicee affichee
	$infos['exercice_entete_debut'] = association_formater_date($ids['debut_periode'], 'dtstart');
	$infos['exercice_entete_fin'] = association_formater_date($ids['fin_periode'], 'dtend');
	echo association_tablinfos_intro($ids['titre_periode'], 'exercice', $ids['id_periode'], $infos);
	// pas de sommes de synthes puisque tous les totaux sont dans la zone centrale ;-
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('informations_comptables', 'grille-24.png', array('comptes', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
		array('encaisse_titre_general', 'finances-24.png', array('encaisse', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
		array('cpte_bilan_titre_general', 'finances-24.png', array('compte_bilan', "$ids[type_periode]=$ids[id_periode]".($ids['destination']?"&destination=$ids[destination]":'')), array('voir_compta', 'association') ),
#		array('annexe_titre_general', 'finances-24.png', array('compte_annexe', "$ids[type_periode]=$ids[id_periode]".($ids['destination']?"&destination=$ids[destination]":'')), array('voir_compta', 'association') ),
	), 16);
	if(autoriser('exporter_compta', 'association')) { // on peut exporter : pdf, csv, xml, ...
		echo debut_cadre_enfonce('', TRUE);
		echo '<h3>'. _T('asso:cpte_resultat_mode_exportation') .'</h3>';
		if (test_plugin_actif('FPDF')) { // impression en PDF : _T('asso:bouton_impression')
			echo association_navigation_raccourci1('PDF', 'print-24.png', generer_action_auteur('pdf_comptesresultat', 0) );
		}
		export_compte($ids, 'resultats');
		echo fin_cadre_enfonce(TRUE);
	}
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('finances-24.png', 'cpte_resultat_titre_general');
/// AFFICHAGES_CENTRAUX : FILTRES
	echo association_form_filtres(array(
		'periode' => array($ids['id_periode'], 'asso_comptes', 'operation'),
		'destination' => $ids['destination'],
	), 'compte_resultat');
/// AFFICHAGES_CENTRAUX : TABLEAU
	$regles = comptabilite_liste_planregles();
	echo comptabilite_tableau_balances($GLOBALS['association_metas']['classe_charges'], 'cpte_resultat', '-1', $ids['debut_periode'], $ids['fin_periode'], $ids['destination']); // liste des charges (depenses d'exploitation) cumulees par comptes
	echo comptabilite_tableau_balances($GLOBALS['association_metas']['classe_produits'], 'cpte_resultat', '+1', $ids['debut_periode'], $ids['fin_periode'], $ids['destination']); // liste des produits (recettes d'exploitation) cumules par comptes
	echo comptabilite_tableau_resultat($regles['A'], $ids['debut_periode'], $ids['fin_periode'], $ids['destination']); // resultat comptable courant : c'est la difference entre les recettes et les depenses d'exploitation
	// liste des contributions volontaires (emplois et ressources) par comptes
	$contributions = association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_contributions_volontaires'], 'cpte_benevolat', 0, $ids['id_periode'], $ids['destination']);
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>