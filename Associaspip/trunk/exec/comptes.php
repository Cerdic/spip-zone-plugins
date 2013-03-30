<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 Emmanuel Saint-James
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_comptes() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
// initialisations
		$vu = _request('vu');
		if (!is_numeric($vu))
			$vu = '';
		$imputation = _request('imputation');
		if (!$imputation)
			$imputation= '%';
		$id_compte = association_passeparam_id('compte');
		list($id_periode, $critere_periode) = association_passeparam_periode('operation', 'asso_comptes', $id_compte);
// traitements
		$where = 'imputation LIKE '. sql_quote($imputation);
		$where .= (!is_numeric($vu) ? '' : " AND vu=$vu");
		$where .= " AND $critere_periode";
		echo association_navigation_onglets('titre_onglet_comptes', 'comptes');
		$journaux = sql_allfetsel('journal, intitule', 'spip_asso_comptes RIGHT JOIN spip_asso_plan ON journal=code', $critere_periode, "intitule DESC"); // on se permet sql_allfetsel car il s'agit d'une association (mois d'une demie dizaine de comptes) et non d'un etablissement financier (des milliers de comptes clients)
		// TOTAUX : operations de l'exercice par compte financier (indique rapidement les comptes financiers les plus utilises ou les modes de paiement preferes...)
		foreach (array('recette','depense') as $direction) {
			foreach ($journaux as $financier) {
				$nombre_direction = sql_countsel('spip_asso_comptes', "journal=".sql_quote($financier['journal'])." AND $critere_periode AND $direction<>0 ");
				if ($nombre_direction) { // on ne s'embarasse pas avec ceux a zero
					$direction_decomptes[$financier['journal']] = array( $financier['intitule'], $nombre_direction, );
				}
			}
			if (count($direction_libelles))
				echo association_totauxinfos_effectifs(_T('asso:compte_entete_financier') .': '. _T('asso:'.$direction.'s'), $direction_decomptes); // ToDo: tri par ordre decroissant (sorte de "top")
		}
		// TOTAUX : operations de l'exercice par type d'operation
		$classes = array('pair'=>'produits', 'impair'=>'charges', 'cv'=>'contributions_volontaires', 'vi'=>'banques');
		$liste_types = array();
		foreach ($classes as $classe_css=>$classe_cpt) {
			$liste_types[$classe_css] = array( 'compte_liste_nombre_'.$classe_css, sql_countsel('spip_asso_comptes', "LEFT(imputation,1)=".sql_quote($GLOBALS['association_metas']["classe_$classe_cpt"])." AND $critere_periode "), );
		}
		echo association_totauxinfos_effectifs(_T('asso:bouton_radio_type_operation_titre'), $liste_types);
		// STATS : montants de l'exercice pour l'imputation choisie (toutes si aucune)
		echo association_totauxinfos_stats('mouvements', 'comptes', array('bilan_recettes'=>'recette','bilan_depenses'=>'depense',), $where, 2);
		// TOTAUX : montants de l'exercice pour l'imputation choisie (toutes si aucune)
		$data = sql_fetsel( 'SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses, code, classe',  'spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code', "$where AND classe<>".sql_quote($GLOBALS['association_metas']['classe_banques']). " AND classe<>".sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']), 'code'); // une contribution benevole ne doit pas etre comptabilisee en charge/produit
		echo association_totauxinfos_montants(($imputation=='%' ? _T('asso:tous') : $imputation), $data['somme_recettes'], $data['somme_depenses']);
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			'encaisse_titre_general' => array('finances-24.png', array('encaisse', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode") ),
			'cpte_resultat_titre_general' => array('finances-24.png', array('compte_resultat', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode") ),
			'cpte_bilan_titre_general' => array('finances-24.png', array('compte_bilan', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode") ),
#			'annexe_titre_general' => array('finances-24.png', array('annexe', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode") ),
			'ajouter_une_operation' => array('ajout-24.png', array('edit_compte')),
		), 10);
		debut_cadre_association('finances-24.png', 'informations_comptables');
		// FILTRES
		$filtre_imputation = '<select name="imputation" onchange="form.submit()">';
		$filtre_imputation .= '<option value="%" ';
		$filtre_imputation .= (($imputation=='%' || $imputation='')?' selected="selected"':'');
		$filtre_imputation .= '>'. _T('asso:entete_tous') .'</option>';
		$sql = sql_select(
			'imputation , code, intitule, classe',
			'spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code',
			"classe<>". sql_quote($GLOBALS['association_metas']['classe_banques']) ." AND active AND $critere_periode ", // pour l'exercice en cours... ; n'afficher ni les comptes de la classe financiere --ce ne sont pas des imputations-- ni les inactifs
			'code', 'code ASC');
		while ($plan = sql_fetch($sql)) { // Remplir le select uniquement avec les comptes utilises
			$filtre_imputation .= '<option value="'.$plan['code'].'"';
			$filtre_imputation .= ($imputation==$plan['code']?' selected="selected"':'');
			$filtre_imputation .= '>'.$plan['code'].' - '.$plan['intitule'].'</option>';
		}
		$filtre_imputation .= '</select>';
		$filtre_vu = '<select name="vu" onchange="form.submit()">';
		$filtre_vu .= '<option value="" '. ($vu==''?'':' selected="selected"') .'>'. _T('asso:cpte_op_vu_tous') .'</option>';
		$filtre_vu .= '<option value="0" '. ($vu=='0'?' selected="selected"':'') .'>'. _T('asso:cpte_op_vu_non') .'</option>';
		$filtre_vu .= '<option value="1" '. ($vu=='1'?' selected="selected"':'') .'>'. _T('asso:cpte_op_vu_oui') .'</option>';
		$filtre_vu .= '</select>';
		echo association_bloc_filtres(array(
			'periode' => array($id_periode, 'asso_comptes', 'operation'),
#			'id' => $id_compte,
		), 'comptes', array(
			'imputation' => $filtre_imputation,
			'vu' => $filtre_vu,
		));
		if ($id_compte) { // (re)calculer la pagination en fonction de id_compte
			$all_id_compte = sql_allfetsel('id_compte', 'spip_asso_comptes', $where, '',  'date_operation DESC,id_compte DESC'); // on recupere les id_comptes de la requete sans le critere de limite...
			$index_id_compte = -1;
			reset($all_id_compte);
			while (($index_id_compte<0) && (list($k,$v) = each($all_id_compte))) { // ...et on en tire l'index de l'id_compte recherche parmis tous ceux disponible
				if ($v['id_compte']==$id_compte) $index_id_compte = $k;
			}
			if ($index_id_compte>=0) { // on recalcule le parametre de limite de la requete
				set_request('debut', intval($index_id_compte/_ASSOCIASPIP_LIMITE_SOUSPAGE)*_ASSOCIASPIP_LIMITE_SOUSPAGE);
			}
		}
		// TABLEAU
		$limit = intval(_request('debut')) . "," . _ASSOCIASPIP_LIMITE_SOUSPAGE;
		$table = comptes_while($where, $limit, $id_compte);
		if ($table) { // affichage de la liste
			// SOUS-PAGINATION
			$nav = association_selectionner_souspage(array('spip_asso_comptes', $where), 'comptes', ($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode".($imputation?"&imputation=$imputation":''). (is_numeric($vu)?"&vu=$vu":''), FALSE);
			// ENTETES
			$table = "<table width='100%' class='asso_tablo' $onload_option id='asso_liste_comptes'>\n"
			. '<tr class="row_first">'
			. '<th>'. _T('asso:entete_id') .'</th>'
			. '<th>'. _T('asso:entete_date') .'</th>'
			. '<th>'. _T('asso:compte_entete_imputation') .'</th>'
			. '<th>'. _T('asso:compte_entete_justification') .'</th>'
			. '<th>'. _T('asso:entete_montant') .'</th>'
			. '<th>'. _T('asso:compte_entete_financier') .'</th>'
			. '<th colspan="2" class="actions">'. _T('asso:entete_actions') .'</th>'
			. '<th><input title="'._T('asso:selectionner_tout').'" type="checkbox" id="selectionnerTous" onclick="var currentVal = this.checked; var checkboxList = document.getElementsByName(\'valide[]\'); for (var i in checkboxList) {checkboxList[i].checked=currentVal;}" /></th>'
			. '</tr>'
			. $table
			. "\n</table>\n"
			. "<table width='100%' class='asso_tablo_filtres'><tr>\n" . $nav . '<td align="right"><input type="submit" value="'. _T('asso:bouton_valider') . '"  /></td></tr></table>';
			echo generer_form_ecrire('action_comptes', $table);
		} else { // absence d'operation pour l'exercice
			echo '<table width="100%"><tr><td class="actions erreur">' .( $id_periode ? _T('asso:exercice_sans_operation') : '<a href="'.generer_url_ecrire('exercices').'">'._T('asso:ajouter_un_exercice').'</a>' ). '</td></tr></table>';
		}
		fin_page_association();
	}
}

function comptes_while($where, $limit, $id_compte) {
	$query = sql_select('*', 'spip_asso_comptes', $where,'',  'date_operation DESC,id_compte DESC', $limit);
	$comptes = '';
	while ($data = sql_fetch($query)) {
		if ($data['depense']>0) { // depense
			$class = 'impair';
		} else { // recette
			$class = 'pair';
		}
		if ($data['imputation']==$GLOBALS['association_metas']['pc_intravirements']) { // virement interne
			$class = 'vi';
		}
		if (substr($data['imputation'],0,1)==$GLOBALS['association_metas']['classe_contributions_volontaires']) { // contribution volontaire
			$class = 'cv';
		}
		if($id_compte==$data['id_compte']) { // operation recherchee
			$onload_option .= 'onLoad="document.getElementById(\'compte'.$id_compte.'\').scrollIntoView(TRUE);"'; // pour voir au chargement l'id_compte recherche
			$class = 'surligne';
		} else {
			$onload_option = '';
		}
		$comptes .= "<tr id='compte".$data['id_compte']."' class='$class'>"
		. '<td class="integer">'.$data['id_compte'].'</td>'
		. '<td class="date">'. association_formater_date($data['date_operation']) .'</td>'
		. '<td class="text">'. $data['imputation'].'</td>'
		. '<td class="text">&nbsp;'. propre($data['justification']) .'</td>'
		. '<td class="decimal">'. association_formater_prix($data['recette']-$data['depense']) .'</td>'
		. '<td class="text">&nbsp;'.$data['journal'].'</td>';
		if ( $data['vu'] ) { // pas d'action sur les operations validees !
			$comptes .= '<td class="action" colspan="2">'. association_formater_puce($data['id_journal'], 'verte', '', $onload_option) .' </td>'; // edition+suppresion
			$comptes .= association_bouton_coch(''); // validation
		} else {  // operation non validee (donc validable et effacable...
			if ( $data['id_journal'] && $data['imputation']!=$GLOBALS['association_metas']['pc_cotisations'] ) { // pas d'edition/suppression des operations gerees par un autre module (exepte les cotisations) ...par souci de coherence avec les donnees dupliquees dans d'autres tables...
				$comptes .= '<td class="action" colspan="2">'. association_formater_puce($data['id_journal'], 'rouge', '', $onload_option) .'</td>'; // edition+suppression
			} else { // operation geree par ce module
				if (substr($data['imputation'],0,1)==$GLOBALS['association_metas']['classe_banques']) { // pas d'edition des virements internes (souci de coherence car il faut modifier deux operations concordament : ToDo...)
					$comptes .= '<td class="action">&nbsp;</td>'; // edition
				} else { // le reste est editable
					$comptes .= association_bouton_edit('compte', 'id='.$data['id_compte']); // edition
				}
				$comptes .= association_bouton_suppr('comptes', 'id='.$data['id_compte']); // suppression
			}
			$comptes .= association_bouton_coch('valide', $data['id_compte']); // validation
		}
		$comptes .= '</tr>';
	}
	return $comptes;
}

?>