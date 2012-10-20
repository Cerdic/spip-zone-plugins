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
	if (!autoriser('editer_profil', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('categories_de_cotisations', 'association');
		// notice
		echo '';
		// quelques stats sur les categories
		echo association_totauxinfos_stats('tous', 'categories', array('entete_duree'=>'duree', 'entete_montant'=>'prix_cotisation') );
		// datation et raccourcis
		raccourcis_association('association', array(
			'ajouter_une_categorie_de_cotisation' => array('cotisation.png', 'edit_categorie'),
		));
		debut_cadre_association('cotisation.png','toutes_categories_de_cotisations');
		echo association_bloc_listehtml(
			array('*', 'spip_asso_categories', '', 'id_categorie'), // requete
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
		fin_page_association();
	}
}

?>