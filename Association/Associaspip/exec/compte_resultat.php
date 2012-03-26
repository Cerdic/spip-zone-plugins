<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 08/2011 par Marcel BOLLA ... à partir de bilan.php           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');

function exec_compte_resultat()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$plan = sql_countsel('spip_asso_plan','active=1');
		$id_exercice = intval(_request('exercice'));
		if(!$id_exercice){
			/* on recupere l'id_exercice dont la date "fin" est "la plus grande" */
			$id_exercice = sql_getfetsel('id_exercice', 'spip_asso_exercices', '', '', 'fin DESC');
			if(!$id_exercice)
				$id_exercice = 0;
		}
		$exercice_data = sql_asso1ligne('exercice', $id_exercice);
		onglets_association('titre_onglet_comptes');
		// INTRO : rappel de l'exercicee affichee
		$infos['exercice_entete_debut'] = association_datefr($exercice_data['debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_datefr($exercice_data['fin'], 'dtend');
		echo totauxinfos_intro($exercice_data['intitule'], 'exercice', $id_exercice, $infos);
		// pas de sommes de synthes puisque tous les totaux sont dans la zone centrale ;-
		// datation et raccourcis
		icones_association(array('comptes', "exercice=$exercice"), array(
			'bilan' => array('finances-24.png', 'bilan', "exercice=$id_exercice"),
			'annexe_titre_general' => array('finances-24.png', 'annexe', "exercice=$id_exercice"),
		));
		// elements communs aux requetes
		if ($plan) {
			$join = ' RIGHT JOIN spip_asso_plan ON imputation=code';
			$sel = ', code, intitule, classe';
			$where = " date>='$exercice_data[debut]' AND date<='$exercice_data[fin]' ";
			$having = 'classe = ';
			$order = 'code';
		} else {
			$join = $sel = $where = $having = $order = '';
		}
		$var = serialize(array($id_exercice, $join, $sel, $where, $having, $order)); //!\ les cles numeriques peuvent poser probleme... <http://www.mail-archive.com/php-bugs@lists.php.net/msg100262.html> mais il semble qu'ici le souci vient de l'absence d'encodage lorsqu'on passe $var par URL...
//		$var = serialize(array('id'=>$id_exercice, '1'=>$join, '2'=>$sel, '3'=>$where, '4'=>$having, '5'=>$order));
		if(autoriser('associer', 'export_compte_resultats') && $plan){ // on peut exporter : pdf, csv, xml, ...
			echo debut_cadre_enfonce('',true);
			echo '<h3>'. _T('asso:cpte_resultat_mode_exportation') .'</h3>';
			if (test_plugin_actif('FPDF')) { // impression en PDF : _T('asso:bouton_impression')
				echo icone1_association('PDF', generer_url_ecrire('export_compteresultats_pdf').'&var='.rawurlencode($var), 'print-24.png'); //!\ generer_url_ecrire() utilise url_enconde() or il est preferable avec les grosses variables serialisees d'utiliser rawurlencode()
			}
			foreach(array('csv','ctx','tex','tsv','xml','yaml') as $type) { // autres exports (donnees brutes) possibles
				echo icone1_association(strtoupper($type), generer_url_ecrire("export_compteresultats_$type").'&var='.rawurlencode($var), 'export-24.png'); //!\ generer_url_ecrire($exec, $param) equivaut a generer_url_ecrire($exec).'&'.urlencode($param) or il faut utiliser rawurlencode($param) ici...
			}
			echo fin_cadre_enfonce(true);
		}
		debut_cadre_association('finances-24.jpg', 'cpte_resultat_titre_general', $exercice_data['intitule']);
		$depenses = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_charges']));
		$recettes = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_produits']));
		compte_resultat_benefice_perte($recettes, $depenses);
		compte_resultat_benevolat($var, intval($GLOBALS['association_metas']['classe_contributions_volontaires']));
/*
		if(autoriser('associer', 'export_compte_resultats') && $plan){ // on peut exporter : pdf, csv, xml, ...
			echo "<br /><table width='100%' class='asso_tablo' cellspacing='6' id='asso_tablo_exports'>\n";
			echo '<tbody><tr>';
			echo '<td>'. _T('asso:cpte_resultat_mode_exportation') .'</td>';
			if (test_plugin_actif('FPDF')) { // impression en PDF
				echo '<td class="action"><a href="'.generer_url_ecrire('export_compteresultats_pdf').'&var='.rawurlencode($var). '"><strong>PDF</strong></td>'; //!\ generer_url_ecrire() utilise url_enconde() or il est preferable avec les grosses variables serialisees d'utiliser rawurlencode()
			}
			foreach(array('csv','ctx','tex','tsv','xml','yaml') as $type) { // autres exports (donnees brutes) possibles
				echo '<td class="action"><a href="'. generer_url_ecrire('export_compteresultats_'.$type).'&var='.rawurlencode($var). '"><strong>'. strtoupper($type) .'</strong></td>'; //!\ generer_url_ecrire($exec, $param) equivaut a generer_url_ecrire($exec).'&'.urlencode($param) or il faut utiliser rawurlencode($param) ici...
			}
			echo '</tr></tbody></table>';
		}
*/
		fin_page_association();
	}
}

