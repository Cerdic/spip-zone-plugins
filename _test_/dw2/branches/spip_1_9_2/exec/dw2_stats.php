<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Statistiques generales documents
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_stats() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

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
$anti_triche = $GLOBALS['dw2_param']['anti_triche'];

// reconstruire .. var=val des get et post
// var : vl ; odb ; wltt
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

//
// prepa page 
//

	// recup' nombre de ligne passe en url, fixe debut LIMIT ...		
	$dl=($vl+0);
		
	//nombre de lignes dans le catalogue (Docs actifs)	
	$rcc_nligne=spip_query("SELECT nom, total FROM spip_dw2_doc WHERE statut='actif' ORDER BY nom");
	$nligne=spip_num_rows($rcc_nligne);
	
	// defini total compteur si tous doc affichés
	$tt_compt=total_compteur_actif();

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
	else if ($odb=='dat') { $orderby = 'dateur DESC'; }
	else if ($odb=='nom') { $orderby = 'nom'; }
	else { $orderby = 'total DESC'; $odb='tot'; }

	// requete principale
	$rq_ttdoc=spip_query("SELECT *, ROUND(total/(TO_DAYS(NOW()) - TO_DAYS(date_crea)),2) AS moyj, ".
						"DATE_FORMAT(date_crea,'%d/%m/%Y') AS datecrea, ".
						"DATE_FORMAT(dateur,'%d/%m/%Y - %H:%i') AS derdate ".
						"FROM spip_dw2_doc ".
						"WHERE statut='actif' $where_ltt ".
						"ORDER BY $orderby LIMIT $dl,$nbr_lignes_tableau");

	
	// premiere date des stats (annee) --> pour dates : selecteur annee
	$tbl_prem_date = premiere_date_stats_dw2();
	$annee_stats = $tbl_prem_date[0];
	$debut_stats = $tbl_prem_date[1];


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
	onglet(_T('dw:stats_generales_titre'), generer_url_ecrire("dw2_stats"), 'aff_gen', 'aff_gen', "cal-mois.gif").
	onglet(_T('dw:stats_periode_titre'), generer_url_ecrire("dw2_stats_prd"), 'aff_prd', '', "cal-semaine.gif").
	fin_onglet();
	
	debut_cadre_relief(_DIR_IMG_PACK."statistiques-24.gif");

	if ($nligne==0)
		{
		echo "<br /><b>"._T('dw:txt_cat_aucun')."<br />";
		echo "<br /><br /><font color='#cf4040'><a href='".generer_url_ecrire("dw2_ajouts")."'>"._T('dw:ajout_doc')."</a></font></b><br />";
		fin_cadre_relief();
		break;
		}
	else
		{
		// premiere val de tranche en cours
		$nba1 = $dl+1;
		
		// premiere date stats DW2
		debut_boite_filet('a','center');
		echo "<span class='verdana3'>".
			_T('dw:premiere_date_stats_site', array('prem_date' => affdate_base($debut_stats,'entier'))).
			"</span>";
		fin_bloc();
		
		
		echo "<br /><div align='center' class='verdana3'>";
		if(isset($wltt))
			{ echo "[ <b>".$wltt."...</b> ]"; }
		echo _T('dw:nbr_docs_nbr_telech', array('nligne' => $nligne, 'tt_compt' => $tt_compt));
		echo "</div><br />";

		debut_band_titre("#dfdfdf");
			echo "<div align='center' class='verdana2'>\n";
			tranches($nba1, $nligne, $nbr_lignes_tableau);
			echo "</div>\n";
		fin_bloc();

		$ifond = 0;
		
		// affichage lettres pour tri-alphabetique
		echo "<div class='verdana2'>";
		bouton_tout_catalogue("dw2_stats");
		reset ($tbl_ltt);
		while (list($k) = each($tbl_ltt))
			{
			$v=$tbl_ltt[$k]['nbl'];
			echo "<a href='".generer_url_ecrire("dw2_stats", "wltt=".$k)."' title='"._T('dw:document_s')." : $v'>\n";
			echo bouton_alpha($k);
			echo "</a>\n";
			}
		echo "</div><div style='clear:both;'></div>";	
		// 
	
	
		// Entete tableau ..
		echo " <table align='center' border='0' cellpadding='2' cellspacing='0' width='100%'>	
				<tr><td width='53%' colspan='2' class='tete_colonne'>
				<div style='float:right;' title='"._T('dw:telech_du_jour')."'>[x]</div>";
		if($odb!='nom') {
			$lien=parametre_url(self(),'odb','');
			$lien=parametre_url(self(),'odb','nom');
			echo "<a href='".$lien."'>"._T('dw:nom_fiche')."</a>";
		} else {
			echo "<b>"._T('dw:nom_fiche')."</b>";
		}
		echo "</td><td width='23%' class='tete_colonne'>";
		if($odb!='dat') {
			$lien=parametre_url(self(),'odb','');
			$lien=parametre_url(self(),'odb','dat');
			echo "<a href='".$lien."'>"._T('dw:dernier_en_date')."</a>";

		} else {
			echo "<b>"._T('dw:dernier_en_date')."</b>";
		}
		echo "</td><td width='13%' class='tete_colonne'>";
		if($odb!='tot') {
			$lien=parametre_url(self(),'odb','');
			$lien=parametre_url(self(),'odb','tot');
			echo "<a href='".$lien."'>"._T('dw:compteur')."</a>";
		} else {
			echo "<b>"._T('dw:compteur')."</b>";
		}
		echo "</td><td width='11%' class='tete_colonne'>";
		if($odb!='moy') {
			$lien=parametre_url(self(),'odb','');
			$lien=parametre_url(self(),'odb','moy');
			echo "<a href='".$lien."'>"._T('dw:moyenne_jours')."</a>";
		} else {
			echo "<b>"._T('dw:moyenne_jours')."</b>";
		}
		echo "</td></tr>";
		
		while ($a_row=spip_fetch_array($rq_ttdoc))
			{
			$iddoc = $a_row['id_document'];
			$nom = $a_row['nom'];
			$url = $a_row['url'];
			$total = $a_row['total'];
			$datecrea = $a_row['datecrea'];
			$moyj=$a_row['moyj'];
			$der_date =$a_row['derdate'];
			
			// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 25 caract
			$nom = wordwrap($nom,25,' ',1);

			// telech du jour
			$res=spip_query("SELECT telech FROM spip_dw2_stats WHERE id_doc=$iddoc AND date=NOW()");
			$nl_res=spip_num_rows($res);
			if($nl_res=1) {
				$r_row=spip_fetch_array($res);
				$telech_jour = $r_row['telech'];
			}
			
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#ffffff' : $couleur_claire;
			
			// ligne du tableau
			echo "<tr bgcolor='$couleur'>";
			echo "<td width='4%' height='20'>";
			echo "<a href='".generer_url_ecrire("dw2_modif", "id=".$iddoc)."'>";
			echo "<img src='"._DIR_IMG_DW2."fiche_doc-15.gif' border='0' align='absmiddle' title='"._T('dw:voir_fiche')."'>";
			echo "</a>";
			echo "</td>";
			echo "<td width='49%'>";
				if($telech_jour)
					{ echo "<div style='float:right;'> [$telech_jour]</div>"; }
			echo "<div class='arial2' title='".$datecrea."'><b>";
				popup_stats_graph($iddoc,$nom);
			echo "</b></div></td>";
			echo "<td width='23%'><div align='center' class='arial2'>$der_date</div></td>\n";
			echo "<td width='13%'><div align='center' class='arial2'><b>$total</b></div></td>\n";
			echo "<td width='11%'><div align='center' class='arial2'>$moyj</div></td>\n";
			echo "</tr>\n";
			}
		echo "</table>";
		}
	fin_cadre_relief();


	//
	// graph stats globales base-105j ( <-- spip)
	//
	include(_DIR_PLUGIN_DW2."/inc/dw2_inc_stats.php");
	
	
	//
	//
	if($anti_triche=='oui') {
		debut_cadre_relief("");
		// Nbre de visit pour telech. ce jour
			$debj = getdate(time());
			// timestamp debut journée (00:00) + 24h
			$ts_debj = mktime(0,0,0,$debj[mon],$debj[mday],$debj[year])+86400;
			// temps T + 24h
			$ts_now = time()+86400;
	
			$query="SELECT COUNT(DISTINCT ip) AS nb_visit 
					FROM spip_dw2_triche WHERE time BETWEEN $ts_debj AND $ts_now";
			$result = spip_query($query);
			if ($rowv = @spip_fetch_array($result)) {
				$nb_visit = $rowv['nb_visit'];
			} else {
				$nb_visit = '0';
			}
		echo "<div class='verdana2' align='right'>";
		if($nb_visit<=1)
			{ echo _T('dw:telech_jour_par', array('nb_visit' => $nb_visit)); }
		else
			{ echo _T('dw:telech_jour_par_s', array('nb_visit' => $nb_visit)); }
		echo "</div>";
		fin_cadre_relief();
	}


	//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_

?>
