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

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_comptes() {
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
// initialisations
		$exercice = intval(_request('exercice'));
		if(!$exercice){
			/* on recupere l'id du dernier exercice */
			$exercice = sql_getfetsel('id_exercice','spip_asso_exercices','','','debut DESC');
			if(!$exercice)
				$exercice = 0;
		}
		$vu = _request('vu');
		if (!is_numeric($vu))
			$vu = '';
		$imputation = _request('imputation');
		if (!$imputation)
			$imputation= '%';
		$max_par_page = intval(_request('max'));
		if (!$max_par_page)
			$max_par_page = 30;
		$id_compte = intval(_request('id_compte', $_GET));
		if (!$id_compte)
			$id_compte = '';
		$debut = intval(_request('debut'));
		$exercice_data = sql_asso1ligne('exercice', $exercice);
// traitements
		$where = 'imputation LIKE '. sql_quote($imputation);
		$where .= (!is_numeric($vu) ? '' : " AND vu=$vu");
		$where .= " AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]'";
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_comptes'));
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		// INTRO : nom du module et annee affichee
		echo totauxinfos_intro($exercice_data['intitule'],'exercice',$exercice);
		$journaux = sql_allfetsel('journal, intitule', 'spip_asso_comptes RIGHT JOIN spip_asso_plan ON journal=code',"classe='".$GLOBALS['association_metas']['classe_banques']."' AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]'", "intitule DESC"); // on se permet sql_allfetsel car il s'agit d'une association (mois d'une demie dizaine de comptes) et non d'un etablissement financier (des milliers de comptes clients)
/* bof *
		// STATS recettes et depenses par comptes financiers (indique rapidement les comptes financiers avec les mouvements les plus importants --en montant !)
		foreach ($journaux as $financier) {
			echo totauxinfos_stats($financier['intitule'], 'comptes', array('bilan_recettes'=>'recette','bilan_depenses'=>'depense',), "journal='".$financier['journal']."' AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]'");
		}
* fob */
		// TOTAUX : operations de l'exercice par compte financier (indique rapidement les comptes financiers les plus utilises ou les modes de paiement preferes...)
		foreach (array('recette','depense') as $direction) {
			foreach ($journaux as $financier) {
				$nombre_direction = sql_countsel('spip_asso_comptes', "journal='".$financier['journal']."' AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' AND $direction<>0 ");
				if ($nombre_direction) { // on ne s'embarasse pas avec ceux a zero
					$direction_libelles[$financier['journal']] = $financier['intitule'];
					$direction_effectifs[$financier['journal']] = $nombre_direction;
				}
			}
			echo totauxinfos_effectifs(_T('asso:compte_entete_financier') .': '. _T('asso:'.$direction.'s'), $direction_libelles, $direction_effectifs); // ToDo: tri par ordre decroissant (sorte de "top")
		}
		// TOTAUX : operations de l'exercice par type d'operation
		$classes = array('pair'=>'produits', 'impair'=>'charges', 'cv'=>'contributions_volontaires', 'vi'=>'banques');
		$liste_libelles = $liste_effectifs = array();
		foreach ($classes as $classe_css=>$classe_cpt) {
			$liste_effectifs[$classe_css] = sql_countsel('spip_asso_comptes', "LEFT(imputation,1)='".$GLOBALS['association_metas']["classe_$classe_cpt"]."' AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' ");
			$liste_libelles[$classe_css] = 'compte_liste_nombre_'.$classe_css;
		}
		echo totauxinfos_effectifs('comptes', $liste_libelles, $liste_effectifs);
		// STATS : montants de l'exercice pour l'imputation choisie (toutes si aucune)
		echo totauxinfos_stats('mouvements', 'comptes', array('bilan_recettes'=>'recette','bilan_depenses'=>'depense',), $where, 2);
		// TOTAUX : montants de l'exercice pour l'imputation choisie (toutes si aucune)
		$data = sql_fetsel( 'SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses, code, classe',  'spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code', "$where AND classe<>".sql_quote($GLOBALS['association_metas']['classe_banques']). " AND classe<>".sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']) ); // une contribution benevole ne doit pas etre comptabilisee en charge/produit
		echo totauxinfos_montants(($imputation=='%' ? _T('asso:tous') : $imputation), $data['somme_recettes'], $data['somme_depenses']);
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = '<p><b>'.$exercice_data['intitule'].'</b><p>'
		. association_icone(_T('asso:cpte_resultat_titre_general'),  generer_url_ecrire('compte_resultat', "exercice=$exercice"), 'finances.jpg')
		. association_icone(_T('asso:bilan'), generer_url_ecrire('bilan', "exercice=$exercice"), 'finances.jpg')
		. association_icone(_T('asso:annexe_titre_general'), generer_url_ecrire('annexe', "exercice=$exercice"), 'finances.jpg')
		. association_icone(_T('asso:ajouter_une_operation'),  generer_url_ecrire('edit_compte'), 'ajout_don.png');
		echo bloc_des_raccourcis($res);
		echo debut_droite('',true);
		debut_cadre_relief('', false, '',  _T('asso:informations_comptables'));
		echo "\n<table width='100%'>";
		echo '<tr><td>';
		echo '<form method="post" action="'.generer_url_ecrire('comptes',"imputation=$imputation").'"><div>';
		echo '<select name ="exercice" class="fondl" onchange="form.submit()">';
		echo '<option value="0" ';
		if (!$exercice) {
			echo ' selected="selected"';
		}
		echo '>Choix Exercice ?</option>';
		$sql = sql_select('id_exercice, intitule', 'spip_asso_exercices','', "intitule DESC");
		while ($val = sql_fetch($sql)) {
			echo '<option value="'.$val['id_exercice'].'" ';
			if ($exercice==$val['id_exercice']) { echo ' selected="selected"'; }
			echo '>'.$val['intitule'].'</option>';
		}
		echo '</select><noscript><input type="submit" value="'._T('lister').'" /></noscript></div></form></td>';
		echo '<td>';
		echo '<form method="post" action="'.generer_url_ecrire('comptes', "exercice=$exercice").'"><div>';
		echo '<select name ="imputation" class="fondl" onchange="form.submit()">';
		echo '<option value="%" ';
		if ($imputation=="%") { echo ' selected="selected"'; }
		echo '>Tous</option>';
		/* Remplir le select uniquement avec les comptes utilises */
		$sql = sql_select(
			'imputation , code, intitule, classe',
			'spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code',
			/* n'afficher ni les comptes de la classe financiere --ce ne sont pas des imputations-- ni les inactifs */
			"classe<>'".$GLOBALS['association_metas']['classe_banques']."' AND active AND date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' ",
			'code', 'code ASC');
		while ($plan = sql_fetch($sql)) {
			echo '<option value="'.$plan['code'].'" ';
			if ($imputation==$plan['code']) { echo ' selected="selected"'; }
			echo '>'.$plan['code'].' - '.$plan['intitule'].'</option>';
		}
		echo '</select><noscript><input type="submit" value="'._T('filtrer').'" /></noscript></div></form></td>';
		echo '</tr></table>';
		/* (re)calculer la pagination en fonction de id_compte */
		if ($id_compte) {
			/* on recupere les id_comptes de la requete sans le critere de limite et on en tire l'index de l'id_compte recherche parmis tous ceux disponible */
			$all_id_compte = sql_allfetsel('id_compte', 'spip_asso_comptes', $where, '',  'date DESC,id_compte DESC');
			$index_id_compte = -1;
			reset($all_id_compte);
			while (($index_id_compte<0) && (list($k,$v) = each($all_id_compte))) {
				if ($v['id_compte']==$id_compte) $index_id_compte = $k;
			}
			/* on recalcule le parametre de limite de la requete */
			if ($index_id_compte>=0) {
				$debut = intval($index_id_compte/$max_par_page)*$max_par_page;
			}
		}
		// TABLEAU
		$table = comptes_while($where, "$debut,$max_par_page", $id_compte);
		if ($table) {
			//SOUS-PAGINATION
			$nombre_selection = sql_countsel('spip_asso_comptes', $where);
			$pages = intval($nombre_selection/$max_par_page)+1;
			$args = 'exercice='.$exercice.'&imputation='.$imputation. (is_numeric($vu) ? "&vu=$vu" : '');
			$nav = '';
			if ($pages!=1)
				for ($i=0; $i<$pages; $i++) {
					$position = $i*$max_par_page;
					if ($position==$debut) {
						$nav .= '<strong>'.$position.' </strong>';
					} else {
						$h = generer_url_ecrire('comptes',$args.'&debut='.$position);
						$nav .= "<a href='$h'>$position</a>\n";
				}
			}
			//
			$table = "<table width='100%' class='asso_tablo' id='asso_tablo_comptes'>\n"
			. "<thead>\n<tr>"
			. '<th>'. _T('asso:entete_id') .'</th>'
			. '<th>'. _T('asso:entete_date') .'</th>'
			. '<th>'. _T('asso:compte_entete_imputation') .'</th>'
			. '<th>'. _T('asso:compte_entete_justification') .'</th>'
			. '<th>'. _T('asso:entete_montant') .'</th>'
			. '<th>'. _T('asso:compte_entete_financier') .'</th>'
			. '<th colspan="3" class="actions">'. _T('asso:entete_action') .'</th>'
			. "</tr>\n</thead><tbody>"
			. $table
			. "</tbody>\n</table>\n"
			. "<table width='100%'><tr>\n<td>" . $nav . '</td><td style="text-align:right;"><input type="submit" value="'. _T('asso:valider') . '" class="fondo" /></td></tr></table>';
			echo generer_form_ecrire('action_comptes', $table);
		} else {
			echo '<table width="100%"><tbody><tr><td class="actions erreur">' .( $exercice ? _T('asso:aucune_operation') : '<a href="'.generer_url_ecrire('exercices').'">'._T('asso:definir_exercice').'</a>' ). '</td></tr></tbody></table>';
		}
		fin_cadre_relief();
		echo fin_page_association();
	}
}

