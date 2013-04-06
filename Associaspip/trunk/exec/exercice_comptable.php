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

function exec_exercice_comptable() {
	if (!autoriser('gerer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('association_modules');
		echo association_navigation_onglets('exercices_budgetaires_titre', 'association');
		// notice
		echo '';
		// quelques stats sur les exrcices
		echo association_totauxinfos_stats('tous', 'exercices', array('semaines'=>"TIMESTAMPDIFF(week,date_debut,date_fin)", 'mois'=>"TIMESTAMPDIFF(month,date_debut,date_fin)") );
		//!\ portability issue on DATEDIFF vs TIMESTAMPDIFF
		// MS SQL Server : "DATEDIFF(day,debut,fin)" & "DATEDIFF(week,debut,fin)" & "DATEDIFF(month,debut,fin)"
		// MySQL : "DATEDIFF(debut,fin)" & "TIMESTAMPDIFF(week,debut,fin)" & "TIMESTAMPDIFF(month,debut,fin)"
		// Oracle : "fin-debut" & & "MONTHS_BETWEEN(debut,fin)"
		// converting to epoch <http://www.epochconverter.com/> doesn't help either
		// ...or maybe something like "CAST(fin AS TIMESTAMP)-CAST(debut AS TIMETAMP)" ?
		// finaly I use ODBC "TIMESTAMPDIFF()" that should be known by latest major rdbms...
		///
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			array('association_infos_contacts', 'assoc_qui.png', array('association'), array('voir_profil', 'association') ),
			'ajouter_un_exercice' => array('calculatrice.gif', array('edit_exercice'),array('gerer_compta', 'association') ),
			array('plan_comptable', 'plan_compte.png', array('plan_comptable'), array('gerer_compta', 'association') ),
			array('destination_comptable', 'euro-39.gif', array('destination_comptable'), $GLOBALS['association_metas']['destinations'] ? array('gerer_compta', 'association') : FALSE ),
		) );
		debut_cadre_association('calculatrice.gif', 'tous_les_exercices');
		echo association_bloc_listehtml2('asso_exercices',
			sql_select('*', 'spip_asso_exercices', '', 'intitule DESC'),
			array(
				'id_exercice' => array('asso:entete_id', 'entier'),
				'intitule' => array('asso:entete_intitule', 'texte'),
				'date_debut' => array('asso:exercice_entete_debut', 'date', 'dtstart'),
				'date_fin' => array('asso:exercice_entete_fin', 'date', 'dtend'),
				'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
			), // entetes et formats des donnees
			array(
				array('suppr', 'exercice', 'id=$$' ),
				array('edit', 'exercice', 'id=$$' ),
			), // boutons d'action
			'id_exercice' // champ portant la cle des lignes et des boutons
		);
		fin_page_association();
	}
}

?>