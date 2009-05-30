<?php
# DW2 -- stats-graph des téléchargements.
# Script original :
#
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2005                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
#
# Modifié pour la circonstance : DW2 2.1 (20/07/2006) - SPIP 1.9
# Scoty - koakidi.com
# (2.13 - prefix .. spip_)
# 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/statistiques');

// Donne la hauteur du graphe en fonction de la valeur maximale
// Doit etre un entier "rond", pas trop eloigne du max, et dont
// les graduations (divisions par huit) soient jolies :
// on prend donc le plus proche au-dessus de x de la forme 12,16,20,40,60,80,100

/* dans inc/statistiques 
function maxgraph($max) {
function http_img_rien($width, $height, $style='', $title='') {
*/

// chryjs 13/10/8 suit une série de fonctions directement inspirées de SPIP/ecrire/inc/statistiques
// mais modifiées pour les intitulés propres au téléchargement
// et aux liens concernés

// cf statistiques_zoom
function statistiques_zoom_document($id_document, $largeur_abs, $date_premier, $date_debut, $date_fin)
{
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jour_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
		$aff_jours_plus = round($aff_jours * 1.5);		
		$aff_jours_moins = round($aff_jours / 1.5);
		
		
		
		if ($id_document) {
			$pour_article="&id_document=$id_document";
			$url_type_aff_page ="dw2_popup_stats";
			$arg_aff_page = "aff_jours=";
		}
		else {
			$url_type_aff_page ="dw2_admin";
			$arg_aff_page = "page_affiche=stats&aff_jours=";
		}
		
		if ($date_premier < $date_debut )
			$zoom = http_href(generer_url_ecrire($url_type_aff_page, $arg_aff_page.$aff_jours_plus.$pour_article),
				 http_img_pack('loupe-moins.gif',
					       _T('info_zoom'). '-',
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
		if ( (($date_fin - $date_debut) / (24*3600)) > 30)
			$zoom .= http_href(generer_url_ecrire($url_type_aff_page, $arg_aff_page.$aff_jours_moins.$pour_article),
				 http_img_pack('loupe-plus.gif',
					       _T('info_zoom'). '+',
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
	return $zoom;
}

// cf statistiques_href
function statistiques_href_telechargement($jour, $moyenne, $script='', $value='')
{
//	$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
	$ce_jour=date("Y-m-d H:i:s", $jour);
//	$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);						

	$title = nom_jour($ce_jour) . ' '
		. affdate_court($ce_jour)  .' '.
		" | " . _T('dw:telechargements')." $value | " ._T('info_moyenne')." "
		. round($moyenne,2);
	return attribut_html(supprimer_tags($title));
}

// cf statistiques_vides
function statistiques_vides_telechargement($prec, $largeur, $rapport, $moyenne, $script='')
{
	$hauteur_moyenne = round($moyenne*$rapport)-1;
	$title = statistiques_href_telechargement($prec, $moyenne, $script, ''); // cf statistiques_vides
	$tagtitle = $script ? '' : $title;	
	if ($hauteur_moyenne > 1) {
		$res = http_img_rien($largeur,1, 'trait_moyen', $tagtitle)
			. http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);						  
	} else $res = '';
	$res .=	http_img_rien($largeur,1,'trait_bas', $tagtitle);
	if (!$script) return $res;
	return "<a href='$script' title='$title'>$res</a>";	
}

// cf statistiques_resume
function statistiques_resume_telechargement($max, $moyenne, $last, $prec, $popularite=0)
{
	return  "\n<td valign='top' style='width: 33%; ' class='verdana1'>"
	. _T('info_maximum')." "
	. $max . "<br />"
	. _T('info_moyenne')." "
	. round($moyenne). "</td>"
	. "\n<td valign='top' style='width: 33%; ' class='verdana1'>"
	. _T('info_aujourdhui')
	. $last
	. (($prec <= 0) ? '' :
	     ('<br />'
	      ._T('info_hier').' '.$prec))
	. (!$popularite ? '' :
	   ("<br />"._T('info_popularite_5').' '.$popularite))
	.  "</td>";
}


//h.8/11 ...............
global $spip_lang_left;


if ($id_document = intval($id_document)) {

	$result = sql_select("nom, total, DATE_FORMAT(date_crea,'%d/%m/%Y') AS datecrea",
						"spip_dw2_doc",
						"statut='actif' AND id_document ='$id_document'");

	if ($row = sql_fetch($result)) {
		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 30 caract
		$titre = wordwrap(typo($row['nom']),30,' ',1);
		$total_absolu = $row['total'];
		$date_crea = $row['datecrea'];
		//$val_popularite = round($row['popularite']);
	}
} 
else {
	$result = sql_select("SUM(total) AS total_absolu",
							"spip_dw2_doc",
							"statut='actif'");

	if ($row = sql_fetch($result)) {
		$total_absolu = $row['total_absolu'];
	}
}

//if ($titre) $pourarticle = " "._T('info_pour')." &laquo; $titre &raquo;";

if ($id_document) {
	if ($titre) echo gros_titre($titre,'','',true);
	echo "<div class='verdana2' style='padding:3px;'>"._T('dw:enreg_dans_cat')." ".$date_crea."</div>";
}


//////

if (!$aff_jours) $aff_jours = 105;

if (!$origine) {

	$order='date';
	$table='spip_dw2_stats';

	if ($id_document){ $where = "id_doc=$id_document"; }
	else { $where = '1'; }
	
	// requete premiere date dans dw2_stats
	$date_premier = sql_getfetsel("UNIX_TIMESTAMP($order) AS d", $table, $where, '', $order, 1);

	// global sur la période (105 j. :$aff_jours)
	$result=sql_select("UNIX_TIMESTAMP(date) AS date_unix, SUM(telech) AS visites",
						"spip_dw2_stats ",
						"$where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) AND TO_DAYS(date) < TO_DAYS(NOW())",
						"date",
						"date");

	while ($row = sql_fetch($result)) {
		$date = $row['date_unix'];
		$visites = $row['visites'];

		$log[$date] = $visites;
		if ($i == 0) $date_debut = $date;
		$i++;
	}

	// Visites du jour
	if ($id_document) {
		$result = sql_select("telech AS visites",
								"spip_dw2_stats",
								"to_days(date) = to_days(NOW()) AND id_doc = $id_document");

	} else {
		$result = sql_select("SUM(telech) AS visites",
								"spip_dw2_stats",
								"to_days(date) = to_days(NOW())");
	}
	if ($row = @sql_fetch($result))
		$visites_today = $row['visites'];
	else
		$visites_today = 0;

	/* =================================================== stats des jours ================================== */

	if (count($log)>0) {
		// ...
		echo debut_cadre_relief("",true);
		// h.23/11
		if(!$id_document) {
			debut_band_titre($couleur_foncee);
			echo "<div align='center' class='verdana3'><b>"._T('dw:evolution_telech')."</b></div>";
			fin_bloc();
		}

		$max = max(max($log),$visites_today);
		$date_today = time();
		$nb_jours = floor(($date_today-$date_debut)/(3600*24));
		$maxgraph = maxgraph($max);
		$rapport = 200 / $maxgraph;

		if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
		if ($largeur < 1) {
			$largeur = 1;
			$agreg = ceil(count($log) / 420);	
		} else {
			$agreg = 1;
		}
		if ($largeur > 50) $largeur = 50;
		
		$largeur_abs = 420 / $aff_jours;

		
//date_fin=date_today
		echo statistiques_zoom_document($id_document, $largeur_abs, $date_premier, $date_debut, $date_today);

	
// cf statistiques_tous		
		echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>" .
			"\n<td ".http_style_background("fond-stats.gif").">"
			. "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>"
			. "\n<td style='background-color: black'>" . http_img_rien(1, 200) . "</td>";

		// Presentation graphique
//cf  stat_log1
		$rien = http_img_rien($largeur, 1, 'trait_bas', '');
		$test_agreg = $decal = $date_prec = $val_prec = $moyenne = 0;
		foreach ($log as $key => $value) {
			$test_agreg ++;
			if ($test_agreg != $agreg) continue;
			$test_agreg = 0;
			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			// Inserer des jours vides si pas d'entrees	
			if ($date_prec > 0) {
				$ecart = floor(($key-$date_prec)/((3600*24)*$agreg)-1);
				for ($i=0; $i < $ecart; $i++){
					if ($decal == 30) $decal = 0;
					$decal ++;
					$tab_moyenne[$decal] = $value;
					reset($tab_moyenne);
					$m = statistiques_moyenne($tab_moyenne);
					$m = statistiques_vides_telechargement($date_prec+(3600*24*($i+1)), $largeur, $rapport, $m, ''); // cf statistiques_vides
					echo "\n<td style='width: ${largeur}px'>$m</td>";
				}
			}
			$moyenne = round(statistiques_moyenne($tab_moyenne),2);
			$hauteur = round($value * $rapport) - 1;


			$m = ($hauteur <= 0) ? '' : statistiques_jour($key, $value, $largeur, $moyenne, $hauteur, $rapport, (date("w",$key) == "0"), '');
			echo "\n<td style='width: ${largeur}px'>$m$rien</td>\n";
			
			$date_prec = $key;
			$val_prec = $value;
		}

		// Dernier jour
// cf statistiques_prevision
// $last =$visites_today
// $popularite = 0 a priori
		echo statistiques_prevision($id_document, $largeur, $moyenne, $rapport, 0, $visites_today);

		echo "\n<td style='background-color: black'>"
			. http_img_rien(1, 1) ."</td>"
			. "</tr></table>"
			. "</td>\n<td "
			. http_style_background("fond-stats.gif")."  valign='bottom'>"
			. http_img_rien(3, 1, 'trait_bas') ."</td>"
			. "\n<td>" . http_img_rien(5, 1) ."</td>"
			. "\n<td valign='top'>"
			. statistiques_echelle($maxgraph)
			. "</td>"
			. "</tr></table>";

		$total = "<b>" .  _T('info_total') ." " . $total_absolu."</b>";

		if ($id_document) $liste = statistiques_classement($id_document, $classement, $liste);
		else $liste='';
		
//date_fin=date_today
// $pas = 24*3600
		$legend = statistiques_nom_des_mois($date_debut, $date_today, $largeur, (24*3600),$agreg)
			. "<span class='arial1 spip_x-small'>"
			. _T('texte_statistiques_visites')
			. "</span><br />";

// cf statistiques_resume
// $popularite = 0 a priori
// $last =$visites_today
// $prec=$val_prec
		$resume = statistiques_resume_telechargement($max, $moyenne, $visites_today, $val_prec, 0);

		$legend .= "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>"
			. $resume
			. "\n<td valign='top' style='width: 33%; ' class='verdana1'>"
			. $total
			. $liste
			. "</td></tr></table>";	

		echo $legend;
	} // stats / jour

	/* =================================================== stats des mois ================================== */
	if (count($log) > 60) {
	
		// voir statistiques_par_mois
		echo "<p>";
		echo "<font face='verdana,arial,helvetica,sans-serif' size='2'><b>"._T('info_visites_par_mois')."</b></font>";

		echo "<div align='left'>";
		///////// Affichage par mois
		$result=sql_select("FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(telech) AS total_visites ",
							"spip_dw2_stats",
							"$where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY)",
							"date_unix",
							"date");

		$i = 0;
		while ($row = sql_fetch($result)) {
			$date = $row['date_unix'];
			$visites = $row['total_visites'];
			$i++;
			$entrees["$date"] = $visites;
		}
		
		if (count($entrees)>0){
		
			$maxgraph = maxgraph(max($entrees));
			$rapport = 200/$maxgraph;
			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}
		
		echo  "\n<table cellpadding='0' cellspacing='0' border='0'><tr>"
			.  "\n<td ".http_style_background("fond-stats.gif").">"
			. "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>"
			. "\n<td class='trait_bas'>" . http_img_rien(1, 200) ."</td>" ;
		
		// Presentation graphique
		$decal = 0;
		$tab_moyenne = array();
		
		$all = '';
	
		while (list($key, $value) = each($entrees)) {			
			$mois = affdate_mois_annee($key);
			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			$moyenne = statistiques_moyenne($tab_moyenne);			
			$hauteur_moyenne = round($moyenne * $rapport) - 1;
			$hauteur = round($value * $rapport) - 1;
			
			echo "\n<td style='width: ${largeur}px'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('dw:telechargements')." : ".$value));
			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'trait_moyen');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"trait_haut");
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						echo http_img_rien($largeur,$hauteur,"couleur_janvier", $tagtitle);
					} else {
						echo http_img_rien($largeur,$hauteur,"couleur_mois", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
							$couleur =  'couleur_janvier';
					} else {
							$couleur = 'couleur_mois';
					}
					echo http_img_rien($largeur,$difference, $couleur, $tagtitle);
					echo http_img_rien($largeur,1,'trait_moyen',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne, $couleur, $tagtitle);
				}
				else {
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						echo http_img_rien($largeur, $hauteur, "couleur_janvier", $tagtitle);
					} else {
						echo http_img_rien($largeur,$hauteur, "couleur_mois", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'background-color:black;', $tagtitle);
			echo "</td>\n";
		}

		echo "\n<td style='background-color: black'>" . http_img_rien(1, 1)
			. "</td>"
			. "</tr></table></td>"
			. "\n<td ".http_style_background("fond-stats.gif")." valign='bottom'>"
			. http_img_rien(3, 1, 'trait_bas') ."</td>"
			. "\n<td>" . http_img_rien(5, 1) ."</td>"
			. "\n<td valign='top'>"
			. statistiques_echelle($maxgraph)
			. "</td></tr></table>";
			
		echo "</div>"; // align-left
	}
	
	/////
		
	echo fin_cadre_relief(true);

}

?>