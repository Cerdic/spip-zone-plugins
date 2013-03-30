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

function exec_destination() {
	if (!autoriser('gerer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
		echo association_navigation_onglets('plan_comptable', 'association');
		// notice
		echo propre(_T('asso:destination_info'));
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			'association_infos_contacts' => array('assoc_qui.png', array('association'), array('voir_profil', 'association') ),
			'destination_nav_ajouter' => array('euro-39.gif', array('edit_destination')),
			'plan_comptable' => array('plan_compte.png', array('plan_comptable'), array('gerer_compta', 'association') ),
			'exercices_budgetaires_titre' => array('calculatrice.gif', array('exercice_comptable'), $GLOBALS['association_metas']['exercices'] ? array('gerer_compta', 'association') : FALSE ),
		) );
		debut_cadre_association('euro-39.gif', 'destination_comptable');
		//Affichage de la table
		echo association_bloc_listehtml2('asso_destination',
			sql_select("*", 'spip_asso_destination', '', 'id_destination', 'intitule'),
			array(
				'id_destination' => array('asso:entete_id', 'entier'),
				'intitule' => array('asso:entete_intitule', 'texte'),

				'commentaire' => array('asso:entete_commentaire', 'texte', 'typo'),
			), // entetes et formats des donnees
			array(
				array('suppr', 'destination', 'id=$$' ),
				array('edit', 'destination', 'id=$$' ),
			), // boutons d'action
			'id_destination' // champ portant la cle des lignes et des boutons
		);
		fin_page_association();
	}
}

?>
