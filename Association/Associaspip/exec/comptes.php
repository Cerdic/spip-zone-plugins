<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_comptes() {

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$annee= intval(_request('annee'));
		if(empty($annee)){$annee = date('Y');}
		$vu = _request('vu');
		if (!is_numeric($vu)) $vu = '';

		if ( isset ($_REQUEST['imputation'] )) { $imputation = $_REQUEST['imputation']; }
		else { $imputation= "%"; }
		$max = intval(_request('max'));
		if (!$max) $max = 30;
		$id_compte = _request('id_compte', $_GET);
		$id_compte = $id_compte ? intval($id_compte):'';
		exec_comptes_args($annee, $vu, $imputation, _request('debut'), $max, $id_compte);
	}
}

function exec_comptes_args($annee, $vu, $imputation, $debut, $max_par_page, $id_compte) 
{
	/* si on a id_compte a afficher, recuperer son annee pour afficher celle la et reinitialiser les autres parametres */
	if ($id_compte) {
		$annee_id_compte = sql_getfetsel("date_format( date, '%Y' )", 'spip_asso_comptes', "id_compte=$id_compte");
		if ($annee_id_compte) {
			$annee = $annee_id_compte;
			/* on reinitialise les autres parametres */
			$imputation = '%';
			$vu = '';
		} else { /* on n'a pas trouve l'id_compte */
			$id_compte = '';
		}
	}
	$where = "imputation like " . sql_quote($imputation)
	  . (!is_numeric($vu) ? '' : (" AND vu=$vu"));

	$sel = comptes_select_annee($where, $annee,'imputation='.$imputation . "&vu=$vu");
	$where .= " AND date_format( date, '%Y' ) = $annee";

	$totaux = comptes_totaux($where, $imputation, $annee);

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
	association_onglets();

	echo debut_gauche("",true);
	echo debut_boite_info(true);
	echo association_date_du_jour();	
	echo '<p>', _T('asso:en_bleu_recettes_en_rose_depenses'), '</p>'; 
	echo $totaux;
	echo fin_boite_info(true);	
	
	$url_bilan = generer_url_ecrire('bilan', "annee=$annee");		
	$res = association_icone(_T('Bilan') . " $annee",  $url_bilan, 'finances.jpg')
	. association_icone(_T('asso:ajouter_une_operation'),  generer_url_ecrire('edit_compte'), 'ajout_don.png');

	echo bloc_des_raccourcis($res);
	
	echo debut_droite("",true);
	
	debut_cadre_relief(  "", false, "",  _T('asso:informations_comptables'));
	
	echo "\n<table width='100%'>";
	echo '<tr><td>', $sel,'</td>';
	
	echo '<td style="text-align:right;">';
	echo '<form method="post" action="';
	echo generer_url_ecrire('comptes');
	echo '"><div>';
	echo '<select name ="imputation" class="fondl" onchange="form.submit()">';
	echo '<option value="%" ';
	if ($imputation=="%") { echo ' selected="selected"'; }
	echo '>Tous</option>';
	$sql = sqL_select('code, classe, intitule', 'spip_asso_plan','', '', "classe,code");
	while ($plan = sql_fetch($sql)) {
		echo '<option value="'.$plan['code'].'" ';
		if ($imputation==$plan['code']) { echo ' selected="selected"'; }
		echo '>'.$plan['classe'].' - '.$plan['intitule'].'</option>';
	}
	echo '</select></div></form></td>';
	echo '</tr></table>';

	/* (re)calculer la pagination en fonction de id_compte */
	if ($id_compte) {
		/* on recupere les id_comptes de la requete sans le critere de limite et on en tire l'index de l'id_compte recherche parmis tous ceux disponible */
		$all_id_compte = sql_allfetsel("id_compte", "spip_asso_comptes", $where, '',  'date DESC,id_compte DESC');
		$index_id_compte = -1;
		reset($all_id_compte);
		while (($index_id_compte<0) && (list($k,$v) = each($all_id_compte))) {
			if ($v['id_compte'] == $id_compte) $index_id_compte = $k;
		}
		/* on recalcule le parametre de limite de la requete */
		if ($index_id_compte>=0) {
			$debut = intval($index_id_compte/$max_par_page)*$max_par_page;
		}
	}

	//TABLEAU

	$table = comptes_while($where, intval($debut).",".$max_par_page, $id_compte);

	if ($table) {

		//SOUS-PAGINATION

		$nombre_selection=sql_countsel('spip_asso_comptes', $where);
		$pages=intval($nombre_selection/$max_par_page) + 1;
		$args = 'annee='.$annee.'&imputation='.$imputation. (is_numeric($vu) ? "&vu=$vu" : ''); 
		$nav = '';
		if ($pages != 1) for ($i=0;$i<$pages;$i++) { 
			$position= $i * $max_par_page;
			if ($position == $debut)
			  { $nav .= '<strong>'.$position.' </strong>'; }
			else { $h = generer_url_ecrire('comptes',$args.'&debut='.$position);
			  $nav .= "<a href='$h'>$position</a>\n"; }
		  }
		
		$table = "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>"
		. "<tr style='background-color: #DBE1C5;'>\n"
		. '<th style="text-align: right;">' . _T('asso:id'). "</th>\n"
		. '<th style="text-align: right;">' . _T('asso:date') . "</th>\n"
		. '<th>' . _T('asso:compte') . "</th>\n"
		. '<th>' . _T('asso:justification') . "</th>\n"
		. '<th style="text-align: right;">' . _T('asso:montant') . "</th>\n"
		. '<th>' . _T('asso:financier') . "</th>\n"
		. '<td colspan="3" style="text-align: center;"><strong>&nbsp;</strong></td>'
		. '</tr>'
		. $table
		. "</table>\n"
		. "<table width='100%'><tr>\n<td>" . $nav . '</td><td style="text-align:right;"><input type="submit" value="' . _L('Valider') . '" class="fondo" /></td></tr></table>';
	
		echo generer_form_ecrire('action_comptes', $table);
	}
	fin_cadre_relief();  
	echo fin_page_association(); 
}

