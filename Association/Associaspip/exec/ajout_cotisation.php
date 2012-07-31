<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/



if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_ajout_cotisation()
{
	$id_auteur = intval(_request('id'));
	$row = sql_fetsel('sexe, nom_famille,prenom,categorie,validite','spip_asso_membres', "id_auteur=$id_auteur");
	if (!autoriser('associer', 'adherents', $id_auteur) OR !$row) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('titre_onglet_membres');
		// info : membre et categorie par defaut
		$categorie = sql_fetsel('*', 'spip_asso_categories', 'id_categorie='. intval($row['categorie']));
		$infos['adherent_libelle_categorie'] = $categorie['libelle'];
		$infos['entete_montant'] = association_prixfr($categorie['cotisation']);
		$infos['adherent_libelle_validite'] = association_datefr($row['validite']);
		echo totauxinfos_intro(htmlspecialchars(association_calculer_nom_membre($row['sexe'], $row['prenom'], $row['nom_famille'])), 'membre', $id_auteur, $infos );
		// datation et raccourcis
		icones_association('', array(
			'voir_adherent' => array('edit-24.gif', 'voir_adherent', "id=$id_auteur"), // item de langue...
		));
		debut_cadre_association('annonce.gif', 'nouvelle_cotisation');
		echo recuperer_fond('prive/editer/ajouter_cotisation', array (
			'id_auteur' => $id_auteur,
			'nom_prenom' => association_calculer_nom_membre($row['sexe'], $row['prenom'], $row['nom_famille']),
			'categorie' => $row['categorie'],
			'validite' => $row['validite'],
		));
		fin_page_association();
	}
}

?>