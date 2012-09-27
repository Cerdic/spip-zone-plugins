<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_destination()
{
	if (!autoriser('configurer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('plan_comptable', 'association');
		// notice
		echo propre(_T('asso:destination_info'));
		// datation et raccourcis
		raccourcis_association('association', array(
			'destination_nav_ajouter' => array('euro-39.gif', 'edit_destination'),
		));
		debut_cadre_association('euro-39.gif', 'destination_comptable');
		//Affichage de la table
		echo association_bloc_listehtml(
			array("d.*, CONCAT('<:asso:nombre_fois{nombre=', COUNT(o.id_destination),'}:>') AS nombre_fois", 'spip_asso_destination AS d INNER JOIN spip_asso_destination_op AS o ON d.id_destination=o.id_destination', '', 'd.id_destination', 'intitule'), // requete
			array(
				'id_destination' => array('asso:entete_id', 'entier'),
				'intitule' => array('asso:entete_intitule', 'texte'),
//				'nombre_fois' => array('asso:entete_utilise', 'texte', 'propre', 'integer'),
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