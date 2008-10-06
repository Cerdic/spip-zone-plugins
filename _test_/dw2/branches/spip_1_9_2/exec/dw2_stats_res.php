<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| statistiques abonnes - visiteur (auteurs)
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/date');

function exec_dw2_stats_res() {

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

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");

include_spip("inc/dw2_inc_statres");

//config
$nbr_lignes_tableau = $GLOBALS['dw2_param']['nbr_lignes_tableau'];


// reconstruire .. var=val des get et post
// var : vl ;
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//
// prepa
//
	
	// recup' nombre de ligne passe en url, fixe debut LIMIT ...		
	$dl=($vl+0);
	
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
	$prdd=$annee1."-".$mois1."-".$jour1;
	$prdf=$annee2."-".$mois2."-".$jour2;
		
	//
	// premiere date des stats (annee) --> pour dates : selecteur annee
	$tbl_prem_date = premiere_date_stats_dw2();
	$annee_select = $tbl_prem_date[0];
	$debut_stats = $tbl_prem_date[1];
	
	
	// valeurs generales 
	//
	# totaux
	$ttgen = totaux_restreint_stats($prdd,$prdf);
	$tot_auteur=$ttgen[0];
	$tot_telech=$ttgen[1];
	$tot_fichier=$ttgen[2];
	
	# prepa tri alpha
	$tbl_ltt=array();
	$tbl_ltt = alpha_restreint_item($prdd,$prdf,'auteurs');
	
	
	#tri alpha
	if(isset($wltt)) {
		$where_ltt = "AND UPPER(sa.nom) LIKE '$wltt%'";
		// redefine :
		$tot_auteur=$tbl_ltt[$wltt];
	}
	
	// requete principale
	//	
	$rq_aut=spip_query("SELECT COUNT(DISTINCT ds.id_doc) as nb_doc, ds.id_auteur, ".
						"sa.nom as n_auteur, sa.statut ".
						"FROM spip_dw2_stats_auteurs as ds, spip_dw2_doc as dd, spip_auteurs as sa ".
						"WHERE $where_date AND ds.id_auteur=sa.id_auteur $where_ltt ".
						"GROUP BY ds.id_auteur ORDER BY sa.nom LIMIT $dl,$nbr_lignes_tableau");


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

	
	//
	// onglets choix periode + stats doc		
	echo debut_onglet().
	onglet(_T('dw:stats_des_visiteurs'), generer_url_ecrire("dw2_stats_res"), 'aff_resaut', 'aff_resaut', "auteur-24.gif").
	onglet(_T('dw:stats_des_docs'), generer_url_ecrire("dw2_stats_resdoc"), 'aff_resdoc', '', _DIR_IMG_DW2."fiche_doc.gif").
	fin_onglet();

	debut_band_titre($couleur_foncee);
		echo "<div align='center' class='verdana3'><b>"._T('dw:stats_abonnes_les_visiteurs')."</b></div>";
	fin_bloc();	
		
	// selecteur periode
	debut_cadre_relief("rien.gif");
		
		// premiere date stats DW2
		debut_boite_filet('a','center');
		echo "<span class='verdana3'>".
			_T('dw:premiere_date_stats_site', array('prem_date' => affdate_base($debut_stats,'entier'))).
			"</span>";
		fin_bloc();
	
	formulaire_periode($periode['date1'],$periode['date2'],$annee_select,_request('exec'));

	fin_cadre_relief();
	
	
	debut_cadre_relief(_DIR_IMG_PACK."statistiques-24.gif");

	
	
	//
	// aff date selection
	//
	debut_cadre_relief("");
		echo "<div style='text-align:center;'>".
			_T('dw:selection')." :<br />".
			(($wltt)? _T('dw:abonne_s')." : [<b> $wltt </b>]" : '')."<br />".
			$aff_date1. (($aff_date2)? "&nbsp;&nbsp;:|:&nbsp;&nbsp;".$aff_date2 : '' )."<br />".
			_T('dw:stats_info_totaux', array('tot_auteur'=>$tot_auteur,'tot_telech'=>$tot_telech,'tot_fichier'=>$tot_fichier)).
			"</div>";
	fin_cadre_relief();
	
	
	if (!$tot_auteur)
		{
		echo _T('dw:stats_abonnes_personne');
		}
	else
		{
		// premiere val de tranche en cours
		$nba1 = $dl+1;

		
		debut_band_titre("#dfdfdf");
			echo "<div align='center' class='verdana2'>\n";
			tranches($nba1, $tot_auteur, $nbr_lignes_tableau);
			echo "</div>\n";
		fin_bloc();
		
		// affichage lettres pour tri-alphabetique
		
		echo "<div class='verdana2'>";
		bouton_tout_catalogue($page_affiche,"&prdd=".$prdd."&prdf=".$prdf);
		reset ($tbl_ltt);
		while (list($k,$v) = each($tbl_ltt)) {
			echo "<a href='".generer_url_ecrire("dw2_stats_res", "wltt=".$k."&prdd=".$prdd."&prdf=".$prdf)."' title='"._T('dw:abonne_s')." : $v'>\n";
			echo bouton_alpha($k);
			echo "</a>\n";
		}
		echo "</div><div style='clear:both;'></div>";	
		//

		$ifond = 0;
		
	
		// Entete tableau ..
		echo "<table align='center' border='0' cellpadding='2' cellspacing='0' width='100%'>	
				<tr><td width='85%' colspan='2' class='tete_colonne'>";

		echo "<b>"._T('dw:visiteurs_abonnes')."</b>";
		if(isset($wltt))
			{ echo ".. [ <b>".$wltt."</b> ]"; }

		echo "</td>";

		echo "<td width='15%' class='tete_colonne'>";

		echo "<b>"._T('dw:telechargements')."</b>";

		echo "</td></tr>";
		
		while ($a_row=spip_fetch_array($rq_aut))
			{
			$id_auteur = $a_row['id_auteur'];
			$nom = $a_row['n_auteur'];
			$statut = $a_row['statut'];
			$nb_doc = $a_row['nb_doc'];
			
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#ffffff' : $couleur_claire;
			
			// ligne du tableau
			echo "<tr bgcolor='$couleur'>";
			echo "<td width='4%' height='20'>";
			
			echo "<a href='".generer_url_ecrire("auteurs_edit", "id_auteur=".$id_auteur)."'>";
			echo bonhomme_statut($a_row);
			echo "</a>";
			echo "</td>";
			echo "<td width='80%'>";
				
			echo "<div class='arial2'><b>".$nom."</b></div>";
				
			echo "</td>";

			echo "<td width='15%'><div align='center' class='arial2'><b>$nb_doc</b></div></td>\n";

			echo "</tr>\n";
			}
		echo "</table>";
		}
	fin_cadre_relief();


//

	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_

?>
