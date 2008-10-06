<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Categories.
| Liste categories , modif intitule, stats categories
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_categories() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');

//
//requis ...
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");


// reconstruire .. var=val des get et post
// var : modif_categ ; nouv_categ ; anc_categ ;
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//
// prepa 
//

// Total Compteurs par Catégories, Modif Cat', type fichiers
$req1="SELECT categorie, COUNT(id_document) AS nbr_doc, SUM(total) AS tt_cat ".
		"FROM spip_dw2_doc WHERE statut='actif' GROUP BY categorie ORDER BY tt_cat DESC";
$res1=spip_query($req1);
$nbcat=spip_num_rows($res1);


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

if ($nbcat==0)
	{
	debut_cadre_relief(_DIR_IMG_PACK."statistiques-24.gif");
	echo "<div class='verdana3 bold center'><br><b>"._T('dw:txt_cat_aucun')."<br><br><br>";
	echo "<a href='".generer_url_ecrire("dw2_ajouts")."'>"._T('dw:ajout_doc')."</a></div><br>";
	fin_cadre_relief();
	}
else
	{
	debut_cadre_trait_couleur("statistiques-24.gif", false, "", _T('dw:txt_categ_trt'));

	$ifond = 0;
	echo "<table width='100%' align='center' border='0' cellpadding='0' cellspacing='0'>".
		"<tr class='cadre-couleur'>\n".
		"<td width='65%' colspan='2'><div class='cadre-padding verdana2'>"._T('dw:categorie')."</div></td>\n".
		"<td width='15%'><div class='verdana2 center'>"._T('dw:nbre_docs')."</div></td>\n".
		"<td width='20%'><div class='verdana2 center'>"._T('dw:compteur')."</div></td>\n".
		"</tr>";
		$add_totaux = array();
		while ($ligne1=spip_fetch_array($res1))
			{
			$nomcat=$ligne1['categorie'];
			$nbrdoc=$ligne1['nbr_doc'];
			$ttcat=$ligne1['tt_cat'];
			$add_totaux[] = $ttcat;
			
			$bouton = bouton_block_invisible('bout'.$nomcat);
			
			$ifond = $ifond ^ 1;
			$bgcolor = ($ifond) ? '#ffffff' : $couleur_claire;
			
			echo "<tr bgcolor='$bgcolor' class='arial2 cadre-padding'>\n
				<td width='5%'>".$bouton."</td>\n
				<td width='60%'><div class='cadre-padding center bold'>".$nomcat."</div></td>\n
				<td width='15%'><div class='cadre-padding center'>".$nbrdoc."</div></td>\n
				<td width='20%'><div class='cadre-padding center bold'>".$ttcat."</div></td>\n
				</tr>\n";
			echo "<tr bgcolor='$bgcolor'><td colspan='4'>\n";
			
			echo debut_block_invisible('bout'.$nomcat);
			echo "<span class='verdana2 bold'>"._T('dw:modif_nom_categ')."</span>\n";
			echo "<br><span class='arial2'>"._T('dw:txt_categ_01')."</span><br><br>\n";
			
			echo "<form action='".generer_url_action("dw2actions", "arg=modifiercategorie-".$nomcat)."' method='post' class='cadre-padding'>\n";
			echo _T('dw:nouveau_nom')." : ";
			echo "<input type='text' name='nouv_categ' value='".$nomcat."' size='40' class='fondl'>\n";
			echo "&nbsp;&nbsp;<input type='submit' value='"._T('dw:modifier')."' class='fondo'>\n";
			echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_categories")."' />\n";
			echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-modifiercategorie-".$nomcat)."' />";
			echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
			echo "</form>";
			
			echo fin_block();		
			echo "</td></tr>\n";			
			}
		reset($add_totaux);
		echo "<tr bgcolor='$couleur_foncee'><td colspan='3'>";
		echo "<div class='bloc_bouton_r bold' style='color:#ffffff;'>"._T('dw:total_compteurs')."</div></td>";
		echo "<td width='20%'><div style='color:#ffffff;' class='verdana2 center bold'>".array_sum($add_totaux)."</div></td>";
		echo "</tr></table>";

	fin_cadre_trait_couleur();


	// Nbre et Type d'extension fichier des catégories
	$req2="SELECT categorie, SUBSTRING_INDEX(url, '.', -1) AS typefich, COUNT(*) AS nbtype ".
		"FROM spip_dw2_doc WHERE statut='actif' GROUP BY categorie, typefich";
	$res2=spip_query($req2);

	debut_cadre_trait_couleur("doc-24.gif", false, "", _T('dw:types_fich_cat'));

	echo "<table width='100%' align='center' border='0' cellpadding='2' cellspacing='0'>\n";
	while ($lig=spip_fetch_array($res2))
		{
		$cat=$lig['categorie'];
		$type_fichier=strtoupper($lig['typefich']);
		$nb_type=$lig['nbtype'];
		
		$ifond = $ifond ^ 1;
		$bgcolor = ($ifond) ? '#ffffff' : $couleur_claire;
		
		echo "<tr class='verdana2' bgcolor='$bgcolor'>\n".
		"<td width='10%'><div align='right'><b>$nb_type</b></div></td>\n".
		"<td width='20%'><div align='center'>[ .$type_fichier ]</div></td>\n".
		"<td width='70%'>$cat</td>".
		"</tr>";
		}
	echo "</table>\n";

	// total des types fichiers
	$req3="SELECT SUBSTRING_INDEX(url, '.', -1) AS typefich, COUNT(*) AS nbtype ".
			"FROM spip_dw2_doc WHERE statut='actif' GROUP BY typefich";
	$res3=spip_query($req3);
	
	echo "<table width='100%' align='center' border='0' cellpadding='2' cellspacing='0'>\n";
	echo "<tr><td colspan='3'>";
	debut_band_titre("#dfdfdf");
	echo _T('dw:total_type');
	fin_bloc();
	echo "</td></tr>\n";
	
	while ($li=spip_fetch_array($res3))
		{
		$type_fichier=strtoupper($li['typefich']);
		$nb_type=$li['nbtype'];
		
		$ifond = $ifond ^ 1;
		$bgcolor = ($ifond) ? '#ffffff' : $couleur_claire;
		
		echo "<tr class='verdana2' bgcolor='$bgcolor'>".
		"<td width='10%'><div align='right'><b>$nb_type</b></div></td>\n".
		"<td width='20%'><div align='center'>[ .$type_fichier ]</div></td>\n".
		"<td width='70%'><div align='center'></div></td>\n".
		"</tr>";
		}
	echo "</table><br>\n";
	
	fin_cadre_trait_couleur();

	}

//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