function compte_resultat_charges_produits($var, $class) {
	include_spip('inc/association_plan_comptable');
	$tableau = @unserialize($var);
	$id_tableau = (($class==$GLOBALS['association_metas']['classe_charges']) ? 'charges' : 'produits');
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_$id_tableau'>\n";
	echo "<thead>\n<tr>";
	echo '<th width="10">&nbsp;</td>';
	echo '<th width="30">&nbsp;</td>';
	echo '<th>'. (($class==$GLOBALS['association_metas']['classe_charges']) ? _T('asso:cpte_resultat_titre_charges') : _T('asso:cpte_resultat_titre_produits')) .'</th>';
	echo '<th width="80">&nbsp;</th>';
	echo "</tr>\n</thead><tbody>";
	$quoi = (($class==$GLOBALS['association_metas']['classe_charges']) ? 'SUM(depense) AS valeurs' : 'SUM(recette) AS valeurs');
	$query = sql_select(
		"imputation, $quoi, DATE_FORMAT(date, '%Y') AS annee $tableau[2]", // select
		"spip_asso_comptes $tableau[1]", // from
		$tableau[3], // where
		$tableau[5], // group by
		$tableau[5], // order by
		'', // limit
		$tableau[4].$class // having
	);
	$total = 0;
	$chapitre = '';
	$i = 0;
	while ($data = sql_fetch($query)) {
		echo '<tr>';
		$valeurs = $data['valeurs'];
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
			echo '<td class="text">'. $new_chapitre . '</td>';
			echo '<td colspan="3" class="text">'. ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'")) .'</td>';
			$chapitre = $new_chapitre;
			echo "</tr>\n<tr>";
		}
		echo "<td>&nbsp;</td>";
		echo '<td class="text">'. $data['code'] .'</td>';
		echo '<td class="text">'. $data['intitule'] .'</td>';
		echo '<td class="decimal">'. association_nbrefr($valeurs) .'</td>';
		echo "</tr>\n";
		$total += $valeurs;
	}
	echo "</tbody><tfoot>\n<tr>";
	echo '<th colspan="2">&nbsp;</th>';
	echo '<th class="text">'. (($class==$GLOBALS['association_metas']['classe_charges']) ? _T('asso:cpte_resultat_total_charges') : _T('asso:cpte_resultat_total_produits')) .'</th>';
	echo '<th class="decimal">'. association_nbrefr($total) . '</th>';
	echo "</tr>\n</tfoot>\n</table>\n";
	return $total;
}

function compte_resultat_benefice_perte($recettes, $depenses) {
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_solde'>\n";
	echo "<thead>\n<tr>";
	echo '<th width="10">&nbsp;</td>';
	echo '<th width="30">&nbsp;</td>';
	echo '<th>'. _T('asso:cpte_resultat_titre_resultat') .'</th>';
	echo '<th width="80">&nbsp;</th>';
	echo "</tr>\n</thead>";
	echo "<tfoot>\n<tr>";
	echo '<th colspan="2">&nbsp;</th>';
	$res = $recettes-$depenses;
	echo '<th class="text">'. (($res<0) ? _T('asso:cpte_resultat_perte') : _T('asso:cpte_resultat_benefice')) .'</th>';
	echo '<th class="decimal">'. association_nbrefr($res) .'</th>';
	echo "</tr></tfoot></table>";
}

