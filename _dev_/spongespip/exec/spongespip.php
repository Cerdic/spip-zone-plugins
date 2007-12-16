<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
if (!_request("onglet"))
{

	//include_spip('inc/presentation');
	function exec_spongespip_dist()
	{

	  global $connect_statut, $connect_toutes_rubriques, $spip_ecran, $taille,
		$abs_total, $nombre_vis, $critere;

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('spongespip:titre_page_spongespip'), "spongespip", "repartition");
		//Pour que les admins & les rédacteurs aient accès aux stats
		if (($GLOBALS['connect_statut'] != "1comite")&&($GLOBALS['connect_statut'] != "0minirezo")){
			echo _T('avis_non_acces_page');
			exit;
		}
	gros_titre(_T('titre_page_spongespip'));
	echo "\n<table width='$largeur_table'><tr><td class='verdana2' style='text-align: center;  width: $largeur_table" . "px;'>";
	echo spongespip_onglets();
	debut_gauche();
	gros_titre(_T('Archives'));

	debut_boite_info();
	echo "<p style='font-size:small; text-align:left;' class='verdana1'>"._T('menu_1')."</p>";
	fin_boite_info();
	debut_droite();
	fin_gauche();
	debut_cadre_relief();
	include_spip("inc/spongespip_fonctions");
	//include_spip("inc/home");
	$liste_pages=array("stats_mois","pages","hotes","referents","plateformes","mots_cles");
	echo "<div id=\"ctn_sps\">";

					$include_name="home";
					include_spip("inc/".$include_name);
	echo "</div>";
	echo "<div id=\"chargement\">Chargement en cours</div>";
	fin_cadre_relief();
		echo "</td></tr></table>";
		echo fin_page();
	}
}
else
{
		function exec_spongespip_dist()
	{
	include_spip("inc/spongespip_fonctions");
	include_spip("inc/"._request("onglet"));
	}
}
?> 