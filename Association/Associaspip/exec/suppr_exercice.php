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

function exec_suppr_exercice() {
	if (!autoriser('associer', 'exercices')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		list($id_exercice, $exercice) = association_passeparam_id('exercice', 'asso_exercices');
		onglets_association('exercices_budgetaires_titre', 'association');
		// info
		$infos['exercice_entete_debut'] = association_formater_date($exercice['date_debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_formater_date($exercice['date_fin'], 'dtend');
		echo association_totauxinfos_intro($exercice['intitule'], 'exercice', $id_exercice, $infos);
		// datation et raccourcis
		raccourcis_association('exercices');
		debut_cadre_association('calculatrice.gif', 'exercice_budgetaire_titre');
		echo association_bloc_suppression('exercice', $id_exercice);
		fin_page_association();
	}
}

?>