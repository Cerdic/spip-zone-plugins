<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Affiche tableaux Rubrique, Articles, Breves
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_liste() {

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


// type de liste produite
$objet=_request('objet');
if($objet=='rubrique') {
	$seq = "id_rubrique, id_parent as parent,";
	$date = "date";
	$lien = "naviguer";
}
elseif($objet=='article') {
	$seq = "id_article, id_rubrique as parent,";
	$date = "date";
	$lien = "articles";
}
elseif($objet=='breve') {
	$seq = "id_breve, id_rubrique as parent,";
	$date = "date_heure";
	$lien = "breves_voir";
}

# tri
if(!_request('odb')) {
	$odb = "id_".$objet;
}
else { $odb=_request('odb'); }

// requete principale
$q = spip_query("SELECT SQL_CALC_FOUND_ROWS  $seq titre, statut, 
				DATE_FORMAT($date,'%d/%m/%Y') as datepub
				FROM spip_".$objet."s 
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

echo "<div style='width:650px;'>";
debut_cadre_formulaire();

// affichage tableau
	if (spip_num_rows($q)) {
		// valeur de tranche affichée	
		$nba1 = $dl+1;
		//	
		
		gros_titre(_T('tabbord:'.$objet.'_s'));
		
		// Présenter valeurs de la tranche de la requête
		echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
		tranches_liste($nba1,$nligne,$fl);
		echo "\n</div>\n";

		// entête ...
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n
			<tr>\n".
				"<th width='7%'>";
				if($odb!='id_'.$objet) {
					echo "<a href='".generer_url_ecrire(_request('exec'),"objet=".$objet."&odb=id_".$objet)."' title='"._T('tabbord:tri_par_id')."'>"._T('tabbord:id_mjsc')."</a>";
				} else { echo ">"._T('tabbord:id_mjsc')."<"; }
				echo "</th>\n".
				"<th width=3%>&nbsp;</th>\n".
				"<th width='39%'>";
				if($odb!='titre') {
					echo "<a href='".generer_url_ecrire(_request('exec'),"objet=".$objet."&odb=titre")."' title='"._T('tabbord:tri_par_titre')."'>"._T('tabbord:titre')."</a>";
				} else { echo ">"._T('tabbord:titre')."<"; }

				echo "</th>\n".
				"<th width=39%>";
				if($odb!='parent') {
					echo "<a href='".generer_url_ecrire(_request('exec'),"objet=".$objet."&odb=parent")."' title='"._T('tabbord:tri_par_parent')."'>"._T('tabbord:parent')."</a>";
				} else { echo ">"._T('tabbord:parent')."<"; }
				echo "</th>\n".
				"<th width=12%>";
				if($odb!=$date) {
					echo "<a href='".generer_url_ecrire(_request('exec'),"objet=".$objet."&odb=".$date)."' title='"._T('tabbord:tri_par_date')."'>"._T('tabbord:cree_le')."</a>";
				} else { echo ">"._T('tabbord:cree_le')."<"; }
				echo "</th>\n".
				
			"</tr>\n";

		// corps du tableau
		while ($r=spip_fetch_array($q)) {
			$id = $r['id_'.$objet];
			$parent = $r['parent'];
			$titre = typo($r['titre']);
			$date = $r['datepub'];
			$statut = $r['statut'];
			
			$rp=spip_fetch_array(spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique=$parent"));
			$titre_parent= typo($rp['titre']);
			
			echo "<tr class='liste'>".
				"<td class='right'>$id</td>".
				"<td class='center'>".icone_statut_objet_tabbord($objet,$statut)."</td>".
				"<td><a href='".generer_url_ecrire($lien,"id_".$objet."=".$id)."' title='$titre'>".
					couper($titre,40)."</a></td>".
				"<td><a href='".generer_url_ecrire("naviguer","id_rubrique=".$parent)."' title='$titre_parent'>".
					couper($titre_parent,40)."</a></td>".
				"<td>$date</td>".
				
				"</tr>";
		
		}

		echo "</table>\n";
	}
	else {
		echo _T('tabbord:pas_elem_sur_site');
	}

fin_cadre_formulaire();
echo "</div>";

//
//
echo fin_gauche(), fin_page();
}
?>
