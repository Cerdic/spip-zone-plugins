<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.52 - 08/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Divers topTen, 8, 30, general
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');


function exec_actijour_top() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

//
// function requises ...
include_spip("inc/func_acj");


//
//
//

debut_page(_T('acjr:titre_actijour'), "suivi", "actijour");
	echo "<br>";
gros_titre(_T('acjr:titre_actijour'));


// Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques)
	{
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
	}

//
// onglets
echo debut_onglet().
onglet(_T('acjr:page_activite'), generer_url_ecrire("actijour_pg"), 'page_activite', '', _DIR_PLUGIN_ACTIJOUR."/img_pack/activ_jour.gif").
onglet(_T('acjr:page_hier'), generer_url_ecrire("actijour_hier"), 'page_hier', '', _DIR_PLUGIN_ACTIJOUR."/img_pack/activ_hier.gif").
onglet(_T('acjr:page_topten'), generer_url_ecrire("actijour_top"), 'page_topten', 'page_topten', "article-24.gif").
fin_onglet();

debut_gauche();


creer_colonne_droite();

//
// bon là ... j'assume mon gag ! sisi !
debut_boite_info();
	echo _T('acjr:signature_plugin')."\n";
fin_boite_info();

echo "<br />";

// info sur colonnes
debut_boite_info();
	echo _T('acjr:info_colonnes_topten')."\n";
fin_boite_info();



debut_droite();

//
// classement des 10 articles les + visités sur 8 jours
//
debut_cadre_relief("article-24.gif");

	$query="SELECT sva.id_article, SUM(sva.visites) AS volume, MAX(sva.visites) AS picvis, sa.statut, sa.titre, sa.visites ".
			"FROM spip_visites_articles sva LEFT JOIN spip_articles sa ON sva.id_article=sa.id_article ".
			"WHERE sa.statut='publie' AND sva.date > DATE_SUB(NOW(),INTERVAL 8 DAY) ".
			"GROUP BY sva.id_article ORDER BY volume DESC LIMIT 0,10";
	$result = spip_query($query);
	
	$ifond = 0;
	echo "<table align='center' border='0' cellpadding='2' cellspacing='1' width='100%'>\n";
	echo "<tr><td colspan='5' class='cart_titre verdana3 bold'>"._T('acjr:top_ten_article_8_j');
	echo "</td></tr>";
	
	while ($row = spip_fetch_array($result))
		{
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		echo "
		<tr bgcolor='$couleur'><td width='7%'>\n
			<div align='right' class='verdana2'>".
			affiche_lien_graph($row['id_article'],$row['titre'],$row['statut'],'spip').
			"</div>\n
		</td><td width='70%'>\n
            <div align='left' class='verdana2'><b>".
			affiche_lien_graph($row['id_article'],$row['titre'],$row['statut']).
			"</b></div>\n
        </td><td width='6%'>\n
            <div align='right' class='verdana1' style='margin-right:3px;'><b>$row[volume]</b></div>\n
        </td><td width='7%'>\n
		        <div align='right' class='verdana1' style='margin-right:3px;'><b>$row[picvis]</b></div>\n
        </td><td width='10%'>\n    
            <div align='right' class='verdana1' style='margin-right:3px;'><b>$row[visites]</b></div>\n
        </td></tr>\n";
		}
		echo "</table>";
fin_cadre_relief();


//
// classement des 10 articles les + visités sur 30 jours
//
debut_cadre_relief("article-24.gif");

	$query="SELECT sva.id_article, SUM(sva.visites) AS volume, MAX(sva.visites) AS picvis, sa.titre, sa.statut, sa.visites ".
			"FROM spip_visites_articles sva LEFT JOIN spip_articles sa ON sva.id_article=sa.id_article ".
			"WHERE sa.statut='publie' AND sva.date > DATE_SUB(NOW(),INTERVAL 30 DAY) ".
			"GROUP BY sva.id_article ORDER BY volume DESC LIMIT 0,10";
	$result = spip_query($query);
	
	$ifond = 0;
	echo "<table align='center' border='0' cellpadding='2' cellspacing='1' width='100%'>\n";
	echo "<tr><td colspan='5' class='cart_titre verdana3 bold'>"._T('acjr:top_ten_article_30_j');
	echo "</td></tr>";
	
	while ($row2 = spip_fetch_array($result))
		{
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		echo "
		<tr bgcolor='$couleur'><td width='7%'>\n
			<div align='right' class='verdana2'>".
			affiche_lien_graph($row2['id_article'],$row2['titre'],$row2['statut'],'spip').
			"</div>\n
		</td><td width='70%'>\n
            <div align='left' class='verdana2'><b>".
			affiche_lien_graph($row2['id_article'],$row2['titre'],$row2['statut']).
			"</b></div>\n
        </td><td width='6%'>\n
            <div align='right' class='verdana1' style='margin-right:3px;'><b>$row2[volume]</b></div>\n
        </td><td width='7%'>\n
		        <div align='right' class='verdana1' style='margin-right:3px;'><b>$row2[picvis]</b></div>\n
        </td><td width='10%'>\n    
            <div align='right' class='verdana1' style='margin-right:3px;'><b>$row2[visites]</b></div>\n
        </td></tr>\n";
		}
		echo "</table>";
fin_cadre_relief();

//
// classement des 10 articles .. general
//
debut_cadre_relief("article-24.gif");

	$query="SELECT id_article, titre, statut, visites ".
			"FROM spip_articles WHERE statut='publie' ".
			"ORDER BY visites DESC LIMIT 0,10";
	$result = spip_query($query);
	
	$ifond = 0;
	echo "<table align='center' border='0' cellpadding='2' cellspacing='1' width='100%'>\n";
	echo "<tr><td colspan='3' class='cart_titre verdana3 bold'>"._T('acjr:top_ten_article_gen');
	echo "</td></tr>";
	
	while ($row = spip_fetch_array($result))
		{
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		echo "
		<tr bgcolor='$couleur'><td width='7%'>\n
			<div align='right' class='verdana2'>".
			affiche_lien_graph($row['id_article'],$row['titre'],$row['statut'],'spip').
			"</div>\n
		</td><td width='80%'>\n
            <div align='left' class='verdana2'><b>".
			affiche_lien_graph($row['id_article'],$row['titre'],$row['statut']).
			"</b></div>\n
        </td><td width='13%'>\n
            <div align='right' class='verdana1' style='margin-right:3px;'><b>$row[visites]</b></div>\n
        </td></tr>\n";
		}
		
		echo "</table>";
fin_cadre_relief();


echo fin_gauche(), fin_page();

} // fin fonction
?>
