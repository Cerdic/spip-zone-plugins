<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Affiche Admin restreint (rubriques liees)
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_auteurs() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;


//
// requis
//
include_spip('inc/tabbord_pres');


//
// prepa
//

	// fixer le nombre de ligne du tableau (tranche)
	$fl=20;

	// recup $vl dans URL
	$dl=($_GET['vl']+0);

	// tri
	$tri=_request('tri');
	if($tri=='nom') { $odb='nom'; }
	else { $odb='id_auteur'; }
	
	// selection statut
	$spe=false;
	$tp = _request('tp');
	if(!$tp) { $tp='0minirezo'; $where = "statut='0minirezo'"; $ttr_page=_T('tabbord:admin_s'); }
	elseif($tp=='1comite') { $where = "statut='1comite'"; $ttr_page=_T('tabbord:redacteur_s'); }
	elseif($tp=='6forum') { $where = "statut='6forum'"; $ttr_page=_T('tabbord:visiteur_s'); }
	elseif($tp=='5poubelle') { $where = "statut='5poubelle'"; $ttr_page=_T('tabbord:efface_s'); }
	elseif($tp=='autres') {
		$where = "statut NOT IN ('0minirezo','1comite','6forum','5poubelle')";
		$ttr_page=_T('tabbord:autre_s');
		$spe=true;
	}
	
	

// requete principale
$q=spip_query("SELECT SQL_CALC_FOUND_ROWS id_auteur, nom, statut 
				FROM spip_auteurs 
				WHERE $where 
				ORDER BY $odb 
				LIMIT $dl,$fl");

// recup nombre total d'entree
	$nl= spip_query("SELECT FOUND_ROWS()");
	$r_found = @spip_fetch_array($nl);
	$nligne=$r_found['FOUND_ROWS()'];


//
// affichage
//
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('tabbord:titre_plugin'), "suivi", "tabbord_gen", '');
	echo "<br />";


// Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques)
	{
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
	}
	

debut_gauche();

menu_gen_tabbord();


debut_droite();

//
// onglets		
echo debut_onglet();
echo onglet(_T('tabbord:admin_s'), generer_url_ecrire("tabbord_auteurs"), '0minirezo', $tp, "admin-12.gif");
echo onglet(_T('tabbord:redacteur_s'), generer_url_ecrire("tabbord_auteurs", "tp=1comite"), '1comite', $tp, "redac-12.gif");
echo onglet(_T('tabbord:visiteur_s'), generer_url_ecrire("tabbord_auteurs", "tp=6forum"), '6forum', $tp, "visit-12.gif");
echo onglet(_T('tabbord:efface_s'), generer_url_ecrire("tabbord_auteurs", "tp=5poubelle"), '5poubelle', $tp, "poubelle.gif");
echo onglet(_T('tabbord:autre_s'), generer_url_ecrire("tabbord_auteurs", "tp=autres"), 'autres', $tp, "aide.gif");
echo fin_onglet();
echo "<br />";


#echo "<div style='width:650px;'>";
debut_cadre_formulaire();

// affichage tableau

	// valeur de tranche affichée	
	$nba1 = $dl+1;
	//	
		
	gros_titre($ttr_page." - ".$GLOBALS['meta']['nom_site']);
		
		// Présenter valeurs de la tranche de la requête
		echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
		tranches_liste($nba1,$nligne,$fl);
		echo "\n</div>\n";

		// entête ...
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n
			<tr>\n".
				"<td width='7%'>";
				if($odb=='id_auteur') { echo "<b>&gt;"._T('tabbord:id_mjsc')."&lt;</b>"; }
				else { echo "<a href='".parametre_url(self(),'tri','')."' title='"._T('tabbord:tri_par_id')."'>"._T('tabbord:id_mjsc')."</a>"; }
				echo "</td>\n".
				"<td width=2%>&nbsp;</td>\n".
				"<td width='31%'>";
				if($odb=='nom') { echo "<b>&gt;"._T('tabbord:nom')."&lt;</b>"; }
				else { echo "<a href='".parametre_url(self(),'tri','nom')."' title='"._T('tabbord:tri_par_nom')."'>"._T('tabbord:nom')."</a>"; }
				echo "</td>\n".
				"<td width=60%>";
				if($tp=='0minirezo') { echo _T('tabbord:admin_de_pt'); }
				if($spe) { echo _T('tabbord:statut_pt'); }
				echo "</td>\n";
		echo "</tr>\n";

		// corps du tableau
		while ($r=spip_fetch_array($q)) {
			
			echo "<tr class='liste'>".
				"<td class='right'>".$r['id_auteur']."</td>".
				"<td class='center'>&nbsp;</td>".
				"<td><a href='".generer_url_ecrire("auteur_infos","id_auteur=".$r['id_auteur'])."'>".$r['nom']."</a></td>".
				"<td>";
			
			if($tp=='0minirezo') {
				# rubriques de restriction
				$qa = spip_query("SELECT sar.id_rubrique, sr.titre, sr.id_parent 
								FROM spip_auteurs_rubriques as sar 
								LEFT JOIN spip_rubriques as sr ON sar.id_rubrique=sr.id_rubrique 
								WHERE sar.id_auteur=".$r['id_auteur']);
				if(spip_num_rows($qa)) {
					while($ra=spip_fetch_array($qa)) {
						if($ra['id_parent']=='0') { $ico = http_img_pack('secteur-12.gif','ico','',_T('tabbord:secteur')); }
						else { $ico = http_img_pack('rubrique-12.gif','ico','',_T('tabbord:rubrique')); }
						
						echo $ico."&nbsp;<a href='".generer_url_ecrire('naviguer','id_rubrique='.$ra['id_rubrique'])."'>".
							typo($ra['titre'])."</a><br />";
					}
				}
				else { echo http_img_pack('admin-12.gif','ico','','')." "._T('tabbord:toutes_rubriques'); }
			}
			if($spe) {
				echo $r['statut'];
			}
			echo "</td></tr>";
		}
		echo "</table>\n";


fin_cadre_formulaire();
#echo "</div>";

//
//
echo fin_gauche(),fin_page();
}
?>
