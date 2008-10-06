<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Statistiques - selecteur periode
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/date');


function exec_dw2_stats_prd() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee, 
		$spip_lang;

// page prim en cours
$page_affiche=_request('exec');

//
//  requis
//
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");


//config
$nbr_lignes_tableau = $GLOBALS['dw2_param']['nbr_lignes_tableau'];

// reconstruire .. var=val des get et post
// var : vl ; odb ; wltt
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//
// prepa page 
//
		
	// traite les dte par le post(array) / get (chaine Y-M-d)
	$periode = traitement_dates_periode($prdd,$prdf);
	
	$jour1=$periode['date1']['jour'];
	$mois1=$periode['date1']['mois'];
	$annee1=$periode['date1']['annee'];
	$jour2=$periode['date2']['jour'];
	$mois2=$periode['date2']['mois'];
	$annee2=$periode['date2']['annee'];
	$where_date=$periode['sql'];
	$diff_date=$periode['diff'];
	
	// mode affichage date
	$aff_date1 = affdate_base($annee1."-".$mois1."-".$jour1,'entier');
	$aff_date2 = affdate_base($annee2."-".$mois2."-".$jour2,'entier');
	
		
	//
	// premiere date des stats (annee) --> pour dates : selecteur annee
	$tbl_prem_date = premiere_date_stats_dw2();
	$annee_select = $tbl_prem_date[0];
	$debut_stats = $tbl_prem_date[1];
	
/*
	// prepa table des prem lettres + compteur
	while ($row=spip_fetch_array($rcc_nligne)) {
		$gen_ltt[][strtoupper(substr($row['nom'],0,1))] = $row['total'];
		}

	// calcul tableau du tri-alphabet
	reset ($gen_ltt);
	while (list(,$sectbl)=each($gen_ltt)) {
		while(list($ltt,$cpt)=each($sectbl)) {
			if($ltt != $ltt_prec) {
				$tbl_ltt[$ltt]["nbl"] = 1;
				$tbl_ltt[$ltt]["cpt"]=$cpt;
			} else {
				$tbl_ltt[$ltt]["nbl"]++;
				$ncpt=$tbl_ltt[$ltt]["cpt"]+$cpt;
				$tbl_ltt[$ltt]["cpt"]=$ncpt;
			}
			$ltt_prec = $ltt;
		}
	}

	// si tri alphabet' : modif requete principale
	if (isset($wltt)) {
		$where_ltt = "AND UPPER(nom) LIKE '$wltt%'";
		// on redéfinis $nligne pour la function tranche et $tt_compt
		reset($tbl_ltt);
		$nligne = $tbl_ltt[$wltt]["nbl"];
		$tt_compt = $tbl_ltt[$wltt]["cpt"];
		}

	// tri selon : moyenne journalière, total, dernier en date, pourcentage
	if ($odb=='moy'){ $orderby = 'moyj DESC'; }
	else if ($odb=='dat') { $orderby = 'ds.date DESC'; }
	else if ($odb=='nom') { $orderby = 'dd.nom'; }
	else { $orderby = 'dd.total DESC'; $odb='tot'; }
*/


	// recup' nombre de ligne passe en url, fixe debut LIMIT ...		
	$dl=($vl+0);
	

	// requete principaleLIMIT $dl,$nbr_lignes_tableau
	$rq=spip_query("SELECT ds.id_doc, ds.date, ds.telech, dd.url, dd.nom, dd.total ".
			"FROM spip_dw2_stats ds LEFT JOIN spip_dw2_doc dd ON ds.id_doc=dd.id_document ".
			"WHERE $where_date ORDER BY dd.nom ");
	
	$tbl_fichier=array();
	$i=0;
	$nom_prec='';
	while ($row=spip_fetch_array($rq)) {
		$iddoc = $row['id_doc'];
		$nomfichier = substr(strrchr($row['url'],'/'), 1);
		$telech = $row['telech'];
		$nom = $row['nom'];
		$total = $row['total'];
		
		if($nom != $nom_prec) {
			$i++;
			$tbl_fichier[$i]["id"]=$iddoc;
			$tbl_fichier[$i]["nomfichier"] = $nomfichier;
			$tbl_fichier[$i]["telech"] = $telech;
			$tbl_fichier[$i]["total"]=$total;
			$tbl_fichier[$i]["vu"] = 1;
			
		} else {
			$ntelech = $tbl_fichier[$i]["telech"]+$telech;
			$tbl_fichier[$i]["telech"]=$ntelech;
			$tbl_fichier[$i]["vu"]++;
		}
		
		$nom_prec = $nom;
		
	}
	reset($tbl_fichier);
	
	// compte resultats []
	if($nligne=count($tbl_fichier)) {
		$tt_telech=array();
		foreach($tbl_fichier as $item) {
			$add=$item['telech'];
			$tt_telech['tt']+=$add;
		}
	}




//
// affichage page
//

debut_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");
echo "<a name='haut_page'></a><br />";

gros_titre(_T('dw:titre_page_admin'));