function comptes_while($where, $limit, $id_compte)
{
	$query = sql_select('*', "spip_asso_comptes", $where,'',  'date DESC,id_compte DESC', $limit);
	$auteurs = '';

	while ($data = sql_fetch($query)) {
		if ($data['recette'] >0) { $class= "pair";}
		else { $class="impair";}	   
		$id = $data['id_compte'];
		
		if($id_compte==$id) {
			$onload_option .= 'onLoad="document.getElementById(\'id_compte'.$id_compte.'\').scrollIntoView(true);"';
		}

		$auteurs .= "\n<tr id='id_compte$id'>"
		. '<td class="'
		. $class. ' border1" style="text-align:right;">'
		.$id
		. "</td>\n<td class=\""
		. $class. ' border1" style="text-align:right;">'
		. association_datefr($data['date'])
		. "</td>\n<td class=\""
		. $class. ' border1">'
		. $data['imputation']
		. "</td>\n<td class=\""
		. $class. ' border1">'
		. propre($data['justification'])
		. "</td>\n<td class=\""
		. $class. ' border1" style="text-align:right;">'
		. association_nbrefr($data['recette']-$data['depense'])
		. "</td>\n<td class=\""
		. $class. ' border1">'
		. $data['journal']
		. '</td>'
		. ($data['vu'] ?
			("<td class='$class' colspan='3' style='text-align: center;'><img src=\""._DIR_PLUGIN_ASSOCIATION_ICONES."puce-verte.gif\" $onload_option /></td>\n")
		   :  ("<td class='$class border1' style='text-align: center;'>" . association_bouton(_T('asso:mettre_a_jour'), 'edit-12.gif', 'edit_compte', 'id='.$id, $onload_option) . "</td>\n"
			. "<td class='$class border1' style='text-align: center;'>" . association_bouton(_T('asso:supprimer'), 'poubelle.gif', 'action_comptes', 'id='.$id) . "</td>\n"
		       . "<td class='$class border1' style='text-align: center;'><input name='valide[]' type='checkbox' value='".$data['id_compte']. "' /></td>\n"))
		 . '</tr>';
	}
	return $auteurs;
}

function comptes_totaux($where, $imputation, $annee)
{
	$data = sql_fetsel("sum(recette) AS somme_recettes, sum(depense) AS somme_depenses", 'spip_asso_comptes', $where);
	$somme_recettes = $data['somme_recettes'];
	$somme_depenses = $data['somme_depenses'];
	$solde= $somme_recettes - $somme_depenses;
			
	return '<table width="100%">' . 
	 '<tr>' . 
	 '<td colspan="2"><strong>' . 
	  _L('Totaux ') . ($imputation=='%' ? '' : $imputation) . 
	 ' ' . $annee . 
	 ' :</strong></td>' . 
	 '</tr>' . 
	 '<tr>' . 
	 '<td><strong style="color:blue;">'. _T('asso:entrees') . '</strong></td>' . 
	 '<td style="text-align:right;">'.association_nbrefr($somme_recettes).' &euro; </td>' . 
	 '</tr>' . 
	 '<tr>' . 
	 '<td><strong style="color:pink;">' . _T('asso:sorties') . '</strong></td>' . 
	 '<td style="text-align:right;">'.association_nbrefr($somme_depenses).' &euro;</td>' . 
	 '</tr>' . 
	 '<tr>' . 
	 '<td><strong style="color: #9F1C30;">' . _T('asso:solde') . '</strong></td>' . 
	 '<td class="impair" style="text-align:right;">'.association_nbrefr($solde).' &euro;</td>' . 
	 '</tr>' . 
	 '</table>';
}

function comptes_select_annee($where, $annee, $args)
{
	$tous = sql_allfetsel("date_format( date, '%Y' )  AS annee", "spip_asso_comptes", $where, "annee", "annee");
		
	foreach ($tous as $k => $data) {
		$an = $data['annee'];
		if ($an==$annee)	
		  $tous[$k] = ' <strong>'.$an.' </strong>';
		else $tous[$k] = '<a href="'. generer_url_ecrire('comptes','annee='.$an.'&'. $args).'">'.$an.'</a>';
	}
	return join("\n", $tous);
}
?>
