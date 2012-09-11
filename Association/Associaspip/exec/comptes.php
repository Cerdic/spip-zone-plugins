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

function exec_comptes()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
// initialisations
		$id_exercice = intval(_request('exercice'));
		if(!$id_exercice){ // on recupere l'id du dernier exercice
			$id_exercice = sql_getfetsel('id_exercice','spip_asso_exercices','','','debut DESC');
		}
		$vu = _request('vu');
		if (!is_numeric($vu))
			$vu = '';
		$imputation = _request('imputation');
		if (!$imputation)
			$imputation= '%';
		$id_compte = intval(_request('id_compte', $_GET));
		if (!$id_compte) {
			$id_compte = intval(_request('id'));
		}
		if (!$id_compte) {
				$id_compte = '';
		} else { // quand on a un id compte, on doit selectionner automatiquement l'exercice dans lequel il se trouve
			$date_operation = sql_getfetsel('date', 'spip_asso_comptes', 'id_compte='.$id_compte);
			$id_exercice = sql_getfetsel('id_exercice','spip_asso_exercices', "fin>='$date_operation' AND debut<='$date_operation'", '', 'debut DESC');
		}
		$exercice_data = sql_asso1ligne('exercice', $id_exercice);
// traitements
		$where = 'imputation LIKE '. sql_quote($imputation);
		$where .= (!is_numeric($vu) ? '' : " AND vu=$vu");
		$where .= " AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]'";
		onglets_association('titre_onglet_comptes', 'comptes');
		// INTRO : rappel de l'exercicee affichee
		echo association_totauxinfos_intro($exercice_data['intitule'],'exercice',$id_exercice);
		$journaux = sql_allfetsel('journal, intitule', 'spip_asso_comptes RIGHT JOIN spip_asso_plan ON journal=code', "date>='$exercice_data[debut]' AND date<='$exercice_data[fin]'", "intitule DESC"); // on se permet sql_allfetsel car il s'agit d'une association (mois d'une demie dizaine de comptes) et non d'un etablissement financier (des milliers de comptes clients)
		// TOTAUX : operations de l'exercice par compte financier (indique rapidement les comptes financiers les plus utilises ou les modes de paiement preferes...)
		foreach (array('recette','depense') as $direction) {
			foreach ($journaux as $financier) {
				$nombre_direction = sql_countsel('spip_asso_comptes', "journal='".$financier['journal']."' AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' AND $direction<>0 ");
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
			$liste_types[$classe_css] = array( 'compte_liste_nombre_'.$classe_css, sql_countsel('spip_asso_comptes', "LEFT(imputation,1)='".$GLOBALS['association_metas']["classe_$classe_cpt"]."' AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' "), );
		}
		echo association_totauxinfos_effectifs('compte_entete_imputation', $liste_types);
		// STATS : montants de l'exercice pour l'imputation choisie (toutes si aucune)
		echo association_totauxinfos_stats('mouvements', 'comptes', array('bilan_recettes'=>'recette','bilan_depenses'=>'depense',), $where, 2);
		// TOTAUX : montants de l'exercice pour l'imputation choisie (toutes si aucune)
		$data = sql_fetsel( 'SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses, code, classe',  'spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code', "$where AND classe<>".sql_quote($GLOBALS['association_metas']['classe_banques']). " AND classe<>".sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']), 'code'); // une contribution benevole ne doit pas etre comptabilisee en charge/produit
		echo association_totauxinfos_montants(($imputation=='%' ? _T('asso:tous') : $imputation), $data['somme_recettes'], $data['somme_depenses']);
		// datation et raccourcis
		raccourcis_association(array(), array(
			'encaisse_titre_general' => array('finances-24.png', array('encaisse', "exercice=$id_exercice") ),
			'cpte_resultat_titre_general' => array('finances-24.png', array('compte_resultat', "exercice=$id_exercice") ),
			'cpte_bilan_titre_general' => array('finances-24.png', array('compte_bilan', "exercice=$id_exercice") ),
#			'annexe_titre_general' => array('finances-24.png', array('annexe', "exercice=$id_exercice") ),
			'ajouter_une_operation' => array('ajout-24.png', 'edit_compte'),
		) );
		debut_cadre_association('finances-24.png', 'informations_comptables');
		// FILTRES
		$filtre_imputation = '<select name="imputation" onchange="form.submit()">';
		$filtre_imputation .= '<option value="%" ';
		if ($imputation=='%' || $imputation='') {
			$filtre_imputation .= ' selected="selected"';
		}
		$filtre_imputation .= '>'. _T('asso:entete_tous') .'</option>';
		$sql = sql_select(
			'imputation , code, intitule, classe',
			'spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code',
			"classe<>". sql_quote($GLOBALS['association_metas']['classe_banques']) ." AND active AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' ", // pour l'exercice en cours... ; n'afficher ni les comptes de la classe financiere --ce ne sont pas des imputations-- ni les inactifs
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
		filtres_association(array(
			'exercice' => $id_exercice,
#			'id' => $id_compte,
		), 'comptes', array(
			'imputation' => $filtre_imputation,
			'vu' => $filtre_vu,
		));
		if ($id_compte) { // (re)calculer la pagination en fonction de id_compte
			$all_id_compte = sql_allfetsel('id_compte', 'spip_asso_comptes', $where, '',  'date DESC,id_compte DESC'); // on recupere les id_comptes de la requete sans le critere de limite...
			$index_id_compte = -1;
			reset($all_id_compte);
			while (($index_id_compte<0) && (list($k,$v) = each($all_id_compte))) { // ...et on en tire l'index de l'id_compte recherche parmis tous ceux disponible
				if ($v['id_compte']==$id_compte) $index_id_compte = $k;
			}
			if ($index_id_compte>=0) { // on recalcule le parametre de limite de la requete
				set_request('debut', intval($index_id_compte/_MAX_ITEMS_ASSOCIASPIP)*_MAX_ITEMS_ASSOCIASPIP);
			}
		}
		// TABLEAU
		$table = comptes_while($where, sql_asso1page(), $id_compte);
		if ($table) { // affichage de la liste
			// SOUS-PAGINATION
			$nav = association_selectionner_souspage(array('spip_asso_comptes', $where), 'comptes', "exercice=$id_exercice"."&imputation=$imputation". (is_numeric($vu)?"&vu=$vu":'') );
			// ENTETES
			$table = "<table width='100%' class='asso_tablo' $onload_option id='asso_liste_comptes'>\n"
			. "<thead>\n<tr>"
			. '<th>'. _T('asso:entete_id') .'</th>'
			. '<th>'. _T('asso:entete_date') .'</th>'
			. '<th>'. _T('asso:compte_entete_imputation') .'</th>'
			. '<th>'. _T('asso:compte_entete_justification') .'</th>'
			. '<th>'. _T('asso:entete_montant') .'</th>'
			. '<th>'. _T('asso:compte_entete_financier') .'</th>'
			. '<th colspan="2" class="actions">'. _T('asso:entete_actions') .'</th>'
			. '<th><input title="'._T('asso:selectionner_tout').'" type="checkbox" id="selectionnerTous" onclick="var currentVal = this.checked; var checkboxList = document.getElementsByName(\'valide[]\'); for (var i in checkboxList){checkboxList[i].checked=currentVal;}" /></th>'
			. "</tr>\n</thead><tbody>"
			. $table
			. "</tbody>\n</table>\n"
			. "<table width='100%' class='asso_tablo_filtres'><tr>\n<td align='left'>" . $nav . '</td><td align="right" width="30"><input type="submit" value="'. _T('asso:bouton_valider') . '"  /></td></tr></table>';
			echo generer_form_ecrire('action_comptes', $table);
		} else { // absence d'operation pour l'exercice
			echo '<table width="100%"><tbody><tr><td class="actions erreur">' .( $id_exercice ? _T('asso:exercice_sans_operation') : '<a href="'.generer_url_ecrire('exercices').'">'._T('asso:ajouter_un_exercice').'</a>' ). '</td></tr></tbody></table>';
		}
		fin_page_association();
	}
}

function comptes_while($where, $limit, $id_compte)
{
	$query = sql_select('*', 'spip_asso_comptes', $where,'',  'date DESC,id_compte DESC', $limit);
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
			$onload_option .= 'onLoad="document.getElementById(\'compte'.$id_compte.'\').scrollIntoView(true);"'; // pour voir au chargement l'id_compte recherche
			$class = 'surligne';
		} else {
			$onload_option = '';
		}
		$comptes .= "<tr id='compte".$data['id_compte']."' class='$class'>"
		. '<td class="integer">'.$data['id_compte'].'</td>'
		. '<td class="date">'. association_formater_date($data['date']) .'</td>'
		. '<td class="text">'. $data['imputation'].'</td>'
		. '<td class="text">&nbsp;'. propre($data['justification']) .'</td>'
		. '<td class="decimal">'. association_formater_prix($data['recette']-$data['depense']) .'</td>'
		. '<td class="text">&nbsp;'.$data['journal'].'</td>';
		if ( $data['vu'] ) { // pas d'action sur les operations validees !
			$comptes .= '<td class="action" colspan="2">'. association_formater_puce('', 'verte', '', $onload_option) .' </td>'; // edition+suppresion
			$comptes .= '<td class="action"><input disabled="disabled" type="checkbox" /></td>'; // validation
		} else {  // operation non validee (donc validable et effacable...
			if ( $data['id_journal'] && $data['imputation']!=$GLOBALS['association_metas']['pc_cotisations'] ) { // pas d'edition/suppression des operations gerees par un autre module (exepte les cotisations) ...par souci de coherence avec les donnees dupliquees dans d'autres tables...
				$comptes .= '<td class="action" colspan="2">'. association_formater_puce('', 'rouge', '', $onload_option) .'</td>'; // edition+suppression
			} else { // operation geree par ce module (donc supprimable ici)
				if (substr($data['imputation'],0,1)==$GLOBALS['association_metas']['classe_banques']) { // pas d'edition des virements internes (souci de coherence car il faut modifier deux operations concordament : ToDo...)
					$comptes .= '<td class="action">&nbsp;</td>'; // edition
				} else { // le reste est editable
					$comptes .= '<td class="action">'. association_bouton_faire('mettre_a_jour', 'edit-12.gif', 'edit_compte', 'id='.$data['id_compte']) . '</td>'; // edition
				}
				$comptes .= association_bouton_supprimer('comptes', 'id='.$data['id_compte'], 'td'); // suppression
			}
			$comptes .= '<td class="action"><input name="valide[]" type="checkbox" value="'.$data['id_compte']. '" /></td>'; // validation
		}
		$comptes .= '</tr>';
	}
	return $comptes;
}

?>