function comptes_while($where, $limit, $id_compte)
{
	$query = sql_select('*', 'spip_asso_comptes', $where,'',  'date DESC,id_compte DESC', $limit);
	$comptes = '';
	while ($data = sql_fetch($query)) {
		if ($data['depense']>0) {
			$class = 'impair';
		} else {
			$class = 'pair';
		}
		if ($data['imputation']==$GLOBALS['association_metas']['pc_intravirements']) {
			$class = 'vi';
		} // virement interne
		if (substr($data['imputation'],0,1)==$GLOBALS['association_metas']['classe_contributions_volontaires']) {
			$class = 'cv';
		}
		$id = $data['id_compte'];
		/* pour voir au chargement l'id_compte recherche */
		if($id_compte==$id) {
			$onload_option .= 'onLoad="document.getElementById(\'id_compte'.$id_compte.'\').scrollIntoView(true);"';
		} else {
			$onload_option = '';
		}
		$comptes .= "\n<tr id='id_compte$id' class='$class'>"
		. '<td class="integer">'.$id.'</td>'
		. '<td class="date">'. association_datefr($data['date']) .'</td>'
		. '<td class="text">'. $data['imputation'].'</td>'
		. '<td class="text">&nbsp;'. propre($data['justification']) .'</td>'
		. '<td class="decimal">'. association_nbrefr($data['recette']-$data['depense']) .'</td>'
		. '<td class="text">&nbsp;'.$data['journal'].'</td>'
		. ($data['vu']
			? ('<td class="actions" colspan="3"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-verte.gif" '.$onload_option.' /></td>')
			/* si c'est un virement interne : imputation du type 58xx ne pas permettre la modification !!!
			 * TODO : coder la modification d'un virement interne c'est a dire la modification de 2 operations comptables
			 */
		   : (
		       ((substr($data['imputation'],0,1)==$GLOBALS['association_metas']['classe_banques'])
		       ? '<td class="actions">&nbsp;</td>'
		       : '<td class="actions">' . association_bouton('mettre_a_jour', 'edit-12.gif', 'edit_compte', 'id='.$id, $onload_option) . '</td>' )
			 . '<td class="actions">' . association_bouton('supprimer', 'poubelle.gif', 'action_comptes', 'id='.$id) . '</td>'
		     . '<td class="actions"><input name="valide[]" type="checkbox" value="'.$data['id_compte']. '" /></td>')
		  )
		. '</tr>';
	}
	return $comptes;
}

?>