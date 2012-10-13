<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 Emmanuel Saint-James
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/
if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Version HTML de la synthese des Comptes d'Annexes ?
function exec_annexe() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/navigation_modules');
		include_spip('inc/association_comptabilite');
// initialisations
		$ids = association_passe_parametres_comptables();
		$exercice_data = sql_asso1ligne('exercice', $ids['exercice']);
// traitements
		onglets_association('titre_onglet_comptes', 'comptes');
		// INTRO : rappel de l'exercicee affichee
		$infos['exercice_entete_debut'] = association_formater_date($exercice_data['debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_formater_date($exercice_data['fin'], 'dtend');
		echo association_totauxinfos_intro($exercice_data['intitule'], 'exercice', $ids['exercice'], $infos);
		// datation et raccourcis
		raccourcis_association(array('comptes', "exercice=$exercice"), array(
			'encaisse_titre_general' => array('finances-24.png', array('encaisse', "exercice=$exercice") ),
			'cpte_resultat_titre_general' => array('finances-24.png', array('compte_resultat', "exercice=$exercice") ),
			'cpte_bilan_titre_general' => array('finances-24.png', array('bilan', "exercice=$exercice") ),
		));
		debut_cadre_association('finances-24.png', 'annexe_titre_general', $exercice_data['intitule']);
		// Filtres
		filtres_association(array(
			'exercice'=>$ids['exercice'],
			'destination'=>$ids['destination'],
		), 'annexe');
		echo _T('asso:non_implemente');
		// http://www.aquadesign.be/actu/article-3678.php
		// http://www.documentissime.fr/dossiers-droit-pratique/dossier-274-les-documents-comptables-obligatoires/les-comptes-annuels/l-annexe.html
		fin_page_association();
	}
}

?>