<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Liste Sites references - article, auteur, breve, syndic
| Testeur de validite (par tranche affichee)
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_tabbord_sites() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;


//
// requis
//
include_spip('inc/tabbord_pres');
include_spip('inc/func_tabbord_sites');



//
// prepa
//
	$tbl_sites=array();
	$i=0;
	#sites ref dans art
	$qa=spip_query("SELECT id_article, nom_site, url_site, statut FROM spip_articles WHERE url_site!=''");
	if(spip_num_rows($qa)) {
		while($ra=spip_fetch_array($qa)){
			$tbl_sites[$i]['type']='article';
			$tbl_sites[$i]['id']=$ra['id_article'];
			$tbl_sites[$i]['statut']=$ra['statut'];
			$tbl_sites[$i]['nom']=$ra['nom_site'];
			$tbl_sites[$i]['url']=$ra['url_site'];
			$i++;
		}
	}

	#sites ref dans auteur
	$qat=spip_query("SELECT id_auteur, nom_site, url_site, statut, nom FROM spip_auteurs WHERE url_site!='' AND statut in('0minirezo', '1comite')");
	if(spip_num_rows($qat)) {
		while($rat=spip_fetch_array($qat)){
			$tbl_sites[$i]['type']='auteur';
			$tbl_sites[$i]['id']=$rat['id_auteur'];
			$tbl_sites[$i]['statut']=$rat['statut'];
			$tbl_sites[$i]['nom']=$rat['nom_site'];
			$tbl_sites[$i]['url']=$rat['url_site'];
			$tbl_sites[$i]['auteur']=$rat['nom'];
			$i++;
		}
	}

	#sites ref dans breve
	$qb=spip_query("SELECT id_breve, lien_titre, lien_url, statut FROM spip_breves WHERE lien_url!=''");
	if(spip_num_rows($qb)) {
		while($rb=spip_fetch_array($qb)){
			$tbl_sites[$i]['type']='breve';
			$tbl_sites[$i]['id']=$rb['id_breve'];
			$tbl_sites[$i]['statut']=$rb['statut'];
			$tbl_sites[$i]['nom']=$rb['lien_titre'];
			$tbl_sites[$i]['url']=$rb['lien_url'];
			$i++;
		}
	}

	#sites ref syndic (rub)
	$qs=spip_query("SELECT id_syndic, nom_site, url_site, statut, syndication FROM spip_syndic");
	if(spip_num_rows($qs)) {
		while($rs=spip_fetch_array($qs)){
			$tbl_sites[$i]['type']='syndic';
			$tbl_sites[$i]['id']=$rs['id_syndic'];
			$tbl_sites[$i]['statut']=$rs['statut'];
			$tbl_sites[$i]['nom']=$rs['nom_site'];
			$tbl_sites[$i]['url']=$rs['url_site'];
			$tbl_sites[$i]['syndic']=$rs['syndication'];
			$i++;
		}
	}

###
### h. 24/11/07 .. faire une config pour modifier le nombre de ligne du validator
###	
	// fixer le nombre de ligne du tableau (tranche)
	$fl=10;

	// recup $vl dans URL
	$dl=($_GET['vl']+0);

	$nbt=ceil($i/$fl);// nb tranches

	$liste_aff = array_slice($tbl_sites,$dl,$fl);


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

gros_titre(_T('tabbord:listes_sites_sur_'.$GLOBALS['meta']['nom_site']));

	// Présenter valeurs de la tranche de la requête

		echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
			tranches_liste($nba1,$i,$fl);
		echo "\n</div>\n";

	
	
	// entête ...
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n".
		"<tr>\n".
			"<th width='33%'>"._T('tabbord:site')."</th>\n".
			"<th width='7%'>"._T('tabbord:type')."</th>\n".
			"<th width='7%' class='center'>"._T('tabbord:statut')."</th>\n".
			"<th width='10%'>"._T('tabbord:syndique')."</th>\n".
			"<th width='43%' class='center'>"._T('tabbord:message_validation')."</th>".
		"</tr>\n";
	
	foreach($liste_aff as $k => $v) {
	
		switch ($v['type']) {
			case 'article' : $redirect = "articles";  break;
			case 'auteur' : $redirect = "auteur_infos";  break;
			case 'breve' : $redirect = "breves_voir";  break;
			case 'syndic' : $redirect = "sites";  break;
		}
		
		$nom=($v['nom']==''? _T('tabbord:url_sans_nom'): $v['nom']);
		
		echo "<tr class='liste'>\n";
		echo "<td><a href='".$v['url']."'>".couper(typo($nom),40)."</a></td>\n";
		echo "<td><a href='".generer_url_ecrire($redirect,"id_".$v['type']."=".$v['id'])."' title='".$v['auteur']."'>".
				$v['type']."</a></td>\n";
		echo "<td class='center'>".
			($v['type']=='auteur' ? bonhomme_statut($v) : icone_statut_objet_tabbord($v['type'],$v['statut']))
			."</td>\n";
		echo "<td class='center'>".$v['syndic']."</td>\n";
		echo "<td id='cheker-$k'>";// id sert pas pour le moment
		
		// tester validite du site
		if(_request('check')=='oui') {
		
			$test = check_connect($v['url']);
			$check = trad_check_connect($test);
			echo "[".$check['code']."] <span style='color:".$check['color'].";'>".$check['message']."</span>";
			if($check['remove']) {
				echo "&nbsp;<a target='_blank' class='spip_out' href='".$check['remove']."'>"._T('tabbord:ici')."</a>";
			}
		}
		
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "<tr><td colspan='5'><div align='right'>";
	echo "<form action='".self()."' method='post'>";
	echo "<input type='hidden' name='check' value='oui' />";
	echo _T('tabbord:verifier_sites_valides')."<input type='submit' value='OK' />";
	echo "</form></div></td></tr>";
	echo "</table>\n";

fin_cadre_formulaire();
echo "</div>";

//
//
echo fin_gauche(), fin_page();
}
?>