function compte_resultat_benevolat($var, $class) {
	$tableau = @unserialize($var);
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_benevolat'>\n";
	echo "<thead>\n<tr>";
	echo '<th width="10">&nbsp;</th>';
	echo '<th width="30">&nbsp;</th>';
	echo '<th>'. _T('asso:cpte_resultat_titre_benevolat') . '</th>';
	echo '<th width="80">'. _T('asso:cpte_resultat_recette_evaluee') .'</th>';
	echo '<th width="80">'. _T('asso:cpte_resultat_depense_evaluee') .'</th>';
	$query = sql_select(
		"imputation, SUM(recette) AS recettes, SUM(depense) AS depenses, DATE_FORMAT(date, '%Y') AS annee $tableau[2]", // select
		"spip_asso_comptes $tableau[1]", // from
		$tableau[3], // where
		$tableau[5], // group by
		$tableau[5], // order by
		'', // limit
		$tableau[4].$class // having
	);
	$chapitre = '';
	$total_recettes = $total_depenses = 0;
	while ($data = sql_fetch($query)) {
		echo '<tr>';
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
			echo '<td class="text">' . $new_chapitre . '</td>';
			echo '<td colspan="4" class="text">'. ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'")) . '</td>';
			$chapitre = $new_chapitre;
			echo "</tr>\n";
		}
		echo '<td>&nbsp;</td>';
		echo '<td class="text">'. $data['code'] .'</td>';
		echo '<td class="text">'. $data['intitule'] .'</td>';
		echo '<td class="decimal">'. association_nbrefr($data['recettes']) .'</td>';
		echo '<td class="decimal">'. association_nbrefr($data['depenses']) .'</td>';
		echo '</tr>';
		$total_recettes += $data['recettes'];
		$total_depenses += $data['depenses'];
	}
	echo "</tbody><tfoot>\n<tr>";
	echo '<th width="10">&nbsp;</td>';
	echo '<th width="30">&nbsp;</td>';
	echo '<th class="decimal">'. _T('asso:resultat_courant') .'</th>';
	echo '<th class="decimal">'. association_nbrefr($total_recettes) .'</th>';
	echo '<th class="decimal">'. association_nbrefr($total_depenses) .'</th>';
	echo "</tr>\n</tfoot>\n</table>\n";
}

	include_spip('inc/charsets');
	include_spip('inc/association_plan_comptable');

// Brique commune aux classes d'exportation des donnees du compte de resultat
class ExportCompteResultats {

	var $exercice;
	var $join;
	var $sel;
	var $where;
	var $having;
	var $order;
	var $out;

	function  __construct($var) {
		$tableau = unserialize(rawurldecode($var));
		$this->exercice = $tableau[0];
		$this->join = $tableau[1];
		$this->sel = $tableau[2];
		$this->where = $tableau[3];
		$this->having = $tableau[4];
		$this->order = $tableau[5];
		$this->out = '';
	}

	// de type CSV,INI,TSV, etc.
	function LignesSimplesEntete($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='') {
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_code')))) .$champFin.$champsSeparateur;
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_intitule')))) .$champFin.$champsSeparateur;
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_montant')))) .$champFin.$lignesSeparateur;
	}

	// de type CSV,INI,TSV, etc.
	function LignesSimplesCorps($key, $champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='') {
		switch ($key) {
			case 'charges' :
				$quoi = "SUM(depense) AS valeurs";
				break;
			case 'produits' :
				$quoi = "SUM(recette) AS valeurs";
				break;
			case 'contributions_volontaires' :
				$quoi = "SUM(depense) AS charge_evaluee, SUM(recette) AS produit_evalue";
				break;
		}
		$query = sql_select(
			"imputation, $quoi, DATE_FORMAT(date, '%Y') AS annee".$this->sel, // select
			'spip_asso_comptes '.$this->join, // from
			$this->where, // where
			$this->order, // group by
			$this->order, // order by
			'', // limit
			$this->having .$GLOBALS['association_metas']['classe_'.$key] // having
		);
		$chapitre = '';
		$i = 0;
		while ($data = sql_fetch($query)) {
			if ($key==='contributions_volontaires') {
				if ($data['charge_evaluee']>0) {
					$valeurs = $data['charge_evaluee'];
				} else {
					$valeurs = $data['produit_evalue'];
				}
			} else {
				$valeurs = $data['valeurs'];
			}
			$new_chapitre = substr($data['code'], 0, 2);
			if ($chapitre!=$new_chapitre) {
				$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $new_chapitre) .$champFin.$champsSeparateur;
				$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))) .$champFin.$champsSeparateur;
				$this->out .= $champsSeparateur.' '.$champsSeparateur;
				$this->out .= $lignesSeparateur;
				$chapitre = $new_chapitre;
			}
			$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $data['code']) .$champFin.$champsSeparateur;
			$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $data['intitule']) .$champFin.$champsSeparateur;
			$this->out .= $champDebut.$valeurs.$champFin.$lignesSeparateur;
		}
	}

	function leFichier($ext) {
		$fichier = _DIR_RACINE.'/'._NOM_TEMPORAIRES_ACCESSIBLES.'compte_resultats_'.$this->exercice.".$ext";
		$f = fopen($fichier, 'w');
		fputs($f, $this->out);
		fclose($f);
		header('Content-type: application/'.$ext);
		header('Content-Disposition: attachment; filename="'.$fichier.'"');
		readfile($fichier);
	}

}

?>