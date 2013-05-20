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

// Version HTML de la synthese des Comptes d'Annexes ?
function exec_compte_annexe() {
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
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('informations_comptables', 'grille-24.png', array('comptes', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
		array('encaisse_titre_general', 'finances-24.png', array('encaisse', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
		array('cpte_resultat_titre_general', 'finances-24.png', array('compte_resultat', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
		'cpte_bilan_titre_general' => array('finances-24.png', array('bilan', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
	), 16);
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('finances-24.png', 'annexe_titre_general');
/// AFFICHAGES_CENTRAUX : FILTRES
	echo association_form_filtres(array(
		'periode' => array($ids['id_periode'], 'asso_comptes', 'operation'),
		'destination' => $ids['destination'],
	), 'annexe');
/// AFFICHAGES_CENTRAUX : TABLEAU
	echo _T('asso:non_implemente');
	// http://www.aquadesign.be/actu/article-3678.php
	// http://www.documentissime.fr/dossiers-droit-pratique/dossier-274-les-documents-comptables-obligatoires/les-comptes-annuels/l-annexe.html
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>