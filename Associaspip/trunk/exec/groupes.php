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

function exec_groupes() {
	if (!autoriser('voir_groupes', 'association', 100)) { // l'id groupe passe en parametre est a 100 car ce sont les groupes definis par l'utilisateur et non ceux des autorisation qu'on liste dans cette page.
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('gestion_groupes', 'adherents');
		// notice
		echo _T('asso:aide_groupes');
		// datation et raccourcis
		echo association_navigation_raccourcis('adherents', array(
			'ajouter_un_groupe' => array('annonce.gif', 'edit_groupe'),
		));
		debut_cadre_association('annonce.gif', 'tous_les_groupes');
		// affichage du tableau
		echo association_bloc_listehtml2('asso_groupes',
			sql_select('*', 'spip_asso_groupes', 'id_groupe>=100','',  'nom'),
			array(
#				'id_groupe' => array('asso:entete_id', 'entier'),
				'nom' => array('asso:entete_nom', 'texte'),
				'commentaire' => array('asso:entete_commentaire', 'texte'),
				'affichage' => array('asso:ordre_affichage_groupe', 'entier'),
			), // entetes et formats des donnees
			array(
				array('suppr', 'groupe', 'id=$$'),
				array('edit', 'groupe', 'id=$$'),
				array('act', 'voir_groupe', 'voir-12.png', 'membres_groupe', 'id=$$'),
			), // boutons d'action
			'id_groupe' // champ portant la cle des lignes et des boutons
		);
		fin_page_association();
	}
}

?>
