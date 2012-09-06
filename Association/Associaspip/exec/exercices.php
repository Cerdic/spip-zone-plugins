<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 11/2011 par Marcel BOLLA ...                                 *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_exercices()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('exercices_budgetaires_titre');
		// notice
		echo '';
		// quelques stats sur les exrcices
		echo association_totauxinfos_stats('tous', 'exercices', array('semaines'=>"TIMESTAMPDIFF(week,debut,fin)", 'mois'=>"TIMESTAMPDIFF(month,debut,fin)") );
		/* portability issue on "DATEDIFF(week,debut,fin)"
		 * MS SQL Server : "DATEDIFF(day,debut,fin)" & "DATEDIFF(week,debut,fin)" & "DATEDIFF(month,debut,fin)"
		 * MySQL : "DATEDIFF(debut,fin)" & "TIMESTAMPDIFF(week,debut,fin)" & "TIMESTAMPDIFF(month,debut,fin)"
		 * Oracle : "fin-debut" & & "MONTHS_BETWEEN(debut,fin)"
		 *
		 * converting to epoch <http://www.epochconverter.com/> doesn't help either
		 * ...or maybe something like "CAST(fin AS TIMESTAMP)-CAST(debut AS TIMETAMP)" ?
		 * finaly I use ODBC "TIMESTAMPDIFF()" that should be known by latest major rdbms...
		 * */
		// datation et raccourcis
		raccourcis_association('association', array(
			'ajouter_un_exercice' => array('calculatrice.gif', 'edit_exercice'),
		) );
		debut_cadre_association('calculatrice.gif', 'tous_les_exercices');
		echo association_bloc_listehtml(
			array('*', 'spip_asso_exercices', '', 'intitule DESC'), // requete
			array(
				'id_exercice' => array('asso:entete_id', 'entier'),
				'intitule' => array('asso:entete_intitule', 'texte'),
				'debut' => array('asso:exercice_entete_debut', 'date', 'dtstart'),
				'fin' => array('asso:exercice_entete_fin', 'date', 'dtend'),
				'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
			), // entetes et formats des donnees
			array(
				array('supprimer', 'exercice', 'id=$$', 'td'),
				array('modifier', 'exercice', 'id=$$', 'td'),
			), // boutons d'action
			'id_exercice' // champ portant la cle des lignes et des boutons
		);
		fin_page_association();
	}
}

?>