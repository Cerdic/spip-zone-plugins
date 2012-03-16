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

function exec_ajout_cotisation()
{
	$id_auteur = intval(_request('id'));
	$row = sql_fetsel('sexe, nom_famille,prenom,categorie,validite','spip_asso_membres', "id_auteur=$id_auteur");
	if (!autoriser('associer', 'adherents', $id_auteur) OR !$row) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		association_onglets(_T('asso:titre_onglet_membres'));
		// info : membre et categorie par defaut
		$categorie = sql_fetsel('*', 'spip_asso_categories', 'id_categorie='. intval($row['categorie']));
		$infos['adherent_libelle_categorie'] = $categorie['libelle'];
		$infos['entete_montant'] = association_prixfr($categorie['cotisation']);
		$infos['adherent_libelle_validite'] = association_datefr($row['validite']);
		echo totauxinfos_intro(htmlspecialchars(association_calculer_nom_membre($row['sexe'], $row['prenom'], $row['nom_famille'])), 'membre', $id_auteur, $infos );
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone('voir_adherent',  generer_url_ecrire('voir_adherent', 'id='.$id_auteur), 'edit.gif' ); // item de langue...
		$res .= association_icone('bouton_retour', str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']), 'retour-24.png');
//		echo bloc_des_raccourcis($res);
		echo association_retour();
		debut_cadre_association('annonce.gif', 'nouvelle_cotisation');
		echo recuperer_fond('prive/editer/editer_cotisations', array (
			'id_auteur' => $id_auteur,
			'nom_prenom' => association_calculer_nom_membre($row['sexe'], $row['prenom'], $row['nom_famille']),
			'categorie' => $row['categorie'],
			'validite' => $row['validite'],
		));
		fin_page_association();
	}
}

?>