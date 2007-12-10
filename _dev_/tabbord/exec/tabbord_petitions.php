<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Liste des petitions (intitule + nb signatures)
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_petitions() {

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

$qp=spip_query("SELECT sp.id_article, sp.texte, sa.titre 
				FROM spip_petitions as sp LEFT JOIN spip_articles as sa 
				ON sp.id_article=sa.id_article ORDER BY sp.id_article");




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

	// valeur de tranche affichée	
	$nba1 = $dl+1;
	//	
		
	gros_titre(_T('tabbord:petition_s'));
		
	if($nligne=spip_num_rows($qp)) {		
		// Présenter valeurs de la tranche de la requête
		echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
		tranches_liste($nba1,$nligne,$fl);
		echo "\n</div>\n";

	// entête ...
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n
		<tr>\n".
		
		"<th colspan='2' width='45%'>"._T('tabbord:article')."</th>\n".
		"<th class='center' width=45%>"._T('tabbord:intitule')."</th>\n".
		"<th class='center' width=10%>"._T('tabbord:signature_s')."</th>\n".
		"</tr>\n";

	while($a=spip_fetch_array($qp)) {
		$id_art=$a['id_article'];
		$texte=typo($a['texte']);
		$titre=typo($a['titre']);
		$ns=spip_fetch_array(spip_query("SELECT COUNT(id_signature) as sign 
										FROM spip_signatures 
										WHERE id_article=$id_art AND statut='publie'"));
		$nb_sign=$ns['sign'];
		
		echo "<tr class='liste'>".
			"<td valign='top' class='right'>$id_art</td>".
			"<td valign='top'><a href='".generer_url_ecrire("articles","id_article=".$id_art)."'>".$titre."</a></td>".
			"<td>".$texte."</td>".
			"<td valign='top' class='center'>".$nb_sign."</td>".
			"</tr>";
	}
	echo "</table>";
}
else {
	echo _T('tabbord:pas_petitions_sur_site');
}

fin_cadre_formulaire();
echo "</div>";

//
//
echo fin_gauche(), fin_page();
}
?>
