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
include_spip('inc/association_comptabilite');

function exec_dons()
{
	if (!autoriser('associer', 'dons')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_don = association_passeparam_id('don');
		if ($id_don) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$annee = sql_getfetsel("DATE_FORMAT(date_don, '%Y')",'spip_asso_dons', "id_don=$id_don"); // on recupere l'annee correspondante
		} else { // on peut prendre en compte les filtres ; on recupere les parametres :
			$annee = association_passeparam_annee();
 			$type = _request('type'); // type de don
		}
		onglets_association('titre_onglet_dons', 'dons');
		// INTRO : nom du module et annee affichee
		echo association_totauxinfos_intro('','dons',$annee);
		// TOTAUX : nombre de dons selon leur nature
		$liste_effectifs = array(
			'argent' => sql_countsel('spip_asso_dons', "argent<>0 AND colis='' AND  DATE_FORMAT(date_don, '%Y')='$annee' "),
			'colis' => sql_countsel('spip_asso_dons', "argent=0 AND colis<>'' AND  DATE_FORMAT(date_don, '%Y')='$annee' ")
		);
		echo association_totauxinfos_effectifs('dons', array(
			'pair' => array( 'dons_en_argent', $liste_effectifs['argent'], ),
			'prospect' => array('dons_en_nature', $liste_effectifs['colis'], ),
			'impair' => array('dons_mixtes', sql_countsel('spip_asso_dons', "DATE_FORMAT(date_don, '%Y')='$annee' ")-$liste_effectifs['argent']-$liste_effectifs['colis'] ),
		));
		// STATS sur les donnations de l'annee
		echo association_totauxinfos_stats('donnations', 'dons', array('dons_en_argent'=>'argent','dons_en_nature'=>'valeur',), "DATE_FORMAT(date_don, '%Y')='$annee' ");
		// TOTAUX : montants des dons et remboursements financiers
		$dons_financiers = sql_getfetsel('SUM(argent) AS somme_recettes', 'spip_asso_dons', "argent AND DATE_FORMAT(date_don, '%Y')=$annee" );
		$remboursements = sql_getfetsel('SUM(argent) AS somme_reversees', 'spip_asso_dons', "argent AND contrepartie AND DATE_FORMAT(date_don, '%Y')=$annee" );
		echo association_totauxinfos_montants($annee, $dons_financiers, $remboursements);
		// datation et raccourcis
		raccourcis_association(array(), array(
			'ajouter_un_don' => array('ajout-24.png', 'edit_don'),
		));
		debut_cadre_association('dons-24.gif', 'tous_les_dons');
		// FILTRES
		$filtre_typedon = '<select name="type" onchange="form.submit()">';
		$filtre_typedon .= '<option value="">' ._T('asso:entete_tous') .'</option>';
		$filtre_typedon .= '<option value="argent"'. ($type=='argent'?' selected="selected"':'') .'>'. _T('asso:dons_en_argent') .'</option>';
		$filtre_typedon .= '<option value="colis"'. ($type=='colis'?' selected="selected"':'') .'>'. _T('asso:dons_en_nature') .'</option>';
//		$filtre_typedon .= '<option value="argent AND colis"'. (($type=='argent AND colis' OR $type=='colis AND argent')?' selected="selected"':'') .'>'. _T('asso:dons_mixtes') .'</option>';
		$filtre_typedon .= '</select>';
		filtres_association(array(
			'annee' => array($annee, 'asso_dons', 'don'),
#			'id' => $id_don,
		), 'dons', array(
			'type' => $filtre_typedon,
		));
		$critere_type = $type?"$type AND ":'';
		// TABLEAU
		echo association_bloc_listehtml(
			array("*, CASE WHEN argent<>0 AND colis='' THEN 'argent' WHEN argent=0 AND colis<>''  THEN 'colis' ELSE 'mixte' END AS type_don ", 'spip_asso_dons', "$critere_type DATE_FORMAT(date_don, '%Y')=$annee", '', 'date_don DESC'), // requete
			array(
				'id_don' => array('asso:entete_id', 'entier'),
				'date_don' => array('asso:entete_date', 'date', ''),
				'id_adherent' => array('asso:entete_nom', 'idnom', array('spip_asso_dons', 'bienfaiteur', 'id_adherent'), 'membre'),
				'argent' => array('asso:argent', 'prix', 'donation cash'),
				'colis' => array('asso:colis', 'texte', 'propre'), // voir s'il est possible de mettre la valeur au survol
//				'valeur' => array('asso:valeur', 'prix', 'donation estimated'),
//				'contrepartiet' => array('asso:argent', 'texte', 'propre'),
//				'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
			), // entetes et formats des donnees
			array(
				array('suppr', 'don', 'id=$$'),
				array('edit', 'don', 'id=$$'),
			), // boutons d'action
			'id_don', // champ portant la cle des lignes et des boutons
			array('argent'=>'pair', 'colis'=>'prospect', 'mixte'=>'impair'), 'type_don', $id_don
		);
		fin_page_association();
	}
}

?>