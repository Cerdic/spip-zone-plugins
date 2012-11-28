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

// Version HTML de la synthese des Comptes de Bilan
function exec_compte_bilan() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/navigation_modules');
		include_spip('inc/association_comptabilite');
		$ids = association_passeparam_compta();
		onglets_association('titre_onglet_comptes', 'comptes');
		// INTRO : rappel de l'exercicee affichee
		$infos['exercice_entete_debut'] = association_formater_date($ids['debut_periode'], 'dtstart');
		$infos['exercice_entete_fin'] = association_formater_date($ids['fin_periode'], 'dtend');
		echo association_totauxinfos_intro($ids['titre_periode'], 'exercice', $ids['id_periode'], $infos);
		// pas de sommes de synthes puisque tous les totaux sont dans la zone centrale ;-
		// datation et raccourcis
		raccourcis_association(array('comptes', "$ids[type_periode]=$ids[id_periode]"), array(
			'encaisse_titre_general' => array('finances-24.png', array('encaisse', "$ids[type_periode]=$ids[id_periode]") ),
			'cpte_resultat_titre_general' => array('finances-24.png', array('compte_resultat', "$ids[type_periode]=$ids[id_periode]".($ids['destination']?"&destination=$ids[destination]":'')) ),
#			'annexe_titre_general' => array('finances-24.png', array('annexe', "$ids[type_periode]=$ids[id_periode]".($ids['destination']?"&destination=$ids[destination]":'')) ),
		));
		if(autoriser('voir_compta', 'association')) { // on peut exporter : pdf, csv, xml, ...
			echo debut_cadre_enfonce('', TRUE);
			echo '<h3>'. _T('asso:cpte_bilan_mode_exportation') .'</h3>';
			if (test_plugin_actif('FPDF')) {  // impression en PDF : _T('asso:bouton_impression')
				echo icone1_association('PDF', generer_action_auteur('pdf_comptesbilan', 0), 'print-24.png');
			}
			export_compte($ids, 'bilan');
			echo fin_cadre_enfonce(TRUE);
		}
		debut_cadre_association('finances-24.png', 'cpte_bilan_titre_general');
		// Filtres
		echo association_bloc_filtres(array(
			'periode' => array($ids['id_periode'], 'asso_comptes', 'operation'),
			'destination' => $ids['destination'],
		), 'compte_bilan');
		// les autres classes a prendre en compte ici
		$classes_bilan = array();
		$query = sql_select(
			'classe', // select
			'spip_asso_plan', // from
			sql_in('classe', array($GLOBALS['association_metas']['classe_charges'],$GLOBALS['association_metas']['classe_produits'],$GLOBALS['association_metas']['classe_contributions_volontaires']), 'NOT'), // where http://programmer.spip.org/sql_in,642
			'classe', // group by
			'classe' // order by
		);
		while ($data = sql_fetch($query)) {
			$classes_bilan[] = $data['classe'];
		}
		// liste des passifs (le patrimoine/avoir) cumulees par comptes
		$passifs = association_liste_totaux_comptes_classes($classes_bilan, 'cpte_bilan', '+1', $ids['id_periode'], $ids['destination']);
		// liste des actifs (les dettes) cumulees par comptes
		$actifs = association_liste_totaux_comptes_classes($classes_bilan, 'cpte_bilan', '-1', $ids['id_periode'], $ids['destination']);
		// resultat comptable courant : en comptabilite francaise, la somme les actifs et les passifs doivent s'egaler, ce qui se fait en incorporant le resultat comptable (perte en actif et benefice en passif)
		association_liste_resultat_net($passifs, $actifs);
		// liste des bilans (actifs et passifs) par comptes
		fin_page_association();
	}
}

?>
