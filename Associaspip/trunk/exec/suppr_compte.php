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

function exec_suppr_compte() {
	$r = association_controle_id('compte', 'asso_comptes', 'editer_compta');
	if ($r) {
		include_spip ('association_modules');
/// INITIALISATIONS
		list($id_compte, $row) = $r;
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('titre_onglet_comptes', 'comptes');
/// AFFICHAGES_LATERAUX : INTRO : rappel operation
		$infos['entete_date'] = association_formater_date($row['date_operation'], 'dtstart');
		$infos['entete_montant'] = association_formater_prix($row['recette']-$row['depense']);
		$infos['compte_entete_imputation'] = association_formater_code($row['imputation'], sql_getfetsel('intitule', 'spip_asso_plan', 'code='.sql_quote($row['imputation'])) );
		$infos['compte_entete_financier'] = association_formater_code($row['journal'], sql_getfetsel('intitule', 'spip_asso_plan', 'code='.sql_quote($row['journal'])) );
		$infos['compte_entete_justification'] = $row['justification'];
		echo association_tablinfos_intro('', 'compte', $id_compte, $infos );
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('informations_comptables', 'grille-24.png', array('comptes', "id=$id_compte"), array('gerer_compta', 'association') ),
		) );
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('finances-32.jpg', 'operations_comptables');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
		echo association_form_suppression('compte', $id_compte );
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>