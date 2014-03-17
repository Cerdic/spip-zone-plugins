<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Lister les mots-cles du site
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_mots() {

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

if(!_request('odb')) {
	$odb = "id_mot";
}
else { $odb=_request('odb'); }

// requete principale
$q = spip_query("SELECT SQL_CALC_FOUND_ROWS  id_mot, titre, type, id_groupe 
				FROM spip_mots 
				ORDER BY $odb 
				LIMIT $dl,$fl");

// récup nombre total d'entrée
	$nl= spip_query("SELECT FOUND_ROWS()");
	$r_found = @spip_fetch_array($nl);
	$nligne=$r_found['FOUND_ROWS()'];


//
// affichage
//

#debut_page(_T('tabbord:titre_plugin'), "suivi", "tabbord");
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
echo onglet(_L('liste mots'), generer_url_ecrire("tabbord_mots"), 'mots', 'mots', "mot-cle-24.gif");
echo onglet(_L('Liste Groupes'), generer_url_ecrire("tabbord_grp_mots"), 'groupes', '', "groupe-mot-24.gif");
echo fin_onglet();
echo "<br />";

debut_cadre_formulaire();

// affichage tableau
	if (spip_num_rows($q)) {
		// valeur de tranche affichée	
		$nba1 = $dl+1;
		//	
		
		gros_titre(_T('tabbord:mot_clef_s'));
		
		// Présenter valeurs de la tranche de la requête
		echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
		tranches_liste($nba1,$nligne,$fl);
		echo "\n</div>\n";

		// entête ...
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n
			<tr>\n".
				"<th width='10%'>";
				
			if($odb!='id_mot') {
				echo "<a href='".generer_url_ecrire(_request('exec'),"odb=id_mot")."' title='"._T('tabbord:tri_par_id')."'>"._T('tabbord:id_mjsc')."</a>";
			} else { echo _T('tabbord:id_mjsc'); }

			echo "</th>\n".
				"<th width='2%'>&nbsp;</th>\n".
				"<th width='45%'>";
				
			if($odb!='titre') {
				echo "<a href='".generer_url_ecrire(_request('exec'),"odb=titre")."' title='"._T('tabbord:tri_par_titre')."'>"._T('tabbord:titre')."</a>";
			} else { echo _T('tabbord:titre'); }
			
			echo "</th>\n";
			echo "<th width=43%>";
				
			if($odb!='id_groupe') {
				echo "<a href='".generer_url_ecrire(_request('exec'),"odb=id_groupe")."' title='"._T('tabbord:tri_par_groupe')."'>"._T('tabbord:groupe')."</a>";
			} else { echo _T('tabbord:groupe'); }

			
			echo "</th>\n".
			"</tr>\n";

		// corps du tableau
		while ($r=spip_fetch_array($q)) {
			$id = $r['id_mot'];
			$titre = typo($r['titre']);
			$type = typo($r['type']);
			
					
			echo "<tr class='liste'>".
				"<td class='right'>".$id."</td>".
				"<td class='right'>&nbsp;</td>".
				"<td>".couper($titre,40)."</td>".
				"<td title='"._T('tabbord:groupe_id_', array('id_groupe'=>$r['id_groupe']))."'>".couper($type,40)."</td>".
				"</tr>";
		
		}

		echo "</table>\n";
	}
	else {
		echo _T('tabbord:pas_mot_cle_sur_site');
	}

fin_cadre_formulaire();

//
//
echo fin_gauche(), fin_page();
}
?>
