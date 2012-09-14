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
		$id_don = intval(_request('id'));
		if ($id_don) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$annee = sql_getfetsel("DATE_FORMAT(date_don, '%Y')",'spip_asso_dons', "id_don=$id_don"); // on recupere l'annee correspondante
		} else {
			$annee = intval(_request('annee')); // on recupere l'annee requetee
			$id_don = ''; // ne pas afficher ce disgracieux '0'
		}
		if (!$annee) {
			$annee = date('Y'); // par defaut c'est l'annee courante
			$id_don = ''; // virer l'ID inexistant
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
			'pair' => array( 'dons_financier', $liste_effectifs['argent'], ),
			'prospect' => array('dons_en_nature', $liste_effectifs['colis'], ),
			'impair' => array('autres', sql_countsel('spip_asso_dons', "DATE_FORMAT(date_don, '%Y')='$annee' ")-$liste_effectifs['argent']-$liste_effectifs['colis'] ),
		));
		// STATS sur les donnations de l'annee
		echo association_totauxinfos_stats('donnations', 'dons', array('dons_financier'=>'argent','don_en_nature'=>'valeur',), "DATE_FORMAT(date_don, '%Y')='$annee' ");
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
		filtres_association(array(
			'annee' => array($annee, 'asso_dons', 'don'),
#			'id' => $id_don,
		), 'dons');
		//TABLEAU
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_dons'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_date') .'</th>';
		echo '<th>'. _T('asso:entete_nom') .'</th>';
		echo '<th>'. _T('asso:argent') .'</th>';
		echo '<th>'. _T('asso:colis') .'</th>';
		echo '<th>&nbsp;</th>';
		echo '<th colspan="2" class="actions">' . _T('asso:entete_actions') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$association_imputation = charger_fonction('association_imputation', 'inc');
		$critere1 = $association_imputation('pc_dons', 'a_c');
		if ($critere1)
			$critere1 .= ' AND ';
		$critere2 = $association_imputation('pc_colis', 'a_c');
		if ($critere2)
			$critere2 .= ($critere1?' OR ':' AND ');
		$query = sql_select('DISTINCT a_d.*', 'spip_asso_dons AS a_d LEFT JOIN spip_asso_comptes AS a_c ON a_c.id_journal=a_d.id_don', "$critere2$critere1 DATE_FORMAT(date_don, '%Y')=$annee", '',  'id_don' ) ;
		while ($data = sql_fetch($query)) {
			echo '<tr class="'. (($data['argent'] && !$data['colis'])?'pair':(($data['argent'] && !$data['colis'])?'prospect':'impair')) . (($id_don==$data['id_don'])?' surligne':'') .'" id="'.$data['id_don'].'">';
			echo '<td class="integer">'.$data['id_don'].'</td>';
			echo '<td class="date">'. association_formater_date($data['date_don']) .'</td>';
			echo '<td class="text">'. association_calculer_lien_nomid($data['bienfaiteur'],$data['id_adherent']) .'</td>';
			echo '<td class="decimal">'. association_formater_prix($data['argent'], 'donation cash') .'</td>';
			echo '<td class="text" colspan="'.($data['vu']?2:1).'">'
				.($data['vu'] ? '' :'<i>'._T('asso:valeur').': '.association_formater_prix($data['valeur'], 'donation estimated').'</i><p class="n">')
				.$data['colis'].'</p></td>';
			echo ($data['vu']
				? ('<td class="text">&nbsp;</td>')
			    : ('<td class="text">'. propre($data['contrepartie']) .'</td>')
				);
			echo '<td  class="action">'. association_bouton_suppr('don', "id=$data[id_don]");
			echo '<td class="action">' . association_bouton_edit('don', "id=$data[id_don]");
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>