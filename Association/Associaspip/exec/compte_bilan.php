<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 08/2011 par Marcel BOLLA ... à partir de bilan.php           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');

function exec_compte_bilan()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
// initialisations
		include_spip('inc/association_comptabilite');
		$ids = association_passe_parametres_comptables();
		$exercice_data = sql_asso1ligne('exercice', $ids['exercice']);
// traitements
		onglets_association('titre_onglet_comptes');
		// INTRO : rappel de l'exercicee affichee
		$infos['exercice_entete_debut'] = association_datefr($exercice_data['debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_datefr($exercice_data['fin'], 'dtend');
		echo totauxinfos_intro($exercice_data['intitule'], 'exercice', $ids['exercice'], $infos);
		// pas de sommes de synthes puisque tous les totaux sont dans la zone centrale ;-
		// datation et raccourcis
		icones_association(array('comptes', "exercice=$ids[exercice]"), array(
			'encaisse_titre_general' => array('finances-24.png', 'encaisse', "exercice=$ids[exercice]".($ids['destination']?"&destination=$ids[destination]":'')),
			'cpte_resultat_titre_general' => array('finances-24.png', 'compte_resultat', "exercice=$ids[exercice]".($ids['destination']?"&destination=$ids[destination]":'')),
#			'annexe_titre_general' => array('finances-24.png', 'annexe', "exercice=$ids[exercice]".($ids['destination']?"&destination=$ids[destination]":'')),
		));
		if(autoriser('associer', 'export_comptes')){ // on peut exporter : pdf, csv, xml, ...
			echo debut_cadre_enfonce('',true);
			echo '<h3>'. _T('asso:cpte_bilan_mode_exportation') .'</h3>';
			if (test_plugin_actif('FPDF')) {  // impression en PDF : _T('asso:bouton_impression')
				echo icone1_association('PDF', generer_url_ecrire('pdf_comptesbilan', "exercice=$ids[exercice]".($ids['destination']?"&destination=$ids[destination]":'')), 'print-24.png');
			}
/*
			foreach(array('csv','ctx','dbk','json','tex','tsv','xml','yaml') as $type) { // autres exports (donnees brutes) possibles
				echo icone1_association(strtoupper($type), generer_url_ecrire("export_comptebilans_$type").'&var='.rawurlencode($ids['url']), 'export-24.png'); //!\ generer_url_ecrire($exec, $param) equivaut a generer_url_ecrire($exec).'&'.urlencode($param) or il faut utiliser rawurlencode($param) ici...
			}
*/
			echo fin_cadre_enfonce(true);
		}
		debut_cadre_association('finances-24.png', 'cpte_bilan_titre_general', $exercice_data['intitule']);
		// Filtres
		filtres_association(array(
			'exercice'=>$ids['exercice'],
			'destination'=>$ids['destination'],
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
		$passifs = association_liste_totaux_comptes_classes($classes_bilan, 'cpte_bilan', '+1', $ids['exercice'], $ids['destination']);
		// liste des actifs (les dettes) cumulees par comptes
		$actifs = association_liste_totaux_comptes_classes($classes_bilan, 'cpte_bilan', '-1', $ids['exercice'], $ids['destination']);
		// resultat comptable courant : en comptabilite francaise, la somme les actifs et les passifs doivent s'egaler, ce qui se fait en incorporant le resultat comptable (perte en actif et benefice en passif)
		association_liste_resultat_net($passifs, $actifs);
		// liste des bilans (actifs et passifs) par comptes
		fin_page_association();
	}
}

?>