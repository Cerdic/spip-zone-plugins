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
	sinon_interdire_acces(autoriser('voir_groupes', 'association', 100)); // l'id groupe passe en parametre est a 100 car ce sont les groupes definis par l'utilisateur et non ceux des autorisation qu'on liste dans cette page.
	include_spip('association_modules');
/// INITIALISATIONS : rien a faire
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('gestion_groupes', 'adherents');
/// AFFICHAGES_LATERAUX : INTRO : notice
	echo _T('asso:aide_groupes');
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('adherent_titre_liste_actifs', 'grille-24.png', array('adherents'), array('voir_membres', 'association') ),
		array('ajouter_un_groupe', 'annonce.gif', array('edit_groupe'), array('editer_groupes', 'association', 100) )
	), 11);
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('annonce.gif', 'tous_les_groupes');
/// AFFICHAGES_CENTRAUX : TABLEAU
	echo association_bloc_listehtml2('asso_groupes',
		sql_select('*', 'spip_asso_groupes', 'id_groupe>=100','',  'nom'),
		array(
#			'id_groupe' => array('asso:entete_id', 'entier'),
			'nom' => array('asso:entete_nom', 'texte'),
			'affichage' => array('asso:ordre_affichage_groupe', 'entier'),
			'commentaire' => array('asso:entete_commentaire', 'texte'),
		), // entetes et formats des donnees
		array(
			array('suppr', 'groupe', 'id=$$'),
			array('edit', 'groupe', 'id=$$'),
			array('list', 'membres_groupe', 'id=$$'),
		), // boutons d'action
		'id_groupe' // champ portant la cle des lignes et des boutons
	);
/// AFFICHAGES_CENTRAUX : PAGINATION
	echo association_form_souspage(array('spip_asso_groupes', 'id_groupe>100'), 'groupes' );
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>