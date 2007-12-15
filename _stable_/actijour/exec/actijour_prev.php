<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.53 - 12/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Visites et articles avant traitement spip
| ../tmp/visites/...
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_actijour_prev() {

# elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

#
# function requises ...
#
include_spip("inc/actijour_init");



#
# affichage
#
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('acjr:titre_actijour'), "suivi", "actijour_pg");
echo "<a name='haut_page'></a>";



# V�rifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
}



debut_gauche();
	entete_page(_T('acjr:titre_actijour'));
	
	echo "<p class='space_10'></p>";
	debut_boite_info();
	echo _T('acjr:info_page_actijour_prev');
	fin_boite_info();

/*---------------------------------------------------------------------------*\
scoty signe son mefait
\*---------------------------------------------------------------------------*/
	echo signature_plugin();




debut_droite();

/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
	echo onglets_actijour(_request('exec'));

/*---------------------------------------------------------------------------*\
Visites en attente de traitement par spip
\*---------------------------------------------------------------------------*/
	$tab = calcul_prevision_visites();

	$temps=$tab[0];
	$visites=$tab[1];
	$visites_a=$tab[2];
	
	if(count($temps)) { sort($temps); }
	
/*
echo "visites :<br /><pre>"; print_r($visites); echo "</pre>";

echo "visites_a :<br /><pre>"; print_r($visites_a); echo "</pre>";

echo "referers :<br /><pre>"; print_r($referers); echo "</pre>";

echo "referers_a :<br /><pre>"; print_r($referers_a); echo "</pre>";

echo "articles :<br /><pre>"; print_r($articles); echo "</pre>";

echo "articles :<br /><pre>"; print_r($temps); echo "</pre>";
*/

	$aff='';
	
	if($nb_articles = count($visites_a)) {
		$aff.= debut_cadre_relief("cal-jour.gif",true);
		
		$heure_f = date('H\hi',$temps[0]);
		$date_f = date('d/m/Y',$temps[0]);
		
		// nombre de visites 
		$aff.= "<div align='center' class='iconeoff verdana2 bold' style='clear:both;'>\n"
			. _T('acjr:depuis_date_visites_prev',
					array(
					'heure'=>$heure_f,
					'date'=>$date_f==date('d/m/Y')?'':'('.$date_f.')',
					'nb_visite'=>$visites,
					'nb_articles'=>$nb_articles
					))
			. "\n</div>\n";
		
		// tableau
		$aff.= "<table align='center' border='0' cellpadding='1' cellspacing='1' width='100%'>\n"
			. "<tr bgcolor='$couleur_foncee' class='head_tbl'>\n"
			. "<td width='8%'>"._T('acjr:numero_court')."</td>\n"
			. "<td width='82%'>"._T('acjr:titre_article')."</td>\n"
			. "<td width=10%>"._T('acjr:visites_jour')."</td>\n"
			. "</tr>\n";
		
		$ifond = 0;
		
		foreach($visites_a as $id_art => $nbr) {
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
			
			$q=spip_query("SELECT titre FROM spip_articles WHERE id_article="._q($id_art));
			$r=spip_fetch_array($q);
			$titre=typo($r['titre']);
	
			$aff.= "<tr bgcolor='$couleur'><td width='8%'>\n"
				. "<div align='right' class='verdana2'>"
				. $id_art
				. "</div>\n</td>"
				. "<td width='82%'>\n"
				. "<div align='left' class='verdana1' style='margin-left:5px;'><b>"
				. $titre
				. "</b></div></td>\n"
				. "<td width='10%'>\n"
				. "<div align='center' class='verdana2'><b>$nbr</b></div></td>\n"
				. "</tr>\n";
		}
		$aff.= "</table>";
	}	
	else {
		$aff.= "<p class='space_10'></p>"
			. "<div align='center' class='iconeoff bold verdana3' style='clear:both;'>"
			. _T('acjr:aucun_article_visite')."</div><br />\n";
	}
	$aff.= fin_cadre_relief(true);
	
	echo $aff;


# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin fonction
?>
