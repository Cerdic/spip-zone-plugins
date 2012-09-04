<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_categories()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('categories_de_cotisations');
		// notice
		echo '';
		// quelques stats sur les categories
		echo association_totauxinfos_stats('tous', 'categories', array('entete_duree'=>'duree', 'entete_montant'=>'cotisation') );
		// datation et raccourcis
		raccourcis_association('association', array(
			'ajouter_une_categorie_de_cotisation' => array('calculatrice.gif', 'edit_categorie'),
		));
		debut_cadre_association('calculatrice.gif','toutes_categories_de_cotisations');
		echo association_bloc_listehtml(
			array('asso:entete_id', 'asso:entete_code', 'asso:libelle_intitule', 'asso:entete_duree', 'asso:entete_montant', 'asso:entete_commentaire', ), // entetes
			sql_select('*', 'spip_asso_categories', '', 'id_categorie'), // ressource requete
			array(
				'id_categorie' => array('entier'),
				'valeur' => array('texte'),
				'libelle' => array('texte'),
				'duree' => array('duree', 'dtstart'),
				'cotisation' => array('prix'),
				'commentaire' => array('texte', 'propre'),
			), // formats des donnees
			array(
				array('categorie', 'exercice', 'id=$$', 'td'),
				array('categorie', 'exercice', 'id=$$', 'td'),
			), // boutons d'action
			array('key'=>'id_categorie') // extra
		);
		fin_page_association();
	}
}

?>