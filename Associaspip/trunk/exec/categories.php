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

function exec_categories() {
	sinon_interdire_acces(autoriser('editer_profil', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('categories_de_cotisations', 'association');
/// AFFICHAGES_LATERAUX : STATS : quelques stats sur les categories
	echo association_tablinfos_stats('tous', 'categories', array('entete_duree'=>'duree', 'entete_montant'=>'prix_cotisation') );
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('association_infos_contacts', 'assoc_qui.png', array('association'), array('voir_profil', 'association') ),
		array('ajouter_une_categorie_de_cotisation', 'cotisation.png', array('edit_categorie'), array('editer_profil', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('cotisation.png', 'toutes_categories_de_cotisations');
/// AFFICHAGES_CENTRAUX : TABLEAU
	echo association_bloc_listehtml2('asso_categories',
		sql_select('*', 'spip_asso_categories', '', 'id_categorie'),
		array(
			'id_categorie' => array('asso:entete_id', 'entier'),
			'valeur' => array('asso:entete_code', 'code', 'x-spip_asso_categories'),
			'libelle' => array('asso:libelle_intitule', 'texte', '', 'n'),
			'duree' => array('asso:entete_duree', 'duree', 'M'),
			'prix_cotisation' => array('asso:entete_montant', 'prix', 'subscription'),
			'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
		), // entetes et formats des donnees
		array(
			array('suppr', 'categorie', 'id=$$' ),
			array('edit', 'categorie', 'id=$$' ),
		), // boutons d'action
		'id_categorie', // champ portant la cle des lignes et des boutons
		array('hproduct')
	);
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>