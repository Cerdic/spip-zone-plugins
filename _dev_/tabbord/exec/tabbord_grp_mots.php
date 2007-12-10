<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_grp_mots() {

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



// requete principale

$q = spip_query("SELECT SQL_CALC_FOUND_ROWS * FROM spip_groupes_mots 
				ORDER BY id_groupe 
				LIMIT $dl,$fl");

// récup nombre total d'entrée
	$nl= spip_query("SELECT FOUND_ROWS()");
	$r_found = @spip_fetch_array($nl);
	$nligne=$r_found['FOUND_ROWS()'];


//
// affichage
//

#debut_page(_L('tableau de Bord'), "suivi", "tabbord");
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

echo "<div style='width:650px;'>";

//
// onglets		
echo debut_onglet();
echo onglet(_L('liste mots'), generer_url_ecrire("tabbord_mots"), 'mots', '', "mot-cle-24.gif");
echo onglet(_L('Liste Groupes'), generer_url_ecrire("tabbord_grp_mots"), 'groupes', 'groupes', "groupe-mot-24.gif");
echo fin_onglet();
echo "<br />";

debut_cadre_formulaire();

// affichage tableau
	if (spip_num_rows($q)) {
		// valeur de tranche affichée	
		$nba1 = $dl+1;
		//	
		
		gros_titre(_L('Groupes Mots-Clefs ').$GLOBALS['meta']['nom_site']);
		
		// Présenter valeurs de la tranche de la requête
		echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
		tranches_liste($nba1,$nligne,$fl);
		echo "\n</div>\n";

		// entête ...
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n
			<tr>\n".
				
				"<th width='5%'>"._T('tabbord:id_mjsc')."</th>\n".
				"<th width='2%'>&nbsp;</th>\n".
				"<th width=21%>"._T('tabbord:groupe')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:unique')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:obligatoire_c')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:rubrique_c')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:article_c')."</th>\n".
				"<th class='center' width=8%>"._T('breve')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:syndic_c')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:forum')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:admin')."</th>\n".
				"<th class='center' width=8%>"._T('tabbord:Redac.')."</th>\n".
				
			"</tr>\n";

		// corps du tableau
		while ($r=spip_fetch_array($q)) {
			$id = $r['id_groupe'];
			$titre = typo($r['titre']);
					
			echo "<tr class='liste'>".
				"<td class='right'>".$id."</td>".
				"<td class='right'>&nbsp;</td>".
				"<td>".couper($titre,40)."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['unseul'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['obligatoire'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['rubriques'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['articles'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['breves'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['syndic'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['forum'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['minirezo'])."</td>".
				"<td class='center'>".icone_statut_objet_tabbord('mot',$r['comite'])."</td>".
				
				"</tr>";
		
		}
		echo "<tr><td colspan='12'>";
		echo icone_statut_objet_tabbord('mot','oui')." : "._T('tabbord:oui')."&nbsp;&middot;|&middot;&nbsp;\n";
		echo icone_statut_objet_tabbord('mot','non')." : "._T('tabbord:non')."&nbsp;&middot;|&middot;&nbsp;\n";
		echo icone_statut_objet_tabbord('mot','')." : "._T('tabbord:non_def')."\n";
		echo "</table>\n";
	}
	else {
		echo _T('tabbord:aucun_mot_sur_site');
	}

fin_cadre_formulaire();
echo "</div>";

//
//
echo fin_gauche(), fin_page();
}
?>