debut_gauche();

	menu_administration_telech();
	menu_voir_fiche_telech();
	menu_config_sauve_telech();
	
	// module outils
	bloc_popup_outils();

	// module delocaliser
	bloc_ico_page(_T('dw:acc_dw2_dd'), generer_url_ecrire("dw2_deloc"), _DIR_IMG_DW2."deloc.gif");


creer_colonne_droite();
	
	// vers popup aide 
	bloc_ico_aide_ligne();

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";

debut_droite();

	echo debut_onglet().
	onglet(_T('dw:stats_generales_titre'), generer_url_ecrire("dw2_stats"), 'aff_gen', '', "cal-mois.gif").
	onglet(_T('dw:stats_periode_titre'), generer_url_ecrire("dw2_stats_prd"), 'aff_prd', 'aff_prd', "cal-semaine.gif").
	fin_onglet();
	
	
	
	// formulaire de periode
	//
	debut_cadre_relief("rien.gif");
		
		// premiere date stats DW2
		debut_boite_filet('a','center');
		echo "<span class='verdana3'>".
			_T('dw:premiere_date_stats_site', array('prem_date' => affdate_base($debut_stats,'entier'))).
			"</span>";
		fin_bloc();
	
	formulaire_periode($periode['date1'],$periode['date2'],$annee_select,_request('exec'));

	fin_cadre_relief();
	
	
	//
	// tableau des documents
	//
	debut_cadre_relief(_DIR_IMG_PACK."statistiques-24.gif");

	if ($nligne==0)
		{
		echo "<br /><b>"._T('dw:aucun_telech_periode')."</b><br /><br />";
		}
	else
		{
		// premiere val de tranche en cours
		$nba1 = $dl+1;
	
		// aff date selection
		debut_cadre_relief("");
			echo "<div style='text-align:center;'>".$aff_date1. (($aff_date2)? "&nbsp;&nbsp;:|:&nbsp;&nbsp;".$aff_date2 : '' )."</div>";
		fin_cadre_relief();
		
		// aff totaux
		debut_boite_filet('a','center');
			echo "<span class='verdana2'>".
				_T('dw:nbr_docs_nbr_telech', array('nligne' => $nligne, 'tt_compt' => $tt_telech['tt'])).
				"&nbsp;".(($diff_date>1)? $diff_date." "._T('dw:jour_s'):'').
				"</span>";
		fin_bloc();

		debut_band_titre("#dfdfdf");
			echo "<div align='center' class='verdana2'>\n";
			tranches($nba1, $nligne, $nbr_lignes_tableau);
			echo "</div>\n";
		fin_bloc();


		
		// table
		//
		$ifond = 0;
		echo "<table align='center' cellpadding='2' cellspacing='0' border='0' width='100%'>\n".
				"<tr bgcolor='$couleur_foncee'>\n".
				"<td>&nbsp;</td>\n".
				"<td><span class='arial2' style='color:#FFFFFF;'>"._T('dw:fichier')."</span></td>\n".
				"<td><div class='arial2' style='color:#FFFFFF; text-align:center;'>"._T('dw:totaux_periode')."</div></td>\n".
				"<td><div class='arial2' style='color:#FFFFFF; text-align:center;'>"._T('dw:totaux_grand')."</div></td>\n".
				"</tr>";
			
		foreach($tbl_fichier as $k => $tbd) {
		if($k>=$nba1 && $k<($nba1+$nbr_lignes_tableau)){
			$ifond = $ifond ^ 1;
			$bgcolor = ($ifond) ? '#FFFFFF' : $couleur_claire;
			
			$id=$tbd['id'];
			$nomfichier = wordwrap($tbd['nomfichier'],35,' ',1);
			$telech = $tbd['telech'];
			$total=$tbd['total'];
			$vu=$tbd['vu'];
			#$moy_prd = round($telech/ ... ,1);
			
			echo "<tr bgcolor='$bgcolor'>";
			echo "<td width='4%' height='20'>";
			echo "<a href='".generer_url_ecrire("dw2_modif", "id=".$id)."'>";
			echo "<img src='"._DIR_IMG_DW2."fiche_doc-15.gif' border='0' align='absmiddle' title='"._T('dw:voir_fiche')."'>";
			echo "</a></td>";
			echo "<td width='65%'><div class='arial2'><b>";
				popup_stats_graph($id,$nomfichier);
			echo "</b></div></td>";
			echo "<td width='16%'><div align='center' class='arial2'><b>".$telech."</b></div></td>";
			#echo "<td width='7%'><div align='center' class='verdana1'><b>".$moy_prd."</b></div></td>";
			echo "<td width='15%'><div align='center' class='verdana2'>".$total."</div></td>";
			echo "</tr>";
			}
		}
		echo "</table>";
		
		
		// info colonne periode (nbre de telech)
		echo "<br />";
		debut_boite_filet('a');
			echo "<span class='verdana2'>"._T('dw:totaux_periode_info')."</span>";
		fin_bloc();
		
		}
	
	fin_cadre_relief();



	//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_

?>
